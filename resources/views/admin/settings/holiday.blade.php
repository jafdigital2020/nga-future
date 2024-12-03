@extends('layouts.settings') @section('title', 'Holiday Setting')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
</script>

<style>

</style>
@section('content')


<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Holidays</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Holidays</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_holiday"><i
                        class="fa fa-plus"></i> Add Holiday</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Holiday Date</th>
                            <th>Day</th>
                            <th>Type</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($holidays as $holiday)
                        <tr>
                            <td>{{ $holiday->title }}</td>
                            <td>{{ $holiday->holidayDate }}</td>
                            <td>{{ $holiday->holidayDay }}</td>
                            <td>{{ $holiday->type }}</td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item" data-toggle="modal"
                                            data-target="#manageUsers_{{ $holiday->id }}">
                                            <i class="fa fa-users m-r-5"></i> Manage Users
                                        </a>
                                        <a href="#" class="dropdown-item edit-holiday" data-toggle="modal"
                                            data-target="#edit_holiday" data-id="{{ $holiday->id }}"
                                            data-title="{{ $holiday->title }}"
                                            data-holidaydate="{{ $holiday->holidayDate }}"
                                            data-holidayday="{{ $holiday->holidayDay }}"
                                            data-type="{{ $holiday->type }}" data-recurring="{{ $holiday->recurring }}">
                                            <i class="fa fa-pencil m-r-5"></i> Edit
                                        </a>

                                        <a href="#" class="dropdown-item" data-toggle="modal"
                                            data-target="#delete_holiday">
                                            <i class="fa fa-trash-o m-r-5"></i> Delete
                                        </a>
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

