<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $genders = ['male', 'female'];
        $cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia', 'San Antonio', 'San Diego', 'Dallas', 'San Jose'];
        $states = ['NY', 'CA', 'IL', 'TX', 'AZ', 'PA', 'FL', 'OH', 'GA', 'NC'];

        // Create 50 customers
        for ($i = 0; $i < 50; $i++) {
            $firstName = $this->generateFirstName($genders[array_rand($genders)]);
            $lastName = $this->generateLastName();
            $email = fake()->email();

            Customer::create([
                'name' => "$firstName $lastName",
                'email' => $email,
                'password' => Hash::make('password'), // Default password
                'photo' => rand(0, 1) ? 'customers/' . Str::uuid() . '.jpg' : null,
                'gender' => $genders[array_rand($genders)],
                'phone' => $this->generatePhoneNumber(),
                'city' => rand(0, 1) ? $cities[array_rand($cities)] : null,
                'state' => rand(0, 1) ? $states[array_rand($states)] : null,
                'birthday' => rand(0, 1) ? $this->generateBirthday() : null,
                'created_at' => now()->subDays(rand(0, 365)),
                'deleted_at' => rand(0, 10) > 8 ? now() : null, // ~20% chance of being soft deleted
            ]);
        }
    }

    protected function generateFirstName(string $gender): string
    {
        $maleNames = ['James', 'John', 'Robert', 'Michael', 'William', 'David', 'Richard', 'Joseph', 'Thomas', 'Charles'];
        $femaleNames = ['Mary', 'Patricia', 'Jennifer', 'Linda', 'Elizabeth', 'Barbara', 'Susan', 'Jessica', 'Sarah', 'Karen'];

        return $gender === 'male'
            ? $maleNames[array_rand($maleNames)]
            : $femaleNames[array_rand($femaleNames)];
    }

    protected function generateLastName(): string
    {
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Miller', 'Davis', 'Garcia', 'Rodriguez', 'Wilson'];
        return $lastNames[array_rand($lastNames)];
    }

    protected function generatePhoneNumber(): string
    {
        return sprintf('(%03d) %03d-%04d', rand(200, 999), rand(200, 999), rand(1000, 9999));
    }

    protected function generateBirthday(): string
    {
        // Generate dates between 18 and 70 years ago
        $start = now()->subYears(70);
        $end = now()->subYears(18);
        $timestamp = mt_rand($start->timestamp, $end->timestamp);
        return date('Y-m-d', $timestamp);
    }
}
