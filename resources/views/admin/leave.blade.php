@extends('layouts.master') @section('title', 'Leave')
<style>
    /* Loader Styles */
    .loader {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.5);
        /* Transparent dark background */
        z-index: 9999;
        /* Ensure it's on top of other content */
    }

    .loading-bar-background {
        --height: 30px;
        display: flex;
        align-items: center;
        box-sizing: border-box;
        padding: 5px;
        width: 200px;
        height: var(--height);
        background-color: transparent;
        /* Make background transparent */
        box-shadow: #0c0c0c -2px 2px 4px 0px inset;
        border-radius: calc(var(--height) / 2);
    }

    .loading-bar {
        position: relative;
        display: flex;
        justify-content: center;
        flex-direction: column;
        --height: 20px;
        width: 0%;
        height: var(--height);
        overflow: hidden;
        background: rgb(206, 46, 46);
        background: linear-gradient(0deg,
                rgba(206, 46, 46, 1) 0%,
                rgba(249, 199, 79, 1) 100%);
        border-radius: calc(var(--height) / 2);
        animation: loading 4s ease-out infinite;
    }

    .white-bars-container {
        position: absolute;
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .white-bar {
        background: rgb(255, 255, 255);
        background: linear-gradient(-45deg,
                rgba(255, 255, 255, 1) 0%,
                rgba(255, 255, 255, 0) 70%);
        width: 10px;
        height: 45px;
        opacity: 0.3;
        rotate: 45deg;
    }

    @keyframes loading {
        0% {
            width: 0;
        }

        80% {
            width: 100%;
        }

        100% {
            width: 100%;
        }
    }

    .loading-text {
        color: white;
        font-size: 14pt;
        font-weight: 600;
    }

</style>
@section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Leaves</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Leaves</li>
                </ul>
            </div>
            <!-- <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_leave"><i class="fa fa-plus"></i>
                    Request Leave</a>
            </div> -->
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Leave Statistics -->
    <div class="row">
        <div class="col-md-3">
            <div class="stats-info">
                <h6>Today Presents</h6>
                <h4>{{ $todayLoginCount }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-info">
                <h6>Paid Leaves</h6>
                <h4>{{ $leaveTypesPaidCountToday }} <span>Today</span>
                </h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-info">
                <h6>Unpaid Leaves</h6>
                <h4>{{ $leaveTypesUnpaidCountToday }} <span>Today</span></h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-info">
                <h6>Pending Requests</h6>
                <h4>{{ $pendingCount }}</h4>
            </div>
        </div>
    </div>
    <!-- /Leave Statistics -->

    <!-- Search Filter -->
    <form action="{{ route('leave.searchadmin') }}" method="GET">
        <div class="row filter-row">
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus">
                    <input type="text" class="form-control floating" name="name">
                    <label class="focus-label">Employee Name</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="department">
                        <option value="" disabled selected>Select Department</option>
                        @foreach ($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                    <label class="focus-label">Department</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="status">
                        <option value=""> -- Select -- </option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved
                        </option>
                        <option value="Declined" {{ request('status') == 'Declined' ? 'selected' : '' }}>Declined
                        </option>
                    </select>
                    <label class="focus-label">Leave Status</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus">
                    <div class="cal-icon">
                        <input class="form-control floating datetimepicker" type="text" name="start_date">
                    </div>
                    <label class="focus-label">From</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus">
                    <div class="cal-icon">
                        <input class="form-control floating datetimepicker" type="text" name="end_date">
                    </div>
                    <label class="focus-label">To</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <button type="submit" class="btn btn-danger btn-block">Search</button>
            </div>
        </div>
    </form>
    <!-- /Search Filter -->


    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-hover table-nowrap custom-table mb-0 datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Credits</th>
                            <th>From</th>
                            <th>To</th>
                            <th>No of Days</th>
                            <th>Date Requested</th>
                            <th>Attached File</th>
                            <th>Approved By</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaveRequests as $leave)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="{{ url('admin/employee/edit/'.$leave->user->id) }}" class="avatar">
                                        @if ($leave->user->image)
                                        <img src="{{ asset('images/' . $leave->user->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{ asset('images/default.png') }}" alt="Profile Image" /></a>
                                    @endif
                                    <a href="{{ url('admin/employee/edit/'.$leave->user->id) }}">
                                        @if ($leave->user->fName || $leave->user->lName)
                                        {{ $leave->user->fName }} {{ $leave->user->lName }}
                                        @else
                                        {{ $leave->user->name }}
                                        @endif
                                        <span>{{ $leave->user->department }}</span>
                                    </a>

                                </h2>
                            </td>
                            <td>{{ $leave->leaveType->leaveType }}</td>
                            <td>
                                {{ $leave->user->leaveCredits()->where('leave_type_id', $leave->leaveType->id)->first()->remaining_credits ?? 0 }}
                            </td>
                            <td>{{ $leave->start_date }}</td>
                            <td>{{ $leave->end_date }}</td>
                            <td>{{ $leave->days }}</td>
                            <td>{{ $leave->created_at->format('Y-m-d') }}</td>
                            <td>
                                @if ($leave->attached_file)
                                <a href="{{ asset('storage/' . $leave->attached_file) }}" target="_blank">
                                    View Attached File
                                </a>
                                @else
                                No document uploaded
                                @endif
                            </td>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="#" class="avatar avatar-xs">
                                        @if ($leave->approver)
                                        @if ($leave->approver->image)
                                        <img src="{{ asset('images/' . $leave->approver->image) }}"
                                            alt="Profile Image" />
                                        @else
                                        <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                        @endif
                                        @else
                                        <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                        @endif
                                    </a>
                                    {{ $leave->approver 
                                        ? ($leave->approver->fName || $leave->approver->lName 
                                            ? $leave->approver->fName . ' ' . $leave->approver->lName 
                                            : $leave->approver->name) 
                                        : 'Not Approved Yet' 
                                    }}

                                </h2>
                            </td>
                            <td class="text-center">
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        @if($leave->status == 'New')
                                        <i class="fa fa-dot-circle-o text-purple"></i> New
                                        @elseif($leave->status == 'Pending')
                                        <i class="fa fa-dot-circle-o text-info"></i> Pending
                                        @elseif($leave->status == 'Approved')
                                        <i class="fa fa-dot-circle-o text-success"></i> Approved
                                        @elseif($leave->status == 'Declined')
                                        <i class="fa fa-dot-circle-o text-danger"></i> Declined
                                        @else
                                        <i class="fa fa-dot-circle-o"></i> Unknown
                                        @endif
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-info"></i>
                                            Pending</a>

                                        <form id="approve-form-{{ $leave->id }}"
                                            action="{{ route('leave.approveadmin', $leave->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item approve-button"
                                                data-leave-id="{{ $leave->id }}">
                                                <i class="fa fa-dot-circle-o text-success"></i> Approved
                                            </button>
                                        </form>



                                        <form id="decline-form-{{ $leave->id }}"
                                            action="{{ route('leave.declineadmin', $leave->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item decline-button"
                                                data-leave-id="{{ $leave->id }}">
                                                <i class="fa fa-dot-circle-o text-danger"></i> Declined
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item view-leave" href="#" data-id="{{ $leave->id }}"
                                            data-type="{{ $leave->type }}" data-start_date="{{ $leave->start_date }}"
                                            data-end_date="{{ $leave->end_date }}" data-days="{{ $leave->days }}"
                                            data-reason="{{ $leave->reason }}" data-status="{{ $leave->status }}">
                                            <i class="fa fa-eye m-r-5"></i> View</a>
                                        <a class="dropdown-item edit-leave" href="#" data-id="{{ $leave->id }}"
                                            data-type_id="{{ $leave->leaveType->id }}"
                                            data-start_date="{{ $leave->start_date }}"
                                            data-end_date="{{ $leave->end_date }}" data-days="{{ $leave->days }}"
                                            data-reason="{{ $leave->reason }}" data-status="{{ $leave->status }}">
                                            <i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item delete-leave" href="#" data-id="{{ $leave->id }}">
                                            <i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->

<!-- Loader for all leave requests -->
<div id="global-loader" class="loader" style="display: none;">
    <div class="loading-bar-background">
        <div class="loading-bar">
            <div class="white-bars-container">
                <div class="white-bar"></div>
                <div class="white-bar"></div>
                <div class="white-bar"></div>
            </div>
        </div>
    </div>
    <span class="loading-text">Processing...</span>
</div>


<!-- Edit Leave Modal -->
<div id="edit_leave" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Leave</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editLeaveForm" method="POST">
                    @csrf
                    <input type="hidden" name="leave_id" id="leave_id">
                    <div class="form-group">
                        <label>Leave Type <span class="text-danger">*</span></label>
                        <select class="form-control" name="typee" id="typee" required>
                            <option value="">-- Select Leave Type --</option>
                            @if($leaveCredits->isNotEmpty())
                            @foreach($leaveCredits as $leaveCredit)
                            <option value="{{ $leaveCredit->leave_type_id }}"
                                data-credits="{{ $leaveCredit->remaining_credits }}">
                                {{ $leaveCredit->leaveType->leaveType }}
                            </option>
                            @endforeach
                            @else
                            <!-- Fallback to all leave types if leave credits are empty -->
                            @foreach($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}">
                                {{ $leaveType->leaveType }}
                            </option>
                            @endforeach
                            @endif
                        </select>

                    </div>
                    <div class="form-group">
                        <label>From <span class="text-danger">*</span></label>
                        <div class="cal-icon">
                            <input class="form-control datetimepicker" type="text" name="start_datee" id="start_datee"
                                required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>To <span class="text-danger">*</span></label>
                        <div class="cal-icon">
                            <input class="form-control datetimepicker" type="text" name="end_datee" id="end_datee"
                                required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Days</label>
                        <input class="form-control" type="text" name="dayse" id="dayse" readonly>
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <textarea class="form-control" name="reasone" id="reasone"></textarea>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Leave Modal -->

<!-- View Leave Modal -->
<div id="view_leave" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Leave</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="leave_id" id="leave_id">
                <div class="form-group">
                    <label>Leave Type</label>
                    <input type="text" class="form-control" name="typeview" id="typeview" readonly>
                </div>
                <div class="form-group">
                    <label>From <span class="text-danger">*</span></label>
                    <div class="cal-icon">
                        <input class="form-control datetimepicker" type="text" name="start_dateview" id="start_dateview"
                            readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label>To <span class="text-danger">*</span></label>
                    <div class="cal-icon">
                        <input class="form-control datetimepicker" type="text" name="end_dateview" id="end_dateview"
                            readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label>Days</label>
                    <input class="form-control" type="text" name="daysview" id="daysview" readonly>
                </div>
                <div class="form-group">
                    <label>Reason</label>
                    <textarea class="form-control" name="reasonview" id="reasonview" rows="7" readonly></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /View Leave Modal -->

<!-- Delete Leave Modal -->
<div class="modal custom-modal fade" id="delete_approve" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Leave</h3>
                    <p>Are you sure you want to cancel this leave?</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-5">
                            <form id="deleteLeaveForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="leave_id" id="delete_leave_id">
                                <button class="btn add-btn" type="submit">Delete</button>
                            </form>
                        </div>
                        <div class="col-6">
                            <a href="javascript:void(0);" data-dismiss="modal" class="btn add-btn">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Leave Modal -->

@endsection

@section('scripts')



<script>
    document.addEventListener('DOMContentLoaded', function () {
        var approveButtons = document.querySelectorAll('.approve-button');
        var declineButtons = document.querySelectorAll('.decline-button');

        approveButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var leaveId = button.getAttribute('data-leave-id');
                confirmApproval(leaveId);
            });
        });

        declineButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var leaveId = button.getAttribute('data-leave-id');
                confirmDecline(leaveId);
            });
        });
    });

    function confirmApproval(leaveId) {
        var form = document.getElementById('approve-form-' + leaveId);
        var confirmAction = confirm("Are you sure you want to approve this leave request?");
        if (confirmAction) {
            form.submit();
        }
    }

    function confirmDecline(leaveId) {
        var form = document.getElementById('decline-form-' + leaveId);
        var confirmAction = confirm("Are you sure you want to decline this leave request?");
        if (confirmAction) {
            form.submit();
        }
    }

