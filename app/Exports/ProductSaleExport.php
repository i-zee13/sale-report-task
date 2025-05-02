<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping; 

class ProductSaleExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Product Name',
            'Total Quantity',
            'Total Sales'
        ];
    }

    public function map($row): array
    {
        return [
            $row->product_id,
            $row->product_name,
            $row->total_quantity,
            $row->total_sales,
        ];
    }
}