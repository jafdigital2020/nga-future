@extends('layouts.master') @section('title', 'Processed Timesheet')
<style>
    /* Custom extra-wide modal */
    .modal-dialog.modal-xl {
        max-width: 90%;
        /* Set this to 90% or adjust as needed */
    }

</style>

@section('content')
@include('sweetalert::alert')

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Processed Timesheet</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payslip</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-white no-print" data-toggle="modal" data-target="#summaryReportModal">
                        <i class="fa fa-file"></i> Summary Report
                    </button>
                    <button class="btn btn-white no-print" onclick="downloadCSV()">
                        <i class="fa fa-download"></i> Download as CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    <form action="{{ route('admin.payslipProcess') }}" method="GET">
        <div class="row filter-row">
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus">
                    <input type="text" class="form-control floating" name="name" value="{{ request('ename') }}">
                    <label class="focus-label">Employee Name</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="department">
                        <option value="" disabled selected>Select Department</option>
                        @foreach ($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                    <label class="focus-label">Department</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" id="monthSelect" name="cutoff_period">
                        <option value="" {{ $cutoffPeriod == '' ? 'selected' : '' }}>-- Select Cut-off Period --
                        </option>
                        <option value="December - January 1st Cut-off"
                            {{ $cutoffPeriod == 'December - January 1st Cut-off' ? 'selected' : '' }}>December - January
                            1st Cut-off</option>
                        <option value="January 2nd Cut-off"
                            {{ $cutoffPeriod == 'January 2nd Cut-off' ? 'selected' : '' }}>January 2nd Cut-off</option>
                        <option value="January - February 1st Cut-off"
                            {{ $cutoffPeriod == 'January - February 1st Cut-off' ? 'selected' : '' }}>January - February
                            1st Cut-off</option>
                        <option value="February 2nd Cut-off"
                            {{ $cutoffPeriod == 'February 2nd Cut-off' ? 'selected' : '' }}>February 2nd Cut-off
                        </option>
                        <option value="February - March 1st Cut-off"
                            {{ $cutoffPeriod == 'February - March 1st Cut-off' ? 'selected' : '' }}>February - March 1st
                            Cut-off</option>
                        <option value="March 2nd Cut-off" {{ $cutoffPeriod == 'March 2nd Cut-off' ? 'selected' : '' }}>
                            March 2nd Cut-off</option>
                        <option value="March - April 1st Cut-off"
                            {{ $cutoffPeriod == 'March - April 1st Cut-off' ? 'selected' : '' }}>March - April 1st
                            Cut-off</option>
                        <option value="April 2nd Cut-off" {{ $cutoffPeriod == 'April 2nd Cut-off' ? 'selected' : '' }}>
                            April 2nd Cut-off</option>
                        <option value="April - May 1st Cut-off"
                            {{ $cutoffPeriod == 'April - May 1st Cut-off' ? 'selected' : '' }}>April - May 1st Cut-off
                        </option>
                        <option value="May 2nd Cut-off" {{ $cutoffPeriod == 'May 2nd Cut-off' ? 'selected' : '' }}>May
                            2nd Cut-off</option>
                        <option value="May - June 1st Cut-off"
                            {{ $cutoffPeriod == 'May - June 1st Cut-off' ? 'selected' : '' }}>May - June 1st Cut-off
                        </option>
                        <option value="June 2nd Cut-off" {{ $cutoffPeriod == 'June 2nd Cut-off' ? 'selected' : '' }}>
                            June 2nd Cut-off</option>
                        <option value="June - July 1st Cut-off"
                            {{ $cutoffPeriod == 'June - July 1st Cut-off' ? 'selected' : '' }}>June - July 1st Cut-off
                        </option>
                        <option value="July 2nd Cut-off" {{ $cutoffPeriod == 'July 2nd Cut-off' ? 'selected' : '' }}>
                            July 2nd Cut-off</option>
                        <option value="July - August 1st Cut-off"
                            {{ $cutoffPeriod == 'July - August 1st Cut-off' ? 'selected' : '' }}>July - August 1st
                            Cut-off</option>
                        <option value="August 2nd Cut-off"
                            {{ $cutoffPeriod == 'August 2nd Cut-off' ? 'selected' : '' }}>August 2nd Cut-off</option>
                        <option value="August - September 1st Cut-off 2024"
                            {{ $cutoffPeriod == 'August - September 1st Cut-off 2024' ? 'selected' : '' }}>August -
                            September 1st Cut-off 2024</option>
                        <option value="September 2nd Cut-off"
                            {{ $cutoffPeriod == 'September 2nd Cut-off' ? 'selected' : '' }}>September 2nd Cut-off
                        </option>
                        <option value="September - October 1st Cut-off"
                            {{ $cutoffPeriod == 'September - October 1st Cut-off' ? 'selected' : '' }}>September -
                            October 1st Cut-off</option>
                        <option value="October 2nd Cut-off"
                            {{ $cutoffPeriod == 'October 2nd Cut-off' ? 'selected' : '' }}>October 2nd Cut-off</option>
                        <option value="October - November 1st Cut-off"
                            {{ $cutoffPeriod == 'October - November 1st Cut-off' ? 'selected' : '' }}>October - November
                            1st Cut-off</option>
                        <option value="November 2nd Cut-off"
                            {{ $cutoffPeriod == 'November 2nd Cut-off' ? 'selected' : '' }}>November 2nd Cut-off
                        </option>
                        <option value="November - December 1st Cut-off"
                            {{ $cutoffPeriod == 'November - December 1st Cut-off' ? 'selected' : '' }}>November -
                            December 1st Cut-off</option>
                        <option value="December 2nd Cut-off"
                            {{ $cutoffPeriod == 'December 2nd Cut-off' ? 'selected' : '' }}>December 2nd Cut-off
                        </option>
                    </select>
                    <label class="focus-label">Cut-off Period</label>
                </div>
            </div>

            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="year">
                        <option value="">--Select Year--</option>
                        <option value="{{ $selectedYear }}" selected>{{ $selectedYear }}</option>
                        <!-- Add more options if needed -->
                    </select>
                    <label class="focus-label">Year</label>
                </div>

            </div>
            <div class="col-sm-6 col-md-4"><button type="submit" class="btn btn-primary btn-block ml-2">Search</button>
            </div>
        </div>
    </form>
    <!-- /Search Filter -->

    <!-- Bulk Action Dropdown -->
    <div class="d-flex align-items-center mb-3">
        <div class="me-2">
            <select id="bulk-action-dropdown" class="select floating">
                <option value="" disabled selected>Select Bulk Action</option>
                <option value="Approved">Approve</option>
                <option value="Revision">Revision</option>
                <option value="Declined">Decline</option>
            </select>
        </div>
        <div>
            <button type="button" id="apply-bulk-action" class="btn btn-primary btn-sm">Apply</button>
        </div>
    </div>

    @php
    $hasNotes = $payslip->some(fn($pay) => !empty($pay->notes));
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-hover table-nowrap mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th><input type="checkbox" name="" id="select_all_ids"></th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Month</th>
                            <th>Cut-Off</th>
                            <th>Total Hours</th>
                            <th>Total Deductions</th>
                            <th>Total Earnings</th>
                            <th>Overtime Pay</th>
                            <th>Paid Leave</th>
                            <th>Regular Holiday</th>
                            <th>Special Holiday</th>
                            <th>Net Pay</th>
                            <th class="text-center">Status</th>
                            @if ($hasNotes)
                            <th>Notes</th>
                            @endif
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                        $netPayTotalSum = 0; // Initialize the total
                        @endphp
                        @foreach ($payslip as $pay)
                        @php
                        $deductions = json_decode($pay->deductions, true);
                        $earnings = json_decode($pay->earnings, true);
                        $loans = json_decode($pay->loans, true);
                        @endphp
                        <tr>
                            <td><input type="checkbox" name="ids" class="checkbox_ids" value="{{ $pay->id }}"></td>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="{{ url('admin/employee/edit/'.$pay->user->id) }}" class="avatar">
                                        @if ($pay->user->image)
                                        <img src="{{ asset('images/' . $pay->user->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                        @endif
                                    </a>
                                    <a href="{{ url('admin/employee/edit/'.$pay->user->id) }}">
                                        @if($pay->user->fName || $pay->user->mName || $pay->user->lName)
                                        {{ $pay->user->fName ?? '' }} {{ $pay->user->mName ?? '' }}
                                        {{ $pay->user->lName ?? '' }}
                                        @else
                                        {{ $pay->user->name }}
                                        @endif
                                        <span>{{ $pay->user->position }}</span>
                                    </a>
                                </h2>
                            </td>
                            <td>{{ $pay->user->department }}</td>
                            <td>{{ $pay->month }}</td>
                            <td>{{ $pay->cut_off }}</td>
                            <td>{{ $pay->approvedAttendance->totalHours }}</td>
                            <td>{{ $pay->total_deductions }}</td>
                            <td>{{ $pay->total_earnings }}</td>
                            <td>{{ $pay->overtimeHours }}</td>
                            <td>{{ $pay->paidLeave }}</td>
                            <td>{{ $pay->regular_holiday_pay }}</td>
                            <td>{{ $pay->special_holiday_pay }}</td>
                            <td>₱{{ number_format($pay->net_pay, 2) }}</td>
                            @php
                            $netPayTotalSum += $pay->net_pay; // Add the current netPayTotal to the sum
                            @endphp
                            <td>
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        @if($pay->status == 'New')
                                        <i class="fa fa-dot-circle-o text-purple"></i> New
                                        @elseif($pay->status == 'pending')
                                        <i class="fa fa-dot-circle-o text-info"></i> Pending
                                        @elseif($pay->status == 'Approved')
                                        <i class="fa fa-dot-circle-o text-success"></i> Approved
                                        @elseif($pay->status == 'Revision')
                                        <i class="fa fa-dot-circle-o text-warning"></i> Revision
                                        @elseif($pay->status == 'Revised')
                                        <i class="fa fa-dot-circle-o text-dark"></i> Revised
                                        @elseif($pay->status == 'Declined')
                                        <i class="fa fa-dot-circle-o text-danger"></i> Declined
                                        @else
                                        <i class="fa fa-dot-circle-o text-info"></i> Pending
                                        @endif
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">

                                        <form id="approve-form-{{ $pay->id }}"
                                            action="{{ url('admin/processed/approved/' . $pay->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item approve-button"
                                                data-pay-id="{{ $pay->id }}">
                                                <i class="fa fa-dot-circle-o text-success"></i> Approve
                                            </button>
                                        </form>

                                        <form id="revision-form-{{ $pay->id }}"
                                            action="{{ url('admin/processed/revision/' . $pay->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item revision-button"
                                                data-pay-id="{{ $pay->id }}">
                                                <i class="fa fa-dot-circle-o text-warning"></i> Revision
                                            </button>
                                        </form>

                                        <form id="decline-form-{{ $pay->id }}"
                                            action="{{ url('admin/processed/declined/' . $pay->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item decline-button"
                                                data-pay-id="{{ $pay->id }}">
                                                <i class="fa fa-dot-circle-o text-danger"></i> Decline
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            </td>
                            @if ($hasNotes)
                            <td>{{ $pay->notes ?? '' }}</td>
                            @endif
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item viewEditLink" href="#" data-toggle="modal"
                                            data-target="#viewEditModal" data-pay-id="{{ $pay->id }}"
                                            data-deductions="{{ json_encode($deductions) }}"
                                            data-earnings="{{ json_encode($earnings) }}"
                                            data-loans="{{ json_encode($loans) }}" data-cut_off="{{ $pay->cut_off }}"
                                            data-month="{{ $pay->month }}" data-year="{{ $pay->year }}"
                                            data-start_date="{{ $pay->start_date }}"
                                            data-end_date="{{ $pay->end_date }}"
                                            data-total_hours="{{ $pay->total_hours }}"
                                            data-monthly_salary="{{ $pay->monthly_salary }}"
                                            data-daily_rate="{{ $pay->daily_rate }}"
                                            data-hourly_rate="{{ $pay->hourly_rate }}"
                                            data-overtime_hour="{{ $pay->overtimeHours }}"
                                            data-paid_leave="{{ $pay->paidLeave }}"
                                            data-basic_pay="{{ $pay->basic_pay }}"
                                            data-gross_pay="{{ $pay->gross_pay }}"
                                            data-total_deductions="{{ $pay->total_deductions }}"
                                            data-total_earnings="{{ $pay->total_earnings }}"
                                            data-net_pay="{{ $pay->net_pay }}" data-fName="{{ $pay->user->fName }}"
                                            data-lName="{{ $pay->user->lName }}"
                                            data-department="{{ $pay->user->department }}"
                                            data-regular_holiday="{{ $pay->regular_holiday_pay }}"
                                            data-special_holiday="{{ $pay->special_holiday_pay }}">
                                            <i class="fa fa-pencil m-r-5"></i>View & Edit
                                        </a>

                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9"></td>
                            <td class="text-right"><strong>Total Net Pay:</strong></td>
                            <td><strong style="color:red;">₱{{ number_format($netPayTotalSum, 2) }}</strong></td>
                            @if ($hasNotes)
                            <td></td>
                            @endif
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Summary Report Modal -->
<div class="modal fade" id="summaryReportModal" tabindex="-1" aria-labelledby="summaryReportLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="summaryReportLabel">Summary Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @php
                // Initialize summary variables
                $totalEmployees = count($payslip); // Total number of employees (entries)
                $totalHours = 0; // Initialize total hours
                $netPayTotalSum = 0; // Initialize total net pay

                foreach ($payslip as $pay) {
                // Safely convert to float in case values are null or non-numeric
                $totalHours += floatval($pay->total_hours); // Sum of total hours worked
                $netPayTotalSum += floatval($pay->net_pay); // Sum of net pay
                }
                @endphp
                <p><strong>Total Employees:</strong> {{ $totalEmployees }}</p>
                <p><strong>Total Hours Worked:</strong> {{ number_format($totalHours, 2) }} hours</p>
                <p><strong>Total Net Pay:</strong> ₱{{ number_format($netPayTotalSum, 2) }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Extra-Wide Modal Structure with Form Groups -->
<div class="modal fade" id="viewEditModal" tabindex="-1" role="dialog" aria-labelledby="viewEditModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewEditModalLabel">View & Edit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="deductionsForm" method="POST" action="{{ route('salary.update') }}">
                    @csrf
                    <input type="hidden" name="salary_id" id="salary_id">

                    <!-- Basic Employee Info -->
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label>Employee Name</label>
                            <input type="text" class="form-control" id="employee_name" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Department</label>
                            <input type="text" class="form-control" id="department" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Cut-off</label>
                            <input type="text" class="form-control" id="cut_off" readonly>
                        </div>
                    </div>

                    <!-- Salary Details -->
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label>Month</label>
                            <input type="text" class="form-control" id="month" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Year</label>
                            <input type="text" class="form-control" id="year" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Start Date</label>
                            <input type="text" class="form-control" id="start_date" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>End Date</label>
                            <input type="text" class="form-control" id="end_date" readonly>
                        </div>
                    </div>

                    <hr>

                    <!-- Deductions, Earnings, and Loans Fields -->
                    <div class="form-row mb-3">
                        <!-- Deductions -->
                        <div class="col-md-4">
                            <h4 class="text-primary">Deductions</h4>
                            <div id="deductionsContainer">
                                <!-- Deduction fields will be dynamically appended here -->
                            </div>
                        </div>

                        <!-- Earnings -->
                        <div class="col-md-4">
                            <h4 class="text-primary">Earnings</h4>
                            <div id="earningsContainer">
                                <!-- Earning fields will be dynamically appended here -->
                            </div>
                            <label class="text-primary">Overtime Pay</label>
                            <input type="text" class="form-control" id="overtimeHours" name="overtimeHours">
                            <label class="text-primary">Paid Leave</label>
                            <input type="text" class="form-control" id="paidLeave" name="paidLeave">
                            <label class="text-primary">Regular Holiday Amount</label>
                            <input type="text" class="form-control" id="regularHolidayPay" name="regularHolidayPay">
                            <label class="text-primary">Special Holiday Amount</label>
                            <input type="text" class="form-control" id="specialHolidayPay" name="specialHolidayPay">
                        </div>

                        <!-- Loans -->
                        <div class="col-md-4">
                            <h4 class="text-primary">Loans</h4>
                            <div id="loansContainer">
                                <!-- Loan fields will be dynamically appended here -->
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Totals and Net Pay -->
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label class="text-primary">Total Deductions</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input type="text" class="form-control" id="total_deductions" name="total_deductions">
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-primary">Total Earnings</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input type="text" class="form-control" id="total_earnings" name="total_earnings">
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="text-primary">Basic Pay</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input type="text" class="form-control" id="basic_pay" name="basic_pay" readonly>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="text-primary">Gross Pay</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input type="text" class="form-control" id="gross_pay" name="gross_pay" readonly>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="text-primary">Net Pay</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input type="text" class="form-control" id="net_pay" name="net_pay" style="color:red;">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- Revision Modal -->
<div class="modal fade" id="revisionModal" tabindex="-1" role="dialog" aria-labelledby="revisionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="revisionModalLabel">Add Revision Notes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="revision-modal-form" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" name="notes" id="notes" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection

@section('scripts')
<!-- Notes Modal -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get all revision buttons
        var revisionButtons = document.querySelectorAll('.revision-button');

        revisionButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var payId = button.getAttribute('data-pay-id');
                // Set the action URL dynamically based on payId
                var form = document.getElementById('revision-modal-form');
                form.action = '/admin/processed/revision/' + payId;

                // Show the modal
                $('#revisionModal').modal('show');
            });
        });
    });