</script>

<script>
    $(document).ready(function () {
        // Initialize datetime pickers for start and end date fields
        $('#start_date, #end_date, #start_datee, #end_datee').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $('#start_date, #end_date').on('dp.change', function () {
            calculateTotalDays('#start_date', '#end_date', '#total_days');
        });

        $('#start_datee, #end_datee').on('dp.change', function () {
            calculateTotalDays('#start_datee', '#end_datee', '#dayse');
        });

        // Function to calculate total days between two dates
        function calculateTotalDays(startSelector, endSelector, outputSelector) {
            var startDate = $(startSelector).data('DateTimePicker').date();
            var endDate = $(endSelector).data('DateTimePicker').date();

            if (startDate && endDate) {
                var diffDays = endDate.diff(startDate, 'days') + 1; // Calculate difference in days and add 1
                $(outputSelector).val(diffDays);
            } else {
                $(outputSelector).val(''); // Clear the output if dates are not both set
            }
        }

        $('.edit-leave').on('click', function () {
            var leaveId = $(this).data('id');
            var leaveTypeId = $(this).data('type_id');
            var startDate = $(this).data('start_date');
            var endDate = $(this).data('end_date');
            var days = $(this).data('days');
            var reason = $(this).data('reason');
            var status = $(this).data('status');

            // if (status === 'Approved') {
            //     alert('This leave request has already been approved and cannot be edited.');
            //     return;
            // }

            $('#leave_id').val(leaveId);
            $('#typee').val(leaveTypeId);
            $('#start_datee').val(startDate);
            $('#end_datee').val(endDate);
            $('#dayse').val(days);
            $('#reasone').val(reason);

            $('#editLeaveForm').attr('action', '/admin/leave/' + leaveId);
            $('#edit_leave').modal('show');
        });




        // View leave request
        $('.view-leave').on('click', function () {
            var leaveId = $(this).data('id');
            var leaveType = $(this).data('type');
            var startDate = $(this).data('start_date');
            var endDate = $(this).data('end_date');
            var days = $(this).data('days');
            var reason = $(this).data('reason');
            var status = $(this).data('status');

            $('#leave_id').val(leaveId);
            $('#typeview').val(leaveType);
            $('#start_dateview').val(startDate);
            $('#end_dateview').val(endDate);
            $('#daysview').val(days);
            $('#reasonview').val(reason);

            $('#view_leave').modal('show');
        });

        // Delete leave request
        $('.delete-leave').on('click', function () {
            var leaveId = $(this).data('id');
            var status = $(this).data('status');

            if (status === 'Approved') {
                alert('This leave request has already been approved and cannot be deleted.');
                return;
            }

            $('#delete_leave_id').val(leaveId);
            $('#deleteLeaveForm').attr('action', '/admin/leave/' + leaveId);
            $('#delete_approve').modal('show');
        });
    });

</script>

<script>
    $(function () {
        $('input[name="daterange"]').daterangepicker({
            opens: 'left'
        }, function (start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' +
                end
                .format('YYYY-MM-DD'));
        });
    });

</script>


<script>
    function initializeApproveButtons() {
        const approveButtons = document.querySelectorAll('.approve-button');

        approveButtons.forEach(button => {
            button.addEventListener('click', function () {
                const leaveId = this.getAttribute('data-leave-id');
                const form = document.getElementById('approve-form-' + leaveId);
                const loader = document.getElementById('global-loader'); // Use global loader

                // Show the loader
                loader.style.display = 'flex';

                // Submit the form after showing the loader
                form.submit();
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initializeApproveButtons(); // Initialize on page load

        // If using AJAX for searching
        document.getElementById('search-button').addEventListener('click', function () {
            // Perform the search...

            // After updating the DOM with new leave requests, reinitialize the approve buttons
            initializeApproveButtons();
        });
    });

</script>

@endsection
