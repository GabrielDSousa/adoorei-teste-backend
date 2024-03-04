<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Dev user for local development
        // password: password
        if (!app()->environment('production')) {
            $user = \App\Models\User::factory()->create([
                'email' => 'email@email.com',
            ]);
        }

        $users = \App\Models\User::factory(10)->create();
        $products = \App\Models\Product::factory(10)->create();
        $sales = \App\Models\Sale::factory(10)->recycle($users)->create();
        foreach ($sales as $key => $sale) {
            $sale->products()->attach($products->random()->id, ['quantity' => rand(1, 10)]);
        }
    }
}
