@extends('layouts.hrmaster') @section('title', 'Attendance') @section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">{{ $att->user->fName }}'s Attendance</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ url('hr/dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="{{ url('hr/timesheet/') }}" class="btn add-btn"><i class="fa fa-arrow-left"></i>
                    Go Back</a>
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

    <form id="auto-search-form" action="{{ url('hr/timesheet/view/' . $att->id) }}" method="GET">
        <input type="hidden" name="loaded" id="loaded" value="0">
        <div class="row filter-row">
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus">
                    <input type="text" class="form-control floating" name="employee_name" id="employee_name"
                        value="{{ $att->user ? $att->user->fName . ' ' . $att->user->lName : 'Name' }}" readonly>
                    <label class="focus-label">Employee Name</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus">
                    <div class="cal-icon">
                        <input type="text" class="datetimepicker form-control floating" name="start_date"
                            id="start_date" value="{{ $att->start_date }}" readonly>
                    </div>
                    <label class="focus-label">From</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus">
                    <div class="cal-icon">
                        <input type="text" class="datetimepicker form-control floating" name="end_date" id="end_date"
                            value="{{ $att->end_date }}" readonly>
                    </div>
                    <label class="focus-label">To</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus">
                    <input type="text" class="form-control floating" name="cut_off" id="cut_off"
                        value="{{ $att->cut_off }}" readonly>
                    <label class="focus-label">Cut-Off</label>
                </div>
            </div>
            <button type="submit" style="display: none;">Search</button>
        </div>
    </form>

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table datatable" id="edittable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Break In</th>
                            <th>Break Out</th>
                            <th>Time Out</th>
                            <th>Total Late</th>
                            <th>Total Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendanceRecords as $attendance)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="#" class="avatar">
                                        @if ($att->user->image)
                                        <img src="{{ asset('images/' . $att->user->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                        @endif
                                    </a>
                                    <a href="#">{{ $att->user->fName }} {{ $att->user->lName }}
                                        <span>{{ $att->user->department }}</span>
                                    </a>
                                </h2>
                            </td>
                            <td>{{ $attendance->date }}</td>
                            <td>{{ $attendance->timeIn }}</td>
                            <td>{{ $attendance->breakIn }}</td>
                            <td>{{ $attendance->breakOut }}</td>
                            <td>{{ $attendance->timeOut }}</td>
                            <td>{{ $attendance->totalLate }}</td>
                            <td>{{ $attendance->timeTotal }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6">Total</th>

                            <th style="color:red;">{{ $totalLate }}</th>
                            <th id="total_hours" style="color:green;">{{ $total }}</th>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('auto-search-form');
        var url = new URL(window.location.href);

        // Check if 'loaded' parameter is present in the URL
        if (!url.searchParams.has('loaded')) {
            // Add 'loaded' parameter to the URL to indicate that the page has been loaded
            url.searchParams.set('loaded', '1');
            window.history.replaceState({}, '', url);

            // Form Submit
            form.submit();
        }
    });

</script>


@endsection
