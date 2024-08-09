@extends('layouts.hrmaster') @section('title', 'Attendance') @section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Attendance</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.html">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <div class="view-icons">
                    <a href="{{ url('hr/attendance') }}" class="grid-view btn btn-link"><i class="fa fa-th"></i></a>
                    <a href="{{ url('hr/attendance/tableview') }}" class="list-view btn btn-link active"><i
                            class="fa fa-bars"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @php
    $currentMonth = date('n'); // Current month as a number (1-12)
    $currentYear = date('Y'); // Current year
    $selectedMonth = old('month', request('month', $currentMonth));
    $selectedYear = old('year', request('year', $currentYear));
    @endphp

    <!-- Search Filter -->
    <form method="GET" action="{{ route('report.empindex') }}">
        <div class="row filter-row">
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus">
                    <input type="text" class="form-control floating" name="employee_name">
                    <label class="focus-label">Employee Name</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="department">
                        <option value="">- </option>
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
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="month">
                        <option value="">-</option>
                        @for($i = 1; $i <= 12; $i++) <option value="{{ $i }}"
                            {{ $i == $selectedMonth ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                            @endfor
                    </select>
                    <label class="focus-label">Select Month</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="year">
                        <option value="">-</option>
                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ $i == $selectedYear ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select> <label class="focus-label">Select Year</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <button type="submit" class="btn btn-primary btn-block"> Search </button>
            </div>
        </div>
    </form>
    <!-- /Search Filter -->

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                @csrf
                <table class="table table-hover table-nowrapdatatable" id="edittable">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Break In</th>
                            <th>Break Out</th>
                            <th>Time Out</th>
                            <th>Status</th>
                            <th>Total Late</th>
                            <th>Total Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filteredData as $user)
                        @foreach($user->employeeAttendance as $attendance)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="#" class="avatar">
                                        @if ($user->image)
                                        <img src="{{ asset('images/' . $user->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{
                                            asset('images/default.png')
                                        }}" alt="Profile Image" />
                                        @endif</a>
                                    <a href="#">{{ $user->name }}
                                        <span>{{ $user->department }}</span></a>
                                </h2>
                            </td>
                            <td>{{ $attendance->date }}</td>
                            <td>{{ $attendance->timeIn }}</td>
                            <td>{{ $attendance->breakIn }}</td>
                            <td>{{ $attendance->breakOut }}</td>
                            <td>{{ $attendance->timeOut }}</td>
                            <td>
                                <span
                                    class="{{ $attendance->status == 'Late' ? 'bg-inverse-danger' : 'bg-inverse-success' }}"
                                    style="
                                                padding: 5px 10px;
                                                border-radius: 5px;
                                                font-size: 12px;
                                                font-weight: bold;
                                                color: white;
                                            ">
                                    {{ $attendance->status }}
                                </span>
                            </td>
                            <td>{{ $attendance->totalLate }}</td>
                            <td>{{ $attendance->timeTotal }}</td>
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7">Total</th>

                            <th>{{ $totalLate }}</th>
                            <th id="total_hours">{{ $total }}</th>
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
