@extends('layouts.managermaster') @section('title', 'Attendance')

<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        padding-top: 100px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-dialog {
        margin: 30px auto;
        max-width: 600px;
    }

    .modal-content {
        padding: 20px;
        background-color: #fff;
        border-radius: 5px;
    }
</style>

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Attendance</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ url('manager/dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Attendance Table</li>
                    </ul>
                </div>
                <div class="col-auto float-right ml-auto">
                    <div class="view-icons">
                        <a href="{{ url('manager/attendance/record') }}" class="grid-view btn btn-link"><i
                                class="fa fa-th"></i></a>
                        <a href="{{ url('manager/attendance/tableview') }}" class="list-view btn btn-link active"><i
                                class="fa fa-bars"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        @php
            $currentMonth = date('n'); // Current month as a number (1-12)
            $currentYear = date('Y'); // Current year
            $selectedMonth = old('month', request('month', $currentMonth));
            $selectedYear = old('year', request('year', $currentYear));
        @endphp

        <!-- Search Filter -->
        <form method="GET" action="{{ route('report.manager') }}">
            <div class="row filter-row">
                <div class="col-sm-6 col-md-3">
                    <div class="form-group form-focus">
                        <input type="text" class="form-control floating" name="employee_name">
                        <label class="focus-label">Employee Name</label>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="form-group form-focus select-focus">
                        <select class="select floating" name="department">
                            <option value="">- </option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->department }}"
                                    {{ $dept->department == $departments ? 'selected' : '' }}>
                                    {{ $dept->department }}
                                </option>
                            @endforeach
                        </select>
                        <label class="focus-label">Select Department</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group form-focus select-focus">
                        <select class="select floating" name="month">
                            <option value="">-</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $i == $selectedMonth ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                        <label class="focus-label">Select Month</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group form-focus select-focus">
                        <select class="select floating" name="year">
                            <option value="">-</option>
                            @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}" {{ $i == $selectedYear ? 'selected' : '' }}>
                                    {{ $i }}</option>
                            @endfor
                        </select> <label class="focus-label">Select Year</label>
                    </div>
                </div>
            </div>
            <div class="row filter-row">
                <div class="col-sm-6 col-md-3">
                    <div class="form-group form-focus">
                        <div class="cal-icon">
                            <input type="text" class="datetimepicker form-control floating" name="date"
                                value="{{ $selectedDate ?? date('Y-m-d') }}">
                        </div>
                        <label class="focus-label">Date</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group form-focus">
                        <div class="cal-icon">
                            <input type="text" class="datetimepicker form-control floating" name="start_date"
                                value="">
                        </div>
                        <label class="focus-label">From</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group form-focus">
                        <div class="cal-icon">
                            <input type="text" class="datetimepicker form-control floating" name="end_date"
                                value="">
                        </div>
                        <label class="focus-label">To</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <button type="submit" class="btn btn-primary btn-block"> Search </button>
                </div>
            </div>

        </form>
        <!-- /Search Filter -->

        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    @csrf
                    <table class="table datatable" id="edittable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Status</th>
                                <th>Location</th>
                                <th>Device</th>
                                <th>Total Late</th>
                                <th>Total Hours</th>
                                <th>Clock In Image</th>
                                <th>Clock Out Image</th>
                                <th>Edited By</th>
                                <th>Edit History</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($filteredData as $user)
                                @foreach ($user->employeeAttendance as $attendance)
                                    <tr>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="#" class="avatar">
                                                    @if ($user->image)
                                                        <img src="{{ asset('images/' . $user->image) }}"
                                                            alt="Profile Image" />
                                                    @else
                                                        <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                                    @endif
                                                </a>
                                                <a href="#">{{ $user->fName }} {{ $user->lName }}
                                                    <span>{{ $user->department }}</span></a>
                                            </h2>
                                        </td>
                                        <td>{{ $attendance->date }}</td>
                                        <td>{{ $attendance->timeIn }}</td>
                                        <td>{{ $attendance->timeOut }}</td>
                                        <td>
                                            <span
                                                class="
                                {{ $attendance->status == 'Late'
                                    ? 'bg-danger'
                                    : ($attendance->status == 'On Time'
                                        ? 'bg-success'
                                        : ($attendance->status == 'Edited'
                                            ? 'bg-warning'
                                            : ($attendance->status == 'Undertime'
                                                ? 'bg-info'
                                                : ''))) }}
                            "
                                                style="
                                padding: 5px 10px;
                                border-radius: 5px;
                                font-size: 12px;
                                font-weight: bold;
                                color: white;
                            ">
                                                {{ $attendance->status }}
                                            </span>

                                        </td>
                                        <td>
                                            <a href="#" class="address-link"
                                                data-latitude="{{ $attendance->latitude }}"
                                                data-longitude="{{ $attendance->longitude }}">
                                                View Location
                                            </a>
                                        </td>
                                        <td>{{ $attendance->device }}</td>
                                        <td>{{ $attendance->totalLate }}</td>
                                        <td>{{ $attendance->timeTotal }}</td>
                                        <td>
                                            @if ($attendance->image_path)
                                                <a href="{{ asset('storage/' . $attendance->image_path) }}"
                                                    target="_blank">View
                                                    Photo</a>
                                            @else
                                                No Photo
                                            @endif
                                        </td>
                                        <td>
                                            @if ($attendance->image_path)
                                                <a href="{{ asset('storage/' . $attendance->clock_out_image_path) }}"
                                                    target="_blank">View
                                                    Photo</a>
                                            @else
                                                No Photo
                                            @endif
                                        </td>
                                        <td>
                                            @if ($attendance->edited)
                                                {{ $attendance->edited->fName ?? $attendance->edited->name }}
                                                {{ $attendance->edited->lName ?? '' }}
                                            @else
                                                Not Edited
                                            @endif
                                        </td>
                                        <td>
                                            @if ($attendance->editHistory->count() > 0)
                                                <a href="#" class="bg-danger view-history"
                                                    data-id="{{ $attendance->id }}"
                                                    style="
                                        padding: 8px 12px;
                                        border-radius: 5px;
                                        font-size: 12px;
                                        font-weight: bold;
                                        color: white;
                                    "><i
                                                        class="las la-history"></i>View History</a>
                                            @else
                                                No History
                                            @endif
                                        </td>

                                        <!-- <td class="text-right">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                                    aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item edit-attendance" href="#"
                                                        data-id="{{ $attendance->id }}" data-date="{{ $attendance->date }}"
                                                        data-time_in="{{ $attendance->timeIn }}"
                                                        data-break_in="{{ $attendance->breakIn }}"
                                                        data-break_out="{{ $attendance->breakOut }}"
                                                        data-time_out="{{ $attendance->timeOut }}"
                                                        data-total_hours="{{ $attendance->totalHours }}"
                                                        data-total_late="{{ $attendance->totalLate }}"
                                                        data-device="{{ $attendance->device }}">
                                                        <i class="fa fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item delete-attendance" href="#"
                                                        data-id="{{ $attendance->id }}">
                                                        <i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td> -->
                                    </tr>
                                @endforeach
                            @endforeach

                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="7">Total</th>

                                <th>{{ $totalLate }}</th>
                                <th id="total_hours">{{ $total }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for viewing location -->
    <div id="locationModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Location on Map</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="map" style="width: 100%; height: 400px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit attendance Modal -->
    <!-- <div id="edit_attendance" class="modal custom-modal fade" role="dialog">
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
                                    <label>Date</label>
                                    <input class="form-control" type="text" name="date" id="date" readonly>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Time In <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" name="timeIn" id="timeIn">

                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Time Out <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" name="timeOut" id="timeOut">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Break Out<span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" name="breakIn" id="breakIn">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Break In<span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" name="breakOut" id="breakOut">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Total Late</label>
                                    <input class="form-control" type="text" name="totalLate" id="totalLate">
                                </div>
                                <div class="form-group">
                                    <label>Total Hours</label>
                                    <input class="form-control" type="text" name="totalHours" id="totalHours">
                                </div>
                                <div class="form-group">
                                    <label>Device</label>
                                    <input class="form-control" type="text" name="device" id="device" readonly>
                                </div>
                                <div class="submit-section">
                                    <button type="submit" class="btn btn-primary submit-btn">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div> -->
    <!-- /Edit attendance Modal -->

    <!-- Delete Leave Modal -->
    <div class="modal custom-modal fade" id="delete_approve" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete Attendance</h3>
                        <p>Are you sure you want to cancel this leave?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <div class="row">
                            <div class="col-5">
                                <form id="deleteAttendanceForm" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="attendance_id" id="attendance_id">
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
        // Listen for clicks on the 'View Location' links
        document.querySelectorAll('.address-link').forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();

                // Get the latitude and longitude from the data attributes
                const latitude = this.getAttribute('data-latitude');
                const longitude = this.getAttribute('data-longitude');

                // Open the modal
                $('#locationModal').modal('show');

                // Initialize the map with the given latitude and longitude
                initMap(parseFloat(latitude), parseFloat(longitude));
            });
        });

        // Initialize Google Map inside the modal
        function initMap(lat, lng) {
            // Define the location
            const location = {
                lat: lat,
                lng: lng
            };

            // Create the map centered at the location
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: location
            });

            // Add a marker at the location
            const marker = new google.maps.Marker({
                position: location,
                map: map
            });
        }
    </script>

    <script>
        // Edit attendance request
        $('.edit-attendance').on('click', function() {
            var attId = $(this).data('id');
            var date = $(this).data('date');
            var timeIn = $(this).data('time_in');
            var breakOut = $(this).data('break_out');
            var breakIn = $(this).data('break_in');
            var timeOut = $(this).data('time_out');
            var totalHours = $(this).data('total_hours');
            var totalLate = $(this).data('total_late');
            var device = $(this).data('device');

            if (status === 'Approved') {
                alert('This leave request has already been approved and cannot be edited.');
                return;
            }

            $('#attendance_id').val(attId);
            $('#date').val(date);
            $('#timeIn').val(timeIn);
            $('#breakOut').val(breakOut);
            $('#breakIn').val(breakIn);
            $('#timeOut').val(timeOut);
            $('#totalHours').val(totalHours);
            $('#totalLate').val(totalLate);
            $('#device').val(device);

            $('#editAttendanceForm').attr('action', '/manager/attendance/edit/' +
                attId);
            $('#edit_attendance').modal('show');
        });

        // Delete attendance request
        $('.delete-attendance').on('click', function() {
            var attId = $(this).data('id');


            $('#delete_attendance_id').val(attId);
            $('#deleteAttendanceForm').attr('action', '/manager/attendance/delete/' + attId);
            $('#delete_approve').modal('show');
        });
    </script>

    <script>
        // Initialize date picker
        $('.datetimepicker').datepicker({
            format: 'yyyy-mm-dd', // Match this format to how your database stores dates
            autoclose: true,
            todayHighlight: true
        });
    </script>

@endsection
