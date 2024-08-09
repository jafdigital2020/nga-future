@extends('layouts.hrmaster') @section('title', 'Approved Timesheet') @section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Approved Timesheet</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('hr/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Approved Timesheet</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    <form action="{{ route('approvedTime') }}" method="GET">
        <div class="row filter-row">
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus">
                    <input type="text" class="form-control floating" name="name" value="{{ request('name') }}">
                    <label class="focus-label">Employee Name</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="department">
                        <option value="">--Select Department--</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->department }}"
                            {{ $dept->department == $departments ? 'selected' : '' }}>
                            {{ $dept->department }}
                        </option>
                        @endforeach
                    </select>
                    <label class="focus-label">Select Department</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" id="monthSelect" name="cutoff_period">
                        <option value="0" {{ $cutoffPeriod == '0' ? 'selected' : '' }}>December - January 1st Cut-off
                        </option>
                        <option value="1" {{ $cutoffPeriod == '1' ? 'selected' : '' }}>January 2nd Cut-off</option>
                        <option value="2" {{ $cutoffPeriod == '2' ? 'selected' : '' }}>January - February 1st Cut-off
                        </option>
                        <option value="3" {{ $cutoffPeriod == '3' ? 'selected' : '' }}>February 2nd Cut-off</option>
                        <option value="4" {{ $cutoffPeriod == '4' ? 'selected' : '' }}>February - March 1st Cut-off
                        </option>
                        <option value="5" {{ $cutoffPeriod == '5' ? 'selected' : '' }}>March 2nd Cut-off</option>
                        <option value="6" {{ $cutoffPeriod == '6' ? 'selected' : '' }}>March - April 1st Cut-off
                        </option>
                        <option value="7" {{ $cutoffPeriod == '7' ? 'selected' : '' }}>April 2nd Cut-off</option>
                        <option value="8" {{ $cutoffPeriod == '8' ? 'selected' : '' }}>April - May 1st Cut-off</option>
                        <option value="9" {{ $cutoffPeriod == '9' ? 'selected' : '' }}>May 2nd Cut-off</option>
                        <option value="10" {{ $cutoffPeriod == '10' ? 'selected' : '' }}>May - June 1st Cut-off</option>
                        <option value="11" {{ $cutoffPeriod == '11' ? 'selected' : '' }}>June 2nd Cut-off</option>
                        <option value="12" {{ $cutoffPeriod == '12' ? 'selected' : '' }}>June - July 1st Cut-off
                        </option>
                        <option value="13" {{ $cutoffPeriod == '13' ? 'selected' : '' }}>July 2nd Cut-off</option>
                        <option value="14" {{ $cutoffPeriod == '14' ? 'selected' : '' }}>July - August 1st Cut-off
                        </option>
                        <option value="15" {{ $cutoffPeriod == '15' ? 'selected' : '' }}>August 2nd Cut-off</option>
                        <option value="16" {{ $cutoffPeriod == '16' ? 'selected' : '' }}>August - September 1st Cut-off
                        </option>
                        <option value="17" {{ $cutoffPeriod == '17' ? 'selected' : '' }}>September 2nd Cut-off</option>
                        <option value="18" {{ $cutoffPeriod == '18' ? 'selected' : '' }}>September - October 1st Cut-off
                        </option>
                        <option value="19" {{ $cutoffPeriod == '19' ? 'selected' : '' }}>October 2nd Cut-off</option>
                        <option value="20" {{ $cutoffPeriod == '20' ? 'selected' : '' }}>October - November 1st Cut-off
                        </option>
                        <option value="21" {{ $cutoffPeriod == '21' ? 'selected' : '' }}>November 2nd Cut-off</option>
                        <option value="22" {{ $cutoffPeriod == '22' ? 'selected' : '' }}>November - December 1st Cut-off
                        </option>
                        <option value="23" {{ $cutoffPeriod == '23' ? 'selected' : '' }}>December 2nd Cut-off</option>
                    </select>

                    <label class="focus-label">Cut-off Period</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="year">
                        <option value="">--Select Year--</option>
                        <option value="{{ $selectedYear }}" selected>{{ $selectedYear }}</option>
                        <!-- Add more options if needed -->
                    </select>
                    <label class="focus-label">Year</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="status">
                        <option value="Approved" {{ old('status', $status) == 'Approved' ? 'selected' : '' }}>Approved
                        </option>
                        <option value="pending" {{ old('status', $status) == 'pending' ? 'selected' : '' }}>Pending
                        </option>
                        <option value="Payslip" {{ old('status', $status) == 'Payslip' ? 'selected' : '' }}>Payslip
                        </option>
                    </select>
                    <label class="focus-label">Status</label>
                </div>
            </div>


            <div class="col-sm-6 col-md-2 col-lg-2 col-xl-2 col-12">
                <button type="submit" class="btn btn-primary btn-block">Search</button>
            </div>
        </div>
    </form>

    <!-- /Search Filter -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-hover table-nowrap mb-0 datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Month</th>
                            <th>Cut-Off</th>
                            <th>Total Late</th>
                            <th>Total Hours</th>
                            <th>Approved By</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($approved as $app)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="{{ url('hr/employee/edit/'.$app->user->id) }}" class="avatar">
                                        @if ($app->user->image)
                                        <img src="{{ asset('images/' . $app->user->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{
                                            asset('images/default.png')
                                        }}" alt="Profile Image" />
                                        @endif</a>
                                    <a href="{{ url('hr/employee/edit/'.$app->user->id) }}">
                                        @if($app->user->fName || $app->user->mName || $app->user->lName)
                                        {{ $app->user->fName ?? '' }}
                                        {{ $app->user->mName ?? '' }}
                                        {{ $app->user->lName ?? '' }}
                                        @else
                                        {{ $app->user->name }}
                                        @endif
                                        <span>{{ $app->user->position }}</span>
                                    </a>
                                </h2>
                            </td>
                            <td>{{ $app->user->department }}</td>
                            <td>{{ $app->start_date }}</td>
                            <td>{{ $app->end_date }}</td>
                            <td>{{ $app->month }}</td>
                            <td>{{ $app->cut_off }}</td>
                            <td>{{ $app->totalLate }}</td>
                            <td>{{ $app->totalHours }}</td>
                            <td>{{ $app->approved_by }}</td>
                            <td>{{ $app->status }}</td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ url('hr/payroll/edit/'.$app->id) }}"><i
                                                class="fa fa-money m-r-5"></i>Payroll</a>
                                        <a class="dropdown-item edit-attendance" href="#" data-id="{{ $app->id }}"
                                            data-department="{{ $app->user->department }}"
                                            data-start_date="{{ $app->start_date }}"
                                            data-end_date="{{ $app->end_date }}" data-months="{{ $app->month }}"
                                            data-cut_off="{{ $app->cut_off }}" data-totalLate="{{ $app->totalLate }}"
                                            data-totalhours="{{ $app->totalHours }}"
                                            data-approved_by="{{ $app->approved_by }}">
                                            <i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item delete-attendance" href="#" data-id="{{ $app->id }}">
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

