<?

namespace App\Http\Controllers;

use App\Models\Shop\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Fetch all products with pagination
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Default to 10 items per page

        // Fetch paginated products
        $products = Product::paginate($perPage);

        // Add image URLs to each product
        $products->getCollection()->transform(function ($product) {
            // Get the media (images) associated with the product
            $media = $product->getMedia('product-images'); // 'images' is the media collection name
            $imageUrls = $media->map(function ($item) {
                return $item->getUrl(); // Get the full URL of the image
            });

            // Add the image URLs to the product object
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
}
