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
                    <li class="breadcrumb-item"><a href="{{ url('hr/dashboard') }}">Dashboard</a></li>
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


@endsection
