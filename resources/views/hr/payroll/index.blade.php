@extends('layouts.hrmaster') @section('title', 'Employees')
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
                    <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payroll</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Create Payroll -->
    <form id="searchForm" action="" method="POST">
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
                                                value="{{ date('Y-m-d') }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">From</label>
                                    <div class="col-lg-9">
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" id="start_date" name="start_date"
                                                placeholder="-- Select Date --" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">To</label>
                                    <div class="col-lg-9">
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" id="end_date" name="end_date"
                                                placeholder="-- Select Date --" required />
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Month</label>
                                    <div class="col-lg-9">
                                        <select class="form-control" name="month" id="month">
                                            <option>-- Select Month --</option>
                                            @for ($i = 1; $i <= 12; $i++) <option
                                                {{ date('n') == $i ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                </option>
                                                @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Full Name</label>
                                    <div class="col-lg-9">
                                        <select class="form-control" id="user_id" name="user_id" required>
                                            <option value="">-- Select --</option>
                                            @foreach($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Position</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="position" id="position" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Total Hours</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="totalHours" id="totalHours"
                                            readonly>
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
                                            style="margin-left: 10px;" readonly>
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
                                            style="margin-left: 10px;" readonly>
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
                                            style="margin-left: 10px;" readonly>
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
                                            style="margin-left: 10px;" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Late</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="late" id="late" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Loan</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="loan" id="loan" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Advance</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="advance" id="advance" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Others</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="others" id="others" class="form-control">
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
                            <label class="col-lg-3 col-form-label">Birthday PTO</label>
                            <div class="col-lg-9" style="display: flex; align-items: center;">
                                <label
                                    style="margin-bottom: 0; display: flex; align-items: center; margin-right: 10px;">
                                    <span style="margin-right: 10px;">Yes</span>
                                    <input type="radio" name="option_birthday" value="yes" class="fetch-data">
                                </label>
                                <label
                                    style="margin-bottom: 0; display: flex; align-items: center; margin-right: 10px;">
                                    <span style="margin-right: 10px;">No</span>
                                    <input type="radio" name="option_birthday" value="no" class="fetch-data">
                                </label>
                                <input type="text" class="form-control" name="birthdayPTO" id="birthdayPTO"
                                    style="margin-left: 10px;" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Vacation Leave</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="vLeave" id="vLeave"
                                    style="display: inline-block; width: 15%;">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                                <input type="text" name="vacLeave" id="vacLeave" class="form-control"
                                    style="display: inline-block; width: 84%;" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Sick Leave</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="sLeave" id="sLeave"
                                    style="display: inline-block; width: 15%;">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                                <input type="text" class="form-control" name="sickLeave" id="sickLeave"
                                    style="display: inline-block; width: 84%;" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Overtime</label>
                            <div class="col-lg-9">
                                <input type="number" class="form-control" name="otHours" id="otHours"
                                    style="display: inline-block; width: 15%;">

                                <input type="text" name="otTotal" id="otTotal" class="form-control"
                                    style="display: inline-block; width: 84%;" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Extra Mile / Bonus</label>
                            <div class="col-lg-9">
                                <input type="text" name="bonus" id="bonus" class="form-control">
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
                                    readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Total Earning</label>
                            <div class="col-lg-9">
                                <input type="text" name="totalEarning" id="totalEarning" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Gross Monthly Pay</label>
                            <div class="col-lg-9">
                                <input type="hidden" name="hourlyRate" id="hourlyRate">
                                <input type="text" class="form-control" name="grossMonthly" id="grossMonthly" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Gross Basic Pay</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="grossBasic" id="grossBasic" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Net Pay</label>
                            <div class="col-lg-9">
                                <input type="hidden" name="netPay" id="netPay" class="form-control" readonly>
                                <input type="text" name="netPayTotal" id="netPayTotal" class="form-control" readonly
                                    style="color: red;">

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="text-right">
            <button type="submit" class="btn btn-danger btn-block">Generate Payroll</button>
        </div>
    </form>
    <!-- /Earnings & Total -->
</div>

@endsection

@section('scripts')

<script>
    $(document).ready(function () {
        // Function to fetch data
        function fetchData() {
            // Clear the input fields before making the AJAX request
            $('#hourlyRate').val('');
            $('#position').val('');
            $('#totalLate').val('00:00:00');
            $('#totalHours').val('00:00:00');
            $('#results').empty();

            // Serialize the form data
            var formData = $('#searchForm').serialize();
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            var userId = $('#user_id').val();

            // Check if user_id is selected
            if (userId) {
                $.ajax({
                    url: "{{ route('emppayroll') }}",
                    method: 'GET',
                    data: formData,
                    success: function (response) {
                        // Check if response contains the necessary data
                        if (response) {
                            // Set the value of the hourlyRate and position input fields
                            $('#hourlyRate').val(response.hourlyRate || '');
                            $('#position').val(response.position || '');

                            // Set the totalLate and totalHours fields only if they exist in the response
                            if (response.totalLate) {
                                $('#totalLate').val(response.totalLate);
                                // Compute late deduction
                                var hourlyRate = parseFloat(response.hourlyRate) || 0;
                                var totalLateMinutes = timeStringToMinutes(response.totalLate);
                                var totalHours = timeStringToHours(response.total);
                                var lateDeduction = (hourlyRate / 60) * totalLateMinutes;
                                $('#late').val(lateDeduction.toFixed(2));
                                var netPay = hourlyRate * totalHours;
                                $('#netPay').val(netPay.toFixed(2));
                                $('#netPayTotal').val(netPay.toFixed(2));
                            }
                            if (response.total) {
                                $('#totalHours').val(response.total);
                            }
                            // Salary Computation
                            var hourlyRate = parseFloat(response.hourlyRate) || 0;
                            var grossMonthly = hourlyRate * 8 * 22;
                            var grossBasic = grossMonthly / 2;
                            $('#grossMonthly').val(grossMonthly.toFixed(2));
                            $('#grossBasic').val(grossBasic.toFixed(2));

                            // Calculate and display contributions based on selected options
                            var sssContribution = 0;
                            var philHealthContribution = 0;
                            var pagIbigContribution = 0;
                            var withHolding = 0;
                            var birthdayPTO = 0;
                            var loan = parseFloat($('#loan').val()) || 0;
                            var advance = parseFloat($('#advance').val()) || 0;
                            var others = parseFloat($('#others').val()) || 0;

                            // Check if SSS option is selected
                            if ($('input[name="option_sss"]:checked').val() === 'yes') {
                                sssContribution = grossMonthly * 0.0225;
                                $('#sss').val(sssContribution.toFixed(2));
                            } else {
                                $('#sss').prop('readonly', true).val('');
                            }

                            // Check if PhilHealth option is selected
                            if ($('input[name="option_philhealth"]:checked').val() === 'yes') {
                                philHealthContribution = grossMonthly * 0.0125;
                                $('#philHealth').val(philHealthContribution.toFixed(2));
                            } else {
                                $('#philHealth').prop('readonly', true).val('');
                            }

                            // Check if Pag-Ibig option is selected
                            if ($('input[name="option_pagibig"]:checked').val() === 'yes') {
                                pagIbigContribution = 100.00;
                                $('#pagIbig').val(pagIbigContribution.toFixed(2));
                            } else {
                                $('#pagIbig').prop('readonly', true).val('');
                            }

                            // Check if withholding option is selected
                            if ($('input[name="option_tax"]:checked').val() === 'yes') {
                                withHolding = parseFloat($('#withHolding').val()) || 0;
                                $('#withHolding').prop('readonly', false);
                            } else {
                                $('#withHolding').prop('readonly', true).val('');
                            }

                            // Check if BirthdayPTO option is selected
                            if ($('input[name="option_birthday"]:checked').val() === 'yes') {
                                birthdayPTO = hourlyRate * 8;
                                $('#birthdayPTO').val(birthdayPTO.toFixed(2));
                            } else {
                                $('#birthdayPTO').prop('readonly', true).val('');
                            }

                            // Calculate total deduction
                            var totalDeduction = sssContribution + philHealthContribution +
                                pagIbigContribution + withHolding + loan + advance + others +
                                lateDeduction;
                            $('#totalDeduction').val(totalDeduction.toFixed(2));

                            // Display the fetched data in the results div
                            $.each(response.filteredData, function (index, data) {
                                $('#results').append('<div>' +
                                    '<p>Name: ' + data.name + '</p>' +
                                    '<p>Hourly Rate: ' + data.hourlyRate + '</p>' +
                                    '<p>Position: ' + data.position + '</p>' +
                                    '<p>Total Hours: ' + (response.total ||
                                        '00:00:00') + '</p>' +
                                    '<p>Total Late: ' + (response.totalLate ||
                                        '00:00:00') + '</p>' +
                                    '</div>');
                            });
                        } else {
                            $('#results').append('<p>No results found</p>');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching data:', error);
                    }
                });
            } else {
                alert('Please select a user.');
                $('#user_id').val(''); // Reset the user selection
            }
        }

        // Function to convert time string (HH:mm:ss) to total minutes
        function timeStringToMinutes(timeString) {
            var parts = timeString.split(':');
            return parseInt(parts[0]) * 60 + parseInt(parts[1]);
        }

        function timeStringToHours(timeString) {
            var parts = timeString.split(':');
            var hours = parseInt(parts[0]);
            var minutes = parseInt(parts[1]);
            var seconds = parseInt(parts[2]);

            // Convert hours, minutes, and seconds to total hours
            var totalHours = hours + (minutes / 60) + (seconds / 3600);
            return totalHours;
        }

        // Function to compute vacation leave value
        function computeSickVacationLeave() {
            var hourlyRate = parseFloat($('#hourlyRate').val()) || 0;
            var vLeave = parseInt($('#vLeave').val()) || 0;
            var sLeave = parseInt($('#sLeave').val()) || 0;
            var otHours = parseInt($('#otHours').val()) || 0; // Updated otHours value
            var otCompute = hourlyRate * 0.25;
            var otTotal = (hourlyRate + otCompute) * otHours; // Compute otTotal dynamically
            var vacLeaveValue = hourlyRate * 8 * vLeave;
            var sickLeaveValue = hourlyRate * 8 * sLeave;
            $('#vacLeave').val(vacLeaveValue.toFixed(2));
            $('#sickLeave').val(sickLeaveValue.toFixed(2));
            $('#otTotal').val(otTotal.toFixed(2));

            updateTotalEarnings();
        }

        // Update vacation leave value when vLeave select changes
        $('#vLeave, #sLeave, #otHours').on('change', function () {
            computeSickVacationLeave();
        });

        // Reset user selection when start_date is clicked
        $('#start_date, #end_date').on('click', function () {
            $('#user_id').val('');
            $('#position').val('');
            $('#totalHours').val('');
            $('#vLeave').val('');
            $('#vacLeave').val('');
            $('#sLeave').val('');
            $('#sickLeave').val('');
        });


        // Fetch data when start_date or end_date is changed
        $('#start_date, #end_date').on('change', function () {
            fetchData();
        });

        // Fetch data when user_id is changed
        $('#user_id').on('change', function () {
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            $('#vLeave').val('0');
            $('#vacLeave').val('');
            $('#sLeave').val('0');
            $('#sickLeave').val('');
            if (startDate && endDate) {
                fetchData();
            } else {
                alert('Please select both start date and end date.');
                $('#user_id').val(''); // Reset the user selection
            }
        });

        // Optional: Fetch data when the form is submitted (if you have a submit button)
        $('#searchForm').on('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission
            fetchData();
        });

        // SSS BUTTON FUNCTION
        $('input[name="option_sss"]').on('change', function () {
            if ($(this).val() === 'yes') {
                // If "Yes" is selected, calculate and set SSS contribution
                var grossMonthly = parseFloat($('#grossMonthly').val()) || 0;
                var sssContribution = grossMonthly * 0.0225;
                $('#sss').val(sssContribution.toFixed(2));
                $('#sss').prop('readonly', false);
            } else {
                // If "No" is selected, make the input readonly and clear its value
                $('#sss').prop('readonly', true).val('');
            }
            updateTotalDeduction();
        });

        // PHILHEALTH BUTTON FUNCTION
        $('input[name="option_philhealth"]').on('change', function () {
            if ($(this).val() === 'yes') {
                var grossMonthly = parseFloat($('#grossMonthly').val()) || 0;
                var philHealthContribution = grossMonthly * 0.0125;
                $('#philHealth').val(philHealthContribution.toFixed(2));
                $('#philHealth').prop('readonly', false);
            } else {
                $('#philHealth').prop('readonly', true).val('');
            }
            updateTotalDeduction();
        });

        // PagIbig BUTTON FUNCTION
        $('input[name="option_pagibig"]').on('change', function () {
            if ($(this).val() === 'yes') {
                var pagIbigContribution = 100.00;
                $('#pagIbig').val(pagIbigContribution.toFixed(2));
                $('#pagIbig').prop('readonly', false);
            } else {
                $('#pagIbig').prop('readonly', true).val('');
            }
            updateTotalDeduction();
        });

        // withHolding BUTTON FUNCTION
        $('input[name="option_tax"]').on('change', function () {
            if ($(this).val() === 'yes') {
                $('#withHolding').prop('readonly', false);
            } else {
                $('#withHolding').prop('readonly', true).val('');
            }
            updateTotalDeduction();
        });

        // Update total deduction when the withholding input field changes
        $('#withHolding, #loan, #advance, #others').on('input', function () {
            updateTotalDeduction();
        });

        // PHILHEALTH BUTTON FUNCTION
        $('input[name="option_philhealth"]').on('change', function () {
            if ($(this).val() === 'yes') {
                var grossMonthly = parseFloat($('#grossMonthly').val()) || 0;
                var philHealthContribution = grossMonthly * 0.0125;
                $('#philHealth').val(philHealthContribution.toFixed(2));
                $('#philHealth').prop('readonly', false);
            } else {
                $('#philHealth').prop('readonly', true).val('');
            }
            updateTotalDeduction();
        });

        // PagIbig BUTTON FUNCTION
        $('input[name="option_pagibig"]').on('change', function () {
            if ($(this).val() === 'yes') {
                var pagIbigContribution = 100.00;
                $('#pagIbig').val(pagIbigContribution.toFixed(2));
                $('#pagIbig').prop('readonly', false);
            } else {
                $('#pagIbig').prop('readonly', true).val('');
            }
            updateTotalDeduction();
        });

        // withHolding BUTTON FUNCTION
        $('input[name="option_tax"]').on('change', function () {
            if ($(this).val() === 'yes') {
                $('#withHolding').prop('readonly', false);
            } else {
                $('#withHolding').prop('readonly', true).val('');
            }
            updateTotalDeduction();
        });

        // BirthdayPTO BUTTON FUNCTION
        $('input[name="option_birthday"]').on('change', function () {
            var hourlyRate = parseFloat($('#hourlyRate').val()) || 0;
            if ($(this).val() === 'yes') {
                var birthdayPTO = hourlyRate * 8;
                $('#birthdayPTO').val(birthdayPTO.toFixed(2));
                $('#birthdayPTO').prop('readonly', false);
            } else {
                $('#birthdayPTO').prop('readonly', true).val('');
            }
            updateTotalEarnings();
        });

        // Update total deduction when the withholding input field changes
        $('#withHolding, #loan, #advance, #others').on('input', function () {
            updateTotalDeduction();
        });

        $('#bonus').on('input', function () {
            updateTotalEarnings();
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
            var bonus = parseFloat($('#bonus').val()) || 0;

            var totalEarnings = birthdayPTO + vacLeave + sickLeave + otTotal + bonus;
            $('#totalEarning').val(totalEarnings.toFixed(2));

            // Get net pay
            var netPay = parseFloat($('#netPay').val()) || 0;
            var totalDeduction = parseFloat($('#totalDeduction').val()) || 0;
            var netPayAfterDeduction = netPay - totalDeduction;

            // Add total earnings to net pay after deduction
            var netPayTotal = netPayAfterDeduction + totalEarnings;

            // Display net pay after adding total earnings
            $('#netPayTotal').val(netPayTotal.toFixed(2));
        }

    });

</script>

@endsection
