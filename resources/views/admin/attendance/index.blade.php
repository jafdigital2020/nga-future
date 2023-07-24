@extends('layouts.master') @section('title', 'Attendance') @section('content')

<div class="datetime">
    <div class="date">
        <span id="dayname">Day</span>,
        <span id="month">Month</span>
        <span id="daynum">00</span>,
        <span id="year">Year</span>
    </div>
    <div class="time">
        <span id="hour">00</span>: <span id="minutes">00</span>:
        <span id="seconds">00</span>
        <span id="period">AM</span>
    </div>
</div>

<div class="report">
    <h1>Employee Attendance Report</h1>

    <form action="{{ route('admin.empreport') }}" method="GET">
        <div class="date">
            <div class="cold-md-4">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" />
            </div>
            <span style="margin: 0 10px"> </span>
            <div class="cold-md-4">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" />
            </div>
            <div class="col-md-4">
                <label for="filter">Filter</label>
                <select name="filter" id="filter" class="form-control">
                    <option value="">-- Select a filter --</option>
                    <option value="last_15_days">Last 15 Days</option>
                    <option value="last_30_days">Last 30 Days</option>
                    <option value="last_year">Last Year</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="user_id">Employee:</label>
                <select class="form-control" id="user_id" name="user_id">
                    <option value="">All Employee</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="button-50">Filter</button>
        </div>
    </form>

    <table class="table" id="example" style="width: 100%">
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
                <td>
                    {{ $item->timeTotal }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7">Total</th>
                <th>{{ $totalLate }}</th>
                <th id="total_hours">
                    {{ $total }}
                </th>
            </tr>
        </tfoot>
    </table>
</div>

<script type="text/javascript">
    function updateClock() {
        var now = new Date();
        var dname = now.getDay(),
            mo = now.getMonth(),
            dnum = now.getDate(),
            yr = now.getFullYear(),
            hou = now.getHours(),
            min = now.getMinutes(),
            sec = now.getSeconds(),
            pe = "AM";

        if (hou == 0) {
            hou = 12;
        }
        if (hou > 12) {
            hou = hou - 12;
            pe = "PM";
        }

        Number.prototype.pad = function (digits) {
            for (var n = this.toString(); n.length < digits; n = 0 + n);
            return n;
        };

        var months = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December",
        ];
        var week = [
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
        ];
        var ids = [
            "dayname",
            "month",
            "daynum",
            "year",
            "hour",
            "minutes",
            "seconds",
            "period",
        ];
        var values = [
            week[dname],
            months[mo],
            dnum.pad(2),
            yr.pad(2),
            hou.pad(2),
            min.pad(2),
            sec.pad(2),
            pe,
        ];
        for (var i = 0; i < ids.length; i++)
            document.getElementById(ids[i]).firstChild.nodeValue = values[i];
    }
    function initClock() {
        updateClock();
        window.setInterval("updateClock()", 1);
    }
</script>

@endsection
