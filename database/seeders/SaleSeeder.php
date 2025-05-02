<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::disableQueryLog();

        $productIds = Product::pluck('id')->toArray();
        $totalSales = 5000000;
        $batchSize = 10000; // ✅ SAFE SIZE (well below MySQL limit)

        for ($i = 0; $i < $totalSales / $batchSize; $i++) {
            $sales = [];

            for ($j = 0; $j < $batchSize; $j++) {
                $sales[] = [
                    'product_id' => $productIds[array_rand($productIds)],
                    'quantity' => rand(1, 10),
                    'sold_at' => now()->subDays(rand(0, 365))->format('Y-m-d'),
                ];
            }

            DB::table('sales')->insert($sales); // insert in smaller batches
            echo ".";
        }
    }
}
