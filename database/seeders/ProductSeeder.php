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
    public function run(): void {
        $chunkSize = 1000;
        $totalProducts = 100000;
        
        for ($i = 0; $i < $totalProducts; $i += $chunkSize) {
            $products = Product::factory()->count($chunkSize)->make()->toArray();
            Product::insert($products);
        }
    }
}
