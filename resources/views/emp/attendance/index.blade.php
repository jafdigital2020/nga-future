@extends('layouts.empmaster') @section('title', 'Attendance')
@section('content')

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Employee</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ url('emp/dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Employee</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    <form action="{{ route('emp.attendance') }}" method="GET">
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
                <button type="submit" class="btn btn-primary btn-block">
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
                                <span class="
                                        {{ $item->status == 'Late' ? 'bg-danger' : 
                                        ($item->status == 'On Time' ? 'bg-success' : 
                                        ($item->status == 'Edited' ? 'bg-warning' : '')) }}
                                    " style="
                                        padding: 5px 10px;
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
@endsection

@section('scripts')



@endsection
