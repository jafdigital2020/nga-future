@extends('layouts.master') @section('title', 'Payslip Edit')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
@include('sweetalert::alert')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Payroll</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payroll</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Create Payroll -->
    <form action="{{ url('admin/payslip/update/'. $edit->id) }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Employee </h4>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Cut-Off</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="cut_off" id="cut_off"
                                            value="{{ $edit->cut_off }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Year</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="year" id="year" class="form-control"
                                            value="{{ date('Y') }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Transaction Date</label>
                                    <div class="col-lg-9">
                                        <div class="cal-icon">
                                            <input type="text" name="transactionDate" id="transactionDate"
                                                class="form-control datetimepicker" placeholder="-- Select Date --"
                                                value="{{ date('Y-m-d') }}" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">From</label>
                                    <div class="col-lg-9">
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" id="start_date" name="start_date"
                                                placeholder="-- Select Date --" value="{{ $edit->start_date }}"
                                                readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">To</label>
                                    <div class="col-lg-9">
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" id="end_date" name="end_date"
                                                placeholder="-- Select Date --" value="{{ $edit->end_date }}"
                                                readonly />
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Month</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="month" id="month"
                                            value="{{ date('F') }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Full Name</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="ename" id="ename"
                                            value="{{ $edit->user->fName }} {{ $edit->user->lName }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Position</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="position" id="position"
                                            value="{{ $edit->user->position }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Department</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="department" id="department"
                                            value="{{ $edit->user->department }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Total Hours</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="totalHours" id="totalHours"
                                            value="{{ $edit->totalHours }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Create Payroll -->

        <!-- Deduction Payroll -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Deductions</h4>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">SSS</label>
                                    <div class="col-lg-9" style="display: flex; align-items: center;">
                                        <label
                                            style="margin-bottom: 0; display: flex; align-items: center; margin-right: 10px;">
                                            <span style="margin-right: 10px;">Yes</span>
                                            <input type="radio" name="option_sss" value="yes" class="fetch-data">
                                        </label>
                                        <label
                                            style="margin-bottom: 0; display: flex; align-items: center; margin-right: 10px;">
                                            <span style="margin-right: 10px;">No</span>
                                            <input type="radio" name="option_sss" value="no" class="fetch-data">
                                        </label>
                                        <input type="text" class="form-control" name="sss" id="sss"
                                            style="margin-left: 10px;" value="{{ $edit->sss }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">PhilHealth</label>
                                    <div class="col-lg-9" style="display: flex; align-items: center;">
                                        <label
                                            style="margin-bottom: 0; display: flex; align-items: center; margin-right: 10px;">
                                            <span style="margin-right: 10px;">Yes</span>
                                            <input type="radio" name="option_philhealth" value="yes" class="fetch-data">
                                        </label>
                                        <label
                                            style="margin-bottom: 0; display: flex; align-items: center; margin-right: 10px;">
                                            <span style="margin-right: 10px;">No</span>
                                            <input type="radio" name="option_philhealth" value="no" class="fetch-data">
                                        </label>
                                        <input type="text" class="form-control" name="philHealth" id="philHealth"
                                            style="margin-left: 10px;" value="{{ $edit->philHealth }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Pag-Ibig</label>
                                    <div class="col-lg-9" style="display: flex; align-items: center;">
                                        <label
                                            style="margin-bottom: 0; display: flex; align-items: center; margin-right: 10px;">
                                            <span style="margin-right: 10px;">Yes</span>
                                            <input type="radio" name="option_pagibig" value="yes" class="fetch-data">
                                        </label>
                                        <label
                                            style="margin-bottom: 0; display: flex; align-items: center; margin-right: 10px;">
                                            <span style="margin-right: 10px;">No</span>
                                            <input type="radio" name="option_pagibig" value="no" class="fetch-data">
                                        </label>
                                        <input type="text" class="form-control" name="pagIbig" id="pagIbig"
                                            style="margin-left: 10px;" value="{{ $edit->pagIbig }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Withholding Tax</label>
                                    <div class="col-lg-9" style="display: flex; align-items: center;">
                                        <label
                                            style="margin-bottom: 0; display: flex; align-items: center; margin-right: 10px;">
                                            <span style="margin-right: 10px;">Yes</span>
                                            <input type="radio" name="option_tax" value="yes" class="fetch-data">
                                        </label>
                                        <label
                                            style="margin-bottom: 0; display: flex; align-items: center; margin-right: 10px;">
                                            <span style="margin-right: 10px;">No</span>
                                            <input type="radio" name="option_tax" value="no" class="fetch-data">
                                        </label>
                                        <input type="text" class="form-control" name="withHolding" id="withHolding"
                                            style="margin-left: 10px;" value="{{ $edit->withHolding }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Late</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name=tLate id="tLate"
                                            style="display: inline-block; width: 30%;" value="{{ $edit->totalLate }}"
                                            readonly>
                                        <input type="text" name="late" id="late" class="form-control"
                                            style="display: inline-block; width: 68%;" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Loan</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="loan" id="loan" class="form-control"
                                            value="{{ $edit->loan }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Advance</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="advance" id="advance" class="form-control"
                                            value="{{ $edit->advance }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Others</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="others" id="others" class="form-control"
                                            value="{{ $edit->others }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- /Deduction Payroll -->

        <!-- Earnings & Total -->
        <div class="row">
            <div class="col-xl-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Earnings</h4>
                    </div>
                    <div class="card-body">

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Birthday Leave</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="bLeave" id="bLeave"
                                    style="display: inline-block; width: 20%;">
                                <input type="text" class="form-control" name="bdayLeave" id="bdayLeave"
                                    style="display: inline-block; width: 78%;" value="{{ $edit->bdayLeave }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Vacation Leave</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name=vLeave id="vLeave"
                                    style="display: inline-block; width: 20%;">
                                <input type="text" name="vacLeave" id="vacLeave" class="form-control"
                                    style="display: inline-block; width: 78%;" value="{{ $edit->vacLeave }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Sick Leave</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name=sLeave id="sLeave"
                                    style="display: inline-block; width: 20%;">
                                <input type="text" class="form-control" name="sickLeave" id="sickLeave"
                                    style="display: inline-block; width: 78%;" value="{{ $edit->sickLeave }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Regular Holiday</label>
                            <div class="col-lg-9">
                                <div style="display: inline-block; width: 30%; position: relative;">
                                    <input type="number" class="form-control" name="rHoliday" id="rHoliday"
                                        style="width: 100%;" value="0">
                                    <span
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%);">Day/s</span>
                                </div>
                                <input type="text" name="regHoliday" id="regHoliday" class="form-control"
                                    style="display: inline-block; width: 68%;" value="{{ $edit->regHoliday }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Overtime</label>
                            <div class="col-lg-9">
                                <div style="display: inline-block; width: 30%; position: relative;">
                                    <input type="text" class="form-control" name="otHours" id="otHours"
                                        style="width: 100%;" value="0">
                                    <span
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%);">Hrs</span>
                                </div>
                                <input type="text" name="otTotal" id="otTotal" class="form-control"
                                    style="display: inline-block; width: 68%;" value="{{ $edit->otTotal }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">10% Night Differential</label>
                            <div class="col-lg-9">
                                <div style="display: inline-block; width: 30%; position: relative;">
                                    <input type="number" class="form-control" name="nDiff" id="nDiff"
                                        style="width: 100%;" value="0">
                                    <span
                                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%);">Hrs</span>
                                </div>
                                <input type="text" name="nightDiff" id="nightDiff" class="form-control"
                                    style="display: inline-block; width: 68%;" value="{{ $edit->nightDiff }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Extra Mile / Bonus</label>
                            <div class="col-lg-9">
                                <input type="text" name="bonus" id="bonus" class="form-control"
                                    value="{{ $edit->bonus }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Total</h4>
                    </div>
                    <div class="card-body">

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Total Deduction</label>
                            <div class="col-lg-9">
                                <input type="text" name="totalDeduction" id="totalDeduction" class="form-control"
                                    value="{{ $edit->totalDeduction }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Total Earning</label>
                            <div class="col-lg-9">
                                <input type="text" name="totalEarning" id="totalEarning" class="form-control"
                                    value="{{ $edit->totalEarning }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Gross Monthly Pay</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="grossMonthly" id="grossMonthly"
                                    value="{{ $edit->grossMonthly  }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Gross Basic Pay</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="grossBasic" id="grossBasic"
                                    value="{{ $edit->grossBasic  }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Daily Rate</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="dailyRate" id="dailyRate"
                                    value="{{ $edit->dailyRate }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Hourly Rate</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="hourlyRate" id="hourlyRate"
                                    value="{{ $edit->hourlyRate }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Net Pay</label>
                            <div class="col-lg-9">
                                <input type="hidden" name="netPay" id="netPay" class="form-control" readonly>
                                <input type="text" name="netPayTotal" id="netPayTotal" class="form-control" readonly
                                    style="color: red; display: inline-block; width: 31%;"
                                    value="{{ $edit->netPayTotal }}">
                                <button type="submit" class="btn btn-danger btn-block"
                                    style="display: inline-block; width: 66%; margin-left: 5px;">
                                    <i class="fa fa-gear" aria-hidden="true"></i> Update & Generate Payslip
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- /Earnings & Total -->
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {

        function totalHoursComputation() {
            var grossMonthly = parseFloat($('#grossMonthly').val()) || 0;
            var dailyRate = grossMonthly / 22;
            var hourlyRate = (grossMonthly / 22).toFixed(2) / 8;

            var timeString = $('#totalHours').val();
            var totalHours = timeStringToHours(timeString);

            var netPay = hourlyRate * totalHours;
            $('#netPay').val(netPay.toFixed(2));
        }

        function computeTotalLate() {
            var totalLateTimeString = $('#tLate').val();
            var totalLateMinutes = timeStringToMinutes(totalLateTimeString);
            var grossMonthly = parseFloat($('#grossMonthly').val()) || 0;
            var dailyRate = grossMonthly / 22;
            var hourlyRate = (grossMonthly / 22).toFixed(2) / 8;

            var lateDeduction = (hourlyRate / 60) * totalLateMinutes;

            $('#late').val(lateDeduction.toFixed(2));

            updateTotalDeduction();
        }

        // Function to convert a time string (HH:MM:SS) to total minutes
        function timeStringToMinutes(timeString) {
            if (!timeString) return 0; // Handle empty input
            var parts = timeString.split(':');
            var hours = parseInt(parts[0], 10);
            var minutes = parseInt(parts[1], 10);
            var seconds = parseInt(parts[2], 10);

            // Convert everything to minutes
            return (hours * 60) + minutes + (seconds / 60);
        }

        $(document).ready(function () {
            computeTotalLate();

            $('#grossMonthly, #tLate').on('input', computeTotalLate);
        });

        // Function to convert time string (HH:mm:ss) to total hours
        function timeStringToHours(timeString) {
            var parts = timeString.split(':');
            var hours = parseInt(parts[0]);
            var minutes = parseInt(parts[1]);
            var seconds = parseInt(parts[2]);
            return hours + (minutes / 60) + (seconds / 3600);
        }

        $(document).ready(function () {
            // Trigger computation on page load
            totalHoursComputation();

            // Set up listeners to trigger computation when fields change
            $('#grossMonthly, #totalHours').on('input', totalHoursComputation);
        });

        // Function to compute sick/vacation leave value
        function computeSickVacationLeave() {
            // Retrieve values from the fields
            var grossMonthly = parseFloat($('#grossMonthly').val()) || 0;
            var dailyRate = grossMonthly / 22;
            var hourlyRate = (grossMonthly / 22).toFixed(2) / 8;

            var vLeave = parseInt($('#vLeave').val()) || 0;
            var sLeave = parseInt($('#sLeave').val()) || 0;
            var bLeave = parseInt($('#bLeave').val()) || 0;
            var otHours = parseInt($('#otHours').val()) || 0;
            var nDiff = parseInt($('#nDiff').val()) || 0;
            var rHoliday = parseInt($('#rHoliday').val()) || 0;

            // Compute values
            var otCompute = hourlyRate * 0.25;
            var nDiffCompute = hourlyRate * 0.10;
            var nDiffTotal = (hourlyRate + nDiffCompute) * nDiff;
            var otTotal = (hourlyRate + otCompute) * otHours;
            var regHolidayValue = dailyRate * rHoliday;
            var bLeaveValue = dailyRate * bLeave;
            var vacLeaveValue = dailyRate * vLeave;
            var sickLeaveValue = dailyRate * sLeave;

            // Update fields with computed values
            $('#vacLeave').val(vacLeaveValue.toFixed(2));
            $('#sickLeave').val(sickLeaveValue.toFixed(2));
            $('#bdayLeave').val(bLeaveValue.toFixed(2));
            $('#otTotal').val(otTotal.toFixed(2));
            $('#nightDiff').val(nDiffTotal.toFixed(2));
            $('#regHoliday').val(regHolidayValue.toFixed(2));

            // Update total earnings
            updateTotalEarnings();
        }

        // Ensure the fields are initialized and the computation is done on page load
        $(document).ready(function () {
            // Trigger computation on page load
            computeSickVacationLeave();

            $('#vLeave, #sLeave, #otHours, #bLeave, #nDiff, #rHoliday').on('change',
                computeSickVacationLeave);

        });

        // Toggle SSS contribution
        $('input[name="option_sss"]').on('change', function () {
            var grossMonthly = parseFloat($('#grossMonthly').val()) || 0;
            if ($(this).val() === 'yes') {
                $('#sss').val((grossMonthly * 0.0225).toFixed(2)).prop('readonly', false);
            } else {
                $('#sss').val('').prop('readonly', true);
            }
            updateTotalDeduction();
        });

        // Toggle PhilHealth contribution
        $('input[name="option_philhealth"]').on('change', function () {
            var grossMonthly = parseFloat($('#grossMonthly').val()) || 0;
            if ($(this).val() === 'yes') {
                $('#philHealth').val((grossMonthly * 0.0125).toFixed(2)).prop('readonly', false);
            } else {
                $('#philHealth').val('').prop('readonly', true);
            }
            updateTotalDeduction();
        });

        // Toggle PagIbig contribution
        $('input[name="option_pagibig"]').on('change', function () {
            if ($(this).val() === 'yes') {
                $('#pagIbig').val('100.00').prop('readonly', false);
            } else {
                $('#pagIbig').val('').prop('readonly', true);
            }
            updateTotalDeduction();
        });

        // Toggle withHolding
        $('input[name="option_tax"]').on('change', function () {
            if ($(this).val() === 'yes') {
                $('#withHolding').prop('readonly', false);
            } else {
                $('#withHolding').val('').prop('readonly', true);
            }
            updateTotalDeduction();
        });

        // Toggle BirthdayPTO
        $('input[name="option_birthday"]').on('change', function () {
            var hourlyRate = parseFloat($('#hourlyRate').val()) || 0;
            if ($(this).val() === 'yes') {
                $('#birthdayPTO').val((hourlyRate * 8).toFixed(2)).prop('readonly', false);
            } else {
                $('#birthdayPTO').val('').prop('readonly', true);
            }
            updateTotalEarnings();
        });

        // Update total deductions and earnings
        $('#withHolding, #loan, #advance, #others, #bonus, #sss, #pagIbig, #philHealth').on('input',
            function () {
                updateTotalDeduction();
            });

        function updateTotalDeduction() {
            var sssContribution = parseFloat($('#sss').val()) || 0;
            var philHealthContribution = parseFloat($('#philHealth').val()) || 0;
            var pagIbigContribution = parseFloat($('#pagIbig').val()) || 0;
            var withHolding = parseFloat($('#withHolding').val()) || 0;
            var loan = parseFloat($('#loan').val()) || 0;
            var advance = parseFloat($('#advance').val()) || 0;
            var others = parseFloat($('#others').val()) || 0;
            var lateDeduction = parseFloat($('#late').val()) || 0;

            var totalDeduction = sssContribution + philHealthContribution + pagIbigContribution + withHolding +
                loan + advance + others + lateDeduction;

            $('#totalDeduction').val(totalDeduction.toFixed(2));
            updateTotalEarnings();
        }

        function updateTotalEarnings() {
            var birthdayPTO = parseFloat($('#birthdayPTO').val()) || 0;
            var vacLeave = parseFloat($('#vacLeave').val()) || 0;
            var sickLeave = parseFloat($('#sickLeave').val()) || 0;
            var otTotal = parseFloat($('#otTotal').val()) || 0;
            var nDiffTotal = parseFloat($('#nightDiff').val()) || 0;
            var regHoliday = parseFloat($('#regHoliday').val()) || 0;
            var bonus = parseFloat($('#bonus').val()) || 0;

            var totalEarnings = birthdayPTO + vacLeave + sickLeave + otTotal + bonus + nDiffTotal + regHoliday;
            $('#totalEarning').val(totalEarnings.toFixed(2));

            var netPay = parseFloat($('#netPay').val()) || 0;
            var totalDeduction = parseFloat($('#totalDeduction').val()) || 0;
            var netPayAfterDeduction = netPay - totalDeduction;
            var netPayTotal = netPayAfterDeduction + totalEarnings;

            $('#netPayTotal').val(netPayTotal.toFixed(2));
        }
    });

</script>

<script>
    // JavaScript Function to Calculate Daily Rate
    function calculateDailyRate() {
        const grossMonthly = parseFloat(document.getElementById('grossMonthly').value);
        const dailyRate = grossMonthly / 22; // Calculate daily rate without rounding
        const hourlyRate = (grossMonthly / 22).toFixed(2) / 8;
        document.getElementById('dailyRate').value = dailyRate.toFixed(2);
        document.getElementById('hourlyRate').value = hourlyRate;
    }

    // Call the function when the page loads
    window.onload = calculateDailyRate;

</script>


@endsection
