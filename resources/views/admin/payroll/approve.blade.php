@extends('layouts.master') @section('title', 'Approved Timesheet') @section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Approved Timesheet</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Approved Timesheet</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    <form action="{{ route('approvedTimeAdmin') }}" method="GET">
        <div class="row filter-row">
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus">
                    <input type="text" class="form-control floating" name="name" value="{{ request('name') }}">
                    <label class="focus-label">Employee Name</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="department">
                        <option value="">--Select Department--</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->department }}"
                            {{ $dept->department == $departments ? 'selected' : '' }}>
                            {{ $dept->department }}
                        </option>
                        @endforeach
                    </select>
                    <label class="focus-label">Select Department</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" id="monthSelect" name="cutoff_period">
                        <option value="" {{ $cutoffPeriod == '' ? 'selected' : '' }}>-- Select Cut-off Period --
                        </option>
                        <option value="December - January 1st Cut-off"
                            {{ $cutoffPeriod == 'December - January 1st Cut-off' ? 'selected' : '' }}>
                            December - January 1st Cut-off
                        </option>
                        <option value="January" {{ $cutoffPeriod == 'January' ? 'selected' : '' }}>
                            January 2nd Cut-off
                        </option>
                        <option value="January-February" {{ $cutoffPeriod == 'January-February' ? 'selected' : '' }}>
                            January - February 1st Cut-off
                        </option>
                        <option value="February" {{ $cutoffPeriod == 'February' ? 'selected' : '' }}>
                            February 2nd Cut-off
                        </option>
                        <option value="February-March" {{ $cutoffPeriod == 'February-March' ? 'selected' : '' }}>
                            February - March 1st Cut-off
                        </option>
                        <option value="March" {{ $cutoffPeriod == 'March' ? 'selected' : '' }}>
                            March 2nd Cut-off
                        </option>
                        <option value="March-April" {{ $cutoffPeriod == 'March-April' ? 'selected' : '' }}>
                            March - April 1st Cut-off
                        </option>
                        <option value="April" {{ $cutoffPeriod == 'April' ? 'selected' : '' }}>
                            April 2nd Cut-off
                        </option>
                        <option value="April-May" {{ $cutoffPeriod == 'April-May' ? 'selected' : '' }}>
                            April - May 1st Cut-off
                        </option>
                        <option value="May" {{ $cutoffPeriod == 'May' ? 'selected' : '' }}>
                            May 2nd Cut-off
                        </option>
                        <option value="May-June" {{ $cutoffPeriod == 'May-June' ? 'selected' : '' }}>
                            May - June 1st Cut-off
                        </option>
                        <option value="June" {{ $cutoffPeriod == 'June' ? 'selected' : '' }}>
                            June 2nd Cut-off
                        </option>
                        <option value="June-July" {{ $cutoffPeriod == 'June-July' ? 'selected' : '' }}>
                            June - July 1st Cut-off
                        </option>
                        <option value="July" {{ $cutoffPeriod == 'July' ? 'selected' : '' }}>
                            July 2nd Cut-off
                        </option>
                        <option value="July-August" {{ $cutoffPeriod == 'July-August' ? 'selected' : '' }}>
                            July - August 1st Cut-off
                        </option>
                        <option value="August" {{ $cutoffPeriod == 'August' ? 'selected' : '' }}>
                            August 2nd Cut-off
                        </option>
                        <option value="August - September 1st Cut-off"
                            {{ $cutoffPeriod == 'August - September 1st Cut-off 2024' ? 'selected' : '' }}>
                            August - September 1st Cut-off 2024
                        </option>
                        <option value="September" {{ $cutoffPeriod == 'September' ? 'selected' : '' }}>
                            September 2nd Cut-off
                        </option>
                        <option value="September-October" {{ $cutoffPeriod == 'September-October' ? 'selected' : '' }}>
                            September - October 1st Cut-off
                        </option>
                        <option value="October" {{ $cutoffPeriod == 'October' ? 'selected' : '' }}>
                            October 2nd Cut-off
                        </option>
                        <option value="October-November" {{ $cutoffPeriod == 'October-November' ? 'selected' : '' }}>
                            October - November 1st Cut-off
                        </option>
                        <option value="November" {{ $cutoffPeriod == 'November' ? 'selected' : '' }}>
                            November 2nd Cut-off
                        </option>
                        <option value="November-December" {{ $cutoffPeriod == 'November-December' ? 'selected' : '' }}>
                            November - December 1st Cut-off
                        </option>
                        <option value="December" {{ $cutoffPeriod == 'December' ? 'selected' : '' }}>
                            December 2nd Cut-off
                        </option>
                    </select>

                    <label class="focus-label">Cut-off Period</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="year">
                        <option value="">--Select Year--</option>
                        <option value="{{ $selectedYear }}" selected>{{ $selectedYear }}</option>

                    </select>
                    <label class="focus-label">Year</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="status">
                        <option value="Approved" {{ old('status', $status) == 'Approved' ? 'selected' : '' }}>Approved
                        </option>
                        <option value="pending" {{ old('status', $status) == 'pending' ? 'selected' : '' }}>Pending
                        </option>
                        <option value="Payslip" {{ old('status', $status) == 'Payslip' ? 'selected' : '' }}>Payslip
                        </option>
                    </select>
                    <label class="focus-label">Status</label>
                </div>
            </div>


            <div class="col-sm-6 col-md-2 col-lg-2 col-xl-2 col-12">
                <button type="submit" class="btn btn-primary btn-block">Search</button>
            </div>
        </div>
    </form>
    <!-- Bulk Action Dropdown -->
    <div class="d-flex align-items-center mb-3">
        <div class="me-2">
            <select id="bulk-action-dropdown" class="select floating">
                <option value="" disabled selected>Select Bulk Action</option>
                <option value="Process">Process Payroll</option>
            </select>
        </div>
        <div>
            <button type="button" id="apply-bulk-action" class="btn btn-primary btn-sm">Apply</button>
        </div>
    </div>

    <!-- /Search Filter -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-hover table-nowrap mb-0 datatable">
                    <thead class="thead-light">
                        <tr>
                            <th><input type="checkbox" name="" id="select_all_ids"></th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Month</th>
                            <th>Cut-Off</th>
                            <th>Total Late</th>
                            <th>Total Hours</th>
                            <th>Approved By</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($approved as $app)
                        <tr>
                            <td><input type="checkbox" name="ids" class="checkbox_ids" value="{{ $app->id }}"></td>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="{{ url('admin/employee/edit/'.$app->user->id) }}" class="avatar">
                                        @if ($app->user->image)
                                        <img src="{{ asset('images/' . $app->user->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{
                                            asset('images/default.png')
                                        }}" alt="Profile Image" />
                                        @endif</a>
                                    <a href="{{ url('admin/employee/edit/'.$app->user->id) }}">
                                        @if($app->user->fName || $app->user->mName || $app->user->lName)
                                        {{ $app->user->fName ?? '' }}
                                        {{ $app->user->mName ?? '' }}
                                        {{ $app->user->lName ?? '' }}
                                        @else
                                        {{ $app->user->name }}
                                        @endif
                                        <span>{{ $app->user->position }}</span>
                                    </a>
                                </h2>
                            </td>
                            <td>{{ $app->user->department }}</td>
                            <td>{{ $app->start_date }}</td>
                            <td>{{ $app->end_date }}</td>
                            <td>{{ $app->month }}</td>
                            <td>{{ $app->cut_off }}</td>
                            <td>{{ $app->totalLate }}</td>
                            <td>{{ $app->totalHours }}</td>
                            <td>
                                @if($app->approved && $app->approved->fName && $app->approved->lName)
                                {{ $app->approved->fName }} {{ $app->approved->lName }}
                                @else
                                {{ $app->approved->name }}
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('payroll.process', $app->id) }}" method="POST"
                                    onsubmit="return confirmPayrollProcessing()">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fa fa-money m-r-5"></i> Process Payroll
                                    </button>
                                </form>
                            </td>

                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">

                                        <a class="dropdown-item preview-payroll" href="#" data-id="{{ $app->id }}">
                                            <i class="fa fa-eye m-r-5"></i> Preview/Edit Payroll
                                        </a>

                                        <!-- Edit Attendance Link -->
                                        <a class="dropdown-item edit-attendance" href="#" data-id="{{ $app->id }}"
                                            data-department="{{ $app->user->department }}"
                                            data-start_date="{{ $app->start_date }}"
                                            data-end_date="{{ $app->end_date }}" data-months="{{ $app->month }}"
                                            data-cut_off="{{ $app->cut_off }}" data-totalLate="{{ $app->totalLate }}"
                                            data-totalhours="{{ $app->totalHours }}"
                                            data-approved_by="{{ $app->approved_by }}">
                                            <i class="fa fa-pencil m-r-5"></i> Edit Attendance
                                        </a>

                                        <!-- Delete Attendance Link -->
                                        <a class="dropdown-item delete-attendance" href="#" data-id="{{ $app->id }}">
                                            <i class="fa fa-trash-o m-r-5"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->