@foreach($holidays as $holiday)
<div class="modal fade" id="manageUsers_{{ $holiday->id }}" tabindex="-1" role="dialog"
    aria-labelledby="manageUsersLabel_{{ $holiday->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Users for {{ $holiday->title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Assigned Users -->
                <h6>Assigned Users</h6>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($holiday->users as $user)
                        <tr>
                            <td>{{ $user->fName }} {{ $user->lName }}</td>
                            <td>
                                <form
                                    action="{{ route('settings.removeUserFromHoliday', ['holiday' => $holiday->id, 'user' => $user->id]) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Add New Users -->
                <h6 class="mt-4">Add Users</h6>
                <form action="{{ route('settings.addUserToHoliday', ['holiday' => $holiday->id]) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="users_{{ $holiday->id }}">Select Users</label>
                        <select name="users[]" id="users_{{ $holiday->id }}" class="form-control" multiple>
                            @foreach($allUsers as $user)
                            @if(!$holiday->users->contains($user->id))
                            <option value="{{ $user->id }}">{{ $user->fName }} {{ $user->lName }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Add Users</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach


<!-- Add Holiday Modal -->
<div class="modal custom-modal fade" id="add_holiday" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Holiday</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('settings.holidayStore') }}" method="POST">
                    @csrf
                    <div class="container p-4 border rounded shadow-sm bg-light">

                        <!-- Holiday Name -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">Holiday Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" class="form-control"
                                placeholder="Enter holiday name" required>
                        </div>

                        <!-- Holiday Date and Day -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="holidayDate" class="form-label fw-bold">Holiday Date <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="fas fa-calendar-alt"></i></span>
                                    <input type="text" id="holidayDate" name="holidayDate"
                                        class="form-control border-start-0 datetimepicker" placeholder="Select date"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="holidayDay" class="form-label fw-bold">Holiday Day <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="holidayDay" name="holidayDay" class="form-control" readonly
                                    placeholder="Auto-filled day">
                            </div>
                        </div>

                        <!-- Holiday Type -->
                        <div class="my-4">
                            <label for="type" class="form-label fw-bold">Holiday Type <span
                                    class="text-danger">*</span></label>
                            <select id="type" name="type" class="form-select" required>
                                <option value="" disabled selected>Select Type</option>
                                <option value="Regular">Regular</option>
                                <option value="Special">Special</option>
                            </select>
                        </div>

                        <!-- Recurring Checkbox -->
                        <div class="form-check my-3">
                            <input type="checkbox" class="form-check-input" id="recurring" name="recurring">
                            <label for="recurring" class="form-check-label fw-bold">Is this a recurring holiday?
                                (Repeats every year)</label>
                        </div>

                        <!-- Filters for Employees -->
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label for="contractType" class="form-label fw-bold">Contract Type</label>
                                <select id="contractType" class="form-select filter-select">
                                    <option value="">All Contract Types</option>
                                    @foreach($contractTypes as $contractType)
                                    <option value="{{ $contractType }}">{{ $contractType }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="location" class="form-label fw-bold">Location</label>
                                <select id="location" class="form-select filter-select">
                                    <option value="">All Locations</option>
                                    @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->location_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="department" class="form-label fw-bold">Department</label>
                                <select id="department" class="form-select filter-select">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Employee Selection -->
                        <div class="mt-4">
                            <label for="employees" class="form-label fw-bold">Select Employees <span
                                    class="text-danger">*</span></label>
                            <div class="border p-3 rounded bg-white" id="employeeList">
                                <div id="employeeLoading" class="text-center py-3" style="display: none;">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span> Loading employees...
                                </div>
                                <div id="employeeContent">
                                    <div id="employeeCheckboxes">
                                        <!-- Dynamically generated employee checkboxes will appear here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Section -->
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Holiday Modal -->
<div class="modal custom-modal fade" id="edit_holiday" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Holiday</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editHolidayForm" action="{{ route('settings.updateHoliday') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="editHolidayId">

                    <div class="container p-4 border rounded shadow-sm bg-light">
                        <!-- Holiday Title -->
                        <div class="mb-4">
                            <label for="editTitle" class="form-label fw-bold">Holiday Name</label>
                            <input type="text" id="editTitle" name="title" class="form-control" required>
                        </div>

                        <!-- Holiday Date -->
                        <div class="mb-4">
                            <label for="editHolidayDate" class="form-label fw-bold">Holiday Date</label>
                            <input type="date" id="editHolidayDate" name="holidayDate" class="form-control" required>
                        </div>

                        <!-- Holiday Day -->
                        <div class="mb-4">
                            <label for="editHolidayDay" class="form-label fw-bold">Holiday Day</label>
                            <input type="text" id="editHolidayDay" name="holidayDay" class="form-control" readonly>
                        </div>

                        <!-- Holiday Type -->
                        <div class="mb-4">
                            <label for="editType" class="form-label fw-bold">Holiday Type</label>
                            <select id="editType" name="type" class="form-select" required>
                                <option value="Regular">Regular</option>
                                <option value="Special">Special</option>
                            </select>
                        </div>

                        <!-- Recurring -->
                        <div class="form-check mb-4">
                            <input type="checkbox" id="editRecurring" name="recurring" class="form-check-input">
                            <label for="editRecurring" class="form-check-label">Recurring</label>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Holiday Modal -->
<div class="modal custom-modal fade" id="delete_holiday" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Holiday</h3>
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
<!-- /Delete Holiday Modal -->



@endsection

@section('scripts')

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize the datetimepicker
        $('#holidayDate').datetimepicker({
            format: 'YYYY-MM-DD',
            useCurrent: false
        });

        // Event listener for date change
        $('#holidayDate').on('dp.change', function (e) {
            var selectedDate = moment(e.date).format('dddd');
            document.getElementById('holidayDay').value = selectedDate;
        });
    });

</script>

<script>
    function fetchEmployees() {
        const contractType = document.getElementById('contractType').value;
        const location = document.getElementById('location').value;
        const department = document.getElementById('department').value;

        const employeeCheckboxes = document.getElementById('employeeCheckboxes');
        const loadingIndicator = document.getElementById('employeeLoading');
        const content = document.getElementById('employeeContent');

        loadingIndicator.style.display = 'block';
        content.style.display = 'none';

        // Build the query parameters
        const params = new URLSearchParams({
            contractType,
            location,
            department
        });

        fetch("{{ route('settings.HolidayfilterEmployees') }}?" + params)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json(); // Parse JSON response
            })
            .then(data => {
                console.log('Employee Response:', data); // Debug the response

                // Clear the previous employee list
                employeeCheckboxes.innerHTML = '';

                // Populate the new list of employees
                data.forEach(employee => {
                    const checkbox = document.createElement('div');
                    checkbox.className = 'form-check';
                    checkbox.innerHTML = `
                    <input type="checkbox" class="form-check-input" id="employee_${employee.id}" name="employees[]" value="${employee.id}">
                    <label class="form-check-label" for="employee_${employee.id}">
                        ${employee.fName} ${employee.lName}
                    </label>
                `;
                    employeeCheckboxes.appendChild(checkbox);
                });

                loadingIndicator.style.display = 'none';
                content.style.display = 'block';
            })
            .catch(error => {
                console.error('Error Fetching Employees:', error); // Log errors
                alert('Failed to load employees. Please try again.');
            });
    }

    document.querySelectorAll('.filter-select').forEach(select => {
        select.addEventListener('change', fetchEmployees);
    });

    $('#add_holiday').on('show.modal', fetchEmployees);

</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.edit-holiday').forEach(button => {
            button.addEventListener('click', function () {
                console.log('Edit Button Clicked:', this.dataset); // Log all data attributes

                const id = this.dataset.id;
                const title = this.dataset.title;
                const holidayDate = this.dataset.holidaydate;
                const holidayDay = this.dataset.holidayday;
                const type = this.dataset.type;
                const recurring = this.dataset.recurring;

                // Populate modal fields
                document.getElementById('editHolidayId').value = id;
                document.getElementById('editTitle').value = title;
                document.getElementById('editHolidayDate').value = holidayDate;
                document.getElementById('editHolidayDay').value = holidayDay;
                document.getElementById('editType').value = type;
                document.getElementById('editRecurring').checked = recurring == '1' ? true :
                    false;
            });
        });
    });

</script>
@endsection
