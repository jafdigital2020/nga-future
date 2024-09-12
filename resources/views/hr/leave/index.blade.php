@extends('layouts.hrmaster') @section('title', 'Leave') @section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Leaves</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('hr/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Leaves</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <div class="view-icons">
                    <a href="{{ url('hr/leave') }}" class="grid-view btn btn-link active"><i class="fa fa-th"></i></a>
                    <a href="{{ url('hr/leave/hr') }}" class="list-view btn btn-link "><i
                            class="fa fa-paper-plane"></i></a>
                </div>
            </div>
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
                <h6>Planned Leaves</h6>
                <h4>{{ $vacationLeaveCountToday + $sickLeaveCountToday + $birthdayLeaveCountToday }} <span>Today</span>
                </h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-info">
                <h6>Unpaid Leaves</h6>
                <h4>{{ $unpaidLeaveCountToday }} <span>Today</span></h4>
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
    <form action="{{ route('leave.searchr') }}" method="GET">
        <div class="row filter-row">
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus">
                    <input type="text" class="form-control floating" name="name">
                    <label class="focus-label">Employee Name</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="type">
                        <option value=""> -- Select -- </option>
                        <option value="Vacation Leave" {{ request('type') == 'Vacation Leave' ? 'selected' : '' }}>
                            Vacation Leave</option>
                        <option value="Sick Leave" {{ request('type') == 'Sick Leave' ? 'selected' : '' }}>Sick Leave
                        </option>
                        <option value="Birthday Leave" {{ request('type') == 'Birthday Leave' ? 'selected' : '' }}>
                            Birthday Leave</option>
                        <option value="Unpaid Leave" {{ request('type') == 'Unpaid Leave' ? 'selected' : '' }}>Unpaid
                            Leave</option>
                    </select>

                    <label class="focus-label">Leave Type</label>
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
                <button type="submit" class="btn btn-success btn-block">Search</button>
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
                            <th>From</th>
                            <th>To</th>
                            <th>No of Days</th>
                            <th>Reason</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaveRequests as $leave)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="{{ url('hr/employee/edit/'.$leave->user->id) }}" class="avatar">
                                        @if ($leave->user->image)
                                        <img src="{{ asset('images/' . $leave->user->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{ asset('images/default.png') }}" alt="Profile Image" /></a>
                                    @endif
                                    <a href="{{ url('hr/employee/edit/'.$leave->user->id) }}">@if ($leave->user->fName
                                        || $leave->user->lName)
                                        {{ $leave->user->fName }} {{ $leave->user->lName }}
                                        @else
                                        {{ $leave->user->name }}
                                        @endif
                                        <span>{{ $leave->user->department }}</span>
                                    </a>
                            </td>
                            <td>{{ $leave->type }}</td>
                            <td>{{ $leave->start_date }}</td>
                            <td>{{ $leave->end_date }}</td>
                            <td>{{ $leave->days }}</td>
                            <td>{{ $leave->reason }}</td>
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
                                            action="{{ route('leave.approver', $leave->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item approve-button"
                                                data-leave-id="{{ $leave->id }}">
                                                <i class="fa fa-dot-circle-o text-success"></i> Approved
                                            </button>
                                        </form>


                                        <form id="decline-form-{{ $leave->id }}"
                                            action="{{ route('leave.decliner', $leave->id) }}" method="POST"
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
                                        <a class="dropdown-item edit-leave" href="#" data-id="{{ $leave->id }}"
                                            data-type="{{ $leave->type }}" data-start_date="{{ $leave->start_date }}"
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

<!-- Add Leave Modal -->
<div id="add_leave" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Leave</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('mstore.leaver') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Leave Type <span class="text-danger">*</span></label>
                        <select class="form-control" name="type" id="type">
                            <option>-- Select Leave Type --</option>
                            <option>Vacation Leave</option>
                            <option>Sick Leave</option>
                            <option>Birthday Leave</option>
                            <option>Unpaid Leave</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>From <span class="text-danger">*</span></label>
                        <div class="cal-icon">
                            <input class="form-control datetimepicker" type="text" name="start_date" id="start_date"
                                required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>To <span class="text-danger">*</span></label>
                        <div class="cal-icon">
                            <input class="form-control datetimepicker" type="text" name="end_date" id="end_date"
                                required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Number of days <span class="text-danger">*</span></label>
                        <input class="form-control" name="total_days" id="total_days" readonly type="text">
                    </div>
                    <div class="form-group">
                        <label>Remaining Leaves <span class="text-danger">*</span></label>
                        <input class="form-control" readonly
                            value="{{ $user->vacLeave + $user->sickLeave + $user->bdayLeave  }}" type="text">
                    </div>
                    <div class="form-group">
                        <label>Leave Reason <span class="text-danger">*</span></label>
                        <textarea rows="4" class="form-control" name="reason" id="reason" required></textarea>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Leave Modal -->

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
                        <label>Leave Type</label>
                        <select class="form-control" name="typee" id="typee">
                            <option value="Vacation Leave">Vacation Leave</option>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Birthday Leave">Birthday Leave</option>
                            <option value="Unpaid Leave">Unpaid Leave</option>
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
        $('#start_date, #end_date, #start_datee, #end_datee').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $('#start_date, #end_date').on('dp.change', function () {
            calculateTotalDays('#start_date', '#end_date', '#total_days');
        });

        $('#start_datee').on('focus', function () {
            $('#end_datee').val('');
        });

        $('#start_datee, #end_datee').on('dp.change', function () {
            calculateTotalDays('#start_datee', '#end_datee', '#dayse');
        });

        function calculateTotalDays(startSelector, endSelector, outputSelector) {
            var startDate = $(startSelector).data('DateTimePicker').date();
            var endDate = $(endSelector).data('DateTimePicker').date();

            if (startDate && endDate) {
                var diffDays = endDate.diff(startDate, 'days') + 1; // Calculate difference in days and add 1
                $(outputSelector).val(diffDays);
            } else {
                $(outputSelector).val('');
            }
        }

        // Edit leave request
        $('.edit-leave').on('click', function () {
            var leaveId = $(this).data('id');
            var leaveType = $(this).data('type');
            var startDate = $(this).data('start_date');
            var endDate = $(this).data('end_date');
            var days = $(this).data('days');
            var reason = $(this).data('reason');
            var status = $(this).data('status');

            if (status === 'Approved') {
                alert('This leave request has already been approved and cannot be edited.');
                return;
            }

            $('#leave_id').val(leaveId);
            $('#typee').val(leaveType);
            $('#start_datee').val(startDate);
            $('#end_datee').val(endDate);
            $('#dayse').val(days);
            $('#reasone').val(reason);

            $('#editLeaveForm').attr('action', '/hr/leave/' +
                leaveId); // Ensure the form action URL is correct
            $('#edit_leave').modal('show');
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
            $('#deleteLeaveForm').attr('action', '/hr/leave/' + leaveId);
            $('#delete_approve').modal('show');
        });
    });

</script>

@endsection
