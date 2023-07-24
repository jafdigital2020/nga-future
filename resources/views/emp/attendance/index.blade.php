@extends('layouts.empmaster') @section('title', 'Attendance')
@section('content')

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
<div class="profile-container">
    <div class="profile">
        @auth @if (Auth::user()->image)
        <img
            src="{{ asset('images/' . Auth::user()->image) }}"
            alt="Profile Image"
        />
        @else
        <img
            src="{{ asset('images/default-profile-image.jpg') }}"
            alt="Profile Image"
        />
        @endif
        <div class="details">
            <h1>{{ Auth::user()->name }}</h1>
            <p>{{ Auth::user()->position }}</p>
            <ul>
                <li>
                    <strong>Employee ID:</strong>{{ Auth::user()->empNumber }}
                </li>
                <li><strong>Email:</strong>{{ Auth::user()->email }}</li>
                <li><strong>Phone:</strong>{{ Auth::user()->phoneNumber }}</li>
                <li>
                    <strong>Address:</strong>{{ Auth::user()->completeAddress }}
                </li>
            </ul>
        </div>
        @endauth
    </div>

    <div class="button-container">
        <!-- FORM ATTENDANCE START -->
        <div class="form-container">
            <form action="{{ url('emp/attendance') }}" method="POST">
                @csrf
                <button type="submit" class="button-92" id="punchin">
                    Time In
                </button>
            </form>

            <form action="{{ url('emp/attendance/breakin') }}" method="POST">
                @csrf @method('PUT')
                <button type="submit" class="button-92" id="breakin">
                    Break Out
                </button>
            </form>

            <div id="countdown"></div>

            <form action="{{ url('emp/attendance/breakout') }}" method="POST">
                @csrf @method('PUT')
                <button type="submit" class="button-92" id="breakout">
                    Break In
                </button>
            </form>

            <form action="{{ url('emp/attendance/') }}" method="POST">
                @csrf @method('PUT')
                <button type="submit" class="button-92" id="punchot">
                    Time Out
                </button>
            </form>
        </div>
        <!-- FORM ATTENDANCE END -->
    </div>
</div>
@if (session('success'))
<div class="alert alert-success">{{ session("success") }}</div>
@endif @if (session('error'))
<div class="alert alert-danger">{{ session("error") }}</div>
@endif

<!-- TABLE   -->
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card-content table-responsive">
                <h3></h3>
                @if($latest)
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Break In</th>
                            <th>Break Out</th>
                            <th>Time Out</th>
                            <th>Status</th>
                            <th>Total Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $latest->name }}</td>
                            <td>{{ $latest->date }}</td>
                            <td>{{ $latest->timeIn }}</td>
                            <td>{{ $latest->breakIn }}</td>
                            <td>{{ $latest->breakOut }}</td>
                            <td>{{ $latest->timeOut }}</td>
                            <td>
                                <span
                                    style="background-color: {{ $latest->status == 'Late' ? 'red' : 'green' }}; color:white; padding:10px 20px 10px 20px; border-radius:20px; font-size: 14px; font-weight: bold;"
                                >
                                    {{ $latest->status }}</span
                                >
                            </td>
                            <td>{{ $latest->timeTotal }}</td>
                        </tr>
                    </tbody>
                </table>
                @else
                <p>No Attendance Found</p>
                @endif
                <!-- <table class="table" id="example" style="width: 100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Break In</th>
                            <th>Break Out</th>
                            <th>Time Out</th>
                            <th>Total Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($empatt as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->date }}</td>
                            <td>{{ $item->timeIn }}</td>
                            <td>{{ $item->breakIn }}</td>
                            <td>{{ $item->breakOut }}</td>
                            <td>{{ $item->timeOut }}</td>
                            <td>{{ $item->timeTotal }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6">Total</th>
                            <th id="total_hours">{{ $total }}</th>
                        </tr>
                    </tfoot>
                </table> -->
            </div>
        </div>
    </div>
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
