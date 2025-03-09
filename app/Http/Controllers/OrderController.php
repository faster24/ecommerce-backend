<?php

namespace App\Http\Controllers;

use App\Models\Shop\Order;
use App\Models\Shop\OrderItem;
use App\Models\Shop\OrderAddress;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Buy Order: Create a new order from the cart
    public function buyOrder(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|min:1',
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:shop_products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'shipping_address.country' => 'nullable|string|max:100',
            'shipping_address.street' => 'required|string|max:255',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'nullable|string|max:100',
            'shipping_address.zip' => 'nullable|string|max:20',
        ]);

        $customerId = $validated['user_id'];
        $cart = $validated['cart'];
        $shippingAddress = $validated['shipping_address'];

        // Calculate total price from cart
        $totalPrice = collect($cart)->sum(function ($item) {
            return $item['quantity'] * $item['price'];
        });

        return DB::transaction(function () use ($customerId, $cart, $totalPrice, $shippingAddress) {
                // Generate a unique order number
                $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

                // Create the order
                $order = Order::create([
                    'shop_customer_id' => $customerId,
                    'number' => $orderNumber,
                    'total_price' => $totalPrice,
                    'status' => OrderStatus::New,
                    'shipping_price' => 500.00, // Adjust dynamically if needed
                ]);

                // Create order items
                foreach ($cart as $item) {
                    OrderItem::create([
                        'shop_order_id' => $order->id,
                        'shop_product_id' => $item['id'],
                        'qty' => $item['quantity'],
                        'unit_price' => $item['price'],
                    ]);
                }

                // Create shipping address with multiple fields
                OrderAddress::create([
                    'addressable_id' => $order->id,
                    'addressable_type' => Order::class,
                    'country' => $shippingAddress['country'],
                    'street' => $shippingAddress['street'],
                    'city' => $shippingAddress['city'],
                    'state' => $shippingAddress['state'],
                    'zip' => $shippingAddress['zip'],
                ]);

                // Load relationships for response
                $order->load(['items.product', 'address']);

                return response()->json([
                    'id' => $order->id,
                    'number' => $order->number,
                    'status' => $order->status->value,
                    'total_price' => $order->total_price,
                    'shipping_price' => $order->shipping_price,
                    'shipping_method' => $order->shipping_method,
                    'shipping_address' => [
                        'country' => $order->address->country,
                        'street' => $order->address->street,
                        'city' => $order->address->city,
                        'state' => $order->address->state,
                        'zip' => $order->address->zip,
                    ],
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_id' => $item->shop_product_id,
                            'product_name' => $item->product->name ?? 'N/A',
                            'quantity' => $item->qty,
                            'unit_price' => $item->unit_price,
                            'total' => $item->qty * $item->unit_price,
                        ];
                    }),
                    'created_at' => $order->created_at,
                ], 201);
            });
           }

    // Get the current order (new or processing) for the authenticated customer
    public function currentOrder()
    {
        $user = Auth::user();
        if (!$user || !$user->customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customerId = $user->customer->id;

        $order = Order::with(['items.product', 'address', 'payments'])
            ->where('shop_customer_id', $customerId)
            ->whereIn('status', [OrderStatus::New, OrderStatus::Processing])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$order) {
            return response()->json(['message' => 'No current order found'], 404);
        }

        return response()->json([
            'id' => $order->id,
            'number' => $order->number,
            'status' => $order->status->value,
            'total_price' => $order->total_price,
            'currency' => $order->currency,
            'shipping_price' => $order->shipping_price,
            'shipping_method' => $order->shipping_method,
            'notes' => $order->notes,
            'shipping_address' => $order->address ? [
                'country' => $order->address->country,
                'street' => $order->address->street,
                'city' => $order->address->city,
                'state' => $order->address->state,
                'zip' => $order->address->zip,
            ] : null,
            'items' => $order->items->map(function ($item) {
                return [
                    'product_id' => $item->shop_product_id,
                    'product_name' => $item->product->name ?? 'N/A',
                    'quantity' => $item->qty,
                    'unit_price' => $item->unit_price,
                    'total' => $item->qty * $item->unit_price,
                ];
            }),
            'payments' => $order->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'method' => $payment->method,
                    'created_at' => $payment->created_at,
                ];
            }),
            'created_at' => $order->created_at,
        ]);
    }

    public function orderHistory(Request $request)
    {
        // Get the authenticated customer using the Sanctum guard
        $customer = Auth::guard('sanctum')->user();

        // Check if customer is authenticated
        if (!$customer) {
            return response()->json(['message' => 'Unauthorized - Customer not authenticated'], 401);
        }

        // Fetch orders for the customer
        $orders = Order::with([
            'items.product',
            'address',
            'payments'
        ])
            ->where('shop_customer_id', $customer->id)
            ->whereIn('status', [OrderStatus::New, OrderStatus::Processing])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No order history found'], 404);
        }

        // Map orders to the desired response format
        return response()->json(
            $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'number' => $order->number,
                    'status' => $order->status->value, // Enum value
                    'total_price' => $order->total_price,
                    'shipping_price' => $order->shipping_price,
                    'shipping_method' => $order->shipping_method,
                    'shipping_address' => $order->address ? [
                        'country' => $order->address->country,
                        'street' => $order->address->street,
                        'city' => $order->address->city,
                        'state' => $order->address->state,
                        'zip' => $order->address->zip,
                    ] : null,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_id' => $item->shop_product_id,
                            'product_name' => $item->product->name ?? 'N/A',
                            'quantity' => $item->qty,
                            'unit_price' => $item->unit_price,
                            'total' => $item->qty * $item->unit_price,
                        ];
                    }),
                    'created_at' => $order->created_at,
                ];
            })
        );
    }
}
