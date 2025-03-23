<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Electronics',
        ]);
        Product::create([
            'category_id' => 1,
            'name' => 'Laptop',
            'price' => 1000,
        ]);

        $user = User::where('email', 'apo@gmail.com')->first();
        if (!$user) {
            User::create([
                'name' => 'Apo',
                'email' => 'apo@gmail.com',
                'password' => '123123'
            ]);
        }
    }
}
