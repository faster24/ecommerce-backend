<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogAuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'photo' => 'authors/john-doe.jpg',
                'bio' => 'Tech enthusiast with 10 years of experience in software development.',
                'github_handle' => 'johndoe',
                'twitter_handle' => '@johndoe',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'photo' => 'authors/jane-smith.jpg',
                'bio' => 'Lifestyle blogger and wellness coach.',
                'github_handle' => null,
                'twitter_handle' => '@janesmith',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@example.com',
                'photo' => null,
                'bio' => 'Travel writer exploring the world one city at a time.',
                'github_handle' => 'mikej',
                'twitter_handle' => '@mikejohnson',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert the authors into the database
        DB::table('blog_authors')->insert($authors);
    }
}
