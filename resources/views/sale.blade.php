<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- DatePicker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body { padding: 20px; }
        .filter-form { margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Sales List</h1>
        <div class="container">
    <h2 class="mb-4">Sales Records</h2>
    <div class="col-md-2 d-flex align-items-end" style="float: inline-end;margin-bottom: 15px;">
       <a href="{{ route('sales.report') }}" class="btn btn-primary w-100" style="margin-right: 10px;">Report</a>
       <a href="{{ route('sales.export') }}" class="btn btn-primary w-100">Export Csv</a>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>{{ $sale->product->name ?? 'N/A' }}</td>
                    <td>{{ $sale->quantity }}</td>
                    <td>{{ $sale->quantity * $sale->product->price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

<div class="d-flex justify-content-center mt-4">
    {{ $sales->links('pagination::bootstrap-5') }}
</div>
</div>
  
        
       
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- Flatpickr (for date picker) -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#salesTable').DataTable({
                "order": [[3, "desc"]],
                "pageLength": 10,
                "searching": true,
                "responsive": true
            });
            
            // Initialize date pickers
            $(".date-picker").flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true
            });
        });
    </script>
</body>
</html>