<!-- Edit  Approved Attendance Modal -->
<div id="edit_attendance" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Attendance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editAttendanceForm" method="POST">
                    @csrf
                    <input type="hidden" name="attendance_id" id="attendance_id">
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" class="form-control" name="department" id="department" readonly>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>From <span class="text-danger">*</span></label>
                            <div class="cal-icon">
                                <input class="form-control datetimepicker" type="text" name="start_date" id="start_date"
                                    required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>To <span class="text-danger">*</span></label>
                            <div class="cal-icon">
                                <input class="form-control datetimepicker" type="text" name="end_date" id="end_date"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Month</label>
                        <input type="text" class="form-control" name="months" id="months" readonly>
                    </div>
                    <div class="form-group">
                        <label>Cut Off</label>
                        <input type="text" class="form-control" name="cut_off" id="cut_off" readonly>
                    </div>
                    <div class="form-group">
                        <label>Total Late</label>
                        <input type="text" class="form-control" name="total_late" id="total_late">
                    </div>
                    <div class="form-group">
                        <label>Total Hours</label>
                        <input type="text" class="form-control" name="total_hours" id="total_hours">
                    </div>
                    <div class="form-group">
                        <label>Approved By</label>
                        <input type="text" class="form-control" name="approved_by" id="approved_by" readonly>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Approved Attendance Modal -->

