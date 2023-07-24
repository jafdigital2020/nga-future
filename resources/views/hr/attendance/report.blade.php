@extends('layouts.hrmaster') @section('title', 'Attendance') @section('content')
<div class="report">
    <h1>Attendance Report</h1>

    <form action="{{ route('report.hrindex') }}" method="GET">
        @csrf
        <div class="date">
            <div class="cold-md-6">
                <div class="cal-icon">
                    <label for="start_date">Start Date:</label>
                    <input
                        class="form-control floating datetimepicker"
                        type="date"
                        id="start_date"
                        name="start_date"
                        style="width: 90%; text-align: center"
                    />
                </div>
            </div>

            <div class="cold-md-6">
                <label for="end_date">End Date:</label>
                <input
                    class="form-control floating datetimepicker"
                    type="date"
                    id="end_date"
                    name="end_date"
                    style="width: 90%; text-align: center"
                />
            </div>
            <div class="col-md-6">
                <label for="filter">Filter</label>
                <select name="filter" id="filter" class="form-control">
                    <option value="">-- Select a filter --</option>
                    <option value="last_15_days">Last 15 Days</option>
                    <option value="last_30_days">Last 30 Days</option>
                    <option value="last_year">Last Year</option>
                </select>
            </div>
            <button type="submit" class="button-50">Find</button>
        </div>
    </form>

    <table class="table" style="width: 100%">
        <thead class="thead-dark">
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
            @foreach($filteredData as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->date }}</td>
                <td>{{ $item->timeIn }}</td>
                <td>{{ $item->breakIn }}</td>
                <td>{{ $item->breakOut }}</td>
                <td>{{ $item->timeOut }}</td>
                <td>
                    <span
                        style="background-color:
                    {{ $item->status == 'Late' ? 'red' : 'green' }};
                    color:white; padding: 10px 20px 10px 20px;
                    border-radius:20px; font-size: 14px; font-weight: bold;"
                    >
                        {{ $item->status }}</span
                    >
                </td>
                <td>{{ $item->totalLate }}</td>
                <td>{{ $item->timeTotal }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7">Total</th>
                <th>{{ $totalLate }}</th>
                <th id="total_hours">{{ $totalTime }}</th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
