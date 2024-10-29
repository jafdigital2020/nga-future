@extends('layouts.master') @section('title', 'Payroll')
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
    <form action="{{ url('admin/payroll/edit/payslip/'. $attendance->id) }}" method="POST">
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
                                            value="{{ $attendance->cut_off }}" readonly>
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
                                                placeholder="-- Select Date --" value="{{ $attendance->start_date }}"
                                                readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">To</label>
                                    <div class="col-lg-9">
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" id="end_date" name="end_date"
                                                placeholder="-- Select Date --" value="{{ $attendance->end_date }}"
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
                                            value="{{ $attendance->user->fName }} {{ $attendance->user->lName }}"
                                            readonly>
                                    </div>
                                </div>
                                <div class="form-group row">

                                    <label class="col-lg-3 col-form-label">Position</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="position" id="position"
                                            value="{{ $attendance->user->position }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Department</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="department" id="department"
                                            value="{{ $attendance->user->department }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Total Hours</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="totalHours" id="totalHours"
                                            value="{{ $attendance->totalHours }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Create Payroll -->


        <div class="row">
            <!-- Earnings Payroll -->
            <div class="col-xl-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Earnings</h4>
                    </div>
                    <div class="card-body">

                        <!-- Conditionally Display Overtime Field -->
                        @if($overtimePay > 0)
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Overtime</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="otHours" id="otHours"
                                    value="{{ number_format($overtimePay, 2) }}" readonly>
                            </div>
                        </div>

                        @endif

                        <!-- Conditionally Display Paid Leave Field -->
                        @if($paidLeaveAmount > 0)
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Paid Leave</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="paidLeaveAmount" id="paidLeaveAmount"
                                    value="{{ number_format($paidLeaveAmount, 2) }}" readonly>
                            </div>
                        </div>
                        @endif

                        <!-- Dynamic Earnings -->
                        @if(!empty($dynamicEarnings) && count($dynamicEarnings) > 0)
                        @foreach($dynamicEarnings as $earningName => $earning)
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">{{ $earningName }}</label>
                            <div class="col-lg-9">
                                <input type="text" name="{{ $earningName }}" id="{{ $earningName }}"
                                    class="form-control" value="{{ number_format($earning['amount'], 2) }}" readonly>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <p>No additional earnings available.</p>
                        @endif

                    </div>
                </div>
            </div>
            <!-- /Earnings Payroll -->

            <!-- Deduction Payroll -->
            <div class="col-xl-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Deductions</h4>
                    </div>
                    <div class="card-body">
                        <!-- Dynamic deduction -->
                        @if(!empty($dynamicDeductions) && count($dynamicDeductions) > 0)
                        @foreach($dynamicDeductions as $deductionName => $deduction)
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">{{ $deductionName }}</label>
                            <div class="col-lg-9">
                                <input type="text" name="{{ $deductionName }}" id="{{ $deductionName }}"
                                    class="form-control" value="{{ number_format($deduction['amount'], 2) }}" readonly>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <p>No additional deductions available.</p>
                        @endif
                    </div>
                </div>
            </div>
            <!-- /Deduction Payroll -->


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
                                <input type="text" class="form-control" name="grossMonthly" id="grossMonthly" value=""
                                    readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Gross Basic Pay</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="grossBasic" id="grossBasic" value=""
                                    readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Daily Rate</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="dailyRate" id="dailyRate" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Hourly Rate</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="hourlyRate" id="hourlyRate" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Net Pay</label>
                            <div class="col-lg-9">
                                <input type="hidden" name="netPay" id="netPay" class="form-control" readonly>
                                <input type="text" name="netPayTotal" id="netPayTotal" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="form-group row">

                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-danger btn-block" name="action" value="save">
                                    <i class="fa fa-file" aria-hidden="true"></i> Save
                                </button>
                            </div>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-outline-danger btn-block" name="action"
                                    value="generate">
                                    <i class="fa fa-gear" aria-hidden="true"></i> Generate Payroll
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- /Earnings & Total -->
</div>

@endsection

@section('scripts')



@endsection
