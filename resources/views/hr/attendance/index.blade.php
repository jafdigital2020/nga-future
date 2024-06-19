@extends('layouts.hrmaster') @section('title', 'Attendance') @section('content')

<div class="content container-fluid">
    <!-- Page Header -->

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Attendance</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/hr/dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Time In & Time Out</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_employee"><i
                        class="fa fa-plus"></i> Request Overtime </a>
            </div>
        </div>
    </div>

    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-4">
            <div class="card punch-status">
                <div class="card-body">
                    <h5 class="card-title">
                        Timesheet
                        <small class="text-muted">{{ \Carbon\Carbon::now()->format('d M Y') }}</small>
                    </h5>
                    <div class="punch-det">
                        @if (session('success'))
                        <div class="alert alert-success">
                            {{ session("success") }}
                        </div>
                        @elseif (session('error'))
                        <div class="alert alert-danger">
                            {{ session("error") }}
                        </div>
                        @else
                        <h6>Hello {{ Auth::user()->name }}</h6>
                        <p>Welcome to One JAF!</p>
                        @endif
                    </div>
                    <div class="punch-info">
                        <div class="punch-hours">
                            @if($latest && $latest->date ===
                            now()->format('Y-m-d'))
                            <span>{{ $latest->timeTotal }}</span>
                            @else
                            <span>Total Time</span>
                            @endif
                        </div>
                    </div>

                    <!-- Attendance -->

                    <!-- Time In -->
                    <div class="punch-btn-container">
                        <form action="{{ url('hr/attendance') }}" method="POST">
                            @csrf
                            <div class="punch-btn-section">
                                <button type="submit" class="btn btn-primary punch-btn">
                                    Time In
                                </button>
                            </div>
                        </form>
                        <!-- Time Out -->

                        <div class="punch-btn-section">
                            <button type="submit" class="btn btn-primary punch-btn" data-toggle="modal"
                                data-target="#exampleModal">
                                Time Out
                            </button>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">
                                        Warning!
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to time out?
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ url('hr/attendance/') }}" method="POST">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-primary">
                                            Yes
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        No
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->

                    <!-- Break Buttons -->

                    <div class="statistics">
                        <div class="row">
                            <div class="col-md-6 col-6 text-center">
                                <form action="{{ url('hr/attendance/breakin') }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="stats-box">
                                        <button class="breakOut">
                                            Break Out
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 col-6 text-center">
                                <form action="{{ url('hr/attendance/breakout') }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="stats-box">
                                        <button class="breakOut" data-toggle="modal" data-target="#exampleModalCenter">
                                            Break In
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card att-statistics">
                <div class="card-body">
                    <h5 class="card-title">Employee Info</h5>
                    <div class="stats-list">
                        <div class="stats-info">
                            <p>
                                Name
                                <strong>{{ Auth::user()->name }}</strong>
                            </p>
                        </div>
                        <div class="stats-info">
                            <p>
                                Employee ID
                                <strong>{{ Auth::user()->empNumber }}</strong>
                            </p>
                        </div>
                        <div class="stats-info">
                            <p>
                                Email
                                <strong>{{ Auth::user()->email }}</strong>
                            </p>
                        </div>
                        <div class="stats-info">
                            <p>
                                Phone
                                <strong>{{ Auth::user()->phoneNumber }}</strong>
                            </p>
                        </div>
                        <div class="stats-info">
                            <p>
                                Address
                                <strong>{{ Auth::user()->completeAddress }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card recent-activity">
                <div class="card-body">
                    <h5 class="card-title">Today's Activity</h5>
                    <ul class="res-activity-list">
                        @if($latest && $latest->date === now()->format('Y-m-d'))
                        @if($latest->timeIn)
                        <li>
                            <p class="mb-0">Punch In at</p>
                            <p class="res-activity-time">
                                <i class="fa fa-clock-o"></i>
                                {{ $latest->timeIn }}
                            </p>
                        </li>
                        @endif @if($latest->breakIn)
                        <li>
                            <p class="mb-0">Break Out at</p>
                            <p class="res-activity-time">
                                <i class="fa fa-clock-o"></i>
                                {{ $latest->breakIn }}
                            </p>
                        </li>
                        @endif @if($latest->breakOut)
                        <li>
                            <p class="mb-0">Break In at</p>
                            <p class="res-activity-time">
                                <i class="fa fa-clock-o"></i>
                                {{ $latest->breakOut }}
                            </p>
                        </li>
                        @endif @if($latest->timeOut)
                        <li>
                            <p class="mb-0">Punch Out at</p>
                            <p class="res-activity-time">
                                <i class="fa fa-clock-o"></i>
                                {{ $latest->timeOut }}
                            </p>
                        </li>
                        @endif @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <form action="{{ route('att.test') }}" method="GET">
        @csrf
        <div class="row filter-row">
            <div class="col-sm-3">
                <div class="form-group form-focus">
                    <div class="cal-icon">
                        <input type="text" class="form-control floating datetimepicker" id="start_date"
                            name="start_date" />
                    </div>
                    <label class="focus-label">From</label>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group form-focus">
                    <div class="cal-icon">
                        <input type="text" class="form-control floating datetimepicker" id="end_date" name="end_date" />
                    </div>
                    <label class="focus-label">To</label>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="filter" id="filter">
                        <option value="">-</option>
                        <option value="last_15_days">Last 15 Days</option>
                        <option value="last_30_days">Last 30 Days</option>
                        <option value="last_year">Last Year</option>
                    </select>
                    <label class="focus-label">Select Days</label>
                </div>
            </div>
            <div class="col-sm-3">
                <button type="submit" class="btn btn-success btn-block">
                    Search
                </button>
            </div>
        </div>
    </form>
    <!-- /Search Filter -->

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-striped table-nowrap custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Punch In</th>
                            <th>Punch Out</th>
                            <th>Status</th>
                            <th>Total Late</th>
                            <th>Total Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filteredData as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->date }}</td>
                            <td>{{ $item->timeIn }}</td>
                            <td>{{ $item->timeOut }}</td>
                            <td>
                                <span class="{{ $item->status == 'Late' ? 'bg-inverse-danger' : 'bg-inverse-success' }}"
                                    style="
                                        padding: 5px 10px 5px 10px;
                                        border-radius: 5px;
                                        font-size: 12px;
                                        font-weight: bold;
                                        color: white;
                                    ">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>{{ $item->totalLate }}</td>
                            <td>{{ $item->timeTotal }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5">Total</th>
                            <th>{{ $totalLate }}</th>
                            <th id="total_hours">{{ $totalTime }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@include('sweetalert::alert') @endsection
