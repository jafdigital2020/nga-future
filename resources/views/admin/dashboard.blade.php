@extends('layouts.master') @section('title', 'One JAF')
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


    <!-- Card stats -->
    <div class="row g-6 mb-6">
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <span class="h6 font-semibold text-muted text-sm d-block mb-2">Client</span>
                            <span class="h3 font-bold mb-0">100</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-tertiary text-white text-lg rounded-circle">
                                <i class="bi bi-credit-card"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <span class="h6 font-semibold text-muted text-sm d-block mb-2">Employees</span>
                            <span class="h3 font-bold mb-0">{{ $totalUsers }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-primary text-white text-lg rounded-circle">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <span class="h6 font-semibold text-muted text-sm d-block mb-2">Attendance</span>
                            <span class="h3 font-bold mb-0">{{ $todayLoginCount }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-info text-white text-lg rounded-circle">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <span class="h6 font-semibold text-muted text-sm d-block mb-2">Leave Today</span>
                            <span
                                class="h3 font-bold mb-0">{{ $vacationLeaveCountToday + $sickLeaveCountToday + $birthdayLeaveCountToday + $unpaidLeaveCountToday }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-warning text-white text-lg rounded-circle">
                                <i class="bi bi-minecart-loaded"></i>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="mt-2 mb-0 text-sm">
                            <span class="badge badge-pill bg-soft-success text-success me-2">
                                <i class="bi bi-arrow-up me-1"></i>10%
                            </span>
                            <span class="text-nowrap text-xs text-muted">Since last month</span>
                        </div> -->
                </div>
            </div>
        </div>
    </div>

    <!-- Card Stats 2 -->


    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6 text-center">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Total Revenue</h3>
                            <div id="bar-charts"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Sales Overview</h3>
                            <div id="line-charts"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-group m-b-30">
                <div class="card">
                    @php
                    // Ensure the percentage is between 0 and 100
                    $progressBarWidth = min(max($percentageIncrease, 0), 100);
                    $formattedPercentage = number_format($percentageIncrease, 2);
                    @endphp
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <span class="d-block">New Employees</span>
                            </div>
                            <div>
                                <span class="text-success">
                                    {{ $percentageIncrease > 0 ? '+' : '' }}{{ $formattedPercentage }}%
                                </span>
                            </div>
                        </div>
                        <h3 class="mb-3">{{ $newUsersThisMonth }}</h3>
                        <div class="progress mb-2" style="height: 5px;">
                            <div class="progress-bar bg-primary" role="progressbar"
                                style="width: {{ $progressBarWidth }}%;" aria-valuenow="{{ $percentageIncrease }}"
                                aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <p class="mb-0">Overall Employees: {{ $totalUsers }}</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <span class="d-block">Earnings</span>
                            </div>
                            <div>
                                <span class="text-success">+12.5%</span>
                            </div>
                        </div>
                        <h3 class="mb-3">₱1,042,300</h3>
                        <div class="progress mb-2" style="height: 5px">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 70%"
                                aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mb-0">
                            Previous Month <span class="text-muted">₱1,015,852</span>
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <span class="d-block">Expenses</span>
                            </div>
                            <div>
                                <span class="text-danger">-2.8%</span>
                            </div>
                        </div>
                        <h3 class="mb-3">₱8,500</h3>
                        <div class="progress mb-2" style="height: 5px">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 70%"
                                aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mb-0">
                            Previous Month <span class="text-muted">₱7,500</span>
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <span class="d-block">Profit</span>
                            </div>
                            <div>
                                <span class="text-danger">-75%</span>
                            </div>
                        </div>
                        <h3 class="mb-3">₱1,012,000</h3>
                        <div class="progress mb-2" style="height: 5px">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 70%"
                                aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mb-0">
                            Previous Month <span class="text-muted">₱1,042,000</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- /Card Stats 2 -->

    <!-- Invoice and Client -->

    <div class="row">
        <div class="col-md-6 d-flex">
            <div class="card card-table flex-fill">
                <div class="card-header">
                    <h3 class="card-title mb-0">Invoices</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Client</th>
                                    <th>Due Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="invoice-view.html">#INV-0001</a></td>
                                    <td>
                                        <h2><a href="#">Lofa Consulting</a></h2>
                                    </td>
                                    <td>11 Mar 2024</td>
                                    <td>₱50,000</td>
                                    <td>
                                        <span class="badge bg-inverse-warning">Partially Paid</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="invoice-view.html">#INV-0002</a></td>
                                    <td>
                                        <h2><a href="#">The Playground</a></h2>
                                    </td>
                                    <td>8 Feb 2024</td>
                                    <td>₱100,000</td>
                                    <td>
                                        <span class="badge bg-inverse-success">Paid</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="invoice-view.html">#INV-0003</a></td>
                                    <td>
                                        <h2><a href="#">Flame 400 Burger Cafe</a></h2>
                                    </td>
                                    <td>23 Jan 2024</td>
                                    <td>₱70,000</td>
                                    <td>
                                        <span class="badge bg-inverse-danger">Unpaid</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="invoices.html">View all invoices</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex">
            <div class="card card-table flex-fill">
                <div class="card-header">
                    <h3 class="card-title mb-0">Payments</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Client</th>
                                    <th>Payment Type</th>
                                    <th>Paid Date</th>
                                    <th>Paid Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="invoice-view.html">#INV-0001</a></td>
                                    <td>
                                        <h2><a href="#">Lofa Consulting</a></h2>
                                    </td>
                                    <td>Paypal</td>
                                    <td>11 Mar 2024</td>
                                    <td>₱50,000</td>
                                </tr>
                                <tr>
                                    <td><a href="invoice-view.html">#INV-0002</a></td>
                                    <td>
                                        <h2><a href="#">The Playground</a></h2>
                                    </td>
                                    <td>Paypal</td>
                                    <td>8 Feb 2024</td>
                                    <td>₱100,000</td>
                                </tr>
                                <tr>
                                    <td><a href="invoice-view.html">#INV-0003</a></td>
                                    <td>
                                        <h2><a href="#">Flame 400 Burger Cafe</a></h2>
                                    </td>
                                    <td>Paypal</td>
                                    <td>23 Jan 2024</td>
                                    <td>₱70,000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="payments.html">View all payments</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 d-flex">
            <div class="card card-table flex-fill">
                <div class="card-header">
                    <h3 class="card-title mb-0">Clients</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="clients.html">View all clients</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex">
            <div class="card card-table flex-fill">
                <div class="card-header">
                    <h3 class="card-title mb-0">Recent Projects</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>Project Name</th>
                                    <th>Progress</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="projects.html">View all projects</a>
                </div>
            </div>
        </div>
    </div>

    <!-- /Invoice and Client -->

</div>
<!-- /Page Content -->

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
    $(document).ready(function () {
        $('#attendance_info').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var employee = button.data('employee');
            var date = button.data('date');
            var timeIn = button.data('timein');
            var timeOut = button.data('timeout');
            var breakIn = button.data('breakin');
            var breakEnd = button.data('breakend');
            var breakOut = button.data('breakout');
            var timeTotal = button.data('timetotal');
            // Update the modal's content
            var modal = $(this);
            modal.find('.modal-title').text('Attendance Info for ' + employee);
            modal.find('.card-title small').text(date);
            modal.find('.punch-det:first p').text(timeIn);
            modal.find('.punch-det:last p').text(timeOut);
            modal.find('.statistics .stats-box:first h6').text(breakEnd);
            modal.find('#time-total').text(timeTotal);

            // Populate activities list
            var activities = `
                <li>
                    <p class="mb-0">Punch In at</p>
                    <p class="res-activity-time">
                        <i class="fa fa-clock-o"></i>
                        ${timeIn}
                    </p>
                </li>
                
                <li>
                    <p class="mb-0">Break In at</p>
                    <p class="res-activity-time">
                        <i class="fa fa-clock-o"></i>
                        ${breakIn}
                    </p>
                </li>
                <li>
                    <p class="mb-0">Break Out at</p>
                    <p class="res-activity-time">
                        <i class="fa fa-clock-o"></i>
                        ${breakOut}
                    </p>
                </li>
                <li>
                    <p class="mb-0">Punch Out at</p>
                    <p class="res-activity-time">
                        <i class="fa fa-clock-o"></i>
                        ${timeOut}
                    </p>
                </li>
            `;
            modal.find('.res-activity-list').html(activities);
        });
    });

</script>


@endsection