<!-- Edit  Approved Attendance Modal -->
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
                    <input type="hidden" name="attendance_id" id="attendance_id">
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" class="form-control" name="department" id="department" readonly>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>From <span class="text-danger">*</span></label>
                            <div class="cal-icon">
                                <input class="form-control datetimepicker" type="text" name="start_date" id="start_date"
                                    required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>To <span class="text-danger">*</span></label>
                            <div class="cal-icon">
                                <input class="form-control datetimepicker" type="text" name="end_date" id="end_date"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Month</label>
                        <input type="text" class="form-control" name="months" id="months" readonly>
                    </div>
                    <div class="form-group">
                        <label>Cut Off</label>
                        <input type="text" class="form-control" name="cut_off" id="cut_off" readonly>
                    </div>
                    <div class="form-group">
                        <label>Total Late</label>
                        <input type="text" class="form-control" name="total_late" id="total_late">
                    </div>
                    <div class="form-group">
                        <label>Total Hours</label>
                        <input type="text" class="form-control" name="total_hours" id="total_hours">
                    </div>
                    <div class="form-group">
                        <label>Approved By</label>
                        <input type="text" class="form-control" name="approved_by" id="approved_by" readonly>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Approved Attendance Modal -->

<!-- Delete Attendance Modal -->
<div class="modal custom-modal fade" id="delete_approve" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Attendance</h3>
                    <p>Are you sure you want to delete this?</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-5">
                            <form id="deleteAttendanceForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="attendance_id" id="delete_attendance_id">
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
<!-- /Delete Attendance Modal -->


@endsection

@section('scripts')

<script>
    $(document).ready(function () {
        // Edit Approved Attendance
        $('.edit-attendance').on('click', function () {
            var attendanceId = $(this).data('id');
            var department = $(this).data('department');
            var startDate = $(this).data('start_date');
            var endDate = $(this).data('end_date');
            var months = $(this).data('months');
            var cutOff = $(this).data('cut_off');
            var totalLate = $(this).data('totallate');
            var totalHours = $(this).data('totalhours');
            var approvedBy = $(this).data('approved_by');

            $('#attendance_id').val(attendanceId);
            $('#department').val(department);
            $('#start_date').val(startDate);
            $('#end_date').val(endDate);
            $('#months').val(months);
            $('#cut_off').val(cutOff);
            $('#total_late').val(totalLate)
            $('#total_hours').val(totalHours);
            $('#approved_by').val(approvedBy);

            $('#editAttendanceForm').attr('action', '/hr/approve/update/' + attendanceId);
            $('#edit_attendance').modal('show');
        });

        // Delete attendance request
        $('.delete-attendance').on('click', function () {
            var attendanceId = $(this).data('id');

            $('#delete_attendance_id').val(attendanceId);
            $('#deleteAttendanceForm').attr('action', '/hr/approve/delete/' + attendanceId);
            $('#delete_approve').modal('show');
        });
    });

</script>


@endsection
