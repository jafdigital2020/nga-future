@extends('layouts.empmaster')
@section('title', 'Payslip')

@section('content')
    @include('sweetalert::alert')

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="page-title">Payslip</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('emp/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Payslip</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <!-- Search Filter -->
                    <form action="{{ route('emp.payslipView') }}" method="GET" style="float: right; width: 100%;">
                        <div class="row filter-row justify-content-end">
                            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-4">
                                <div class="form-group form-focus select-focus">
                                    <select class="select floating" id="monthSelect" name="cutoff_period">
                                        <option value="" {{ $cutoffPeriod == '' ? 'selected' : '' }}>-- Select Cut-off
                                            Period --
                                        </option>
                                        <option value="December - January 1st Cut-off"
                                            {{ $cutoffPeriod == 'December - January 1st Cut-off' ? 'selected' : '' }}>
                                            December - January 1st Cut-off
                                        </option>
                                        <option value="January" {{ $cutoffPeriod == 'January' ? 'selected' : '' }}>
                                            January 2nd Cut-off
                                        </option>
                                        <option value="January-February"
                                            {{ $cutoffPeriod == 'January-February' ? 'selected' : '' }}>
                                            January - February 1st Cut-off
                                        </option>
                                        <option value="February" {{ $cutoffPeriod == 'February' ? 'selected' : '' }}>
                                            February 2nd Cut-off
                                        </option>
                                        <option value="February-March"
                                            {{ $cutoffPeriod == 'February-March' ? 'selected' : '' }}>
                                            February - March 1st Cut-off
                                        </option>
                                        <option value="March" {{ $cutoffPeriod == 'March' ? 'selected' : '' }}>
                                            March 2nd Cut-off
                                        </option>
                                        <option value="March-April" {{ $cutoffPeriod == 'March-April' ? 'selected' : '' }}>
                                            March - April 1st Cut-off
                                        </option>
                                        <option value="April" {{ $cutoffPeriod == 'April' ? 'selected' : '' }}>
                                            April 2nd Cut-off
                                        </option>
                                        <option value="April-May" {{ $cutoffPeriod == 'April-May' ? 'selected' : '' }}>
                                            April - May 1st Cut-off
                                        </option>
                                        <option value="May" {{ $cutoffPeriod == 'May' ? 'selected' : '' }}>
                                            May 2nd Cut-off
                                        </option>
                                        <option value="May-June" {{ $cutoffPeriod == 'May-June' ? 'selected' : '' }}>
                                            May - June 1st Cut-off
                                        </option>
                                        <option value="June" {{ $cutoffPeriod == 'June' ? 'selected' : '' }}>
                                            June 2nd Cut-off
                                        </option>
                                        <option value="June-July" {{ $cutoffPeriod == 'June-July' ? 'selected' : '' }}>
                                            June - July 1st Cut-off
                                        </option>
                                        <option value="July" {{ $cutoffPeriod == 'July' ? 'selected' : '' }}>
                                            July 2nd Cut-off
                                        </option>
                                        <option value="July-August" {{ $cutoffPeriod == 'July-August' ? 'selected' : '' }}>
                                            July - August 1st Cut-off
                                        </option>
                                        <option value="August" {{ $cutoffPeriod == 'August' ? 'selected' : '' }}>
                                            August 2nd Cut-off
                                        </option>
                                        <option value="August - September 1st Cut-off"
                                            {{ $cutoffPeriod == 'August - September 1st Cut-off 2024' ? 'selected' : '' }}>
                                            August - September 1st Cut-off 2024
                                        </option>
                                        <option value="September" {{ $cutoffPeriod == 'September' ? 'selected' : '' }}>
                                            September 2nd Cut-off
                                        </option>
                                        <option value="September-October"
                                            {{ $cutoffPeriod == 'September-October' ? 'selected' : '' }}>
                                            September - October 1st Cut-off
                                        </option>
                                        <option value="October" {{ $cutoffPeriod == 'October' ? 'selected' : '' }}>
                                            October 2nd Cut-off
                                        </option>
                                        <option value="October-November"
                                            {{ $cutoffPeriod == 'October-November' ? 'selected' : '' }}>
                                            October - November 1st Cut-off
                                        </option>
                                        <option value="November" {{ $cutoffPeriod == 'November' ? 'selected' : '' }}>
                                            November 2nd Cut-off
                                        </option>
                                        <option value="November-December"
                                            {{ $cutoffPeriod == 'November-December' ? 'selected' : '' }}>
                                            November - December 1st Cut-off
                                        </option>
                                        <option value="December" {{ $cutoffPeriod == 'December' ? 'selected' : '' }}>
                                            December 2nd Cut-off
                                        </option>
                                    </select>

                                    <label class="focus-label">Cut-off Period</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                                <div class="form-group form-focus select-focus">
                                    <select class="select floating" name="year">
                                        <option value="">--Select Year--</option>
                                        <option value="{{ $selectedYear }}" selected>{{ $selectedYear }}</option>
                                        <!-- Add more options if needed -->
                                    </select>
                                    <label class="focus-label">Year</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-4">
                                <button type="submit" class="btn btn-primary btn-block">Search</button>
                            </div>
                        </div>
                    </form>
                    <!-- /Search Filter -->
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        {{-- ACCESS CODE --}}
        <div id="codeModal" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Enter Access Code</h5>
                        <button type="button" class="close" onclick="window.location.href='{{ url('emp/dashboard') }}';" aria-label="Close" >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="accessCodeForm">
                            @csrf
                            <div class="form-group">
                                <label>Access Code <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="accessCodeInput"
                                    placeholder="Enter your code" required>
                            </div>
                            <div class="submit-section">
                                <button type="button" class="btn btn-primary submit-btn"
                                    id="submitAccessCode">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


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
                                <th>Total Hours</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payslip as $pay)
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="{{ url('emp/profile') }}" class="avatar">
                                                @if ($pay->user->image)
                                                    <img src="{{ asset('images/' . $pay->user->image) }}"
                                                        alt="Profile Image" />
                                                @else
                                                    <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                                @endif
                                            </a>
                                            <a href="{{ url('emp/profile') }}">
                                                @if ($pay->user->fName || $pay->user->mName || $pay->user->lName)
                                                    {{ $pay->user->fName ?? '' }}
                                                    {{ $pay->user->mName ?? '' }}
                                                    {{ $pay->user->lName ?? '' }}
                                                @else
                                                    {{ $pay->user->name }}
                                                @endif
                                                <span>{{ $pay->user->position }}</span>
                                            </a>
                                        </h2>
                                    </td>

                                    <td>{{ $pay->user->department }}</td>
                                    <td>{{ $pay->start_date }}</td>
                                    <td>{{ $pay->end_date }}</td>
                                    <td>{{ $pay->month }}</td>
                                    <td>{{ $pay->cut_off }}</td>
                                    <td>{{ $pay->total_hours }}</td>
                                    <td class="text-right">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                                aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="{{ url('emp/payslip/view/' . $pay->id) }}">
                                                    <i class="fa fa-eye m-r-5"></i>View
                                                </a>
                                                <a class="dropdown-item edit-attendance" href="#">
                                                    <i class="fa fa-download m-r-5"></i>Download
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Show the modal on page load
            $('#codeModal').modal({
                backdrop: 'static', // Prevent closing the modal by clicking outside
                keyboard: false // Disable closing the modal with the Escape key
            });

            // Handle Access Code Submission
            $('#submitAccessCode').click(function() {
                const enteredCode = $('#accessCodeInput').val().trim();
                const token = $('meta[name="csrf-token"]').attr('content'); // CSRF token

                if (enteredCode === "") {
                    alert("Access code is required!");
                    return;
                }

                // Make an AJAX request to validate the code
                $.ajax({
                    url: '{{ route('emp.validateCode') }}', // Replace with your route name
                    method: 'POST',
                    data: {
                        access_code: enteredCode,
                        _token: token // Include CSRF token for security
                    },
                    success: function(response) {
                        if (response.success) {
                            alert("Access code accepted!");
                            $('#codeModal').modal('hide'); // Close the modal
                        } else {
                            alert(response.message); // Display invalid code message
                        }
                    },
                    error: function(xhr) {
                        alert(
                            "An error occurred while validating the access code. Please try again."
                            );
                    }
                });
            });
        });
    </script>
@endsection
