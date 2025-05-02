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
        <h1 class="my-4">Sales Report</h1>
        
        <div class="card filter-form">
            <div class="card-body">
                <form action="{{ route('sales.view') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="text" class="form-control date-picker" id="start_date" name="start_date" 
                               value="{{ $start_date ?? '' }}" placeholder="YYYY-MM-DD" required>
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="text" class="form-control date-picker" id="end_date" name="end_date" 
                               value="{{ $end_date ?? '' }}" placeholder="YYYY-MM-DD" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                        <a href="{{ route('sales.export') }}" 
                        class="btn btn-sm btn-success">Export All Sales to CSV</a>
                    </div>
                </form>
            </div>
        </div>
        
        @if(isset($reports) && count($reports) > 0)
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Results for {{ $start_date }} to {{ $end_date }}</h5>
                <a href="{{ route('sales.export') }}?start_date={{ $start_date }}&end_date={{ $end_date }}" 
                class="btn btn-sm btn-success">Export to CSV</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="salesTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Total Quantity</th>
                                <th>Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                            <tr>
                                <td>{{ $report->product_id }}</td>
                                <td>{{ $report->product_name }}</td>
                                <td>{{ $report->total_quantity }}</td>
                                <td>${{ number_format($report->total_sales, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @elseif(isset($reports) && count($reports) == 0)
        <div class="alert alert-info">
            No sales data found for the selected date range.
        </div>
        @endif
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