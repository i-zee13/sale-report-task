<?php

use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\SalesController;

Route::get('/', function () {
    return view('welcome');
});
 
Route::get('/sales-report',         [SalesController::class, 'index'])->name('sales.report');
Route::get('/sales-view',           [SalesController::class, 'view'])->name('sales.view');
Route::get('/sales-export-csv',     [SalesController::class, 'exportCsv'])->name('sales.export');
Route::get('/sales-report-json',    [SalesController::class, 'reportJson'])->name('sales.report-json'); 