<!-- Delete Attendance Modal -->
<div class="modal custom-modal fade" id="delete_approve" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Attendance</h3>
                    <p>Are you sure you want to delete this?</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-5">
                            <form id="deleteAttendanceForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="attendance_id" id="delete_attendance_id">
                                <button class="btn add-btn" type="submit">Delete</button>
                            </form>
                        </div>
                        <div class="col-6">
                            <a href="javascript:void(0);" data-dismiss="modal" class="btn add-btn">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Attendance Modal -->


<!-- Payroll Summary Modal -->
<div class="modal fade" id="payrollSummaryModal" tabindex="-1" role="dialog" aria-labelledby="payrollSummaryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payrollSummaryModalLabel">Payroll Summary</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- General Payroll Summary -->
                <p><strong>Gross Pay:</strong> ₱<span id="grossPay"></span></p>
                <p><strong>Net Pay:</strong> ₱<span id="netPay"></span></p>

                <br>
                <!-- Breakdown of Deductions -->
                <h6><strong>Deductions:</strong></h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Deduction</th>
                            <th>Amount (₱)</th>
                        </tr>
                    </thead>
                    <tbody id="deductionsTableBody">
                        <!-- Deductions will be dynamically added here -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total Deductions</th>
                            <th>₱<span id="totalDeductions"></span></th>
                        </tr>
                    </tfoot>
                </table>

                <br>
                <!-- Breakdown of Earnings -->
                <h6><strong>Earnings:</strong></h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Earning</th>
                            <th>Amount (₱)</th>
                        </tr>
                    </thead>
                    <tbody id="earningsTableBody">
                        <!-- Earnings will be dynamically added here -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total Earnings</th>
                            <th>₱<span id="totalEarnings"></span></th>
                        </tr>
                    </tfoot>
                </table>

                <br>
                <!-- Breakdown of Loans -->
                <h6><strong>Loans:</strong></h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Loan</th>
                            <th>Amount (₱)</th>
                        </tr>
                    </thead>
                    <tbody id="loansTableBody">
                        <!-- Loans will be dynamically added here -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total Loans</th>
                            <th>₱<span id="totalLoans"></span></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Payroll Preview Modal -->
