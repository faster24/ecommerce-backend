<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            $createdAt = $faker->dateTimeBetween('-1 year', 'now');
            Ticket::create([
                'user_id' => 1,
                'subject' => $faker->sentence(10),
                'message' => $faker->paragraph(3),
                'status' => $faker->randomElement(['pending', 'resolved']),
                'created_at' => $createdAt,
                'updated_at' => $faker->dateTimeBetween($createdAt, 'now'),
            ]);
        }
    }
}
