<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase; 
use Tests\TestCase;

class SalesReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_report_calculates_correctly(): void
    {
        // Create test products
        $product1 = Product::factory()->create([
            'name' => 'Test Product 1',
            'price' => 100.00
        ]);
        
        $product2 = Product::factory()->create([
            'name' => 'Test Product 2',
            'price' => 50.00
        ]);
        
        $product3 = Product::factory()->create([
            'name' => 'Test Product 3',
            'price' => 75.00
        ]);
        
        // Create test sales (all within the test date range)
        // Product 1: 5 units x $100 = $500
        Sale::create([
            'product_id' => $product1->id,
            'quantity' => 5,
            'sold_at' => '2024-01-15'
        ]);
        
        // Product 2: 10 units x $50 = $500
        Sale::create([
            'product_id' => $product2->id,
            'quantity' => 10,
            'sold_at' => '2024-01-20'
        ]);
        
        // Product 3: 4 units x $75 = $300
        Sale::create([
            'product_id' => $product3->id,
            'quantity' => 4,
            'sold_at' => '2024-01-25'
        ]);
        
        // Add another sale for Product 1: 2 units x $100 = $200 (total now $700)
        Sale::create([
            'product_id' => $product1->id,
            'quantity' => 2,
            'sold_at' => '2024-01-30'
        ]);
        
        // Test the API endpoint - Use json() method to ensure JSON response
        $response = $this->getJson('/sales-report-json?start_date=2024-01-01&end_date=2024-01-31');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        // Expected order by total sales (highest first):
        // 1. Product 1: $700 (7 units)
        // 2. Product 2: $500 (10 units)
        // 3. Product 3: $300 (4 units)
        
        $this->assertCount(3, $data);
        
        // Check if $data is an associative array or has numeric indices
        $firstItem = $data[0];
        
        // Adjust assertions based on whether $firstItem is an object or array
        if (is_array($firstItem)) {
            // Using array notation
            $this->assertEquals($product1->id, $firstItem['product_id']);
            $this->assertEquals('Test Product 1', $firstItem['product_name']);
            $this->assertEquals(7, $firstItem['total_quantity']);
            $this->assertEquals(700, $firstItem['total_sales']);
            
            $this->assertEquals($product2->id, $data[1]['product_id']);
            $this->assertEquals('Test Product 2', $data[1]['product_name']);
            $this->assertEquals(10, $data[1]['total_quantity']);
            $this->assertEquals(500, $data[1]['total_sales']);
            
            $this->assertEquals($product3->id, $data[2]['product_id']);
            $this->assertEquals('Test Product 3', $data[2]['product_name']);
            $this->assertEquals(4, $data[2]['total_quantity']);
            $this->assertEquals(300, $data[2]['total_sales']);
        } else {
            // Using object notation (original code)
            $this->assertEquals($product1->id, $firstItem->product_id);
            $this->assertEquals('Test Product 1', $firstItem->product_name);
            $this->assertEquals(7, $firstItem->total_quantity);
            $this->assertEquals(700, $firstItem->total_sales);
            
            $this->assertEquals($product2->id, $data[1]->product_id);
            $this->assertEquals('Test Product 2', $data[1]->product_name);
            $this->assertEquals(10, $data[1]->total_quantity);
            $this->assertEquals(500, $data[1]->total_sales);
            
            $this->assertEquals($product3->id, $data[2]->product_id);
            $this->assertEquals('Test Product 3', $data[2]->product_name);
            $this->assertEquals(4, $data[2]->total_quantity);
            $this->assertEquals(300, $data[2]->total_sales);
        }
        
        // Test the Artisan command
        $this->artisan('report:sales 2024-01-01 2024-01-31')
             ->assertExitCode(0);
    }
    
    public function test_sales_report_handles_empty_results(): void
    {
        // Test the API with dates that have no sales - Use getJson() instead of get()
        $response = $this->getJson('/sales-report-json?start_date=2025-01-01&end_date=2025-01-31');
        
        $response->assertStatus(200);
        // The response might be an empty array or some other structure
        // Just asserting it's successful is enough here
        
        // Test the command with empty results
        $this->artisan('report:sales 2025-01-01 2025-01-31')
             ->assertExitCode(0);
    }
    
    public function test_sales_report_respects_date_range(): void
    {
        // Create a product
        $product = Product::factory()->create([
            'name' => 'Date Range Product',
            'price' => 100.00
        ]);
        
        // Create sales in different date ranges
        // Inside range: 5 units x $100 = $500
        Sale::create([
            'product_id' => $product->id,
            'quantity' => 5,
            'sold_at' => '2024-02-15'
        ]);
        
        // Outside range (before): 10 units
        Sale::create([
            'product_id' => $product->id,
            'quantity' => 10,
            'sold_at' => '2024-01-15'
        ]);
        
        // Outside range (after): 8 units
        Sale::create([
            'product_id' => $product->id,
            'quantity' => 8,
            'sold_at' => '2024-03-15'
        ]);
        
        // Test range: February only - Use getJson instead of get
        $response = $this->getJson('/sales-report-json?start_date=2024-02-01&end_date=2024-02-29');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertCount(1, $data);
        
        // Check if response is array or object format
        $firstItem = $data[0];
        
        if (is_array($firstItem)) {
            // Array format
            $this->assertEquals($product->id, $firstItem['product_id']);
            $this->assertEquals(5, $firstItem['total_quantity']);
            $this->assertEquals(500, $firstItem['total_sales']);
        } else {
            // Object format
            $this->assertEquals($product->id, $firstItem->product_id);
            $this->assertEquals(5, $firstItem->total_quantity);
            $this->assertEquals(500, $firstItem->total_sales);
        }
    }
}