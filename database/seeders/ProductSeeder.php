<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'Latest iPhone with advanced features',
                'price' => 999.99,
                'stock' => 50,
                'category' => 'Electronics',
                'is_active' => true,
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'description' => 'High-end Android smartphone',
                'price' => 899.99,
                'stock' => 30,
                'category' => 'Electronics',
                'is_active' => true,
            ],
            [
                'name' => 'MacBook Pro M3',
                'description' => 'Professional laptop with M3 chip',
                'price' => 1999.99,
                'stock' => 25,
                'category' => 'Computers',
                'is_active' => true,
            ],
            [
                'name' => 'Dell XPS 13',
                'description' => 'Ultrabook for professionals',
                'price' => 1299.99,
                'stock' => 20,
                'category' => 'Computers',
                'is_active' => true,
            ],
            [
                'name' => 'AirPods Pro',
                'description' => 'Wireless earbuds with noise cancellation',
                'price' => 249.99,
                'stock' => 100,
                'category' => 'Accessories',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
