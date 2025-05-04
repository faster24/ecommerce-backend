<?php

namespace App\Http\Controllers;

use App\Models\Shop\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $products = Product::paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            $media = $product->getMedia('product-images');
            $imageUrls = $media->map(function ($item) {
                return $item->getUrl();
            });

            $product->image_urls = $imageUrls;

            return $product;
        });

        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::with(['brand', 'categories'])
            ->findOrFail($id);

        $media = $product->getMedia('product-images'); // Default collection
        $image = $media->first() ? $media->first()->getUrl() : null; // Get URL of the first image

        $productData = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'description' => $product->description,
            'stock_qty' => $product->stock_qty,
            'image' => $image,
            'brand' => $product->brand ? [
                'id' => $product->brand->id,
                'name' => $product->brand->name,
            ] : null,
            'categories' => $product->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                ];
            })->toArray(),
        ];

        return response()->json($productData);
    }

    public function search(Request $request)
    {
        $categoryId = $request->input('category_id');
        $brandId = $request->input('brand_id');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        // Start building the query
        $query = Product::query();

        // Filter by category if category_id is provided
        if ($categoryId) {
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('shop_categories.id', $categoryId);
            });
        }

        // Filter by brand if brand_id is provided
        if ($brandId) {
            $query->where('shop_brand_id', $brandId);
        }

        // Filter by price range if min_price and/or max_price are provided
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        // Execute the query and get the results
        $products = $query->with(['brand', 'categories', 'media'])->get();

        return response()->json([
            'data' => $products,
        ]);
    }

    public function filterByName(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
        ]);

        $query = Product::query()
            ->with('media') // Load media relationship
            ->select('shop_products.*'); // Explicitly select product fields

        if ($name = $request->query('name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        // Paginate results (default 10 per page)
        $products = $query->paginate(10)->through(function ($product) {
            $product->media->each(function ($media) {
                $media->url = $media->getUrl(); // Include media URL
            });
            return $product;
        });

        // Return JSON response
        return response()->json([
            'status' => 'success',
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
        ], 200);
    }

    public function getRelatedProducts($id)
    {
        try {
            $product = Product::with('brand')->find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found',
                ], 404);
            }

            $brandId = $product->shop_brand_id;

            if (!$brandId) {
                return response()->json([
                    'message' => 'No brand associated with this product',
                    'related_products' => [],
                ], 200);
            }

            $relatedProducts = Product::where('shop_brand_id', $brandId)
                ->where('id', '!=', $id)
                ->where('is_visible', true)
                ->with('media')
                ->limit(5)
                ->get(['id', 'name', 'slug', 'price', 'description', 'stock_qty']);

            $relatedProducts->transform(function ($relatedProduct) {
                $media = $relatedProduct->getMedia('product-images');
                $image = $media->first() ? $media->first()->getUrl() : null;
                $relatedProduct->image = $image;
                unset($relatedProduct->media);
                return $relatedProduct;
            });

            return response()->json([
                'message' => 'Related products retrieved successfully',
                'related_products' => $relatedProducts,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve related products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
