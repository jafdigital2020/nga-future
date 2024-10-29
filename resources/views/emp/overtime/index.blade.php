@extends('layouts.empmaster') @section('title', 'Overtime')
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
                    <li class="breadcrumb-item"><a href="{{ url('emp/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Overtime</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#overtimeModal"><i
                        class="fa fa-plus"></i> Add Overtime</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Overtime Statistics -->
    <div class="row">
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="stats-info">
                <h6>Overtime</h6>
                <h4>{{ $totalRequestsThisMonth }} <span>this month</span></h4>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="stats-info">
                <h6>Overtime Hours</h6>
                <h4>{{ $approvedHours }} <span>this month</span></h4>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="stats-info">
                <h6>Remaining Overtime Hours</h6>
                <h4>{{ $decimalHours }} <span>this month</span></h4>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="stats-info">
                <h6>Pending Request</h6>
                <h4>{{ $pendingCount }}</h4>
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
                        @foreach ($overtime as $ot)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="#" class="avatar">
                                        @if ($ot->user->image)
                                        <img src="{{ asset('images/' . $ot->user->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{
                                                asset('images/default.png')
                                            }}" alt="Profile Image" />
                                        @endif</a>
                                    <a href="{{ url('emp/profile/') }}">{{ $ot->user->fName }}
                                        {{ $ot->user->lName }}
                                        <span>{{ $ot->user->position }}</span></a>
                                </h2>
                            </td>
                            <td>{{ $ot->date }}</td>
                            <td>
                                @if($ot->start_time)
                                {{ \Carbon\Carbon::parse($ot->start_time)->format('h:i:s A') }}
                                @else
                                N/A
                                @endif
                            </td>
                            <td>
                                @if($ot->end_time)
                                {{ \Carbon\Carbon::parse($ot->end_time)->format('h:i:s A') }}
                                @else
                                N/A
                                @endif
                            </td>
                            <td>{{ $ot->total_hours }}</td>
                            <td>{{ $ot->reason }}</td>
                            <td>{{ $ot->created_at->format('Y-m-d') }}</td>
                            <td class="text-center">
                                <div class="action-label">
                                    <a class="btn btn-white btn-sm btn-rounded" href="#">
                                        @if($ot->status == 'New')
                                        <i class="fa fa-dot-circle-o text-purple"></i> New
                                        @elseif($ot->status == 'Pending')
                                        <i class="fa fa-dot-circle-o text-info"></i> Pending
                                        @elseif($ot->status == 'Approved')
                                        <i class="fa fa-dot-circle-o text-success"></i> Approved
                                        @elseif($ot->status == 'Rejected')
                                        <i class="fa fa-dot-circle-o text-danger"></i> Rejected
                                        @else
                                        <i class="fa fa-dot-circle-o"></i> Unknown
                                        @endif
                                    </a>
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
                                    @if($ot->otapprover)
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
                                        <a class="dropdown-item edit-ot" href="#" data-id="{{ $ot->id }}"
                                            data-date="{{ $ot->date }}" data-start_time="{{ $ot->start_time }}"
                                            data-end_time="{{ $ot->end_time }}" data-status="{{ $ot->status }}"
                                            data-total_hours="{{ $ot->total_hours }}" data-reason="{{ $ot->reason }}">
                                            <i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item delete-ot" href="#" data-id="{{ $ot->id }}"
                                            data-status="{{ $ot->status }}">
                                            <i class="fa fa-trash-o m-r-5"></i> Delete
                                        </a>
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
    <!-- /Page Content -->


    <!-- Request OT Modal -->
    <div id="overtimeModal" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Overtime</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('overtime.request') }}">
                        @csrf
                        <div class="form-group">
                            <label>Date</label>
                            <div class="cal-icon">
                                <input class="form-control floating datetimepicker" type="text" name="date" id="date">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Start Time<span class="text-danger">*</span></label>
                                    <input class="form-control" type="time" name="start_time" id="start_time">

                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>End Time <span class="text-danger">*</span></label>
                                    <input class="form-control" type="time" name="end_time" id="end_time">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Total Hours</label>
                                    <input type="text" class="form-control" id="total_hours" name="total_hours"
                                        placeholder="HH:MM:SS" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Reason</label>
                            <textarea class="form-control" name="reason" id="reason"></textarea>
                        </div>
                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Request OT Modal -->

    <!-- Edit OT Modal -->
    <div id="edit_ot" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Overtime</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editOtForm" method="POST">
                        @csrf
                        <input type="hidden" name="ot_id" id="ot_id">
                        <div class="form-group">
                            <label>Date</label>
                            <div class="cal-icon">
                                <input class="form-control floating datetimepicker" type="text" name="datee" id="datee">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Start Time<span class="text-danger">*</span></label>
                                    <input class="form-control" type="time" name="start_timee" id="start_timee">

                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>End Time <span class="text-danger">*</span></label>
                                    <input class="form-control" type="time" name="end_timee" id="end_timee">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Total Hours</label>
                                    <input type="text" class="form-control" id="total_hourse" name="total_hourse"
                                        placeholder="HH:MM:SS" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Reason</label>
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
    <!-- /Edit OT Modal -->

    <!-- Delete OT Modal -->
    <div class="modal custom-modal fade" id="delete_approve" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete OT Request</h3>
                        <p>Are you sure you want to delete this OT request?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <div class="row">
                            <div class="col-5">
                                <form id="deleteOtForm" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="ot_id" id="delete_ot_id">
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
    <!-- /Delete OT Modal -->

    @endsection


    @section('scripts')

    <script>
        function formatInput(e) {
            let input = e.target.value;

            // Remove all non-digit characters
            input = input.replace(/[^0-9]/g, '');

            // Insert colons after every 2 digits
            if (input.length >= 3 && input.length <= 4) {
                input = input.slice(0, 2) + ':' + input.slice(2);
            }
            if (input.length >= 5) {
                input = input.slice(0, 2) + ':' + input.slice(2, 4) + ':' + input.slice(4, 6);
            }

            e.target.value = input;
        }

        document.getElementById('total_hours').addEventListener('input', formatInput);
        document.getElementById('total_hourse').addEventListener('input', formatInput);

    </script>


    <script>
        $(document).ready(function () {

            // Edit ot request
            $('.edit-ot').on('click', function () {
                var otId = $(this).data('id');
                var otDate = $(this).data('date');
                var startTime = $(this).data('start_time');
                var endTime = $(this).data('end_time');
                var totalHours = $(this).data('total_hours');
                var reason = $(this).data('reason');
                var status = $(this).data('status');

                if (status === 'Approved') {
                    alert('This OT request has already been approved and cannot be edited.');
                    return;
                }

                $('#ot_id').val(otId);
                $('#datee').val(otDate);
                $('#start_timee').val(startTime);
                $('#end_timee').val(endTime);
                $('#total_hourse').val(totalHours);
                $('#reasone').val(reason);

                $('#editOtForm').attr('action', '/emp/overtime/edit/' +
                    otId);
                $('#edit_ot').modal('show');
            });

            // Delete OT request
            $('.delete-ot').on('click', function () {
                var otId = $(this).data('id');
                var status = $(this).data('status');

                if (status === 'Approved') {
                    alert('This OT request has already been approved and cannot be deleted.');
                    return;
                }

                $('#delete_ot_id').val(otId);
                $('#deleteOtForm').attr('action', '/emp/overtime/delete/' +
                    otId);
                $('#delete_approve').modal('show');
            });
        });

    </script>



    @endsection
