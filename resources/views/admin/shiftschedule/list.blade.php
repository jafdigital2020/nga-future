@extends('layouts.master') @section('title', 'Shift List')

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Shift List</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('admin/employee') }}">Employees</a></li>
                    <li class="breadcrumb-item active">Shift List</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn m-r-5" data-toggle="modal" data-target="#add_shift">Add
                    Shifts</a>
                <a href="#" class="btn add-btn m-r-5" data-toggle="modal" data-target="#add_schedule">
                    Assign Shifts</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Content Starts -->
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Shift Name</th>
                            <th>Start Time</th>
                            <th>Late Threshold</th>
                            <th>End Time</th>
                            <th>Break Time</th>
                            <th class="text-right no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach($shiftlist as $list)
                            <td>{{ $list->id }}</td>
                            <td>{{ $list->shift_name }}</td>
                            <td>{{ $list->start_time }}</td>
                            <td>{{ $list->late_threshold }}</td>
                            <td>{{ $list->end_time }}</td>
                            <td>{{ $list->break_time }}mins.</td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#edit_shift"><i class="fa fa-pencil m-r-5"></i>
                                            Edit</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#delete_employee"><i class="fa fa-trash-o m-r-5"></i>
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
    <!-- /Content End -->

</div>
<!-- /Page Content -->


<!-- Add Shift Modal -->
<div id="add_shift" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Shift</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.shiftadd') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Shift Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="shift_name" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="start_time" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Late Threshold <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="late_threshold" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>End Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="end_time" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Break Time (In Minutes) </label>
                                <input type="number" class="form-control" name="break_time">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck1" name="recurring">
                                <label class="custom-control-label" for="customCheck1">Recurring Shift</label>
                            </div>
                        </div>


                        <div class="col-sm-12">
                            <div class="form-group wday-box">
                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']
                                as $day)
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="days[]" value="{{ $day }}" class="days recurring">
                                    <span class="checkmark">{{ strtoupper(substr($day, 0, 1)) }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- /Add Shift Modal -->

<!-- Edit Shift Modal -->
<div id="edit_shift" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Shift</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Shift Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="shift_name" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="start_time" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Late Threshold <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="late_threshold" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>End Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="end_time" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Break Time (In Minutes) </label>
                                <input type="number" class="form-control" name="break_time">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck1" name="recurring">
                                <label class="custom-control-label" for="customCheck1">Recurring Shift</label>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Repeat Every</label>
                                <select class="form-control" name="repeat_every">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                </select>
                                <label class="col-form-label">Week(s)</label>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group wday-box">
                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']
                                as $day)
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="days[]" value="{{ $day }}" class="days recurring">
                                    <span class="checkmark">{{ strtoupper(substr($day, 0, 1)) }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">End On</label>
                                <div class="cal-icon">
                                    <input type="text" class="form-control floating datetimepicker" name="end_on">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck2" name="indefinite">
                                <label class="custom-control-label" for="customCheck2">Indefinite</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Tag </label>
                                <input type="text" class="form-control" name="tag">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Note </label>
                                <textarea class="form-control" name="note"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Shift Modal -->

<!-- Assign List Schedule Modal -->
<div id="add_schedule" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.assignShiftList') }}" method="POSt">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-control" name="department" id="department-select">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Shifts <span class="text-danger">*</span></label>
                                <select class="form-control" name="shift_id">
                                    <option value="">Select Shift</option>
                                    @foreach ($shiftlist as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->shift_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Employee Name <span class="text-danger">*</span></label>
                                <select class="form-control picker" name="users_id[]" id="employee-select" multiple>
                                    <option value="">Select User</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Assign Schedule Modal -->

<!-- Delete Shift Modal -->
<div class="modal custom-modal fade" id="delete_employee" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Shift</h3>
                    <p>Are you sure want to delete?</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-6">
                            <a href="javascript:void(0);" class="btn btn-primary continue-btn">Delete</a>
                        </div>
                        <div class="col-6">
                            <a href="javascript:void(0);" data-dismiss="modal"
                                class="btn btn-primary cancel-btn">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Employee Modal -->

@endsection

@section('scripts')



<script>
    $(document).ready(function () {
        // When a department is selected
        $('#department-select').change(function () {
            var department = $(this).val(); // Get selected department

            // Clear previous employee options
            $('#employee-select').empty().append('<option value="">Select User</option>');

            if (department) {
                $.ajax({
                    url: '{{ route("getEmployeesByDepartment") }}',
                    method: 'GET',
                    data: {
                        department: department
                    },
                    success: function (data) {
                        // Populate employee select with the data from the server
                        $.each(data, function (key, employee) {
                            $('#employee-select').append('<option value="' +
                                employee.id + '">' + employee.name + '</option>'
                            );
                        });
                    },
                    error: function () {
                        alert('Unable to fetch employees.');
                    }
                });
            }
        });
    });

</script>

@endsection
