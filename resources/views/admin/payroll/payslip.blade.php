@extends('layouts.master') @section('title', 'Approved Timesheet')


@section('content')
@include('sweetalert::alert')

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Payslip</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payslip</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    <form action="{{ route('admin.payslipView') }}" method="GET">
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
                        <option value="0" {{ $cutoffPeriod == '0' ? 'selected' : '' }}>December - January 1st Cut-off
                        </option>
                        <option value="1" {{ $cutoffPeriod == '1' ? 'selected' : '' }}>January 2nd Cut-off</option>
                        <option value="2" {{ $cutoffPeriod == '2' ? 'selected' : '' }}>January - February 1st Cut-off
                        </option>
                        <option value="3" {{ $cutoffPeriod == '3' ? 'selected' : '' }}>February 2nd Cut-off</option>
                        <option value="4" {{ $cutoffPeriod == '4' ? 'selected' : '' }}>February - March 1st Cut-off
                        </option>
                        <option value="5" {{ $cutoffPeriod == '5' ? 'selected' : '' }}>March 2nd Cut-off</option>
                        <option value="6" {{ $cutoffPeriod == '6' ? 'selected' : '' }}>March - April 1st Cut-off
                        </option>
                        <option value="7" {{ $cutoffPeriod == '7' ? 'selected' : '' }}>April 2nd Cut-off</option>
                        <option value="8" {{ $cutoffPeriod == '8' ? 'selected' : '' }}>April - May 1st Cut-off</option>
                        <option value="9" {{ $cutoffPeriod == '9' ? 'selected' : '' }}>May 2nd Cut-off</option>
                        <option value="10" {{ $cutoffPeriod == '10' ? 'selected' : '' }}>May - June 1st Cut-off</option>
                        <option value="11" {{ $cutoffPeriod == '11' ? 'selected' : '' }}>June 2nd Cut-off</option>
                        <option value="12" {{ $cutoffPeriod == '12' ? 'selected' : '' }}>June - July 1st Cut-off
                        </option>
                        <option value="13" {{ $cutoffPeriod == '13' ? 'selected' : '' }}>July 2nd Cut-off</option>
                        <option value="14" {{ $cutoffPeriod == '14' ? 'selected' : '' }}>July - August 1st Cut-off
                        </option>
                        <option value="15" {{ $cutoffPeriod == '15' ? 'selected' : '' }}>August 2nd Cut-off</option>
                        <option value="16" {{ $cutoffPeriod == '16' ? 'selected' : '' }}>August - September 1st Cut-off
                        </option>
                        <option value="17" {{ $cutoffPeriod == '17' ? 'selected' : '' }}>September 2nd Cut-off</option>
                        <option value="18" {{ $cutoffPeriod == '18' ? 'selected' : '' }}>September - October 1st Cut-off
                        </option>
                        <option value="19" {{ $cutoffPeriod == '19' ? 'selected' : '' }}>October 2nd Cut-off</option>
                        <option value="20" {{ $cutoffPeriod == '20' ? 'selected' : '' }}>October - November 1st Cut-off
                        </option>
                        <option value="21" {{ $cutoffPeriod == '21' ? 'selected' : '' }}>November 2nd Cut-off</option>
                        <option value="22" {{ $cutoffPeriod == '22' ? 'selected' : '' }}>November - December 1st Cut-off
                        </option>
                        <option value="23" {{ $cutoffPeriod == '23' ? 'selected' : '' }}>December 2nd Cut-off</option>
                    </select>

                    <label class="focus-label">Cut-off Period</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="year">
                        <option value="">--Select Year--</option>
                        <option value="{{ $selectedYear }}" selected>{{ $selectedYear }}</option>
                        <!-- Add more options if needed -->
                    </select>
                    <label class="focus-label">Year</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-4 col-xl-4 col-12">
                <button type="submit" class="btn btn-primary btn-block">Search</button>
            </div>
        </div>
    </form>
    <!-- /Search Filter -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-hover table-nowrap mb-0 datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Month</th>
                            <th>Cut-Off</th>
                            <th>Total Hours</th>
                            <th>Net Pay</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payslip as $pay)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="{{ url('admin/employee/edit/'.$pay->user->id) }}" class="avatar">
                                        @if ($pay->user->image)
                                        <img src="{{ asset('images/' . $pay->user->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{
                                            asset('images/default.png')
                                        }}" alt="Profile Image" />
                                        @endif</a>
                                    <a href="{{ url('admin/employee/edit/'.$pay->user->id) }}">
                                        @if($pay->user->fName || $pay->user->mName || $pay->user->lName)
                                        {{ $pay->user->fName ?? '' }}
                                        {{ $pay->user->mName ?? '' }}
                                        {{ $pay->user->lName ?? '' }}
                                        @else
                                        {{ $pay->user->name }}
                                        @endif
                                        <span>{{ $pay->user->position }}</span>
                                    </a>
                                </h2>
                            </td>

                            <td>{{ $pay->user->department }}</td>
                            <td>{{ $pay->start_date }}</td>
                            <td>{{ $pay->end_date }}</td>
                            <td>{{ $pay->month }}</td>
                            <td>{{ $pay->cut_off }}</td>
                            <td>{{ $pay->totalHours }}</td>
                            <td>₱{{ number_format($pay->netPayTotal, 2) }}</td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ url('admin/payslip/view/'.$pay->id) }}"><i
                                                class="fa fa-eye m-r-5"></i>View</a>
                                        <a class="dropdown-item edit-attendance" href="#">
                                            <i class="fa fa-download m-r-5"></i>Download</a>
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




@endsection

@section('scripts')

@endsection
