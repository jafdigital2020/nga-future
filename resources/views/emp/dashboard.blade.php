@extends('layouts.empmaster') @section('title', 'Payroll Dashboard')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')

<div class="content container-fluid">
    <!-- ATTENDANCE -->
    <div class="row">
        <div class="col-md-12">
            <div class="welcome-box">
                <div class="welcome-img">
                    <img alt="" src="{{ asset('images/' . Auth::user()->image) }}">
                </div>
                <div class="welcome-det">
                    <h3>Hi, {{ Auth::user()->name }}</h3>
                    <p>{{ date('l, j F Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card punch-status">
                <div class="card-body">
                    <h5 class="card-title">
                        Timesheet
                        <small class="text-muted" id="clock"></small>
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
                        <form action="{{ url('emp/dashboard') }}" method="POST">
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
                                    <form action="{{ url('emp/dashboard/') }}" method="POST">
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
                                <form action="{{ url('emp/dashboard/breakin') }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="stats-box">
                                        <button class="breakOut">
                                            Break Out
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 col-6 text-center">
                                <form action="{{ url('emp/dashboard/breakout') }}" method="POST">
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
                                Position
                                <strong>{{ Auth::user()->position }}</strong>
                            </p>
                        </div>
                        <div class="stats-info">
                            <p>
                                Reporting to
                                @if ($supervisor === 'Management')

                                <strong>Management</strong>
                                @elseif ($supervisor)
                                <strong>{{ $supervisor->name }}</strong>
                                @else
                                <strong>No supervisor assigned for this department.</strong>
                                @endif
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
    <!--/ATTENDANCE -->

    <!-- CALENDAR RECORD -->

    <div class="row filter-row">
        <div class="calendar-container">
            <div class="calendar-header">
                <div class="filter-controls">
                    <div class="form-group form-focus select-focus">
                        <select class="select floating" id="monthSelect">
                            <option value="0">January</option>
                            <option value="1">February</option>
                            <option value="2">March</option>
                            <option value="3">April</option>
                            <option value="4">May</option>
                            <option value="5">June</option>
                            <option value="6">July</option>
                            <option value="7">August</option>
                            <option value="8">September</option>
                            <option value="9">October</option>
                            <option value="10">November</option>
                            <option value="11">December</option>
                        </select>
                        <label class="focus-label">Month</label>
                    </div>
                    <div class="form-group form-focus select-focus">
                        <select class="select floating" id="cutoffSelect">
                            <option value="first">1st Cut-off (1-15)</option>
                            <option value="second">2nd Cut-off (16-30/31)</option>
                            <option value="whole">Whole Month</option>
                        </select>
                        <label class="focus-label">Cut-off</label>
                    </div>
                    <button id="searchButton" class="btn btn-danger">Search</button>
                    <button class="btn btn-outline-danger">Send</button>
                </div>
            </div>
            <div id="calendar"></div>
        </div>
    </div>

    <!-- /Calendar Record -->

    <!-- Employee Tabs -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"></h4>
                    <ul class="nav nav-tabs nav-tabs-top">
                        <li class="nav-item"><a class="nav-link active" href="#top-tab1" data-toggle="tab">Employement
                                Record</a></li>
                        <li class="nav-item"><a class="nav-link" href="#top-tab2" data-toggle="tab">Salary</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="top-tab1">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Responsive Tables</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-nowrap mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>First Name</th>
                                                        <th>Last Name</th>
                                                        <th>Age</th>
                                                        <th>City</th>
                                                        <th>Country</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Anna</td>
                                                        <td>Pitt</td>
                                                        <td>35</td>
                                                        <td>New York</td>
                                                        <td>USA</td>
                                                    </tr>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Anna</td>
                                                        <td>Pitt</td>
                                                        <td>35</td>
                                                        <td>New York</td>
                                                        <td>USA</td>
                                                    </tr>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Anna</td>
                                                        <td>Pitt</td>
                                                        <td>35</td>
                                                        <td>New York</td>
                                                        <td>USA</td>
                                                    </tr>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Anna</td>
                                                        <td>Pitt</td>
                                                        <td>35</td>
                                                        <td>New York</td>
                                                        <td>USA</td>
                                                    </tr>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Anna</td>
                                                        <td>Pitt</td>
                                                        <td>35</td>
                                                        <td>New York</td>
                                                        <td>USA</td>
                                                    </tr>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Anna</td>
                                                        <td>Pitt</td>
                                                        <td>35</td>
                                                        <td>New York</td>
                                                        <td>USA</td>
                                                    </tr>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Anna</td>
                                                        <td>Pitt</td>
                                                        <td>35</td>
                                                        <td>New York</td>
                                                        <td>USA</td>
                                                    </tr>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Anna</td>
                                                        <td>Pitt</td>
                                                        <td>35</td>
                                                        <td>New York</td>
                                                        <td>USA</td>
                                                    </tr>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Anna</td>
                                                        <td>Pitt</td>
                                                        <td>35</td>
                                                        <td>New York</td>
                                                        <td>USA</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="top-tab2">
                            Tab content 2
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- /Employee Tabs -->

</div>



@endsection

@section('scripts')

<script>
    function startTime() {
        const today = new Date();
        let h = today.getHours();
        let m = today.getMinutes();
        let s = today.getSeconds();
        let ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12;
        h = h ? h : 12; // the hour '0' should be '12'
        m = checkTime(m);
        s = checkTime(s);
        document.getElementById('clock').innerHTML = h + ":" + m + ":" + s + " " + ampm;
        setTimeout(startTime, 1000);
    }

    function checkTime(i) {
        if (i < 10) {
            i = "0" + i
        }; // add zero in front of numbers < 10
        return i;
    }

</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendar = document.getElementById('calendar');
        const monthSelect = document.getElementById('monthSelect');
        const cutoffSelect = document.getElementById('cutoffSelect');
        const searchButton = document.getElementById('searchButton');
        const monthDisplay = document.getElementById('monthDisplay');

        const daysOfWeek = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN']; // Adjusted to start with Monday
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        function renderCalendar(month, year, startDay, endDay, data) {
            calendar.innerHTML = '';

            const firstDayOfMonth = new Date(year, month, 1).getDay();
            const startIndex = firstDayOfMonth === 0 ? 6 : firstDayOfMonth - 1;

            daysOfWeek.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.className = 'day-header';
                dayHeader.innerText = day;
                calendar.appendChild(dayHeader);
            });

            for (let i = 0; i < startIndex; i++) {
                const emptyDayDiv = document.createElement('div');
                emptyDayDiv.className = 'day';
                calendar.appendChild(emptyDayDiv);
            }

            let totalWorkedSeconds = 0;
            let totalLateSeconds = 0;

            for (let i = startDay; i <= endDay; i++) {
                const day = new Date(year, month, i);
                const dayDiv = document.createElement('div');
                dayDiv.className = 'day';
                dayDiv.style.padding = '5px';
                const dateNumber = document.createElement('span');
                dateNumber.className = 'date-number';
                dateNumber.innerText = i;
                dayDiv.appendChild(dateNumber);

                const dayData = data.find(item => new Date(item.date).getDate() === i && new Date(item.date)
                    .getMonth() === month);
                if (dayData) {
                    const timeIn = document.createElement('div');
                    timeIn.className = 'calendar-text';
                    timeIn.innerText = "Time In: " + dayData.timeIn;
                    dayDiv.appendChild(timeIn);

                    const timeOut = document.createElement('div');
                    timeOut.className = 'calendar-text';
                    timeOut.innerText = "Time Out: " + (dayData.timeOut || 'N/A');
                    dayDiv.appendChild(timeOut);

                    const timeTotal = document.createElement('div');
                    timeTotal.className = 'calendar-text';
                    timeTotal.innerText = "Total: " + (dayData.timeTotal || 'N/A');
                    dayDiv.appendChild(timeTotal);

                    const totalLate = document.createElement('div');
                    totalLate.className = 'calendar-text';
                    totalLate.innerText = "Total Late: " + (dayData.totalLate || 'N/A');
                    dayDiv.appendChild(totalLate);

                    if (dayData.timeTotal) {
                        const timeParts = dayData.timeTotal.split(':').map(Number);
                        totalWorkedSeconds += timeParts[0] * 3600 + timeParts[1] * 60 + timeParts[2];
                    }

                    if (dayData.totalLate) {
                        const lateParts = dayData.totalLate.split(':').map(Number);
                        totalLateSeconds += lateParts[0] * 3600 + lateParts[1] * 60 + lateParts[2];
                    }
                }

                calendar.appendChild(dayDiv);
            }

            const workedHours = Math.floor(totalWorkedSeconds / 3600);
            const workedMinutes = Math.floor((totalWorkedSeconds % 3600) / 60);
            const workedSeconds = totalWorkedSeconds % 60;
            const totalWorkedFormatted =
                `${workedHours.toString().padStart(2, '0')}:${workedMinutes.toString().padStart(2, '0')}:${workedSeconds.toString().padStart(2, '0')}`;

            const lateHours = Math.floor(totalLateSeconds / 3600);
            const lateMinutes = Math.floor((totalLateSeconds % 3600) / 60);
            const lateSeconds = totalLateSeconds % 60;
            const totalLateFormatted =
                `${lateHours.toString().padStart(2, '0')}:${lateMinutes.toString().padStart(2, '0')}:${lateSeconds.toString().padStart(2, '0')}`;

            const totalBox = document.createElement('div');
            totalBox.className = 'day total-box';
            totalBox.innerText =
                `Total Worked Hours: ${totalWorkedFormatted}\nTotal Late: ${totalLateFormatted}`;
            calendar.appendChild(totalBox);
        }


        function fetchAttendanceData(month, year, cutoff, callback) {
            const startDate = new Date(year, month, cutoff === 'second' ? 16 : 1);
            const endDate = new Date(year, month + (cutoff === 'first' ? 0 : 1), cutoff === 'first' ? 15 :
                new Date(year, month + 1, 0).getDate());

            $.ajax({
                url: "{{ route('attendance.get') }}",
                method: 'GET',
                dataType: 'json',
                data: {
                    start_date: startDate.toISOString().split('T')[0],
                    end_date: endDate.toISOString().split('T')[0]
                },
                success: function (data) {
                    callback(data);
                },
                error: function (err) {
                    console.error('Error fetching attendance data:', err);
                }
            });
        }

        function searchCalendar() {
            const month = parseInt(monthSelect.value);
            const year = new Date().getFullYear(); // Assuming current year
            const cutoff = cutoffSelect.value;

            fetchAttendanceData(month, year, cutoff, function (data) {
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                let startDay = 1,
                    endDay = daysInMonth;

                if (cutoff === 'first') {
                    endDay = 15;
                } else if (cutoff === 'second') {
                    startDay = 16;
                }

                renderCalendar(month, year, startDay, endDay, data);
                // updateMonthDisplay(month, year);
            });
        }

        searchButton.addEventListener('click', searchCalendar);

        // Initial search when the page loads
        const currentDate = new Date();
        monthSelect.value = currentDate.getMonth();
        cutoffSelect.value = currentDate.getDate() <= 15 ? 'first' : 'second';
        searchCalendar();
    });

</script>


@endsection