</script>

<!-- <script>
    $('.viewEditLink').on('click', function () {
        // Retrieve data attributes and parse as JSON if needed
        const deductions = JSON.parse($(this).attr('data-deductions') || '[]');
        const earnings = JSON.parse($(this).attr('data-earnings') || '[]');
        const loans = JSON.parse($(this).attr('data-loans') || '[]');


        // Log arrays for debugging
        console.log("Deductions:", deductions);
        console.log("Earnings:", earnings);
        console.log("Loans:", loans);

        const fName = $(this).data('fname');
        const lName = $(this).data('lname');
        const department = $(this).data('department');
        const cutOff = $(this).data('cut_off');
        const month = $(this).data('month');
        const year = $(this).data('year');
        const startDate = $(this).data('start_date');
        const endDate = $(this).data('end_date');
        const totalHours = $(this).data('total_hours');
        const grossPay = parseFloat($(this).data('gross_pay')) || 0;
        const basicPay = parseFloat($(this).data('basic_pay')) || 0;
        const dailyRate = $(this).data('daily_rate');
        const hourlyRate = $(this).data('hourly_rate');
        const totalDeductions = parseFloat($(this).data('total_deductions')) || 0;
        const totalEarnings = parseFloat($(this).data('total_earnings')) || 0;
        const netPay = parseFloat($(this).data('net_pay')) || 0;
        const overtimeHours = $(this).data('overtime_hour');
        const paidLeave = $(this).data('paid_leave');
        const salaryId = $(this).data('pay-id');

        // Assign pay-id to the deductions form for later use
        $('#deductionsForm').data('pay-id', salaryId);

        $('#salary_id').val(salaryId);
        $('#employee_name').val(`${fName} ${lName}`);
        $('#department').val(department);
        $('#cut_off').val(cutOff);
        $('#month').val(month);
        $('#year').val(year);
        $('#start_date').val(startDate);
        $('#end_date').val(endDate);
        $('#total_hours').val(totalHours);
        $('#gross_pay').val(grossPay);
        $('#basic_pay').val(basicPay);
        $('#daily_rate').val(dailyRate);
        $('#hourly_rate').val(hourlyRate);
        $('#total_deductions').val(totalDeductions.toFixed(2));
        $('#total_earnings').val(totalEarnings.toFixed(2));
        $('#net_pay').val(netPay.toFixed(2));
        $('#overtimeHours').val(overtimeHours);
        $('#paidLeave').val(paidLeave);

        $('#deductionsContainer').empty();
        $('#earningsContainer').empty();
        $('#loansContainer').empty();

        // Populate deductions, earnings, and loans
        deductions.forEach((deduction) => {
            $('#deductionsContainer').append(`
                <label>${deduction.name}</label>
                <input type="hidden" name="deduction_ids[]" value="${deduction.deduction_id}">
                <input type="hidden" name="deduction_names[]" value="${deduction.name}">
                <input type="text" class="form-control deduction-input" value="${parseFloat(deduction.amount).toFixed(2)}" name="deductions[]" data-deduction-id="${deduction.deduction_id}">
            `);
        });

        earnings.forEach((earning) => {
            $('#earningsContainer').append(`
                <label>${earning.name}</label>
                <input type="hidden" name="earning_ids[]" value="${earning.earning_id}">
                <input type="hidden" name="earning_names[]" value="${earning.name}">
                <input type="text" class="form-control earning-input" value="${parseFloat(earning.amount).toFixed(2)}" name="earnings[]" data-earning-id="${earning.earning_id}">
            `);
        });

        loans.forEach((loan) => {
            $('#loansContainer').append(`
                <label>${loan.loan_name || "N/A"}</label>
                <input type="hidden" name="loan_ids[]" value="${loan.loan_id}">
                <input type="hidden" name="loan_names[]" value="${loan.loan_name || "N/A"}">
                <input type="text" class="form-control loan-input" value="${parseFloat(loan.amount).toFixed(2)}" name="loans[]" data-loan-id="${loan.loan_id}">
            `);
        });

        $('#viewEditModal').modal('show');
    });

    // Update deduction, earnings, and loan totals
    $(document).on('input', '.deduction-input', updateDeductionsAndNetPay);
    $(document).on('input', '.earning-input', updateEarningsAndNetPay);
    $(document).on('input', '.loan-input', updateLoansAndNetPay);
    $(document).on('input', '#overtimeHours, #paidLeave', updateEarningsAndNetPay);

    function updateDeductionsAndNetPay() {
        let totalDeductions = 0;
        $('.deduction-input').each(function () {
            totalDeductions += parseFloat($(this).val()) || 0;
        });
        $('#total_deductions').val(totalDeductions.toFixed(2));
        updateNetPay();
    }

    function updateEarningsAndNetPay() {
        let totalEarnings = 0;
        // Add earnings from input fields
        $('.earning-input').each(function () {
            totalEarnings += parseFloat($(this).val()) || 0;
        });

        // Get the overtime hours and paid leave values
        const overtimeHours = parseFloat($('#overtimeHours').val()) || 0;
        const paidLeave = parseFloat($('#paidLeave').val()) || 0;

        // Add overtime and paid leave to total earnings
        totalEarnings += overtimeHours + paidLeave;

        $('#total_earnings').val(totalEarnings.toFixed(2));
        updateNetPay();
    }


    function updateLoansAndNetPay() {
        let totalLoans = 0;
        $('.loan-input').each(function () {
            totalLoans += parseFloat($(this).val()) || 0;
        });
        // You can add further handling of total loans if needed
        updateNetPay();
    }

    function updateNetPay() {
        const basicPay = parseFloat($('#basic_pay').val()) || 0;
        const totalDeductions = parseFloat($('#total_deductions').val()) || 0;
        const totalEarnings = parseFloat($('#total_earnings').val()) || 0;
        const netPay = basicPay + totalEarnings - totalDeductions;
        $('#net_pay').val(netPay.toFixed(2));
    }

