@extends('layouts.managermaster') @section('title', 'Request Attendance')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Attendance Certificate</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('manager/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Attendance Certificate</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- attendance Statistics -->
    <div class="row">
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
            <div class="stats-info">
                <h6>Total Request</h6>
                <h4>{{ $reqcount ?? 0 }} <span>this month</span></h4>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
            <div class="stats-info">
                <h6>Pending Request</h6>
                <h4>{{ $pendingCount ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
            <div class="stats-info">
                <h6>Declined</h6>
                <h4>{{ $decCount ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <!-- /attendance Statistics -->

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
                            <th class="text-center">Status</th>
                            <th>Approved By</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pending as $req)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="#" class="avatar">
                                        @if ($req->user->image)
                                        <img src="{{ asset('images/' . $req->user->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{
                                                asset('images/default.png')
                                            }}" alt="Profile Image" />
                                        @endif</a>
                                    <a href="{{ url('manager/profile/') }}">{{ $req->user->fName }}
                                        {{ $req->user->lName }}
                                        <span>{{ $req->user->position }}</span></a>
                                </h2>
                            </td>
                            <td>{{ $req->date }}</td>
                            <td>{{ $req->timeIn }}</td>
                            <td>{{ $req->timeOut }}</td>
                            <td>{{ $req->timeTotal }}</td>
                            <td>{{ $req->reason }}</td>
                            <td>{{ $req->created_at->format('Y-m-d') }}</td>
                            <td class="text-center">
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        @if($req->status_code == 'New')
                                        <i class="fa fa-dot-circle-o text-purple"></i> New
                                        @elseif($req->status_code == 'Pending')
                                        <i class="fa fa-dot-circle-o text-info"></i> Pending
                                        @elseif($req->status_code == 'Pre-Approved')
                                        <i class="fa fa-dot-circle-o text-warning"></i> Pre-Approved
                                        @elseif($req->status_code == 'Declined')
                                        <i class="fa fa-dot-circle-o text-danger"></i> Declined
                                        @else
                                        <i class="fa fa-dot-circle-o"></i> Unknown
                                        @endif
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-info"></i>
                                            Pending</a>

                                        <form id="approve-form-{{ $req->id }}"
                                            action="{{ route('manager.approveAttendance', $req->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item approve-button"
                                                data-attendance-id="{{ $req->id }}">
                                                <i class="fa fa-dot-circle-o text-warning"></i> Pre-Approved
                                            </button>
                                        </form>
                                        <form id="decline-form-{{ $req->id }}"
                                            action="{{ route('manager.declineAttendance', $req->id ) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item decline-button"
                                                data-attendance-id="{{ $req->id }}">
                                                <i class="fa fa-dot-circle-o text-danger"></i> Declined
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="#" class="avatar avatar-xs">
                                        @if ($req->attendance_approver)
                                        @if ($req->attendance_approver->image)
                                        <img src="{{ asset('images/' . $req->attendance_approver->image) }}"
                                            alt="Profile Image" />
                                        @else
                                        <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                        @endif
                                        @else
                                        <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                        @endif
                                    </a>
                                    @if($req->attendance_approver)
                                    {{ $req->attendance_approver->fName }} {{ $req->attendance_approver->lName }}
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
                                        <a class="dropdown-item edit-attendance" href="#" data-id="{{ $req->id }}"
                                            data-date="{{ $req->date }}" data-time_in="{{ $req->timeIn }}"
                                            data-time_out="{{ $req->timeOut }}" data-time_total="{{ $req->timeTotal }}"
                                            data-reason="{{ $req->reason }}" data-file="{{ $req->image_path }}"><i
                                                class="fa fa-pencil m-r-5"></i>
                                            Edit</a>
                                        <a class="dropdown-item delete-att" href="#" data-id="{{ $req->id }}">
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
    <!-- /Page Content -->

    <!-- Edit Attendance Modal -->
    <div id="edit_att" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Attendance Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editAttForm" method="POST">
                        @csrf
                        <input type="hidden" name="att_id" id="att_id">
                        <input type="hidden" name="is_manual_entry" value="1">
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
                                    <label>Break Hours</label>
                                    <input type="text" class="form-control" id="break_hourse" placeholder="HH:MM:SS"
                                        pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="">Attached File (Proof)</label>
                                    <input type="file" class="form-control" id="filee" name="filee">
                                    <div id="file-preview" class="mt-2">
                                        <!-- File preview/link will be displayed here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Total Hours</label>
                                    <input type="text" class="form-control" id="total_hourse" name="total_hourse"
                                        placeholder="HH:MM:SS" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" readonly>
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
    <!-- /Edit Attendance Modal -->

    <!-- Delete Attendance Req Modal -->
    <div class="modal custom-modal fade" id="delete_approve" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete Request</h3>
                        <p>Are you sure you want to delete this request?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <div class="row">
                            <div class="col-5">
                                <form id="deleteAttForm" method="POST">
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
    <!-- /Delete Attendance Req Modal -->

    @endsection

    @section('scripts')

    <script>
        $(document).ready(function () {
            // Edit attendance request
            $(document).on('click', '.edit-attendance', function () {
                var attId = $(this).data('id');
                var attDate = $(this).data('date');
                var timeIn = convertTo24HourFormat($(this).data('time_in'));
                var timeOut = convertTo24HourFormat($(this).data('time_out'));
                var totalHours = $(this).data('time_total');
                var reason = $(this).data('reason');
                var filePath = $(this).data('file');

                // Populate fields in the modal
                $('#att_id').val(attId);
                $('#datee').val(attDate);
                $('#start_timee').val(timeIn);
                $('#end_timee').val(timeOut);
                $('#total_hourse').val(totalHours);
                $('#reasone').val(reason);

                // Set file preview if available
                var filePreview = $('#file-preview');
                if (filePath) {
                    var fileUrl = `/storage/${filePath}`;
                    filePreview.html(
                        `<a href="${fileUrl}" target="_blank">View Current Attached File</a>`);
                } else {
                    filePreview.html('<p>No file attached.</p>');
                }

                $('#editAttForm').attr('action', '/manager/attendance/request/update/' +
                    attId);
                // Show the modal
                $('#edit_att').modal('show');
            });

            // Helper function to convert 12-hour format to 24-hour format if needed
            function convertTo24HourFormat(timeString) {
                if (!timeString) return '';

                var [time, modifier] = timeString.split(' ');
                var [hours, minutes] = time.split(':');

                // Adjust hours based on AM/PM modifier
                if (modifier === 'PM' && hours !== '12') {
                    hours = parseInt(hours, 10) + 12;
                } else if (modifier === 'AM' && hours === '12') {
                    hours = '00';
                }

                // Return as HH:MM for input[type="time"]
                return `${hours}:${minutes}`;
            }

            // Delete OT request
            $('.delete-att').on('click', function () {
                var attId = $(this).data('id');
                // var status = $(this).data('status');

                // if (status === 'Approved') {
                //     alert('This OT request has already been approved and cannot be deleted.');
                //     return;
                // }

                $('#delete_att_id').val(attId);
                $('#deleteAttForm').attr('action', '/manager/attendance/request/delete/' + attId);
                $('#delete_approve').modal('show');
            });
        });

    </script>

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

        document.getElementById('break_hourse').addEventListener('input', formatInput);

    </script>

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

        document.getElementById('break_hours').addEventListener('input', formatInput);

    </script>

    <script>
        function calculateTotalHours() {
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;
            const breakHours = document.getElementById('break_hours').value;

            if (startTime && endTime) {
                // Convert start and end times to Date objects
                const start = new Date(`1970-01-01T${startTime}:00`);
                const end = new Date(`1970-01-01T${endTime}:00`);

                // Calculate the difference in milliseconds
                let diff = end - start;

                // Handle negative difference (if end time is before start time, assume it spans to the next day)
                if (diff < 0) {
                    diff += 24 * 60 * 60 * 1000; // add 24 hours in milliseconds
                }

                // If break_hours is provided, subtract it from the diff
                if (breakHours) {
                    // Convert break hours to milliseconds
                    const [breakH, breakM, breakS] = breakHours.split(':').map(Number);
                    const breakMillis = ((breakH || 0) * 60 * 60 + (breakM || 0) * 60 + (breakS || 0)) * 1000;

                    // Subtract breakMillis from diff
                    diff -= breakMillis;

                    // Ensure total doesn't go below zero
                    if (diff < 0) {
                        diff = 0;
                    }
                }

                // Calculate hours, minutes, seconds from the diff
                const hours = String(Math.floor(diff / (1000 * 60 * 60))).padStart(2, '0');
                const minutes = String(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                const seconds = String(Math.floor((diff % (1000 * 60)) / 1000)).padStart(2, '0');

                // Display the result in HH:MM:SS format
                document.getElementById('total_hours').value = `${hours}:${minutes}:${seconds}`;
            }
        }

        // Event listeners for input changes
        document.getElementById('start_time').addEventListener('input', calculateTotalHours);
        document.getElementById('end_time').addEventListener('input', calculateTotalHours);
        document.getElementById('break_hours').addEventListener('input', calculateTotalHours);

    </script>

    <script>
        function calculateTotalHours() {
            const startTime = document.getElementById('start_timee').value;
            const endTime = document.getElementById('end_timee').value;
            const breakHours = document.getElementById('break_hourse').value;

            if (startTime && endTime) {
                // Convert start and end times to Date objects
                const start = new Date(`1970-01-01T${startTime}:00`);
                const end = new Date(`1970-01-01T${endTime}:00`);

                // Calculate the difference in milliseconds
                let diff = end - start;

                // Handle negative difference (if end time is before start time, assume it spans to the next day)
                if (diff < 0) {
                    diff += 24 * 60 * 60 * 1000; // add 24 hours in milliseconds
                }

                // If break_hours is provided, subtract it from the diff
                if (breakHours) {
                    const [breakH, breakM, breakS] = breakHours.split(':').map(Number);
                    const breakMillis = ((breakH || 0) * 60 * 60 + (breakM || 0) * 60 + (breakS || 0)) * 1000;

                    // Subtract breakMillis from diff
                    diff -= breakMillis;
                    if (diff < 0) {
                        diff = 0; // Ensure total doesn't go below zero
                    }
                }

                // Calculate hours, minutes, seconds from the diff
                const hours = String(Math.floor(diff / (1000 * 60 * 60))).padStart(2, '0');
                const minutes = String(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                const seconds = String(Math.floor((diff % (1000 * 60)) / 1000)).padStart(2, '0');

                // Display the result in HH:MM:SS format
                document.getElementById('total_hourse').value = `${hours}:${minutes}:${seconds}`;
            }
        }

        // Event listeners for input changes
        document.getElementById('start_timee').addEventListener('input', calculateTotalHours);
        document.getElementById('end_timee').addEventListener('input', calculateTotalHours);
        document.getElementById('break_hourse').addEventListener('input', calculateTotalHours);

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var approveButtons = document.querySelectorAll('.approve-button');
            var declineButtons = document.querySelectorAll('.decline-button');

            approveButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var attId = button.getAttribute('data-attendance-id');
                    confirmApproval(attId);
                });
            });

            declineButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var attId = button.getAttribute('data-attendance-id');
                    confirmDecline(attId);
                });
            });
        });

        function confirmApproval(attId) {
            var form = document.getElementById('approve-form-' + attId);
            var confirmAction = confirm("Are you sure you want to approve this request?");
            if (confirmAction) {
                form.submit();
            }
        }

        function confirmDecline(attId) {
            var form = document.getElementById('decline-form-' + attId);
            var confirmAction = confirm("Are you sure you want to decline this request?");
            if (confirmAction) {
                form.submit();
            }
        }

    </script>


    @endsection
