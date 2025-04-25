<?php

namespace Database\Seeders;

use App\Models\Shop\Customer;
use App\Models\Shop\Order;
use App\Models\Shop\OrderItem;
use App\Models\Shop\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $customers = Customer::all();

        $statuses = ['new', 'processing', 'shipped', 'delivered', 'cancelled'];

        Order::factory(50)->create([
            'shop_customer_id' => fn() => $customers->random()->id,
            'status' => fn() => $statuses[array_rand($statuses)],
            'number' => fn() => 'ORD-' . Str::upper(Str::random(8)),
            'shipping_price' => fn() => rand(5, 20),
        ])->each(function (Order $order) use ($products) {
            $itemsCount = rand(1, 5);
            $totalPrice = 0;

            for ($i = 0; $i < $itemsCount; $i++) {
                $product = $products->random();
                $qty = rand(1, 5);
                $unitPrice = $product->price;

                OrderItem::create([
                    'shop_order_id' => $order->id,
                    'shop_product_id' => $product->id,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                ]);

                $totalPrice += $qty * $unitPrice;
            }

            $order->total_price = $totalPrice + $order->shipping_price;
            $order->save();

            if (rand(0, 1)) {
                $order->update([
                    'shipping_address' => $this->generateAddress(),
                    'billing_address' => $this->generateAddress(),
                ]);
            }
        });
    }

    protected function generateAddress(): string
    {
        $streets = ['Main St', 'First Ave', 'Park Blvd', 'Oak Lane', 'Maple Rd'];
        $cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'];

        return rand(100, 9999) . ' ' . $streets[array_rand($streets)] . ', ' .
               $cities[array_rand($cities)] . ', ' .
               strtoupper(Str::random(2)) . ' ' . rand(10000, 99999);
    }

}
