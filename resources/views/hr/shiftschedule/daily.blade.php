@extends('layouts.hrmaster') @section('title', 'Daily Scheduling')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Daily Scheduling</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('hr/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('hr/employee') }}">Employees</a></li>
                    <li class="breadcrumb-item active">Shift Scheduling</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <!-- <a href="{{ url('admin/shift/list') }}" class="btn add-btn m-r-5">Shifts</a> -->
                <a href="#" class="btn add-btn m-r-5" data-toggle="modal" data-target="#assign_schedule"> Assign
                    Shifts</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Content Starts -->
    <!-- Search Filter -->
    <form action="{{ route('hr.dailyShift') }}" method="GET">
        <div class="row filter-row">
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus">
                    <input type="text" class="form-control floating" name="name">
                    <label class="focus-label">Employee</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="department">
                        <option value="">All Departments</option>
                        @foreach ($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                    <label class="focus-label">Department</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus focused">
                    <div class="cal-icon">
                        <input class="form-control floating datetimepicker" type="text" name="startDate">
                    </div>
                    <label class="focus-label">From</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus focused">
                    <div class="cal-icon">
                        <input class="form-control floating datetimepicker" type="text" name="endDate">
                    </div>
                    <label class="focus-label">To</label>
                </div>
            </div>

            <div class="col-sm-6 col-md-2">
                <button type="submit" class="btn btn-danger btn-block"> Search </button>
            </div>
        </div>
    </form>
    <!-- Search Filter -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table datatable">
                    <thead>
                        <tr>
                            <th>Scheduled Shift</th>
                            @foreach($dates as $date)
                            <th>{{ $date->format('M d D') }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="{{ url('hr/employee/edit/'. $user->id) }}" class="avatar"><img alt=""
                                            src="{{ asset('images/' . $user->image) }}"></a>
                                    <a href="{{ url('hr/employee/edit/'. $user->id) }}">{{ $user->fName }}
                                        {{ $user->lName }}
                                        <span>{{ $user->role }}</span></a>
                                </h2>
                            </td>
                            @foreach($dates as $date)
                            <td>
                                @php
                                $schedule = $user->shiftSchedule->firstWhere('date', $date->format('Y-m-d'));
                                @endphp
                                @if($schedule)
                                <div class="user-add-shedule-list">
                                    <h2>
                                        <a href="#" data-toggle="modal" data-target="#edit_schedule"
                                            data-date="{{ $date->format('Y-m-d') }}" data-id="{{ $user->id }}"
                                            data-department="{{ $user->department }}" data-name="{{ $user->name }}"
                                            data-allowed-hours="{{ $schedule->allowedHours }}"
                                            data-shift-start="{{ \Carbon\Carbon::parse($schedule->shiftStart)->format('H:i') }}"
                                            data-late-threshold="{{ \Carbon\Carbon::parse($schedule->lateThreshold)->format('H:i') }}"
                                            data-shift-end="{{ \Carbon\Carbon::parse($schedule->shiftEnd)->format('H:i') }}"
                                            data-break-time="{{ $schedule->break_time }}"
                                            data-shift-id="{{ $schedule->id }}"
                                            data-is-flexible-time="{{ $schedule->isFlexibleTime ? 'true' : 'false' }}">
                                            <span class="username-info m-b-10">
                                                @if ($schedule->isFlexibleTime)
                                                Flexible Time
                                                ({{ $schedule->allowedHours ?? 'No allowed hours is set' }})

                                                @else
                                                {{ \Carbon\Carbon::parse($schedule->shiftStart)->format('h:i:s A') }} -
                                                {{ \Carbon\Carbon::parse($schedule->shiftEnd)->format('h:i:s A') }}
                                                ({{ $schedule->allowedHours }} hours)
                                                @endif
                                            </span>
                                            <span class="userrole-info">{{ $user->department }} -
                                                {{ $user->position }} </span>
                                        </a>
                                        <!-- Delete Button placed after the user role info -->
                                        <!-- <form action="" method="POST" class="d-inline" style="margin-left: 10px;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this schedule?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form> -->
                                    </h2>
                                </div>

                                @else
                                <div class="user-add-shedule-list">
                                    <a href="#" class="open-add-schedule-modal" data-id="{{ $user->id }}"
                                        data-date="{{ $date->format('Y-m-d') }}"
                                        data-department="{{ $user->department }}" data-fname="{{ $user->fName }}"
                                        data-lname="{{ $user->lName }}" data-toggle="modal" data-target="#add_schedule">
                                        <span><i class="fa fa-plus"></i> Add Shift</span>
                                    </a>
                                </div>
                                @endif
                            </td>
                            @endforeach
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


<!-- Add Schedule in Table Modal -->
<div id="add_schedule" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Daily Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('hr.dailyschedule') }}" method="POST">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="users_id" value="">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Department <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="department" id="department" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Employee Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" id="name" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Date</label>
                                <div class="cal-icon">
                                    <input class="form-control datetimepicker" type="text" name="date" id="date"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="" class="col-form-label">Allowed hours</label>
                                <input type="text" class="form-control" id="allowedHours" name="allowedHours"
                                    value="{{ $shift->allowedHours ?? '' }}" placeholder="HH:MM:SS"
                                    pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" required>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Start Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control" type="time" name="shiftStart" id="shiftStart"><span
                                        class="input-group-append input-group-addon">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Late Threshold<span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control" type="time" name="lateThreshold" id="lateThreshold">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">End Time<span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control" type="time" name="shiftEnd" id="shiftEnd">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Break Time (in minutes) <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="break_time" id="break_time" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Flexible Time</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" onchange="toggleShiftFields(this, 'add')"
                                        class="custom-control-input" id="flexibleTime" name="flexibleTime" value="1">
                                    <label class="custom-control-label" for="flexibleTime"></label>
                                </div>

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
<!-- /Add Schedule in Table Modal -->

<!-- Assign Shift -->
<div id="assign_schedule" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('hr.assignschedule') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Department <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="department" id="department">

                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Employee Name <span class="text-danger">*</span></label>
                                <select class="form-control selectpicker" name="users_id[]" id="users_id" multiple
                                    data-live-search="true">
                                    @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->fName }} {{ $user->lName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Date</label>
                                <div class="cal-icon">
                                    <input class="form-control" type="text" id="flatpickr"
                                        placeholder="Select multiple dates" />
                                    <input type="hidden" id="dates-hidden" name="dates[]" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="" class="col-form-label">Allowed hours</label>
                                <input type="text" class="form-control" id="allowedHours" name="allowedHours"
                                    value="{{ $shift->allowedHours ?? '' }}" placeholder="HH:MM:SS"
                                    pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" required>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Start Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control" type="time" name="shiftStart" id="shiftStart"><span
                                        class="input-group-append input-group-addon">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Late Threshold<span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control" type="time" name="lateThreshold" id="lateThreshold">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">End Time<span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control" type="time" name="shiftEnd" id="shiftEnd">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Break Time (in minutes) <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="break_time" id="break_time" required>
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
                                <label class="col-form-label">Flexible Time</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" onchange="toggleShiftFields(this, 'add')"
                                        class="custom-control-input" id="flexibleTime" name="flexibleTime" value="1">
                                    <label class="custom-control-label" for="flexibleTime"></label>
                                </div>

                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck1" name="recurring">
                                <label class="custom-control-label" for="customCheck1">Recurring Shift</label>
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
<!-- /Assign Shift -->

<!-- Edit Schedule Modal -->
<div id="edit_schedule" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Daily Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editScheduleForm" method="POST">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="users_ide" id="users_ide" value="">
                        <input type="text" name="shift_id" id="shift_id" value="">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Department <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="department" id="departmente" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Employee Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" id="namee" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Date</label>
                                <div class="cal-icon">
                                    <input class="form-control datetimepicker" type="text" name="date" id="date"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="" class="col-form-label">Allowed hours</label>
                                <input type="text" class="form-control" id="allowedHourse" name="allowedHours"
                                    value="{{ $shift->allowedHours ?? '' }}" placeholder="HH:MM:SS"
                                    pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Start Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control" type="time" name="shiftStart" id="shiftStarte"><span
                                        class="input-group-append input-group-addon">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Late Threshold<span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control" type="time" name="lateThreshold" id="lateThresholde">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">End Time<span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control" type="time" name="shiftEnd" id="shiftEnde">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Break Time (in minutes) <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="break_time" id="break_time">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Flexible Time</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" onchange="toggleShiftFields(this, 'edit')"
                                        class="custom-control-input" id="flexibleTimee" name="flexibleTime" value="1">
                                    <label class="custom-control-label" for="flexibleTimee"></label>
                                </div>

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
<!-- /Edit Schedule Modal -->

@endsection

@section('scripts')

<!-- Allowed Hours -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to get the URL parameter
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            const results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        // Get the tab parameter from the URL
        const tab = getUrlParameter('tab');

        if (tab) {
            // Find the tab link with the corresponding href attribute
            const tabLink = document.querySelector(`a[href="#${tab}"]`);

            if (tabLink) {
                // Activate the tab using Bootstrap's tab plugin
                $(tabLink).tab('show');
            }
        }
    });

