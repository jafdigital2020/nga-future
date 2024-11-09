@extends('layouts.master')
@section('title', 'Payroll Summary')
<style>
    /* Set a minimum width for select boxes and prevent them from shrinking */
    .form-group.select-focus {
        min-width: 200px;
        /* Adjust as needed */
        flex: 0 1 auto;
        /* Prevent shrinking */
    }

    /* Set a consistent width for buttons */
    .dropdown .btn,
    .form-group .form-control {
        min-width: 150px;
    }

    /* Date Range and Submit button */
    #dateRange,
    .btn-primary {
        min-width: 200px;
    }

</style>
@section('content')
@include('sweetalert::alert')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Payroll Summary</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payroll Summary</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-primary no-print" onclick="printTable()">
                        <i class="fa fa-print"></i> Print
                    </button>
                    <button class="btn btn-success no-print" onclick="downloadCSV()">
                        <i class="fa fa-download"></i> Download as CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Dropdown Options Form with Add Button -->
    <form id="optionsForm" action="{{ route('admin.payrollSummary') }}" method="GET"
        class="d-flex align-items-center flex-wrap p-2">

        <!-- Date Range Picker -->
        <div class="form-group form-focus mr-3">
            <input type="text" id="dateRange" name="date_range" class="form-control floating"
                placeholder="Select Date Range" value="{{ request()->get('date_range') }}">
            <label class="focus-label">Date Range</label>
        </div>

        <!-- Department Filter -->
        <div class="form-group form-focus select-focus mr-3">
            <select class="select floating form-control" name="department">
                <option value="" {{ request()->get('department') == '' ? 'selected' : '' }}>All Departments</option>
                @foreach ($departments as $dept)
                <option value="{{ $dept }}" {{ request()->get('department') == $dept ? 'selected' : '' }}>
                    {{ $dept }}
                </option>
                @endforeach
            </select>
            <label class="focus-label">Department</label>
        </div>


        <!-- Earnings Dropdown -->
        <div class="form-group form-focus mr-3">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="earningsDropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Select Earnings
                </button>
                <div class="dropdown-menu" aria-labelledby="earningsDropdown">
                    <a class="dropdown-item">
                        <input type="checkbox" id="select_all_earnings" onchange="toggleSelectAll('earnings')"> Select
                        All
                    </a>
                    @foreach ($earningsOptions as $id => $name)
                    <a class="dropdown-item">
                        <input type="checkbox" class="option-item-earnings" name="earnings[]" value="{{ $id }}"
                            {{ in_array($id, $selectedEarnings) ? 'checked' : '' }}>
                        {{ $name }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Deductions Dropdown -->
        <div class="form-group form-focus mr-3">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="deductionsDropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Select Deductions
                </button>
                <div class="dropdown-menu" aria-labelledby="deductionsDropdown">
                    <a class="dropdown-item">
                        <input type="checkbox" id="select_all_deductions" onchange="toggleSelectAll('deductions')">
                        Select All
                    </a>
                    @foreach ($deductionsOptions as $id => $name)
                    <a class="dropdown-item">
                        <input type="checkbox" class="option-item-deductions" name="deductions[]" value="{{ $id }}"
                            {{ in_array($id, $selectedDeductions) ? 'checked' : '' }}>
                        {{ $name }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Loans Dropdown -->
        <div class="form-group form-focus mr-3">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="loansDropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Select Loans
                </button>
                <div class="dropdown-menu" aria-labelledby="loansDropdown">
                    <a class="dropdown-item">
                        <input type="checkbox" id="select_all_loans" onchange="toggleSelectAll('loans')"> Select All
                    </a>
                    @foreach ($loansOptions as $id => $name)
                    <a class="dropdown-item">
                        <input type="checkbox" class="option-item-loans" name="loans[]" value="{{ $id }}"
                            {{ in_array($id, $selectedLoans) ? 'checked' : '' }}>
                        {{ $name }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="form-group form-focus mr-3">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>

    <!-- Payroll Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <!-- Conditionally display the table only if a date range is selected -->
                @if(request()->has('date_range') && request()->get('date_range') != '')
                <!-- Payroll Table -->
                <table id="payrollTable" class="table table-hover table-nowrap mb-0 datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th class="text-center">Payroll Period</th>
                            <th>Basic Pay</th>
                            <th>Gross Pay</th>
                            <th>Overtime Pay</th>
                            <!-- Conditionally Add Headers for Selected Earnings -->
                            @foreach ($earningsOptions as $id => $name)
                            @if (in_array($id, $selectedEarnings))
                            <th>{{ $name }} (Earnings)</th>
                            @endif
                            @endforeach
                            <!-- Conditionally Add Headers for Selected Deductions -->
                            @foreach ($deductionsOptions as $id => $name)
                            @if (in_array($id, $selectedDeductions))
                            <th>{{ $name }} (Deductions)</th>
                            @endif
                            @endforeach
                            <!-- Conditionally Add Headers for Selected Loans -->
                            @foreach ($loansOptions as $id => $name)
                            @if (in_array($id, $selectedLoans))
                            <th>{{ $name }} (Loans)</th>
                            @endif
                            @endforeach
                            <th>Total Earnings</th>
                            <th>Total Deductions</th>
                            <th style="color:red;">Net Pay</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $totalOvertimeHours = 0;
                        $totalEarnings = 0;
                        $totalDeductions = 0;
                        $totalNetPay = 0;
                        @endphp
                        @foreach ($summary as $payroll)
                        <tr>
                            <td>{{ $payroll->user->fName }} {{ $payroll->user->lName }}</td>
                            <td>{{ $payroll->user->department }}</td>
                            <td>{{ $payroll->start_date }} to {{ $payroll->end_date }}</td>
                            <td>{{ number_format($payroll->basic_pay, 2) }}</td>
                            <td>{{ number_format($payroll->gross_pay, 2) }}</td>
                            <td>{{ number_format($payroll->overtimeHours, 2) }}</td>
                            @php
                            $totalOvertimeHours += $payroll->overtimeHours;
                            @endphp

                            <!-- Display Selected Earnings -->
                            @foreach ($payroll->earnings as $earning)
                            @if (isset($earning['earning_id']) && in_array($earning['earning_id'], $selectedEarnings))
                            <td>{{ number_format($earning['amount'], 2) }}</td>
                            @endif
                            @endforeach

                            <!-- Display Selected Deductions -->
                            @foreach ($payroll->deductions as $deduction)
                            @if (isset($deduction['deduction_id']) && in_array($deduction['deduction_id'],
                            $selectedDeductions))
                            <td>{{ number_format($deduction['amount'], 2) }}</td>
                            @endif
                            @endforeach

                            <!-- Display Selected Loans -->
                            @foreach ($payroll->loans as $loan)
                            @if (isset($loan['loan_id']) && in_array($loan['loan_id'], $selectedLoans))
                            <td>{{ number_format($loan['amount'], 2) }}</td>
                            @endif
                            @endforeach

                            <td>₱{{ number_format($payroll->total_earnings, 2) }}</td>
                            @php $totalEarnings += $payroll->total_earnings; @endphp

                            <td>₱{{ number_format($payroll->total_deductions, 2) }}</td>
                            @php $totalDeductions += $payroll->total_deductions; @endphp

                            <td style="color:red;">₱{{ number_format($payroll->net_pay, 2) }}</td>
                            @php $totalNetPay += $payroll->net_pay; @endphp
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Totals:</strong></td>
                            <td><strong>{{ number_format($totalOvertimeHours, 2) }}</strong></td>
                            <!-- Empty cells for dynamically generated columns -->
                            @foreach ($selectedEarnings as $_) <td></td> @endforeach
                            @foreach ($selectedDeductions as $_) <td></td> @endforeach
                            @foreach ($selectedLoans as $_) <td></td> @endforeach
                            <td><strong>₱{{ number_format($totalEarnings, 2) }}</strong></td>
                            <td><strong>₱{{ number_format($totalDeductions, 2) }}</strong></td>
                            <td style="color:red;"><strong>₱{{ number_format($totalNetPay, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
                @else
                <p>Please select a date range to view the payroll summary.</p>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- JavaScript for Select All, Download CSV, and Print -->
<script>
    function toggleSelectAll(category) {
        const selectAllCheckbox = document.getElementById(`select_all_${category}`);
        const checkboxes = document.querySelectorAll(`.option-item-${category}`);

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }

    function downloadCSV() {
        const table = document.getElementById("payrollTable");
        let csvContent = "\uFEFF"; // Adding BOM for UTF-8 encoding

        // Get header cells for counting columns and alignment
        const headers = table.querySelectorAll("thead tr th");
        const headerValues = Array.from(headers).map(header => `"${header.innerText.trim()}"`);
        const columnCount = headerValues.length; // Total number of columns
        csvContent += headerValues.join(",") + "\n";

        // Get rows data from tbody
        const rows = table.querySelectorAll("tbody tr");
        rows.forEach(row => {
            const cells = row.querySelectorAll("td");
            const cellValues = Array.from(cells).map(cell => `"${cell.innerText.trim()}"`);
            csvContent += cellValues.join(",") + "\n";
        });

        // Prepare the totals row in tfoot with alignment
        const totalsRow = Array(columnCount).fill(""); // Start with empty cells for alignment
        const totalCells = table.querySelectorAll("tfoot tr td");
        let totalIndex = columnCount - totalCells.length; // Calculate the starting index for totals

        totalCells.forEach((totalCell, index) => {
            totalsRow[totalIndex + index] = `"${totalCell.innerText.trim()}"`;
        });

        // Add the totals row to CSV content
        csvContent += totalsRow.join(",") + "\n";

        // Download CSV
        const blob = new Blob([csvContent], {
            type: "text/csv;charset=utf-8;"
        });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "payroll_summary.csv";
        link.click();
    }

    function printTable() {
        const table = document.getElementById("payrollTable").outerHTML;
        const newWindow = window.open("", "", "width=1600, height=600");
        newWindow.document.write("<html><head><title>Print Payroll Summary</title></head><body>");
        newWindow.document.write("<h3>Payroll Summary</h3>");
        newWindow.document.write(table);
        newWindow.document.write("</body></html>");
        newWindow.document.close();
        newWindow.print();
    }

</script>



<script>
    $(document).ready(function () {
        // Initialize the date range picker
        $('#dateRange').daterangepicker({
            autoUpdateInput: false, // Prevent auto-filling
            locale: {
                cancelLabel: 'Clear' // Label for the clear button
            }
        });

        // Event listener to update the input with the selected dates
        $('#dateRange').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                'YYYY-MM-DD'));
        });

        // Event listener to clear the input when cancel is clicked
        $('#dateRange').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });
    });

</script>



@endsection
