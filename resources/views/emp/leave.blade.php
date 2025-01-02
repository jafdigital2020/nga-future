@extends('layouts.empmaster') @section('title', 'Leave')

<style>
    /* From Uiverse.io by KSAplay */
    .loader {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 5px;
    }

    .loading-text {
        color: black;
        font-size: 14pt;
        font-weight: 600;
        margin-left: 10px;
    }

    .dot {
        margin-left: 3px;
        animation: blink 1.5s infinite;
    }

    .dot:nth-child(2) {
        animation-delay: 0.3s;
    }

    .dot:nth-child(3) {
        animation-delay: 0.6s;
    }

    .loading-bar-background {
        --height: 30px;
        display: flex;
        align-items: center;
        box-sizing: border-box;
        padding: 5px;
        width: 200px;
        height: var(--height);
        background-color: #212121;
        /* Black background */
        box-shadow: #0c0c0c -2px 2px 4px 0px inset;
        border-radius: calc(var(--height) / 2);
    }

    .loading-bar {
        position: relative;
        display: flex;
        justify-content: center;
        flex-direction: column;
        --height: 20px;
        width: 0%;
        height: var(--height);
        overflow: hidden;
        background: rgb(206, 46, 46);
        /* Red gradient */
        background: linear-gradient(0deg,
                rgba(206, 46, 46, 1) 0%,
                rgba(249, 199, 79, 1) 100%);
        border-radius: calc(var(--height) / 2);
        animation: loading 4s ease-out infinite;
    }

    .white-bars-container {
        position: absolute;
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .white-bar {
        background: rgb(255, 255, 255);
        background: linear-gradient(-45deg,
                rgba(255, 255, 255, 1) 0%,
                rgba(255, 255, 255, 0) 70%);
        width: 10px;
        height: 45px;
        opacity: 0.3;
        rotate: 45deg;
    }

    @keyframes loading {
        0% {
            width: 0;
        }

        80% {
            width: 100%;
        }

        100% {
            width: 100%;
        }
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 0;
        }

        50% {
            opacity: 1;
        }
    }
