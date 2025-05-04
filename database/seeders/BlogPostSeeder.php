<?php

namespace Database\Seeders;

use App\Models\Blog\Post;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'blog_author_id' => 1, // John Doe
                'blog_category_id' => 1, // Technology
                'title' => 'The Future of Artificial Intelligence',
                'slug' => 'future-of-artificial-intelligence',
                'content' => 'This is a detailed post about AI advancements and their impact on society...',
                'published_at' => now()->subDays(2),
                'seo_title' => 'AI Future Trends',
                'seo_description' => 'Explore how artificial intelligence is shaping our future',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blog_author_id' => 2, // Jane Smith
                'blog_category_id' => 2, // Lifestyle
                'title' => '10 Tips for a Healthier Lifestyle',
                'slug' => 'healthier-lifestyle-tips',
                'content' => 'Learn these simple tricks to improve your daily wellbeing...',
                'published_at' => now()->subDay(),
                'seo_title' => 'Healthy Living Tips',
                'seo_description' => 'Discover 10 practical tips for better health and wellness',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blog_author_id' => 3, // Mike Johnson
                'blog_category_id' => 3, // Travel
                'title' => 'Hidden Gems of Europe',
                'slug' => 'hidden-gems-europe',
                'content' => 'Explore these lesser-known European destinations...',
                'published_at' => now()->subDay(),
                'seo_title' => 'European Hidden Gems',
                'seo_description' => 'Uncover secret travel spots in Europe',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $sampleFiles = [
            storage_path('sample-uploads/post-1.jpg'),
            storage_path('sample-uploads/post-2.jpg'),
            storage_path('sample-uploads/post-3.jpg'),
        ];

        foreach ($posts as $index => $postData) {
            $post = Post::create($postData);

            $filePath = $sampleFiles[$index] ?? null;

            if ($filePath && file_exists($filePath)) {
                $post->addMedia($filePath)
                     ->usingFileName(basename($filePath))
                     ->preservingOriginal()
                     ->toMediaCollection('blog-images');
            }
        }
    }
}
