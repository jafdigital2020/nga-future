@extends('layouts.managermaster') @section('title', 'Payroll')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
@include('sweetalert::alert')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Payslip</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('manager/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payslip</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Create Payroll -->
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
                                <label class="col-lg-3 col-form-label">Name</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ $view->user->fName }} {{ $view->user->lName }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Email</label>
                                <div class="col-lg-9">
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="{{ $view->user->email }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Employee ID</label>
                                <div class="col-lg-9">
                                    <input type="text" name="empNumber" id="empNumber" class="form-control"
                                        value="{{ $view->user->empNumber }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Designation</label>
                                <div class="col-lg-9">
                                    <input type="text" name="cut_off" id="cut_off" class="form-control"
                                        value="{{ $view->user->position }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Pay Cutoff</label>
                                <div class="col-lg-9">
                                    <input type="text" name="cut_off" id="cut_off" class="form-control"
                                        value="{{ $view->start_date }} to {{ $view->end_date }}" readonly>
                                </div>
                            </div>
                        </div>
                        <!-- 2nd Row -->
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">SSS</label>
                                <div class="col-lg-9">
                                    <input type="text" name="sss" id="sss" class="form-control"
                                        value="{{ $view->user->sss }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">HDMF</label>
                                <div class="col-lg-9">
                                    <input type="text" name="pagIbig" id="spagIbigss" class="form-control"
                                        value="{{ $view->user->pagIbig }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">PHIC</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="philHealth" id="philHealth"
                                        value="{{ $view->user->philHealth }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">TIN No.</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="tin" id="tin"
                                        value="{{ $view->user->tin }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Payroll Date</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="transactionDate" id="transactionDate"
                                        value="{{ $view->transactionDate }}" readonly>
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
                                <div class="col-lg-9">
                                    <input type="text" name="sss" id="sss" class="form-control" value="{{ $view->sss }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">PHIC</label>
                                <div class="col-lg-9">
                                    <input type="text" name="philHealth" id="philHealth" class="form-control"
                                        value="{{ $view->philHealth }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">HDMF</label>
                                <div class="col-lg-9">
                                    <input type="text" name="pagIbig" id="pagIbig" class="form-control"
                                        value="{{ $view->pagIbig }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Withholding Tax</label>
                                <div class="col-lg-9">
                                    <input type="text" name="withHolding" id="withHolding" class="form-control"
                                        value="{{ $view->withHolding }}" readonly>
                                </div>
                            </div>
                        </div>
                        <!-- 2nd row -->
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Late</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name=tLate id="tLate"
                                        style="display: inline-block; width: 25%;" value="{{ $view->totalLate }}"
                                        readonly>
                                    <input type="text" name="late" id="late" class="form-control"
                                        style="display: inline-block; width: 74%;" value="₱{{ $view->late }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Loan</label>
                                <div class="col-lg-9">
                                    <input type="text" name="loan" id="loan" class="form-control"
                                        value="{{ $view->loan }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Advance</label>
                                <div class="col-lg-9">
                                    <input type="text" name="advance" id="advance" class="form-control"
                                        value="{{ $view->advance }}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Others</label>
                                <div class="col-lg-9">
                                    <input type="text" name="others" id="others" class="form-control"
                                        value="{{ $view->others }}" readonly>
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
                            <input type="text" name="bdayLeav" id="bdayLeave" class="form-control"
                                value="{{ $view->bdayLeave }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Vacation Leave</label>
                        <div class="col-lg-9">
                            <input type="text" name="vacLeave" id="vacLeave" class="form-control"
                                value="{{ $view->vacLeave }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Sick Leave</label>
                        <div class="col-lg-9">
                            <input type="text" name="sickLeave" id="sickLeave" class="form-control"
                                value="{{ $view->sickLeave }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Regular Holiday</label>
                        <div class="col-lg-9">
                            <input type="text" name="regHoliday" id="regHoliday" class="form-control"
                                value="{{ $view->regHoliday }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Overtime</label>
                        <div class="col-lg-9">
                            <input type="text" name="otTotal" id="otTotal" class="form-control"
                                value="{{ $view->otTotal }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">10% Night Differential</label>
                        <div class="col-lg-9">
                            <input type="text" name="nightDiff" id="nightDiff" class="form-control"
                                value="{{ $view->nightDiff }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Extra Mile / Bonus</label>
                        <div class="col-lg-9">
                            <input type="text" name="bonus" id="bonus" class="form-control" value="{{ $view->bonus }}"
                                readonly>
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
                                value="{{ $view->totalDeduction }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Total Earning</label>
                        <div class="col-lg-9">
                            <input type="text" name="totalEarning" id="totalEarning" class="form-control"
                                value="{{ $view->totalEarning }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Gross Monthly Pay</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" name="grossMonthly" id="grossMonthly"
                                value="{{ number_format($view->grossMonthly, 0)  }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Gross Basic Pay</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" name="grossBasic" id="grossBasic"
                                value="{{ number_format($view->grossBasic, 0) }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Daily Rate</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" name="dailyRate" id="dailyRate"
                                value="{{ $view->dailyRate }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Hourly Rate</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" name="hourlyRate" id="hourlyRate"
                                value="{{ $view->hourlyRate }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Net Pay</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" name="netPayTotal" id="netPayTotal"
                                value="₱{{ $view->netPayTotal }}"
                                style="color: red; display: inline-block; width: 100%;" readonly>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- /Earnings & Total -->
</div>

@endsection

@section('scripts')

@endsection