</style>
@section('content')

    <!-- Page Content -->
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Leaves</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('emp/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Leaves</li>
                    </ul>
                </div>
                <div class="col-auto float-right ml-auto">
                    <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_leave"><i
                            class="fa fa-plus"></i>
                        Request Leave</a>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Leave Statistics -->
        <div class="row">
            <div class="col-md-4">
                <div class="stats-info">
                    <h6>Pending</h6>
                    <h4>{{ $pendingRequest }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-info">
                    <h6>Declined</h6>
                    <h4>{{ $declineRequest }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-info">
                    <h6>Available Leave</h6>
                    <h4>
                        <a href="#" id="leaveCreditsLink">{{ $totalLeaveCredits }}</a>
                    </h4>
                </div>
            </div>
        </div>
        <!-- /Leave Statistics -->

        <!-- Leave Credits Modal -->
        <div id="leaveCreditsModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Available Leave Credits</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group">
                            @foreach ($leaveCredits as $leaveCredit)
                                <li class="list-group-item">
                                    {{ $leaveCredit->leaveType->leaveType }}:
                                    <strong>{{ $leaveCredit->remaining_credits }}</strong> days
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

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
                                <th>Attached File</th>
                                <th>Reason</th>
                                <th class="text-center">Status</th>
                                <th>Approved by</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($request as $req)
                                <tr>
                                    <td>{{ $req->leaveType->leaveType }}</td>
                                    <td>{{ $req->start_date }}</td>
                                    <td>{{ $req->end_date }}</td>
                                    <td>{{ $req->days }}</td>
                                    <td>
                                        @if ($req->attached_file)
                                            <a href="{{ asset('storage/' . $req->attached_file) }}" target="_blank">
                                                View Attached File
                                            </a>
                                        @else
                                            No document uploaded
                                        @endif
                                    </td>
                                    <td>{{ $req->reason }}</td>
                                    <td class="text-center">
                                        <div class="action-label">
                                            <a class="btn btn-white btn-sm btn-rounded" href="#">
                                                @if ($req->status == 'New')
                                                    <i class="fa fa-dot-circle-o text-purple"></i> New
                                                @elseif($req->status == 'Pending')
                                                    <i class="fa fa-dot-circle-o text-info"></i> Pending
                                                @elseif($req->status == 'Pre-Approved')
                                                    <i class="fa fa-dot-circle-o text-warning"></i> Pre-Approved
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
                                                        <img src="{{ asset('images/' . $req->approver->image) }}"
                                                            alt="Profile Image" />
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
                                                : 'Not Approved Yet' }}

                                        </h2>
                                    </td>
                                    <td class="text-right">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                                aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item edit-leave" href="#"
                                                    data-id="{{ $req->id }}" data-type_id="{{ $req->leaveType->id }}"
                                                    data-start_date="{{ $req->start_date }}"
                                                    data-end_date="{{ $req->end_date }}" data-days="{{ $req->days }}"
                                                    data-reason="{{ $req->reason }}" data-status="{{ $req->status }}">
                                                    <i class="fa fa-pencil m-r-5"></i> Edit
                                                </a>
                                                <a class="dropdown-item delete-leave" href="#"
                                                    data-id="{{ $req->id }}">
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
                    <form id="leaveRequestForm" action="{{ route('store.leave') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Leave Type <span class="text-danger">*</span></label>
                            <select class="form-control" name="type" id="type" required>
                                <option value="">-- Select Leave Type --</option>
                                @foreach ($leaveCredits as $leaveCredit)
                                    <option value="{{ $leaveCredit->leave_type_id }}"
                                        data-credits="{{ $leaveCredit->remaining_credits }}">
                                        {{ $leaveCredit->leaveType->leaveType }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>From <span class="text-danger">*</span></label>
                            <div class="cal-icon">
                                <input class="form-control datetimepicker" type="text" name="start_date"
                                    id="add_start_date" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>To <span class="text-danger">*</span></label>
                            <div class="cal-icon">
                                <input class="form-control datetimepicker" type="text" name="end_date"
                                    id="add_end_date" required>
                            </div>
                        </div>

                        <div id="add_halfday-container" style="display: none;">
                            <label>
                                <input type="checkbox" id="add_halfday" name="halfday"> Half Day
                            </label>
                            <div id="add_halfday-options" style="display: none; margin-top: 10px;">
                                <label>
                                    <input type="radio" name="halfday_type" value="first_half"> 1st Half
                                </label>
                                <label style="margin-left: 10px;">
                                    <input type="radio" name="halfday_type" value="second_half"> 2nd Half
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Number of days <span class="text-danger">*</span></label>
                            <input class="form-control" name="total_days" id="add_total_days" readonly type="text">
                        </div>

                        <div class="form-group">
                            <label>Remaining Leaves <span class="text-danger">*</span></label>
                            <input class="form-control" id="remaining_leaves" readonly value="0" type="text">
                        </div>
                        <div class="form-group">
                            <label>Attached File <span class="text-danger">*</span></label>
                            <input class="form-control" type="file" name="attached_file" id="attached_file">
                        </div>
                        <div class="form-group">
                            <label>Leave Reason <span class="text-danger">*</span></label>
                            <textarea rows="4" class="form-control" name="reason" id="reason" required></textarea>
                        </div>
                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                        </div>
                    </form>

                    <!-- Loader -->
                    <div class="loader" id="loader" style="display: none;">
                        <div class="loading-text">
                            Please Wait<span class="dot">.</span><span class="dot">.</span><span
                                class="dot">.</span>
                        </div>
                        <div class="loading-bar-background">
                            <div class="loading-bar">
                                <div class="white-bars-container">
                                    <div class="white-bar"></div>
                                    <div class="white-bar"></div>
                                    <div class="white-bar"></div>
                                    <div class="white-bar"></div>
                                    <div class="white-bar"></div>
                                    <div class="white-bar"></div>
                                    <div class="white-bar"></div>
                                    <div class="white-bar"></div>
                                    <div class="white-bar"></div>
                                    <div class="white-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



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
                            <label>Leave Type <span class="text-danger">*</span></label>
                            <select class="form-control" name="typee" id="typee" required>
                                <option value="">-- Select Leave Type --</option>
                                @foreach ($leaveCredits as $leaveCredit)
                                    <option value="{{ $leaveCredit->leave_type_id }}"
                                        data-credits="{{ $leaveCredit->remaining_credits }}">
                                        {{ $leaveCredit->leaveType->leaveType }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>From <span class="text-danger">*</span></label>
                            <div class="cal-icon">
                                <input class="form-control datetimepicker" type="text" name="start_datee"
                                    id="start_datee" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>To <span class="text-danger">*</span></label>
                            <div class="cal-icon">
                                <input class="form-control datetimepicker" type="text" name="end_datee"
                                    id="end_datee" required>
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
        $(document).ready(function() {
            $('#type').on('change', function() {
                var selectedOption = $(this).find('option:selected');

                var remainingCredits = selectedOption.data('credits');

                $('#remaining_leaves').val(remainingCredits);
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            $('#leaveCreditsLink').on('click', function(e) {
                e.preventDefault();
                $('#leaveCreditsModal').modal('show');
            });
        });
    </script>


    <script>
        document.getElementById('leaveRequestForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Show loader
            document.getElementById('loader').style.display = 'flex';
            const submitButton = document.querySelector('.submit-btn');
            submitButton.disabled = true;

            // Submit the form using AJAX or default submission
            this.submit(); // Use default submission or handle AJAX here
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initialize datetime pickers for both "add" and "edit" date inputs
            function initializeDatetimePickers() {
                $('#add_start_date, #add_end_date, #edit_start_date, #edit_end_date').datetimepicker({
                    format: 'YYYY-MM-DD'
                });
            }

            // Initialize datetime pickers when the document is ready
            initializeDatetimePickers();

            // Add Leave: Calculate total days and toggle half-day options
            $('#add_start_date, #add_end_date').on('dp.change', function() {
                calculateTotalDays('#add_start_date', '#add_end_date', '#add_total_days',
                    '#add_halfday-container');
            });

            // Edit Leave: Calculate total days and toggle half-day options
            $('#edit_start_date, #edit_end_date').on('dp.change', function() {
                calculateTotalDays('#edit_start_date', '#edit_end_date', '#edit_total_days',
                    '#edit_halfday-container');
            });

            // Function to calculate total days and toggle half-day options
            function calculateTotalDays(startSelector, endSelector, outputSelector, halfdayContainerSelector) {
                var startDatePicker = $(startSelector).data('DateTimePicker');
                var endDatePicker = $(endSelector).data('DateTimePicker');

                if (startDatePicker && endDatePicker) {
                    var startDate = startDatePicker.date();
                    var endDate = endDatePicker.date();

                    if (startDate && endDate) {
                        var diffDays = endDate.diff(startDate, 'days') +
                            1; // Calculate difference in days and add 1

                        // Check if half-day is selected and adjust the total days
                        var isHalfDay = $(halfdayContainerSelector).find('input[type="checkbox"]:checked').length >
                            0;
                        if (isHalfDay && diffDays === 1) {
                            diffDays = 0.5; // Set to 0.5 day if half-day is selected and the period is 1 day
                        }

                        $(outputSelector).val(diffDays); // Update total days field

                        // Show or hide the half-day checkbox and options
                        if (diffDays === 1) {
                            $(halfdayContainerSelector).show();
                            $(halfdayContainerSelector).find('input[type="checkbox"]').prop('checked',
                                false); // Uncheck checkbox initially
                            $('#edit_halfday-options').hide(); // Hide 1st and 2nd half options initially
                        } else if (diffDays === 0.5) {
                            $(halfdayContainerSelector).show();
                            $(halfdayContainerSelector).find('input[type="checkbox"]').prop('checked',
                                true); // Ensure half-day checkbox is checked
                            $('#edit_halfday-options').show(); // Show the half-day options (1st and 2nd half)
                        } else {
                            $(halfdayContainerSelector).hide();
                            $(halfdayContainerSelector).find('input[type="checkbox"]').prop('checked',
                                false); // Uncheck half-day checkbox
                            $(halfdayContainerSelector).find('input[type="radio"]').prop('checked',
                                false); // Uncheck radio buttons
                            $('#edit_halfday-options').hide(); // Hide the half-day options
                        }
                    } else {
                        $(outputSelector).val(''); // Clear the output if dates are not both set
                        $(halfdayContainerSelector).hide();
                    }
                } else {
                    console.log("datetimepicker is not initialized for the selected input fields.");
                }
            }

            // Toggle half-day options when checkbox is checked
            $('#add_halfday, #edit_halfday').on('change', function() {
                var optionsContainer = $(this).closest('div').find('div[id$="_halfday-options"]');
                if ($(this).is(':checked')) {
                    optionsContainer.show();
                } else {
                    optionsContainer.hide();
                    optionsContainer.find('input[type="radio"]').prop('checked',
                        false); // Uncheck the radio buttons
                }

                // Recalculate days if halfday checkbox changes
                calculateTotalDays('#add_start_date', '#add_end_date', '#add_total_days',
                    '#add_halfday-container');
                calculateTotalDays('#edit_start_date', '#edit_end_date', '#edit_total_days',
                    '#edit_halfday-container');
            });

            // Edit Leave Modal - Populate fields
            $('.edit-leave').on('click', function() {
                var leaveId = $(this).data('id');
                var startDate = $(this).data('start_date');
                var endDate = $(this).data('end_date');
                var days = $(this).data('days');
                var reason = $(this).data('reason');

                $('#leave_id').val(leaveId);
                $('#edit_start_date').val(startDate);
                $('#edit_end_date').val(endDate);
                $('#edit_total_days').val(days);
                $('#reasone').val(reason);

                // Check if it's a half day
                if (days === 1) {
                    $('#edit_halfday-container').show();
                    $('#edit_halfday-options').hide(); // Initially hide the options
                } else {
                    $('#edit_halfday-container').hide();
                    $('#edit_halfday-container').find('input[type="checkbox"]').prop('checked', false);
                    $('#edit_halfday-options').hide();
                }

                // Reinitialize the datetimepickers in the modal (if necessary)
                $('#edit_start_date, #edit_end_date').datetimepicker({
                    format: 'YYYY-MM-DD'
                });

                $('#editLeaveForm').attr('action', '/emp/leave/' + leaveId);
                $('#edit_leave').modal('show');
            });

            // Delete leave request
            $('.delete-leave').on('click', function() {
                var leaveId = $(this).data('id');
                var status = $(this).data('status');

                if (status === 'Approved') {
                    alert('This leave request has already been approved and cannot be deleted.');
                    return;
                }

                $('#delete_leave_id').val(leaveId);
                $('#deleteLeaveForm').attr('action', '/emp/leave/' + leaveId);
                $('#delete_approve').modal('show');
            });
        });
    </script>



@endsection
