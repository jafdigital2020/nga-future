@extends('layouts.managermaster') @section('title', 'Attendance') @section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Timesheet Approval</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ url('manager/dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Timesheet Approval</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <div class="view-icons">
                    <a href="{{ url('manager/attendance') }}" class="grid-view btn btn-link"><i
                            class="las la-calendar-check"></i></a>
                    <a href="{{ url('manager/attendance/record') }}" class="list-view btn btn-link active"><i
                            class="la la-calendar"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Attendance Statistics -->
    <div class="row">
        <div class="col-md-4">
            <div class="stats-info">
                <h6>Pending</h6>
                <h4>{{ $pendingCount }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-info">
                <h6>Approved</h6>
                <h4>{{ $approvedCount }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-info">
                <h6>Declined</h6>
                <h4>{{ $declinedCount }}</h4>
            </div>
        </div>
    </div>
    <!-- /Attendance Statistics -->

    <!-- Search Filter -->
    <form action="{{ route('attendance.search') }}" method="GET">
        <div class="row filter-row">
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus">
                    <input type="text" class="form-control floating" name="name">
                    <label class="focus-label">Employee Name</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="cut_off">
                        <option value=""> -- Select -- </option>
                        <option value="December - January 1st Cut-off"
                            {{ request('cut_off') == 'December - January 1st Cut-off' ? 'selected' : '' }}>December -
                            January 1st Cut-off</option>
                        <option value="January 2nd Cut-off"
                            {{ request('cut_off') == 'January 2nd Cut-off' ? 'selected' : '' }}>January 2nd Cut-off
                        </option>
                        <option value="January - February 1st Cut-off"
                            {{ request('cut_off') == 'January - February 1st Cut-off' ? 'selected' : '' }}>January -
                            February 1st Cut-off</option>
                        <option value="February 2nd Cut-off"
                            {{ request('cut_off') == 'February 2nd Cut-off' ? 'selected' : '' }}>February 2nd Cut-off
                        </option>
                        <option value="February - March 1st Cut-off"
                            {{ request('cut_off') == 'February - March 1st Cut-off' ? 'selected' : '' }}>February -
                            March 1st Cut-off</option>
                        <option value="March 2nd Cut-off"
                            {{ request('cut_off') == 'March 2nd Cut-off' ? 'selected' : '' }}>March 2nd Cut-off</option>
                        <option value="March - April 1st Cut-off"
                            {{ request('cut_off') == 'March - April 1st Cut-off' ? 'selected' : '' }}>March - April 1st
                            Cut-off</option>
                        <option value="April 2nd Cut-off"
                            {{ request('cut_off') == 'April 2nd Cut-off' ? 'selected' : '' }}>April 2nd Cut-off</option>
                        <option value="April - May 1st Cut-off"
                            {{ request('cut_off') == 'April - May 1st Cut-off' ? 'selected' : '' }}>April - May 1st
                            Cut-off</option>
                        <option value="May 2nd Cut-off" {{ request('cut_off') == 'May 2nd Cut-off' ? 'selected' : '' }}>
                            May 2nd Cut-off</option>
                        <option value="May - June 1st Cut-off"
                            {{ request('cut_off') == 'May - June 1st Cut-off' ? 'selected' : '' }}>May - June 1st
                            Cut-off</option>
                        <option value="June 2nd Cut-off"
                            {{ request('cut_off') == 'June 2nd Cut-off' ? 'selected' : '' }}>June 2nd Cut-off</option>
                        <option value="June - July 1st Cut-off"
                            {{ request('cut_off') == 'June - July 1st Cut-off' ? 'selected' : '' }}>June - July 1st
                            Cut-off</option>
                        <option value="July 2nd Cut-off"
                            {{ request('cut_off') == 'July 2nd Cut-off' ? 'selected' : '' }}>July 2nd Cut-off</option>
                        <option value="July - August 1st Cut-off"
                            {{ request('cut_off') == 'July - August 1st Cut-off' ? 'selected' : '' }}>July - August 1st
                            Cut-off</option>
                        <option value="August 2nd Cut-off"
                            {{ request('cut_off') == 'August 2nd Cut-off' ? 'selected' : '' }}>August 2nd Cut-off
                        </option>
                        <option value="August - September 1st Cut-off"
                            {{ request('cut_off') == 'August - September 1st Cut-off' ? 'selected' : '' }}>August -
                            September 1st Cut-off</option>
                        <option value="September 2nd Cut-off"
                            {{ request('cut_off') == 'September 2nd Cut-off' ? 'selected' : '' }}>September 2nd Cut-off
                        </option>
                        <option value="September - October 1st Cut-off"
                            {{ request('cut_off') == 'September - October 1st Cut-off' ? 'selected' : '' }}>September -
                            October 1st Cut-off</option>
                        <option value="October 2nd Cut-off"
                            {{ request('cut_off') == 'October 2nd Cut-off' ? 'selected' : '' }}>October 2nd Cut-off
                        </option>
                        <option value="October - November 1st Cut-off"
                            {{ request('cut_off') == 'October - November 1st Cut-off' ? 'selected' : '' }}>October -
                            November 1st Cut-off</option>
                        <option value="November 2nd Cut-off"
                            {{ request('cut_off') == 'November 2nd Cut-off' ? 'selected' : '' }}>November 2nd Cut-off
                        </option>
                        <option value="November - December 1st Cut-off"
                            {{ request('cut_off') == 'November - December 1st Cut-off' ? 'selected' : '' }}>November -
                            December 1st Cut-off</option>
                        <option value="December 2nd Cut-off"
                            {{ request('cut_off') == 'December 2nd Cut-off' ? 'selected' : '' }}>December 2nd Cut-off
                        </option>
                    </select>

                    <label class="focus-label">Cut-Off</label>
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
                    <label class="focus-label">Attendance Status</label>
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
                <button type="submit" class="btn btn-primary btn-block">Search</button>
            </div>
        </div>
    </form>
    <!-- /Search Filter -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Month</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Cut-off</th>
                            <th>Total Hours</th>
                            <th>Total Late</th>
                            <th>OT Hours</th>
                            <th>Vacation Leave</th>
                            <th>Sick Leave</th>
                            <th>Birthday Leave</th>
                            <th>Unpaid Leave</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendance as $att)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="profile.html" class="avatar">
                                        @if ($att->user->image)
                                        <img src="{{ asset('images/' . $att->user->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{ asset('images/default.png') }}" alt="Profile Image" /></a>
                                    @endif
                                    <a href="#">{{ $att->user->fName }}
                                        {{ $att->user->lName }}<span>{{ $att->department }}</span></a>
                                </h2>
                            </td>
                            <td>{{ $att->month }}</td>
                            <td>{{ $att->start_date }}</td>
                            <td>{{ $att->end_date }}</td>
                            <td>{{ $att->cut_off }}</td>
                            <td>{{ $att->totalHours }}</td>
                            <td>{{ $att->totalLate }}</td>
                            <td>{{ $att->otHours }}</td>
                            <td>{{ $att->vacLeave }}</td>
                            <td>{{ $att->sickLeave }}</td>
                            <td>{{ $att->bdayLeave }}</td>
                            <td>{{ $att->unpaidLeave }}</td>
                            <td class="text-center">
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        @if($att->status == 'New')
                                        <i class="fa fa-dot-circle-o text-purple"></i> New
                                        @elseif($att->status == 'pending')
                                        <i class="fa fa-dot-circle-o text-info"></i> Pending
                                        @elseif($att->status == 'Approved')
                                        <i class="fa fa-dot-circle-o text-success"></i> Approved
                                        @elseif($att->status == 'Declined')
                                        <i class="fa fa-dot-circle-o text-danger"></i> Declined
                                        @else
                                        <i class="fa fa-dot-circle-o"></i> Unknown
                                        @endif
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-info"></i>
                                            Pending</a>

                                        <form id="approve-form-{{ $att->id }}"
                                            action="{{ route('att.approve', $att->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item approve-button"
                                                data-att-id="{{ $att->id }}">
                                                <i class="fa fa-dot-circle-o text-success"></i> Approve
                                            </button>
                                        </form>

                                        <form id="decline-form-{{ $att->id }}"
                                            action="{{ route('att.decline', $att->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item decline-button"
                                                data-att-id="{{ $att->id }}">
                                                <i class="fa fa-dot-circle-o text-danger"></i> Decline
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
                                        <a class="dropdown-item edit-attendance" href="#" data-id="{{ $att->id }}"
                                            data-type="{{ $att->cut_off }}" data-start_date="{{ $att->start_date }}"
                                            data-end_date="{{ $att->end_date }}"
                                            data-total_hours="{{ $att->totalHours }}"
                                            data-total_late="{{ $att->totalLate }}">
                                            <i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item" href="{{ url('manager/timesheet/view/' . $att->id) }}">
                                            <i class="fa fa-eye m-r-5"></i> View</a>
                                        <a class="dropdown-item delete-attendance" href="#" data-id="{{ $att->id }}">
                                            <i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->

<!-- Edit attendance Modal -->
<div id="edit_attendance" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Attendance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editAttendanceForm" method="POST">
                    @csrf
                    <input type="hidden" name="att_id" id="att_id">
                    <div class="form-group">
                        <label>Cut-Off</label>
                        <input class="form-control" type="text" name="cut_off" id="cut_off" readonly>
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
                        <label>Total hours</label>
                        <input class="form-control" type="text" name="totalHours" id="totalHours">
                    </div>
                    <div class="form-group">
                        <label>Total Late</label>
                        <input class="form-control" type="text" name="totalLate" id="totalLate">
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit attendance Modal -->

<!-- Delete Leave Modal -->
<div class="modal custom-modal fade" id="delete_approve" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Attendance</h3>
                    <p>Are you sure you want to cancel this attendance?</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-5">
                            <form id="deleteAttendanceForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="att_id" id="delete_att_id">
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
                var attId = button.getAttribute('data-att-id');
                confirmApproval(attId);
            });
        });

        declineButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var attId = button.getAttribute('data-att-id');
                confirmDecline(attId);
            });
        });
    });

    function confirmApproval(attId) {
        var form = document.getElementById('approve-form-' + attId);
        var confirmAction = confirm("Are you sure you want to approve this attendance request?");
        if (confirmAction) {
            form.submit();
        }
    }

    function confirmDecline(attId) {
        var form = document.getElementById('decline-form-' + attId);
        var confirmAction = confirm("Are you sure you want to decline this attendance request?");
        if (confirmAction) {
            form.submit();
        }
    }

</script>

<script>
    // Edit leave request
    $('.edit-attendance').on('click', function () {
        var attId = $(this).data('id');
        var cutOff = $(this).data('type');
        var startDate = $(this).data('start_date');
        var endDate = $(this).data('end_date');
        var totalHours = $(this).data('total_hours');
        var totalLate = $(this).data('total_late');

        if (status === 'Approved') {
            alert('This leave request has already been approved and cannot be edited.');
            return;
        }

        $('#att_id').val(attId);
        $('#cut_off').val(cutOff);
        $('#start_date').val(startDate);
        $('#end_date').val(endDate);
        $('#totalHours').val(totalHours);
        $('#totalLate').val(totalLate);

        $('#editAttendanceForm').attr('action', '/manager/attendance/approve/' +
            attId); // Ensure the form action URL is correct
        $('#edit_attendance').modal('show');
    });

    // Delete leave request
    $('.delete-attendance').on('click', function () {
        var attId = $(this).data('id');


        $('#delete_attendance_id').val(attId);
        $('#deleteAttendanceForm').attr('action', '/manager/attendance/delete/' + attId);
        $('#delete_approve').modal('show');
    });

</script>

@endsection