</script> -->

<script>
    $('.viewEditLink').on('click', function () {
        // Retrieve data attributes and parse as JSON if needed
        const deductions = JSON.parse($(this).attr('data-deductions') || '[]');
        const earnings = JSON.parse($(this).attr('data-earnings') || '[]');
        const loans = JSON.parse($(this).attr('data-loans') || '[]');

        // Log arrays for debugging
        console.log("Deductions:", deductions);
        console.log("Earnings:", earnings);
        console.log("Loans:", loans);

        const fName = $(this).data('fname');
        const lName = $(this).data('lname');
        const department = $(this).data('department');
        const cutOff = $(this).data('cut_off');
        const month = $(this).data('month');
        const year = $(this).data('year');
        const startDate = $(this).data('start_date');
        const endDate = $(this).data('end_date');
        const totalHours = $(this).data('total_hours');
        const grossPay = parseFloat($(this).data('gross_pay')) || 0;
        const basicPay = parseFloat($(this).data('basic_pay')) || 0;
        const dailyRate = $(this).data('daily_rate');
        const hourlyRate = $(this).data('hourly_rate');
        const totalDeductions = parseFloat($(this).data('total_deductions')) || 0;
        const totalEarnings = parseFloat($(this).data('total_earnings')) || 0;
        const netPay = parseFloat($(this).data('net_pay')) || 0;
        const overtimeHours = $(this).data('overtime_hour');
        const paidLeave = $(this).data('paid_leave');
        const regularHolidayPay = $(this).data('regular_holiday');
        const specialHolidayPay = $(this).data('special_holiday');
        const salaryId = $(this).data('pay-id');

        $('#deductionsForm').data('pay-id', salaryId);

        $('#salary_id').val(salaryId);
        $('#employee_name').val(`${fName} ${lName}`);
        $('#department').val(department);
        $('#cut_off').val(cutOff);
        $('#month').val(month);
        $('#year').val(year);
        $('#start_date').val(startDate);
        $('#end_date').val(endDate);
        $('#total_hours').val(totalHours);
        $('#gross_pay').val(grossPay);
        $('#basic_pay').val(basicPay);
        $('#daily_rate').val(dailyRate);
        $('#hourly_rate').val(hourlyRate);
        $('#total_deductions').val(totalDeductions.toFixed(2));
        $('#total_earnings').val(totalEarnings.toFixed(2));
        $('#net_pay').val(netPay.toFixed(2));
        $('#overtimeHours').val(overtimeHours);
        $('#paidLeave').val(paidLeave);
        $('#regularHolidayPay').val(regularHolidayPay);
        $('#specialHolidayPay').val(specialHolidayPay);

        $('#deductionsContainer').empty();
        $('#earningsContainer').empty();
        $('#loansContainer').empty();

        // Populate deductions, earnings, and loans
        deductions.forEach((deduction) => {
            $('#deductionsContainer').append(`
                <label>${deduction.name}</label>
                <input type="hidden" name="deduction_ids[]" value="${deduction.deduction_id}">
                <input type="hidden" name="deduction_names[]" value="${deduction.name}">
                <input type="text" class="form-control deduction-input" value="${parseFloat(deduction.amount).toFixed(2)}" name="deductions[]" data-deduction-id="${deduction.deduction_id}">
            `);
        });

        earnings.forEach((earning) => {
            $('#earningsContainer').append(`
                <label>${earning.name}</label>
                <input type="hidden" name="earning_ids[]" value="${earning.earning_id}">
                <input type="hidden" name="earning_names[]" value="${earning.name}">
                <input type="text" class="form-control earning-input" value="${parseFloat(earning.amount).toFixed(2)}" name="earnings[]" data-earning-id="${earning.earning_id}">
            `);
        });

        loans.forEach((loan) => {
            $('#loansContainer').append(`
                <label>${loan.loan_name || "N/A"}</label>
                <input type="hidden" name="loan_ids[]" value="${loan.loan_id}">
                <input type="hidden" name="loan_names[]" value="${loan.loan_name || "N/A"}">
                <input type="text" class="form-control loan-input" value="${parseFloat(loan.amount).toFixed(2)}" name="loans[]" data-loan-id="${loan.loan_id}">
            `);
        });

        $('#viewEditModal').modal('show');
    });

    // Update deduction, earnings, and loan totals
    $(document).on('input', '.deduction-input', updateDeductionsAndNetPay);
    $(document).on('input', '.earning-input', updateEarningsAndNetPay);
    $(document).on('input', '.loan-input', updateLoansAndNetPay);
    $(document).on('input', '#overtimeHours, #paidLeave, #regularHolidayPay, #specialHolidayPay',
        updateEarningsAndNetPay);

    function updateDeductionsAndNetPay() {
        let totalDeductions = 0;
        $('.deduction-input').each(function () {
            totalDeductions += parseFloat($(this).val()) || 0;
        });
        $('#total_deductions').val(totalDeductions.toFixed(2));
        updateNetPay();
    }

    function updateEarningsAndNetPay() {
        let totalEarnings = 0;
        // Add earnings from input fields
        $('.earning-input').each(function () {
            totalEarnings += parseFloat($(this).val()) || 0;
        });

        // Get the overtime hours and paid leave values
        const overtimeHours = parseFloat($('#overtimeHours').val()) || 0;
        const paidLeave = parseFloat($('#paidLeave').val()) || 0;
        const regularHolidayPay = parseFloat($('#regularHolidayPay').val()) || 0;
        const specialHolidayPay = parseFloat($('#specialHolidayPay').val()) || 0;

        // Add overtime and paid leave to total earnings
        totalEarnings += overtimeHours + paidLeave + regularHolidayPay + specialHolidayPay;

        $('#total_earnings').val(totalEarnings.toFixed(2));

        // Update gross pay when earnings change
        const basicPay = parseFloat($('#basic_pay').val()) || 0;
        const grossPay = basicPay + totalEarnings;
        $('#gross_pay').val(grossPay.toFixed(2));

        updateNetPay();
    }

    function updateLoansAndNetPay() {
        let totalLoans = 0;
        $('.loan-input').each(function () {
            totalLoans += parseFloat($(this).val()) || 0;
        });
        // You can add further handling of total loans if needed
        updateNetPay();
    }

    function updateNetPay() {
        const basicPay = parseFloat($('#basic_pay').val()) || 0;
        const totalDeductions = parseFloat($('#total_deductions').val()) || 0;
        const totalEarnings = parseFloat($('#total_earnings').val()) || 0;
        const netPay = basicPay + totalEarnings - totalDeductions;
        $('#net_pay').val(netPay.toFixed(2));
    }