</script>

<!-- Add daily schedule in table -->

<script>
    $(document).ready(function () {
        // When the "Add Shift" button is clicked
        $('.open-add-schedule-modal').on('click', function () {
            // Get the user ID and the date from the button's data attributes
            var userId = $(this).data('id');
            var selectedDate = $(this).data('date');
            var department = $(this).data('department');
            var fName = $(this).data('fname');
            var lName = $(this).data('lname');

            var fullName = fName + ' ' + lName;

            // Set the user ID and the date in the modal's respective input fields
            $('#add_schedule').find('input[name="users_id"]').val(userId);
            $('#add_schedule').find('input[name="department"]').val(department);
            $('#add_schedule').find('input[name="name"]').val(fullName);
            $('#add_schedule').find('input.datetimepicker').val(selectedDate);
        });
    });

</script>

<!-- Flexible Time Toggle -->

<script>
    function toggleShiftFields(checkbox, modalType) {
        // Determine the input fields based on the modal type
        let shiftStart, lateThreshold, shiftEnd;

        if (modalType === 'edit') {
            // For the edit modal
            shiftStart = $('#shiftStarte');
            lateThreshold = $('#lateThresholde');
            shiftEnd = $('#shiftEnde');
        } else {
            // For the add modal
            shiftStart = $('#shiftStart');
            lateThreshold = $('#lateThreshold');
            shiftEnd = $('#shiftEnd');
        }

        // Toggle the disabled property based on the checkbox state
        const isDisabled = $(checkbox).is(':checked');
        shiftStart.prop('disabled', isDisabled);
        lateThreshold.prop('disabled', isDisabled);
        shiftEnd.prop('disabled', isDisabled);

        // Optionally, clear the values of the disabled fields
        if (isDisabled) {
            shiftStart.val('');
            lateThreshold.val('');
            shiftEnd.val('');
        }
    }

