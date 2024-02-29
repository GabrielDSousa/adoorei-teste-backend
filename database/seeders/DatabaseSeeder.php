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
        if (!app()->environment('production')) {
            \App\Models\User::factory()->create([
                'name' => 'Developer User',
                'email' => 'dev@abc.com',
            ]);
        }

        \App\Models\Product::factory(10)->create();
    }
}
