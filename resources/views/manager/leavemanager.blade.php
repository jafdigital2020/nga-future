@extends('layouts.managermaster') @section('title', 'Leave') @section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Leaves</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('manager/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Leaves</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <div class="view-icons">
                    <a href="{{ url('manager/leave') }}" class="grid-view btn btn-link"><i class="fa fa-th"></i></a>
                    <a href="{{ url('manager/leave/manager') }}" class="list-view btn btn-link active"><i
                            class="fa fa-paper-plane"></i></a>
                </div>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_leave"><i class="fa fa-plus"></i>
                    Request Leave</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Leave Statistics -->
    <div class="row">
        <div class="col-md-3">
            <div class="stats-info">
                <h6>Vacation Leave</h6>
                @if(isset($user->vacLeave))
                <h4>{{ $user->vacLeave }}</h4>
                @else
                <h4>0</h4>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-info">
                <h6>Sick Leave</h6>
                @if(isset($user->sickLeave))
                <h4>{{ $user->sickLeave }}</h4>
                @else
                <h4>0</h4>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-info">
                <h6>Birthday Leave</h6>
                @if(isset($user->bdayLeave))
                <h4>{{ $user->bdayLeave }}</h4>
                @else
                <h4>0</h4>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-info">
                <h6>Remaining Leave</h6>
                <h4>{{ $user->vacLeave + $user->sickLeave + $user->bdayLeave }}</h4>
            </div>
        </div>
    </div>
    <!-- /Leave Statistics -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th class="text-center">Status</th>
                            <th>Approved by</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($request as $req)
                        <tr>
                            <td>{{ $req->type }}</td>
                            <td>{{ $req->start_date }}</td>
                            <td>{{ $req->end_date }}</td>
                            <td>{{ $req->days }}</td>
                            <td>{{ $req->reason }}</td>
                            <td class="text-center">
                                <div class="action-label">
                                    <a class="btn btn-white btn-sm btn-rounded" href="#">
                                        @if($req->status == 'New')
                                        <i class="fa fa-dot-circle-o text-purple"></i> New
                                        @elseif($req->status == 'Pending')
                                        <i class="fa fa-dot-circle-o text-info"></i> Pending
                                        @elseif($req->status == 'Approved')
                                        <i class="fa fa-dot-circle-o text-success"></i> Approved
                                        @elseif($req->status == 'Declined')
                                        <i class="fa fa-dot-circle-o text-danger"></i> Declined
                                        @else
                                        <i class="fa fa-dot-circle-o"></i> Unknown
                                        @endif
                                    </a>
                                </div>
                            </td>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="#" class="avatar avatar-xs">
                                        @if ($req->approver)
                                        @if ($req->approver->image)
                                        <img src="{{ asset('images/' . $req->approver->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                        @endif
                                        @else
                                        <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                        @endif
                                    </a>
                                    {{ $req->approver 
                                    ? ($req->approver->fName || $req->approver->lName 
                                        ? $req->approver->fName . ' ' . $req->approver->lName 
                                        : $req->approver->name) 
                                    : 'Not Approved Yet' 
                                }}

                                </h2>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item edit-leave" href="#" data-id="{{ $req->id }}"
                                            data-type="{{ $req->type }}" data-start_date="{{ $req->start_date }}"
                                            data-end_date="{{ $req->end_date }}" data-days="{{ $req->days }}"
                                            data-reason="{{ $req->reason }}" data-status="{{ $req->status }}">
                                            <i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item delete-leave" href="#" data-id="{{ $req->id }}">
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
                <form action="{{ route('store.leavemanager') }}" method="POST">
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

</div>


@endsection

@section('scripts')


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

        $('#end_datee').on('focus', function () {
            $('#start_datee').val('');
            alert('Select "from" date first');
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

            $('#editLeaveForm').attr('action', '/manager/leave/update/' +
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
            $('#deleteLeaveForm').attr('action', '/manager/leave/delete/' + leaveId);
            $('#delete_approve').modal('show');
        });
    });

</script>
@endsection
