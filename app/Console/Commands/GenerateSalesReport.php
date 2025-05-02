<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GenerateSalesReport extends Command {
    protected $signature    = 'report:sales {start_date} {end_date}';
    protected $description  = 'Generate top 10 product sales report';

    public function handle(): void {
        $start              = $this->argument('start_date');
        $end                = $this->argument('end_date');
        $query              = " 1=1"; 
        $current_date       = date('Y-m-d'); 
        $limit              = 10;
        if (isset($start)   != '' && isset($end) != '') {
            $query          .= " AND DATE(s.sold_at) BETWEEN '$start' AND '$end'";
        } else {
            $query          .= " AND  DATE(s.sold_at) = '$current_date'";
        }  
        $cacheKey           = "sales_report_{$start}_{$end}"; 
        $report             = Cache::remember($cacheKey, 300, function () use ($query, $limit) {
    
                                        $sales = DB::select("
                                                        SELECT 
                                                            p.id AS product_id,
                                                            p.name AS product_name,
                                                            SUM(s.quantity) AS total_quantity,
                                                            SUM(s.quantity * p.price) AS total_sales
                                                        FROM 
                                                            sales s
                                                        INNER JOIN 
                                                            products p ON s.product_id = p.id
                                                        WHERE 
                                                            $query
                                                        GROUP BY 
                                                            p.id, p.name
                                                        ORDER BY 
                                                            total_sales DESC
                                                        LIMIT $limit;
                                                    ");  
                                                    
                                                return $sales;
                    });
                    $report =  collect($report)->map(function($row) {
                                                    return [
                                                        $row->product_id,
                                                        $row->product_name,
                                                        $row->total_quantity,
                                                        $row->total_sales
                                                    ];
                                                })->toArray();
                    $this->table([
                        'Product ID', 'Product Name', 'Total Quantity', 'Total Sales'
                    ], $report);
        }
}