<div id="payrollPreviewModal" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payroll Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Add form here -->
                <form id="payrollForm" method="POST">
                    @csrf
                    <!-- Add CSRF token if using Laravel -->
                    <div class="row">
                        <!-- Net Pay and Gross Pay -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Basic Pay</label>
                                <input class="form-control" type="text" id="basicPaye" name="basicPay" value="">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Gross Pay</label>
                                <input class="form-control" type="text" id="grossPaye" name="grossPay" value="">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Net Pay</label>
                                <input class="form-control" type="text" id="netPaye" name="netPay" value="">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Total Earnings</label>
                                <input class="form-control" type="text" id="totalEarningse" name="totalEarnings"
                                    value="">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Total Deductions</label>
                                <input class="form-control" type="text" id="totalDeductionse" name="totalDeductions"
                                    value="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Earnings Section -->
                        <div class="col-sm-6">
                            <h4 class="text-primary">Earnings</h4>
                            <div class="form-group">
                                <label>Overtime Pay</label>
                                <input class="form-control" type="text" id="overtimePay" name="overtimePay" value="">
                            </div>
                            <div class="form-group">
                                <label>Paid Leave Amount</label>
                                <input class="form-control" type="text" id="paidLeaveAmount" name="paidLeaveAmount"
                                    value="">
                            </div>
                            <div id="earningsContainer">
                                <!-- Earnings will be dynamically populated here -->
                            </div>
                        </div>
                        <!-- Deductions Section -->
                        <div class="col-sm-6">
                            <h4 class="text-primary">Deductions</h4>
                            <div id="deductionsContainer">
                                <!-- Deductions will be dynamically populated here -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Loans Section -->
                        <div class="col-sm-12">
                            <h4 class="text-primary">Loans</h4>
                            <div id="loansContainer">
                                <!-- Loans will be dynamically populated here -->
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary submit-btn">Process Payroll</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@endsection

@section('scripts')

<script>
    $(document).ready(function () {
        // Edit Approved Attendance
        $('.edit-attendance').on('click', function () {
            var attendanceId = $(this).data('id');
            var department = $(this).data('department');
            var startDate = $(this).data('start_date');
            var endDate = $(this).data('end_date');
            var months = $(this).data('months');
            var cutOff = $(this).data('cut_off');
            var totalLate = $(this).data('totallate');
            var totalHours = $(this).data('totalhours');
            var approvedBy = $(this).data('approved_by');

            $('#attendance_id').val(attendanceId);
            $('#department').val(department);
            $('#start_date').val(startDate);
            $('#end_date').val(endDate);
            $('#months').val(months);
            $('#cut_off').val(cutOff);
            $('#total_late').val(totalLate)
            $('#total_hours').val(totalHours);
            $('#approved_by').val(approvedBy);

            $('#editAttendanceForm').attr('action', '/admin/approve/update/' + attendanceId);
            $('#edit_attendance').modal('show');
        });

        // Delete attendance request
        $('.delete-attendance').on('click', function () {
            var attendanceId = $(this).data('id');

            $('#delete_attendance_id').val(attendanceId);
            $('#deleteAttendanceForm').attr('action', '/admin/approve/delete/' + attendanceId);
            $('#delete_approve').modal('show');
        });
    });

</script>

<script>
    @if(session('summaryData'))
    const summaryData = @json(session('summaryData'));

    // Populate the general payroll summary
    document.getElementById('grossPay').textContent = summaryData.grossPay;
    document.getElementById('netPay').textContent = summaryData.netPay;

    // Populate deductions table and total
    const deductionsTableBody = document.getElementById('deductionsTableBody');
    summaryData.deductions.forEach(deduction => {
        const row = document.createElement('tr');
        row.innerHTML = `<td>${deduction.name}</td><td>₱${deduction.amount}</td>`;
        deductionsTableBody.appendChild(row);
    });
    document.getElementById('totalDeductions').textContent = summaryData.totalDeductions;

    // Populate earnings table with dynamic earnings
    const earningsTableBody = document.getElementById('earningsTableBody');

    // Add Overtime Pay to earnings
    if (summaryData.overtimePay > 0) {
        const overtimeRow = document.createElement('tr');
        overtimeRow.innerHTML = `<td>Overtime Pay</td><td>₱${summaryData.overtimePay}</td>`;
        earningsTableBody.appendChild(overtimeRow);
    }

    if (summaryData.paidLeaveAmount !== undefined) {
        const paidLeaveRow = document.createElement('tr');
        paidLeaveRow.innerHTML = `<td>Paid Leave</td><td>₱${summaryData.paidLeaveAmount}</td>`;
        earningsTableBody.appendChild(paidLeaveRow);
    }


    // Add regular earnings
    summaryData.earnings.forEach(earning => {
        const row = document.createElement('tr');
        row.innerHTML = `<td>${earning.name}</td><td>₱${earning.amount}</td>`;
        earningsTableBody.appendChild(row);
    });

    // Add total earnings
    document.getElementById('totalEarnings').textContent = summaryData.totalEarnings;

    // Populate loans table and total
    const loansTableBody = document.getElementById('loansTableBody');
    summaryData.loans.forEach(loan => {
        const row = document.createElement('tr');
        row.innerHTML = `<td>${loan.loan_name}</td><td>₱${loan.amount}</td>`;
        loansTableBody.appendChild(row);
    });
    document.getElementById('totalLoans').textContent = summaryData.totalLoans;

    // Trigger the modal to show
    $('#payrollSummaryModal').modal('show');
    @endif

