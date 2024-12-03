@extends('layouts.managermaster') @section('title', 'User Geofence')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">User Geofence</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('manager/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">User Geofence</a></li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn m-r-5" data-toggle="modal" data-target="#assign_geofence"> Assign
                    Geofence</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Content Starts -->
    <!-- Search Filter -->
    <form action="" method="GET">
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
                        <option value="" disabled selected>Select Department</option>
                        @foreach ($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                    <label class="focus-label">Department</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="earning">
                        <option value="" disabled selected>Select Geofence</option>
                        @foreach ($geofences as $geofence)
                        <option value="{{ $geofence->id }}">{{ $geofence->fencing_name }}</option>
                        @endforeach
                    </select>
                    <label class="focus-label">Geofence</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
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
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Geofence Name</th>
                            <th>Address</th>
                            <th>Radius(meters)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userGeofences as $user)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="{{ url('manager/department-record/'. $user->user->id) }}"
                                        class="avatar"><img alt=""
                                            src="{{ asset('images/' . $user->user->image) }}"></a>
                                    <a href="{{ url('manager/department-record/'. $user->user->id) }}">{{ $user->user->fName }}
                                        {{ $user->user->lName }}
                                        <span>{{ $user->user->position }}</span></a>
                                </h2>
                            </td>
                            <td>{{ $user->user->department }}</td>
                            <td>{{ $user->geofenceSetting->fencing_name }}</td>
                            <td>{{ $user->geofenceSetting->fencing_address }}</td>
                            <td>{{ $user->geofenceSetting->fencing_radius }}</td>
                            <td> <button class="btn btn-danger delete-geofence" data-id="{{ $user->id }}">
                                    <i class="fa fa-trash"></i>
                                </button></td>
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

