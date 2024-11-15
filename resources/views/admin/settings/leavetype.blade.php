@extends('layouts.settings') @section('title', 'Leave Type')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Leave Type</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Leave Type</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_leavetype"><i
                        class="fa fa-plus"></i> Add Leave Type</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table datatable mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Leave Type</th>
                            <th>Leave Days</th>
                            <th>Restriction Days</th>
                            <th>Paid / Unpaid</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ltype as $type)
                        <tr>
                            <td>{{ $type->id }}</td>
                            <td>{{ $type->leaveType }}</td>
                            <td>{{ $type->leaveDays }}</td>
                            <td>{{ $type->restriction_days }}</td>
                            <td>{{ $type->is_paid ? 'Paid' : 'Unpaid' }}</td>
                            <td>
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-dot-circle-o text-success"></i> Active
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item"><i
                                                class="fa fa-dot-circle-o text-success"></i> Active</a>
                                        <a href="#" class="dropdown-item"><i class="fa fa-dot-circle-o text-danger"></i>
                                            Inactive</a>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item edit-leaveType" href="#" data-id="{{ $type->id }}"
                                            data-leave_type="{{ $type->leaveType }}"
                                            data-leave_days="{{ $type->leaveDays }}"
                                            data-is_paid="{{ $type->is_paid }}"><i class="fa fa-pencil m-r-5"></i>
                                            Edit</a>
                                        <a class="dropdown-item delete-leaveType" href="#" data-id="{{ $type->id }}"><i
                                                class="fa fa-trash-o m-r-5"></i>
                                            Delete</a>
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

<!-- Add Leavetype Modal -->
<div id="add_leavetype" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Leave Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('settings.leaveTypeStore') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Leave Type <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="leaveType" required>
                    </div>
                    <div class="form-group">
                        <label>Leave Days<span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="leaveDays" required>
                    </div>
                    <div class="form-group">
                        <label>Restriction Days<span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="restriction_days" required>
                    </div>
                    <div class="form-group">
                        <label>Paid / Unpaid</label>
                        <select name="is_paid" class="form-control" required>
                            <option value="" disabled selected>Select Type</option>
                            <option value="1">Paid</option>
                            <option value="0">Unpaid</option>
                        </select>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Leavetype Modal -->

<!-- Edit Leavetype Modal -->
<div id="edit_leavetype" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Leave Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editLeaveTypeForm" method="POST">
                    @csrf
                    <input type="hidden" name="leave_type_id" id="leave_type_id">
                    <div class="form-group">
                        <label>Leave Type <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="leaveType" id="leaveType" required>
                    </div>
                    <div class="form-group">
                        <label>Leave Days<span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="leaveDays" id="leaveDays" required>
                    </div>
                    <div class="form-group">
                        <label>Paid / Unpaid</label>
                        <select name="is_paid" id="is_paid" class="form-control" required>
                            <option value="" disabled selected>Select Type</option>
                            <option value="1">Paid</option>
                            <option value="0">Unpaid</option>
                        </select>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Leavetype Modal -->

<!-- Delete Leavetype Modal -->
<div class="modal custom-modal fade" id="delete_leavetype" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Leave Type</h3>
                    <p>Are you sure you want to cancel this leave type?</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-5">
                            <form id="deleteLeaveTypeForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="leave_type_id" id="leave_type_id">
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
<!-- /Delete Leavetype Modal -->

@endsection


@section('scripts')

<script>
    // Edit leave request
    $('.edit-leaveType').on('click', function () {
        var leaveTypeId = $(this).data('id');
        var leaveType = $(this).data('leave_type');
        var leaveDays = $(this).data('leave_days');
        var isPaid = $(this).data('is_paid');

        $('#leave_type_id').val(leaveTypeId);
        $('#leaveType').val(leaveType);
        $('#leaveDays').val(leaveDays);
        $('#is_paid').val(isPaid);

        $('#editLeaveTypeForm').attr('action', '/admin/settings/leavetype/edit/' +
            leaveTypeId);
        $('#edit_leavetype').modal('show');
    });

    // Delete Earning request
    $('.delete-leaveType').on('click', function () {
        var leaveTypeId = $(this).data('id');

        $('#leave_type_id').val(leaveTypeId);
        $('#deleteLeaveTypeForm').attr('action', '/admin/settings/leavetype/delete/' + leaveTypeId);
        $('#delete_leavetype').modal('show');
    });

</script>


@endsection
