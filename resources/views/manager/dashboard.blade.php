@extends('layouts.managermaster') @section('title', 'One JAF')
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
                        <form action="{{ url('manager/dashboard') }}" method="POST">
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
                                    <form action="{{ url('manager/dashboard/') }}" method="POST">
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
                                <form action="{{ url('manager/dashboard/breakin') }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="stats-box">
                                        <button class="breakOut">
                                            Break Out
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 col-6 text-center">
                                <form action="{{ url('manager/dashboard/breakout') }}" method="POST">
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
                            <option value="0">December - January 1st Cut-off</option>
                            <option value="1">January 2nd Cut-off</option>
                            <option value="2">January - February 1st Cut-off</option>
                            <option value="3">February 2nd Cut-off</option>
                            <option value="4">February - March 1st Cut-off</option>
                            <option value="5">March 2nd Cut-off</option>
                            <option value="6">March - April 1st Cut-off</option>
                            <option value="7">April 2nd Cut-off</option>
                            <option value="8">April - May 1st Cut-off</option>
                            <option value="9">May 2nd Cut-off</option>
                            <option value="10">May - June 1st Cut-off</option>
                            <option value="11">June 2nd Cut-off</option>
                            <option value="12">June - July 1st Cut-off</option>
                            <option value="13">July 2nd Cut-off</option>
                            <option value="14">July - August 1st Cut-off</option>
                            <option value="15">August 2nd Cut-off</option>
                            <option value="16">August - September 1st Cut-off</option>
                            <option value="17">September 2nd Cut-off</option>
                            <option value="18">September - October 1st Cut-off</option>
                            <option value="19">October 2nd Cut-off</option>
                            <option value="20">October - November 1st Cut-off</option>
                            <option value="21">November 2nd Cut-off</option>
                            <option value="22">November - December 1st Cut-off</option>
                            <option value="23">December 2nd Cut-off</option>
                        </select>
                        <label class="focus-label">Cut-off Period</label>
                    </div>
                    <div class="form-group form-focus select-focus">
                        <select name="" id="yearSelect" class="select floating">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                        </select>
                        <label class="focus-label">Year</label>
                    </div>
                    <button id="searchButton" class="btn btn-danger">Search</button>
                    <button id="saveButton" class="btn btn-outline-danger">Send</button>
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
                        <li class="nav-item"><a class="nav-link" href="#top-tab2" data-toggle="tab">Salary Record</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="top-tab1">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Employment Record</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-nowrap mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Hired Date</th>
                                                        <th>Job Title</th>
                                                        <th>Department</th>
                                                        <th>Location</th>
                                                    </tr>
                                                </thead>
                                                @foreach ($record as $rec)
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $rec->name }}</td>
                                                        <td>{{ $rec->hiredDate }}</td>
                                                        <td>{{ $rec->jobTitle }}</td>
                                                        <td>{{ $rec->department }}</td>
                                                        <td>{{ $rec->location }}</td>
                                                    </tr>
                                                </tbody>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="top-tab2">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Salary Record</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-nowrap mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Annual Salary</th>
                                                        <th>Salary Frequency Monthly</th>
                                                        <th>Salary Rate</th>
                                                        <th>Currency</th>
                                                        <th>Proposal Reason</th>
                                                    </tr>
                                                </thead>
                                                @foreach ($salrecord as $sal)
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $sal->name }}</td>
                                                        <td>{{ $sal->annSalary }}</td>
                                                        <td>{{ $sal->salFreqMonthly }}</td>
                                                        <td>{{ $sal->salRate }}</td>
                                                        <td>{{ $sal->currency }}</td>
                                                        <td>{{ $sal->proposalReason }}</td>
                                                    </tr>
                                                </tbody>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
        const searchButton = document.getElementById('searchButton');
        const saveButton = document.getElementById('saveButton');
        const yearSelect = document.getElementById('yearSelect');
        const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        function renderCalendar(startDate, endDate, attendanceData, leaveData, status = 'new') {
            calendar.innerHTML = '';

            let totalWorkedSeconds = 0;
            let totalLateSeconds = 0;
            let unpaidLeaveCount = 0;
            let vacationLeaveCount = 0;
            let sickLeaveCount = 0;
            let bdayLeaveCount = 0;

            let currentDate = new Date(startDate);
            while (currentDate <= endDate) {
                const dayDiv = document.createElement('div');
                dayDiv.className = 'day-att';
                dayDiv.style.padding = '5px';

                const dateHeader = document.createElement('div');
                dateHeader.className = 'day-header-att';
                dateHeader.innerText =
                    `${monthNames[currentDate.getMonth()]} ${currentDate.getDate()}, ${currentDate.getFullYear()}, ${daysOfWeek[currentDate.getDay()]}`;
                dayDiv.appendChild(dateHeader);

                const dayData = attendanceData.find(item => {
                    const itemDate = new Date(item.date);
                    return itemDate.getDate() === currentDate.getDate() &&
                        itemDate.getMonth() === currentDate.getMonth() &&
                        itemDate.getFullYear() === currentDate.getFullYear();
                });
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

                // Check for leave data on this day and status is Approved
                const leaveOnDay = leaveData.filter(leave => {
                    const leaveStart = new Date(leave.start_date);
                    const leaveEnd = new Date(leave.end_date);
                    return leave.status === 'Approved' && currentDate >= leaveStart && currentDate <=
                        leaveEnd;
                });

                leaveOnDay.forEach(leave => {
                    const leaveTypeDiv = document.createElement('div');
                    leaveTypeDiv.className =
                        'calendar-text leave-type-button';
                    leaveTypeDiv.innerText = "Leave Type: " + leave.type;
                    dayDiv.appendChild(leaveTypeDiv);

                    if (leave.type === 'Unpaid Leave') {
                        unpaidLeaveCount++;
                    } else if (leave.type === 'Vacation Leave') {
                        vacationLeaveCount++;
                    } else if (leave.type === 'Sick Leave') {
                        sickLeaveCount++;
                    } else if (leave.type === 'Birthday Leave') {
                        bdayLeaveCount++;
                    }
                });

                calendar.appendChild(dayDiv);
                currentDate.setDate(currentDate.getDate() + 1);
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
                `Total Worked Hours: ${totalWorkedFormatted}\nTotal Late: ${totalLateFormatted}\nUnpaid Leave: ${unpaidLeaveCount}\nVacation Leave: ${vacationLeaveCount}\nSick Leave: ${sickLeaveCount}\nBirthday Leave: ${bdayLeaveCount}\nStatus: ${status}`;
            calendar.appendChild(totalBox);
        }

        function fetchAttendanceData(startDate, endDate, callback) {
            $.ajax({
                url: "{{ route('attendance.getm') }}",
                method: 'GET',
                dataType: 'json',
                data: {
                    start_date: startDate.toISOString().split('T')[0],
                    end_date: endDate.toISOString().split('T')[0]
                },
                success: function (data) {
                    callback(data.attendance, data.leaves);
                },
                error: function (err) {
                    console.error('Error fetching attendance data:', err);
                    // Handle the error on the front-end, e.g., display an alert or message to the user
                }
            });
        }

        function fetchStatus(cutoff, callback) {
            $.ajax({
                url: "{{ route('attendance.statusm') }}", // Ensure this route matches your Laravel route for fetching status
                method: 'GET',
                dataType: 'json',
                data: {
                    cutoff: cutoff
                },
                success: function (data) {
                    callback(data.status);
                },
                error: function (err) {
                    console.error('Error fetching status:', err);
                    callback('new'); // Default to 'new' if there's an error
                    // Handle the error on the front-end, e.g., display an alert or message to the user
                }
            });
        }

        function getCutoffDates(monthIndex, year) {
            let startDate, endDate;

            switch (monthIndex) {
                case 0:
                    startDate = new Date(Date.UTC(year - 1, 11, 26));
                    endDate = new Date(Date.UTC(year, 0, 10));
                    break;
                case 1:
                    startDate = new Date(Date.UTC(year, 0, 11));
                    endDate = new Date(Date.UTC(year, 0, 25));
                    break;
                case 2:
                    startDate = new Date(Date.UTC(year, 0, 26));
                    endDate = new Date(Date.UTC(year, 1, 10));
                    break;
                case 3:
                    startDate = new Date(Date.UTC(year, 1, 11));
                    endDate = new Date(Date.UTC(year, 1, 25));
                    break;
                case 4:
                    startDate = new Date(Date.UTC(year, 1, 26));
                    endDate = new Date(Date.UTC(year, 2, 10));
                    break;
                case 5:
                    startDate = new Date(Date.UTC(year, 2, 11));
                    endDate = new Date(Date.UTC(year, 2, 25));
                    break;
                case 6:
                    startDate = new Date(Date.UTC(year, 2, 26));
                    endDate = new Date(Date.UTC(year, 3, 10));
                    break;
                case 7:
                    startDate = new Date(Date.UTC(year, 3, 11));
                    endDate = new Date(Date.UTC(year, 3, 25));
                    break;
                case 8:
                    startDate = new Date(Date.UTC(year, 3, 26));
                    endDate = new Date(Date.UTC(year, 4, 10));
                    break;
                case 9:
                    startDate = new Date(Date.UTC(year, 4, 11));
                    endDate = new Date(Date.UTC(year, 4, 25));
                    break;
                case 10:
                    startDate = new Date(Date.UTC(year, 4, 26));
                    endDate = new Date(Date.UTC(year, 5, 10));
                    break;
                case 11:
                    startDate = new Date(Date.UTC(year, 5, 11));
                    endDate = new Date(Date.UTC(year, 5, 25));
                    break;
                case 12:
                    startDate = new Date(Date.UTC(year, 5, 26));
                    endDate = new Date(Date.UTC(year, 6, 10));
                    break;
                case 13:
                    startDate = new Date(Date.UTC(year, 6, 11));
                    endDate = new Date(Date.UTC(year, 6, 25));
                    break;
                case 14:
                    startDate = new Date(Date.UTC(year, 6, 26));
                    endDate = new Date(Date.UTC(year, 7, 10));
                    break;
                case 15:
                    startDate = new Date(Date.UTC(year, 7, 11));
                    endDate = new Date(Date.UTC(year, 7, 25));
                    break;
                case 16:
                    startDate = new Date(Date.UTC(year, 7, 26));
                    endDate = new Date(Date.UTC(year, 8, 10));
                    break;
                case 17:
                    startDate = new Date(Date.UTC(year, 8, 11));
                    endDate = new Date(Date.UTC(year, 8, 25));
                    break;
                case 18:
                    startDate = new Date(Date.UTC(year, 8, 26));
                    endDate = new Date(Date.UTC(year, 9, 10));
                    break;
                case 19:
                    startDate = new Date(Date.UTC(year, 9, 11));
                    endDate = new Date(Date.UTC(year, 9, 25));
                    break;
                case 20:
                    startDate = new Date(Date.UTC(year, 9, 26));
                    endDate = new Date(Date.UTC(year, 10, 10));
                    break;
                case 21:
                    startDate = new Date(Date.UTC(year, 10, 11));
                    endDate = new Date(Date.UTC(year, 10, 25));
                    break;
                case 22:
                    startDate = new Date(Date.UTC(year, 10, 26));
                    endDate = new Date(Date.UTC(year, 11, 10));
                    break;
                case 23:
                    startDate = new Date(Date.UTC(year, 11, 11));
                    endDate = new Date(Date.UTC(year, 11, 25));
                    break;
            }

            return {
                startDate,
                endDate
            };
        }

        function searchCalendar() {
            const monthIndex = parseInt(monthSelect.value);
            const year = parseInt(yearSelect.value);

            const {
                startDate,
                endDate
            } = getCutoffDates(monthIndex, year);
            const selectedOptionText = monthSelect.options[monthSelect.selectedIndex].text;
            const cutoff = `${selectedOptionText} ${year}`;

            fetchStatus(cutoff, function (status) {
                fetchAttendanceData(startDate, endDate, function (attendanceData, leaveData) {
                    renderCalendar(startDate, endDate, attendanceData, leaveData, status);
                });
            });
        }

        function saveAttendance() {
            const monthSelect = document.getElementById('monthSelect');
            const yearSelect = document.getElementById('yearSelect');
            const selectedOptionText = monthSelect.options[monthSelect.selectedIndex].text;
            const year = parseInt(yearSelect.value);
            const cutoff = `${selectedOptionText} ${year}`;

            const {
                startDate,
                endDate
            } = getCutoffDates(monthSelect.selectedIndex, year);

            const totalBox = document.querySelector('.total-box');
            const totalWorked = totalBox.innerText.match(/Total Worked Hours: (\d{2}:\d{2}:\d{2})/)[1];
            const totalLate = totalBox.innerText.match(/Total Late: (\d{2}:\d{2}:\d{2})/)[1];
            const unpaidLeaveCount = totalBox.innerText.match(/Unpaid Leave: (\d+)/)[1];
            const vacationLeaveCount = totalBox.innerText.match(/Vacation Leave: (\d+)/)[1];
            const sickLeaveCount = totalBox.innerText.match(/Sick Leave: (\d+)/)[1];
            const bdayLeaveCount = totalBox.innerText.match(/Birthday Leave: (\d+)/)[1];

            const data = {
                total_worked: totalWorked,
                total_late: totalLate,
                cutoff: cutoff,
                start_date: startDate.toISOString().split('T')[0],
                end_date: endDate.toISOString().split('T')[0],
                unpaid_leave: unpaidLeaveCount,
                vacation_leave: vacationLeaveCount,
                sick_leave: sickLeaveCount,
                birthday_leave: bdayLeaveCount,
                status: 'pending'
            };

            console.log('Data to be saved:', data);

            // Check if the attendance record already exists
            $.ajax({
                url: "{{ route('attendance.checkm') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data,
                success: function (response) {
                    console.log('Check response:', response);
                    if (response.exists) {
                        if (response.status === 'pending') {
                            alert(
                                'The attendance sheet is already recorded and is waiting for approval.'
                            );
                            updateStatus('pending');
                        } else if (response.status === 'approved' || response.status ===
                            'rejected') {
                            alert(`The attendance sheet has already been ${response.status}.`);
                            updateStatus(response.status);
                        } else {
                            alert('Attendance record already exists for this period.');
                        }
                    } else {
                        // Ask for confirmation before saving
                        if (confirm('Do you wish to send this cut-off?')) {
                            // Save the attendance record
                            $.ajax({
                                url: "{{ route('attendance.savem') }}",
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                },
                                data: data,
                                success: function (response) {
                                    console.log('Attendance saved successfully:',
                                        response);
                                    alert('Attendance saved successfully.');
                                    updateStatus('pending');
                                },
                                error: function (err) {
                                    console.error('Error saving attendance:', err);
                                    alert('Error saving attendance.');
                                }
                            });
                        }
                    }
                },
                error: function (err) {
                    console.error('Error checking attendance:', err);
                    alert('Error checking attendance.');
                }
            });
        }

        function updateStatus(status) {
            const totalBox = document.querySelector('.total-box');
            if (totalBox) {
                const newText = totalBox.innerText.replace(/Status:.*/, `Status: ${status}`);
                totalBox.innerText = newText;
                console.log('Status updated to:', status);
            }
        }

        searchButton.addEventListener('click', searchCalendar);
        saveButton.addEventListener('click', saveAttendance);

        // Initial search when the page loads
        const currentDate = new Date();
        const currentDay = currentDate.getDate();
        let initialMonthIndex;

        if (currentDay <= 10) {
            initialMonthIndex = currentDate.getMonth() * 2;
        } else if (currentDay <= 25) {
            initialMonthIndex = currentDate.getMonth() * 2 + 1;
        } else {
            initialMonthIndex = currentDate.getMonth() * 2 + 2;
        }

        monthSelect.value = initialMonthIndex.toString();
        yearSelect.value = currentDate.getFullYear().toString();

        searchCalendar();
    });

</script>

@endsection