<!-- Assign Geofence -->
<div id="assign_geofence" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Geofence</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('manager.storeUserGeofence') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label">Employee Name <span class="text-danger">*</span></label>
                        <select class="form-control selectpicker" name="user_id[]" id="user_id" multiple
                            data-live-search="true">
                            @foreach ($subordinates as $subordinate)
                            <option value="{{ $subordinate->id }}">{{ $subordinate->fName }} {{ $subordinate->lName }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="geofence-container">
                        <div class="row row-sm geofence-row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Geofence</label>
                                    <select name="geofence_id[]" class="form-control geofence-select" required>
                                        <option value="">Select Geofence</option>
                                        @foreach ($geofences as $geofence)
                                        <option value="{{ $geofence->id }}"
                                            data-location="{{ $geofence->fencing_address }}"
                                            data-radius="{{ $geofence->fencing_radius }}">
                                            {{ $geofence->fencing_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Location</label>
                                    <input class="form-control location-input" type="text" name="location[]" readonly>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Radius</label>
                                    <input class="form-control radius-input" type="text" name="radius[]" readonly>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <label class="d-none d-sm-block">&nbsp;</label>
                                    <button class="btn btn-success btn-block set-btn add-more" type="button"><i
                                            class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            <!-- <div class="col-sm-1">
                                <div class="form-group">
                                    <label class="d-none d-sm-block">&nbsp;</label>
                                    <button class="btn btn-danger btn-block set-btn remove-row" type="button"><i
                                            class="fa fa-trash-o"></i></button>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary">Assign Geofence</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Assign Geofence -->

@endsection

@section('scripts')

<script>
    $(document).ready(function () {
        // Use event delegation for dynamically rendered rows
        $(document).on('click', '.delete-geofence', function () {
            var geoId = $(this).data('id');

            if (confirm('Are you sure you want to delete this geofence for this user?')) {
                $.ajax({
                    url: '/manager/geofencing/assign/delete/' + geoId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}' // Include CSRF token
                    },
                    success: function (response) {
                        if (response.success) {
                            // Remove the row from the table
                            $('button[data-id="' + geoId + '"]').closest('tr').remove();
                            alert(response.message); // Show success message
                        } else {
                            alert('Error deleting geofence: ' + response.message);
                        }
                    },
                    error: function (xhr) {
                        alert('Error deleting geofence: ' + xhr.responseText);
                    }
                });
            }
        });
    });

</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const geofenceContainer = document.getElementById('geofence-container');
        let selectedGeofences = new Set(); // Track selected geofence IDs

        // Function to create a new geofence row
        function createGeofenceRow() {
            const row = document.createElement('div');
            row.classList.add('row', 'row-sm', 'geofence-row');
            row.innerHTML = `
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Geofence</label>
                    <select name="geofence_id[]" class="form-control geofence-select" required>
                        <option value="">Select Geofence</option>
                        @foreach ($geofences as $geofence)
                            <option value="{{ $geofence->id }}" data-location="{{ $geofence->fencing_address }}"
                                    data-radius="{{ $geofence->fencing_radius }}">
                                {{ $geofence->fencing_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Location</label>
                    <input class="form-control location-input" type="text" name="location[]" readonly>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Radius</label>
                    <input class="form-control radius-input" type="text" name="radius[]" readonly>
                </div>
            </div>
            <div class="col-sm-1">
                <div class="form-group">
                    <label class="d-none d-sm-block">&nbsp;</label>
                    <button class="btn btn-success btn-block set-btn add-more" type="button"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="col-sm-1">
                <div class="form-group">
                    <label class="d-none d-sm-block">&nbsp;</label>
                    <button class="btn btn-danger btn-block set-btn remove-row" type="button"><i class="fa fa-trash-o"></i></button>
                </div>
            </div>
        `;
            return row;
        }

        // Handle geofence selection and populate location and radius
        function handleGeofenceChange(select) {
            const selectedOption = select.options[select.selectedIndex];
            const geofenceRow = select.closest('.geofence-row');
            const locationInput = geofenceRow.querySelector('.location-input');
            const radiusInput = geofenceRow.querySelector('.radius-input');

            // Populate location and radius based on selected geofence
            if (selectedOption) {
                locationInput.value = selectedOption.getAttribute('data-location') || '';
                radiusInput.value = selectedOption.getAttribute('data-radius') || '';
            }

            // Update selected geofences and dropdown options
            updateSelectedGeofences();
            updateDropdownOptions();
        }

        // Track selected geofences to ensure uniqueness
        function updateSelectedGeofences() {
            selectedGeofences.clear();
            const selectElements = geofenceContainer.querySelectorAll('.geofence-select');
            selectElements.forEach(select => {
                const value = select.value;
                if (value) {
                    selectedGeofences.add(value);
                }
            });
        }

        // Update dropdown options to disable already selected geofences
        function updateDropdownOptions() {
            const selectElements = geofenceContainer.querySelectorAll('.geofence-select');
            selectElements.forEach(select => {
                const currentValue = select.value;
                Array.from(select.options).forEach(option => {
                    if (selectedGeofences.has(option.value) && option.value !== currentValue) {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                });
            });
        }

        // Add a new geofence row
        function addNewRow() {
            const newRow = createGeofenceRow();
            geofenceContainer.appendChild(newRow);
            updateDropdownOptions();
        }

        // Event delegation for adding and removing rows
        geofenceContainer.addEventListener('click', function (event) {
            if (event.target.closest('.add-more')) {
                addNewRow();
            }

            if (event.target.closest('.remove-row')) {
                const geofenceRow = event.target.closest('.geofence-row');
                if (geofenceRow) {
                    geofenceRow.remove();
                    updateSelectedGeofences();
                    updateDropdownOptions();
                }
            }
        });

        // Event delegation for geofence select change
        geofenceContainer.addEventListener('change', function (event) {
            if (event.target.classList.contains('geofence-select')) {
                handleGeofenceChange(event.target);
            }
        });
    });

</script>

@endsection