</script>

<!-- <script>
    $(document).on('click', '.preview-payroll', function (e) {
        e.preventDefault();

        const payrollId = $(this).data('id');
        console.log('Payroll preview clicked, ID:', payrollId);

        // Set the payrollId as data-id on the form
        $('#payrollForm').data('id', payrollId);

        $.ajax({
            url: `/admin/payroll/preview/${payrollId}`,
            method: 'GET',
            success: function (data) {
                console.log('Data received:', data);

                // Populate modal fields with cleaned values (without commas)
                $('#basicPaye').val(removeCommas(data.basicPay));
                $('#grossPaye').val(removeCommas(data.grossPay));
                $('#netPaye').val(removeCommas(data.netPay));
                $('#totalEarningse').val(removeCommas(data.totalEarnings));
                $('#totalDeductionse').val(removeCommas(data.totalDeductions));
                $('#overtimePay').val(removeCommas(data.overtimePay));
                $('#paidLeaveAmount').val(removeCommas(data.paidLeaveAmount));

                // Clear and populate earnings, deductions, loans
                $('#earningsContainer').empty();
                $('#deductionsContainer').empty();
                $('#loansContainer').empty();

                // Populate earnings
                data.earnings.forEach(earning => {
                    $('#earningsContainer').append(`
                        <div class="form-group">
                            <label>${earning.name}</label>
                            <input type="hidden" name="earning_ids[]" value="${earning.earning_id}">
                            <input type="hidden" name="earning_names[]" value="${earning.name}">
                            <input type="text" class="form-control earning-input" value="${removeCommas(earning.amount)}" name="earnings[]" data-earning-id="${earning.earning_id}">
                        </div>
                    `);
                });

                // Populate deductions
                data.deductions.forEach(deduction => {
                    $('#deductionsContainer').append(`
                        <div class="form-group">
                            <label>${deduction.name}</label>
                            <input type="hidden" name="deduction_ids[]" value="${deduction.deduction_id}">
                            <input type="hidden" name="deduction_names[]" value="${deduction.name}">
                            <input type="text" class="form-control deduction-input" value="${removeCommas(deduction.amount)}" name="deductions[]" data-deduction-id="${deduction.deduction_id}">
                        </div>
                    `);
                });

                // Populate loans
                data.loans.forEach(loan => {
                    $('#loansContainer').append(`
                        <div class="form-group">
                            <label>${loan.loan_name}</label>
                            <input type="hidden" name="loan_ids[]" value="${loan.loan_id}">
                            <input type="hidden" name="loan_names[]" value="${loan.loan_name}">
                            <input type="text" class="form-control loan-input" value="${removeCommas(loan.amount)}" name="loans[]" data-loan-id="${loan.loan_id}">
                        </div>
                    `);
                });

                $('#payrollPreviewModal').modal('show');
            },
            error: function (err) {
                console.log('Error fetching payroll preview:', err);
            }
        });
    });

    // Event listener to automatically adjust the total deductions and net pay
    $(document).on('input', '.deduction-input', function () {
        updateDeductionsAndNetPay();
    });

    // Event listener to automatically adjust the total earnings and net pay
    $(document).on('input', '.earning-input', function () {
        updateEarningsAndNetPay();
    });

    // Event listener for overtime and paid leave fields to update total earnings and net pay
    $(document).on('input', '#overtimePay, #paidLeaveAmount', function () {
        updateEarningsAndNetPay();
    });

    // Event listener for loans to update net pay
    $(document).on('input', '.loan-input', function () {
        updateLoansAndNetPay();
    });

    function updateDeductionsAndNetPay() {
        let totalDeductions = 0;
        // Iterate through each deduction input and sum up the values
        $('.deduction-input').each(function () {
            const deductionValue = parseFloat(removeCommas($(this).val())) || 0;
            totalDeductions += deductionValue;
        });

        // Update the total deductions input field
        $('#totalDeductionse').val(totalDeductions.toFixed(2));

        // Recalculate net pay
        calculateNetPay();
    }

    function updateEarningsAndNetPay() {
        let totalEarnings = 0;
        // Iterate through each earning input and sum up the values
        $('.earning-input').each(function () {
            const earningValue = parseFloat(removeCommas($(this).val())) || 0;
            totalEarnings += earningValue;
        });

        // Add overtime pay and paid leave amount to total earnings
        const overtimePay = parseFloat(removeCommas($('#overtimePay').val())) || 0;
        const paidLeaveAmount = parseFloat(removeCommas($('#paidLeaveAmount').val())) || 0;

        totalEarnings += overtimePay + paidLeaveAmount;

        // Update the total earnings input field
        $('#totalEarningse').val(totalEarnings.toFixed(2));

        // Recalculate net pay
        calculateNetPay();
    }

    function updateLoansAndNetPay() {
        let totalLoans = 0;
        // Iterate through each loan input and sum up the values
        $('.loan-input').each(function () {
            const loanValue = parseFloat(removeCommas($(this).val())) || 0;
            totalLoans += loanValue;
        });

        // Recalculate net pay
        calculateNetPay();
    }

    function calculateNetPay() {
        const grossPay = parseFloat(removeCommas($('#basicPaye').val())) || 0;
        const totalEarnings = parseFloat(removeCommas($('#totalEarningse').val())) || 0;
        const totalDeductions = parseFloat(removeCommas($('#totalDeductionse').val())) || 0;

        // Sum up loans
        let totalLoans = 0;
        $('.loan-input').each(function () {
            const loanValue = parseFloat(removeCommas($(this).val())) || 0;
            totalLoans += loanValue;
        });

        // Debugging output to check values before calculating
        console.log('Gross Pay:', grossPay);
        console.log('Total Earnings:', totalEarnings);
        console.log('Total Deductions:', totalDeductions);
        console.log('Total Loans:', totalLoans);

        // Calculate the net pay
        const netPay = (grossPay + totalEarnings) - (totalDeductions + totalLoans);

        // Debugging output for net pay
        console.log('Calculated Net Pay:', netPay);

        // Update net pay field
        $('#netPaye').val(netPay.toFixed(2));

        // Check if net pay goes negative
        if (netPay < 0) {
            console.warn('Warning: Net pay is negative.');
        }
    }

    // Helper function to remove commas from a string
    function removeCommas(value) {
        return value.toString().replace(/,/g, '');
    }

