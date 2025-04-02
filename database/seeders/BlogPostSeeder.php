<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'image' => 'https://img.freepik.com/free-photo/blog-notes-concept-with-wooden-blocks-pen-black-notebook-top-view_176474-10347.jpg?t=st=1743579377~exp=1743582977~hmac=f89da90cc7760609acf8c190cd51ae570efebd6e1182c392c7065bab94aee1d1&w=826',
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
                'image' => 'https://img.freepik.com/free-photo/blog-notes-concept-with-wooden-blocks-pen-black-notebook-top-view_176474-10347.jpg?t=st=1743579377~exp=1743582977~hmac=f89da90cc7760609acf8c190cd51ae570efebd6e1182c392c7065bab94aee1d1&w=826',
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
                'image' => 'https://img.freepik.com/free-photo/blog-notes-concept-with-wooden-blocks-pen-black-notebook-top-view_176474-10347.jpg?t=st=1743579377~exp=1743582977~hmac=f89da90cc7760609acf8c190cd51ae570efebd6e1182c392c7065bab94aee1d1&w=826',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('blog_posts')->insert($posts);
    }
}
