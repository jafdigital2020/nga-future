@extends('layouts.managermaster') @section('title', 'Overtime')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')

    <!-- Page Content -->
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Overtime</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('manager/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Overtime</li>
                    </ul>
                </div>

            </div>
        </div>
        <!-- /Page Header -->

        <!-- Overtime Statistics -->
        <div class="row">
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="stats-info">
                    <h6>Overtime Employee</h6>
                    <h4>{{ $otEmpCount }} <span>this month</span></h4>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="stats-info">
                    <h6>Overtime Hours</h6>
                    <h4>{{ $otHoursCount }} <span>this month</span></h4>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="stats-info">
                    <h6>Pending Request</h6>
                    <h4>{{ $otPendingCount }}</h4>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                <div class="stats-info">
                    <h6>Rejected</h6>
                    <h4>{{ $otRejectCount }}</h4>
                </div>
            </div>
        </div>
        <!-- /Overtime Statistics -->

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped custom-table mb-0 datatable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Total Hours</th>
                                <th>Reason</th>
                                <th>Date Requested</th>
                                <th>Status</th>
                                <th>Approved By</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalHoursInSeconds = 0; // Initialize total hours variable in seconds
                            @endphp
                            @foreach ($overtime as $ot)
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="#" class="avatar">
                                                @if ($ot->user->image)
                                                    <img src="{{ asset('images/' . $ot->user->image) }}"
                                                        alt="Profile Image" />
                                                @else
                                                    <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                                @endif
                                            </a>
                                            <a href="#">{{ $ot->user->fName }}
                                                {{ $ot->user->lName }}
                                                <span>{{ $ot->user->position }}</span></a>
                                        </h2>
                                    </td>
                                    <td>{{ $ot->date }}</td>
                                    <td>
                                        @if ($ot->start_time)
                                            {{ \Carbon\Carbon::parse($ot->start_time)->format('h:i:s A') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if ($ot->end_time)
                                            {{ \Carbon\Carbon::parse($ot->end_time)->format('h:i:s A') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $ot->total_hours }}</td>
                                    <td>{{ $ot->reason }}</td>
                                    <td>{{ $ot->created_at->format('Y-m-d') }}</td>
                                    <td class="text-center">
                                        <div class="dropdown action-label">
                                            <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                                data-toggle="dropdown" aria-expanded="false">
                                                @if ($ot->status == 'New')
                                                    <i class="fa fa-dot-circle-o text-purple"></i> New
                                                @elseif($ot->status == 'Pending')
                                                    <i class="fa fa-dot-circle-o text-info"></i> Pending
                                                @elseif($ot->status == 'Pre-Approved')
                                                    <i class="fa fa-dot-circle-o text-warning"></i> Pre-Approved
                                                @elseif($ot->status == 'Rejected')
                                                    <i class="fa fa-dot-circle-o text-danger"></i> Rejected
                                                @else
                                                    <i class="fa fa-dot-circle-o"></i> Unknown
                                                @endif
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#"><i
                                                        class="fa fa-dot-circle-o text-info"></i>
                                                    Pending</a>

                                                <form id="approve-form-{{ $ot->id }}"
                                                    action="{{ route('ot.approvemanager', $ot->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    <button type="button" class="dropdown-item approve-button"
                                                        data-ot-id="{{ $ot->id }}">
                                                        <i class="fa fa-dot-circle-o text-warning"></i> Pre-Approved
                                                    </button>
                                                </form>
                                                <form id="decline-form-{{ $ot->id }}"
                                                    action="{{ route('ot.rejectmanager', $ot->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    <button type="button" class="dropdown-item decline-button"
                                                        data-ot-id="{{ $ot->id }}">
                                                        <i class="fa fa-dot-circle-o text-danger"></i> Rejected
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="#" class="avatar avatar-xs">
                                                @if ($ot->otapprover)
                                                    @if ($ot->otapprover->image)
                                                        <img src="{{ asset('images/' . $ot->otapprover->image) }}"
                                                            alt="Profile Image" />
                                                    @else
                                                        <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                                    @endif
                                                @else
                                                    <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                                @endif
                                            </a>
                                            @if ($ot->otapprover)
                                                {{ $ot->otapprover->fName }} {{ $ot->otapprover->lName }}
                                            @else
                                                N/A
                                            @endif

                                        </h2>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                                aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item edit-ot" href="#"
                                                    data-id="{{ $ot->id }}" data-date="{{ $ot->date }}"
                                                    data-start_time="{{ $ot->start_time }}"
                                                    data-end_time="{{ $ot->end_time }}" data-status="{{ $ot->status }}"
                                                    data-total_hours="{{ $ot->total_hours }}"
                                                    data-reason="{{ $ot->reason }}">
                                                    <i class="fa fa-pencil m-r-5"></i> Edit</a>
                                                <a class="dropdown-item delete-ot" href="#"
                                                    data-id="{{ $ot->id }}">
                                                    <i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @php
                                    // Convert total_hours from "H:i:s" to seconds
                                    $timeParts = explode(':', $ot->total_hours);
                                    $hours = isset($timeParts[0]) ? (int) $timeParts[0] : 0;
                                    $minutes = isset($timeParts[1]) ? (int) $timeParts[1] : 0;
                                    $seconds = isset($timeParts[2]) ? (int) $timeParts[2] : 0;

                                    // Convert total time to seconds
                                    $totalHoursInSeconds += $hours * 3600 + $minutes * 60 + $seconds;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Total Hours:</strong></td>
                                <td>
                                    @php
                                        // Convert total seconds back to "H:i:s"
                                        $totalHours = gmdate('H:i:s', $totalHoursInSeconds);
                                    @endphp
                                    {{ $totalHours }}
                                </td>
                                <td colspan="5"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <!-- /Page Content -->



    @endsection

    @section('scripts')

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var approveButtons = document.querySelectorAll('.approve-button');
                var declineButtons = document.querySelectorAll('.decline-button');

                approveButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        var otId = button.getAttribute('data-ot-id');
                        confirmApproval(otId);
                    });
                });

                declineButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        var otId = button.getAttribute('data-ot-id');
                        confirmDecline(otId);
                    });
                });
            });

            function confirmApproval(otId) {
                var form = document.getElementById('approve-form-' + otId);
                var confirmAction = confirm("Are you sure you want to approve this OT request?");
                if (confirmAction) {
                    form.submit();
                }
            }

            function confirmDecline(otId) {
                var form = document.getElementById('decline-form-' + otId);
                var confirmAction = confirm("Are you sure you want to decline this OT request?");
                if (confirmAction) {
                    form.submit();
                }
            }
        </script>


    @endsection
