<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Post;

class BlogPostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['author', 'category', 'tags']) // Eager load relationships
            ->whereNotNull('published_at')                  // Only published posts
            ->where('published_at', '<=', now())            // Published up to current date
            ->orderBy('published_at', 'desc')               // Latest first
            ->paginate(10);                                 // Paginate with 10 posts per page

        return response()->json([
            'success' => true,
            'data' => $posts->items(),                      // Only the posts for the current page
            'pagination' => [                               // Pagination metadata
                'total' => $posts->total(),                 // Total number of posts
                'per_page' => $posts->perPage(),            // Posts per page
                'current_page' => $posts->currentPage(),    // Current page number
                'last_page' => $posts->lastPage(),          // Last page number
                'from' => $posts->firstItem(),              // Index of the first item
                'to' => $posts->lastItem(),                 // Index of the last item
                'next_page_url' => $posts->nextPageUrl(),   // URL for the next page
                'prev_page_url' => $posts->previousPageUrl(), // URL for the previous page
            ],
        ], 200);
    }

    public function show($id)
    {
        $post = Post::with(['author', 'category', 'tags', 'comments']) // Include comments too
            ->where('id', $id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $post,
        ], 200);
    }
}