</script> -->

<script>
    $(document).on('click', '.preview-payroll', function (e) {
        e.preventDefault();

        const payrollId = $(this).data('id');
        console.log('Payroll preview clicked, ID:', payrollId);

        // Set the payrollId as data-id on the form
        $('#payrollForm').data('id', payrollId);

        $.ajax({
            url: `/admin/payroll/preview/${payrollId}`,
            method: 'GET',
            success: function (data) {
                console.log('Data received:', data);

                // Populate modal fields with cleaned values (without commas)
                $('#basicPaye').val(removeCommas(data.basicPay));
                $('#grossPaye').val(removeCommas(data.grossPay));
                $('#netPaye').val(removeCommas(data.netPay));
                $('#totalEarningse').val(removeCommas(data.totalEarnings));
                $('#totalDeductionse').val(removeCommas(data.totalDeductions));
                $('#overtimePay').val(removeCommas(data.overtimePay));
                $('#paidLeaveAmount').val(removeCommas(data.paidLeaveAmount));

                // Clear and populate earnings, deductions, loans
                $('#earningsContainer').empty();
                $('#deductionsContainer').empty();
                $('#loansContainer').empty();

                // Populate earnings
                data.earnings.forEach(earning => {
                    $('#earningsContainer').append(`
                        <div class="form-group">
                            <label>${earning.name}</label>
                            <input type="hidden" name="earning_ids[]" value="${earning.earning_id}">
                            <input type="hidden" name="earning_names[]" value="${earning.name}">
                            <input type="text" class="form-control earning-input" value="${removeCommas(earning.amount)}" name="earnings[]" data-earning-id="${earning.earning_id}">
                        </div>
                    `);
                });

                // Populate deductions
                data.deductions.forEach(deduction => {
                    $('#deductionsContainer').append(`
                        <div class="form-group">
                            <label>${deduction.name}</label>
                            <input type="hidden" name="deduction_ids[]" value="${deduction.deduction_id}">
                            <input type="hidden" name="deduction_names[]" value="${deduction.name}">
                            <input type="text" class="form-control deduction-input" value="${removeCommas(deduction.amount)}" name="deductions[]" data-deduction-id="${deduction.deduction_id}">
                        </div>
                    `);
                });

                // Populate loans
                data.loans.forEach(loan => {
                    $('#loansContainer').append(`
                        <div class="form-group">
                            <label>${loan.loan_name}</label>
                            <input type="hidden" name="loan_ids[]" value="${loan.loan_id}">
                            <input type="hidden" name="loan_names[]" value="${loan.loan_name}">
                            <input type="text" class="form-control loan-input" value="${removeCommas(loan.amount)}" name="loans[]" data-loan-id="${loan.loan_id}">
                        </div>
                    `);
                });

                $('#payrollPreviewModal').modal('show');
            },
            error: function (err) {
                console.log('Error fetching payroll preview:', err);
            }
        });
    });

    $(document).on('input', '.deduction-input', function () {
        updateDeductionsAndNetPay();
    });

    $(document).on('input', '.earning-input', function () {
        updateEarningsAndNetPay();
    });

    $(document).on('input', '#overtimePay, #paidLeaveAmount', function () {
        updateEarningsAndNetPay();
    });

    $(document).on('input', '.loan-input', function () {
        updateLoansAndNetPay();
    });

    function updateDeductionsAndNetPay() {
        let totalDeductions = 0;
        $('.deduction-input').each(function () {
            const deductionValue = parseFloat(removeCommas($(this).val())) || 0;
            totalDeductions += deductionValue;
        });

        $('#totalDeductionse').val(totalDeductions.toFixed(2));
        calculateNetPay();
    }

    function updateEarningsAndNetPay() {
        let totalEarnings = 0;
        $('.earning-input').each(function () {
            const earningValue = parseFloat(removeCommas($(this).val())) || 0;
            totalEarnings += earningValue;
        });

        const overtimePay = parseFloat(removeCommas($('#overtimePay').val())) || 0;
        const paidLeaveAmount = parseFloat(removeCommas($('#paidLeaveAmount').val())) || 0;
        totalEarnings += overtimePay + paidLeaveAmount;

        $('#totalEarningse').val(totalEarnings.toFixed(2));

        calculateGrossPay(); // Update gross pay with new total earnings
        calculateNetPay();
    }

    function updateLoansAndNetPay() {
        calculateNetPay();
    }

    function calculateGrossPay() {
        const basicPay = parseFloat(removeCommas($('#basicPaye').val())) || 0;
        const totalEarnings = parseFloat(removeCommas($('#totalEarningse').val())) || 0;
        const grossPay = basicPay + totalEarnings;

        $('#grossPaye').val(grossPay.toFixed(2));
    }

    function calculateNetPay() {
        const grossPay = parseFloat(removeCommas($('#grossPaye').val())) || 0;
        const totalDeductions = parseFloat(removeCommas($('#totalDeductionse').val())) || 0;

        let totalLoans = 0;
        $('.loan-input').each(function () {
            const loanValue = parseFloat(removeCommas($(this).val())) || 0;
            totalLoans += loanValue;
        });

        const netPay = grossPay - totalDeductions - totalLoans;
        $('#netPaye').val(netPay.toFixed(2));

        if (netPay < 0) {
            console.warn('Warning: Net pay is negative.');
        }
    }

    function removeCommas(value) {
        return value.toString().replace(/,/g, '');
    }