</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        var approveButtons = document.querySelectorAll('.approve-button');
        var declineButtons = document.querySelectorAll('.decline-button');


        approveButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var payId = button.getAttribute('data-pay-id');
                confirmApproval(payId);
            });
        });



        declineButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var payId = button.getAttribute('data-pay-id');
                confirmDecline(payId);
            });
        });
    });

    function confirmApproval(payId) {
        var form = document.getElementById('approve-form-' + payId);
        var confirmAction = confirm("Are you sure you want to approve this?");
        if (confirmAction) {
            form.submit();
        }
    }


    function confirmDecline(payId) {
        var form = document.getElementById('decline-form-' + payId);
        var confirmAction = confirm("Are you sure you want to decline this?");
        if (confirmAction) {
            form.submit();
        }
    }

</script>

<script>
    // Handle select all checkbox
    document.getElementById('select_all_ids').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.checkbox_ids');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Handle bulk action
    document.getElementById('apply-bulk-action').addEventListener('click', function () {
        const selectedAction = document.getElementById('bulk-action-dropdown').value;
        const selectedIds = Array.from(document.querySelectorAll('.checkbox_ids:checked')).map(checkbox =>
            checkbox.value);

        if (selectedIds.length === 0) {
            alert('Please select at least one item to apply the action.');
            return;
        }

        if (!selectedAction) {
            alert('Please select a bulk action to apply.');
            return;
        }

        // AJAX request to handle bulk action
        fetch(`/admin/processed/bulk-action`, {
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
                // Handle response
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Reload the page to see changes
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    });

</script>

<script>
    function downloadCSV() {
        let csv = 'Employee Name, Department, From, To, Month, Cut-Off, Total Hours, Net Pay\n';
        @foreach($payslip as $pay)
        csv +=
            "{{ $pay->user->fName ?? '' }} {{ $pay->user->mName ?? '' }} {{ $pay->user->lName ?? $pay->user->name }}";
        csv += ',';
        csv += "{{ $pay->user->department }}";
        csv += ',';
        csv += "{{ $pay->start_date }}";
        csv += ',';
        csv += "{{ $pay->end_date }}";
        csv += ',';
        csv += "{{ $pay->month }}";
        csv += ',';
        csv += "{{ $pay->cut_off }}";
        csv += ',';
        csv += "{{ $pay->total_hours }}";
        csv += ',';
        csv += "{{ $pay->net_pay }}";
        csv += '\n';
        @endforeach

        let hiddenElement = document.createElement('a');
        hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
        hiddenElement.target = '_blank';
        hiddenElement.download = 'payslip_report.csv';
        hiddenElement.click();
    }

</script>
@endsection