</script>


<!-- Daily Schedule Edit -->

<script>
    $(document).ready(function () {
        $('#edit_schedule').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var userId = button.data('id'); // Extract info from data-* attributes
            var department = button.data('department');
            var name = button.data('name');
            var allowedHours = button.data('allowed-hours');
            var shiftStart = button.data('shift-start');
            var lateThreshold = button.data('late-threshold');
            var shiftEnd = button.data('shift-end');
            var breakTime = button.data('break-time');
            var flexibleTime = button.data('flexible-time');
            var shiftId = button.data('shift-id'); // Get the shift ID

            // Update the modal's content
            var modal = $(this);
            modal.find('input[name="users_ide"]').val(userId);
            modal.find('#departmente').val(department);
            modal.find('#namee').val(name);
            modal.find('#date').val(button.data('date'));
            modal.find('#allowedHourse').val(allowedHours);
            modal.find('#shiftStarte').val(shiftStart);
            modal.find('#lateThresholde').val(lateThreshold);
            modal.find('#shiftEnde').val(shiftEnd);
            modal.find('#break_time').val(breakTime);

            // Set the flexible time checkbox
            modal.find('#flexibleTimee').prop('checked', flexibleTime ===
                true); // Check if flexible time is checked

            // Disable input fields based on flexible time status
            if (flexibleTime) {
                modal.find('#shiftStarte').prop('disabled', true);
                modal.find('#lateThresholde').prop('disabled', true);
                modal.find('#shiftEnde').prop('disabled', true);
            } else {
                modal.find('#shiftStarte').prop('disabled', false);
                modal.find('#lateThresholde').prop('disabled', false);
                modal.find('#shiftEnde').prop('disabled', false);
            }

            $('#editScheduleForm').attr('action', '/hr/shift/daily/edit/' + shiftId);

        });
    });

</script>


<script>
    document.getElementById('allowedHours').addEventListener('input', function (e) {
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
    });

</script>

<script>
    flatpickr("#flatpickr", {
        mode: "multiple", // Allow multiple dates
        dateFormat: "Y-m-d", // Format to Y-m-d to match the server's expected format
        onChange: function (selectedDates, dateStr, instance) {
            // Join the dates into a single string and set it to the hidden input
            document.getElementById('dates-hidden').value = selectedDates.map(function (date) {
                return instance.formatDate(date, 'Y-m-d');
            }).join(',');
        }
    });

</script>


@endsection