</script>


<script>
    $('#payrollForm').on('submit', function (e) {
        e.preventDefault();

        const payrollId = $(this).data('id');

        // Sanitize the numeric fields by removing commas
        $('#payrollForm input[name="basicPay"]').val(function (i, val) {
            return val.replace(/,/g, ''); // Remove commas
        });

        $('#payrollForm input[name="grossPay"]').val(function (i, val) {
            return val.replace(/,/g, ''); // Remove commas
        });
        $('#payrollForm input[name="netPay"]').val(function (i, val) {
            return val.replace(/,/g, ''); // Remove commas
        });
        $('#payrollForm input[name="totalEarnings"]').val(function (i, val) {
            return val.replace(/,/g, ''); // Remove commas
        });
        $('#payrollForm input[name="totalDeductions"]').val(function (i, val) {
            return val.replace(/,/g, ''); // Remove commas
        });
        $('#payrollForm input[name="overtimePay"]').val(function (i, val) {
            return val.replace(/,/g, ''); // Remove commas
        });
        $('#payrollForm input[name="paidLeaveAmount"]').val(function (i, val) {
            return val.replace(/,/g, ''); // Remove commas
        });

        // **Deductions** //
        const deductions = [];
        $('input[name^="deductions"]').each(function () {
            const amount = $(this).val();
            const name = $(this).closest('.form-group').find('input[name^="deduction_names"]').val();
            const id = $(this).closest('.form-group').find('input[name^="deduction_ids"]').val();
            if (amount && name && id) {
                deductions.push({
                    amount,
                    name,
                    id
                });
            }
        });

        // **Earnings** //
        const earnings = [];
        $('input[name^="earnings"]').each(function () {
            const amount = $(this).val();
            const name = $(this).closest('.form-group').find('input[name^="earning_names"]').val();
            const id = $(this).closest('.form-group').find('input[name^="earning_ids"]').val();
            if (amount && name && id) {
                earnings.push({
                    amount,
                    name,
                    id
                });
            }
        });

        // **Loans** //
        const loans = [];
        $('input[name^="loans"]').each(function () {
            const amount = $(this).val();
            const name = $(this).closest('.form-group').find('input[name^="loan_names"]').val();
            const id = $(this).closest('.form-group').find('input[name^="loan_ids"]').val();
            if (amount && name && id) {
                earnings.push({
                    amount,
                    name,
                    id
                });
            }
        });

        // Sanitize loan inputs
        // $('#payrollForm input[name^="loans"]').each(function () {
        //     $(this).val($(this).val().replace(/,/g, '')); // Remove commas
        // });

        const formData = $(this).serialize();
        console.log('Form Data:', formData); // Log the sanitized data for debugging
        console.log('Deductions:', deductions); // Log deductions
        console.log('Earnings:', earnings); // Log earnings
        console.log('Loan:', loans);

        if (!payrollId) {
            alert('Error: Payroll ID is missing!');
            return;
        }

        $.ajax({
            url: `/admin/payroll/preview/process/${payrollId}`,
            method: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    alert('Payroll processed successfully!');
                    $('#payrollPreviewModal').modal('hide');
                    location.reload();
                } else {
                    alert('Failed to process payroll: ' + (response.error || 'Unknown error'));
                }
            },
            error: function (err) {
                console.log('Error processing payroll:', err);
                alert('Error processing payroll: ' + err.responseText);
            }
        });
    });

