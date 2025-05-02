<?php

namespace App\Http\Controllers;

use App\Exports\ProductSaleExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        return view('report');
    } 
    public function view(Request $request)
    {
        $start          = $request->query('start_date');
        $end            = $request->query('end_date'); 
        $limit          = $request->query('limit', 10);
        $report         = self::salesReport($start, $end, $limit); 
        return view('report', [
                                'reports'    => $report,
                                'start_date' => $start,
                                'end_date'   => $end
                            ]);
    }
    public function exportCsv(Request $request)
    {
        $start          = $request->query('start_date');
        $end            = $request->query('end_date'); 
        $limit          = $request->query('limit', 10);
        $report         = self::salesReport($start, $end, $limit); 
        $fileName       = "sales_report_{$start}_to_{$end}.csv";
        return Excel::download(new ProductSaleExport($report), $fileName);
    }
    public function reportJson(Request $request)
    {
        $start          = $request->query('start_date');
        $end            = $request->query('end_date'); 
        $limit          = $request->query('limit', 10);
        $report         = self::salesReport($start, $end, $limit);
        return response()->json($report);
    }   
    static function salesReport($start, $end, $limit){
        $query          = " 1=1"; 
        $current_date   = date('Y-m-d'); 
        if (isset($start) != '' && isset($end) != '') {
            $query      .= " AND DATE(s.sold_at) BETWEEN '$start' AND '$end'";
        } else {
            $query      .=  " AND  DATE(s.sold_at) = '$current_date'";
        }  
        $cacheKey       = "sales_report_{$start}_{$end}"; 
        $report         = Cache::remember($cacheKey, 300, function () use ($query, $limit) {
    
                $sales = DB::select("
                                SELECT 
                                    p.id AS product_id,
                                    p.name AS product_name,
                                    SUM(s.quantity) AS   total_quantity,
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
        return $report;
    }
}