</script>

<script>
    function confirmPayrollProcessing() {
        return confirm("Are you sure you want to process payroll for this employee?");
    }

</script>

<!-- Bulk Action -->

<script>
    // Handle "Select All" checkbox
    document.getElementById('select_all_ids').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.checkbox_ids');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Handle Bulk Action "Apply" button
    document.getElementById('apply-bulk-action').addEventListener('click', function () {
        const selectedAction = document.getElementById('bulk-action-dropdown').value;
        const selectedIds = Array.from(document.querySelectorAll('.checkbox_ids:checked')).map(checkbox =>
            checkbox.value);

        // Validation: Ensure an action is selected and at least one item is checked
        if (selectedIds.length === 0) {
            alert('Please select at least one item to apply the action.');
            return;
        }

        if (!selectedAction) {
            alert('Please select a bulk action to apply.');
            return;
        }

        // Confirm the bulk action with the user
        const confirmAction = confirm(`Are you sure you want to ${selectedAction} for the selected records?`);
        if (!confirmAction) {
            return;
        }

        // Send AJAX request to the server for bulk processing
        fetch('/admin/approve/processed/bulk-action', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    action: selectedAction,
                    ids: selectedIds
                })
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response
                if (data.success) {
                    alert(data.message); // Display success message
                    location.reload(); // Reload the page to see updated status
                } else {
                    alert(data.message); // Display error message
                }
            })
            .catch(error => console.error('Error:', error));
    });

</script>


@endsection
