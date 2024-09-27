@extends('layouts.hrmaster') @section('title', 'Processed Timesheet')


@section('content')
@include('sweetalert::alert')

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Processed Timesheet</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('hr/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payslip</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    <form action="{{ route('hr.payslipProcess') }}" method="GET">
        <div class="row filter-row">
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus">
                    <input type="text" class="form-control floating" name="name" value="{{ request('ename') }}">
                    <label class="focus-label">Employee Name</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="department">
                        <option value="">--Department--</option>
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
                        <option value="" {{ $cutoffPeriod == '' ? 'selected' : '' }}>-- Select Cut-off Period --
                        </option>
                        <option value="December - January 1st Cut-off"
                            {{ $cutoffPeriod == 'December - January 1st Cut-off' ? 'selected' : '' }}>December - January
                            1st Cut-off</option>
                        <option value="January 2nd Cut-off"
                            {{ $cutoffPeriod == 'January 2nd Cut-off' ? 'selected' : '' }}>January 2nd Cut-off</option>
                        <option value="January - February 1st Cut-off"
                            {{ $cutoffPeriod == 'January - February 1st Cut-off' ? 'selected' : '' }}>January - February
                            1st Cut-off</option>
                        <option value="February 2nd Cut-off"
                            {{ $cutoffPeriod == 'February 2nd Cut-off' ? 'selected' : '' }}>February 2nd Cut-off
                        </option>
                        <option value="February - March 1st Cut-off"
                            {{ $cutoffPeriod == 'February - March 1st Cut-off' ? 'selected' : '' }}>February - March 1st
                            Cut-off</option>
                        <option value="March 2nd Cut-off" {{ $cutoffPeriod == 'March 2nd Cut-off' ? 'selected' : '' }}>
                            March 2nd Cut-off</option>
                        <option value="March - April 1st Cut-off"
                            {{ $cutoffPeriod == 'March - April 1st Cut-off' ? 'selected' : '' }}>March - April 1st
                            Cut-off</option>
                        <option value="April 2nd Cut-off" {{ $cutoffPeriod == 'April 2nd Cut-off' ? 'selected' : '' }}>
                            April 2nd Cut-off</option>
                        <option value="April - May 1st Cut-off"
                            {{ $cutoffPeriod == 'April - May 1st Cut-off' ? 'selected' : '' }}>April - May 1st Cut-off
                        </option>
                        <option value="May 2nd Cut-off" {{ $cutoffPeriod == 'May 2nd Cut-off' ? 'selected' : '' }}>May
                            2nd Cut-off</option>
                        <option value="May - June 1st Cut-off"
                            {{ $cutoffPeriod == 'May - June 1st Cut-off' ? 'selected' : '' }}>May - June 1st Cut-off
                        </option>
                        <option value="June 2nd Cut-off" {{ $cutoffPeriod == 'June 2nd Cut-off' ? 'selected' : '' }}>
                            June 2nd Cut-off</option>
                        <option value="June - July 1st Cut-off"
                            {{ $cutoffPeriod == 'June - July 1st Cut-off' ? 'selected' : '' }}>June - July 1st Cut-off
                        </option>
                        <option value="July 2nd Cut-off" {{ $cutoffPeriod == 'July 2nd Cut-off' ? 'selected' : '' }}>
                            July 2nd Cut-off</option>
                        <option value="July - August 1st Cut-off"
                            {{ $cutoffPeriod == 'July - August 1st Cut-off' ? 'selected' : '' }}>July - August 1st
                            Cut-off</option>
                        <option value="August 2nd Cut-off"
                            {{ $cutoffPeriod == 'August 2nd Cut-off' ? 'selected' : '' }}>August 2nd Cut-off</option>
                        <option value="August - September 1st Cut-off"
                            {{ $cutoffPeriod == 'August - September 1st Cut-off' ? 'selected' : '' }}>August -
                            September 1st Cut-off</option>
                        <option value="September 2nd Cut-off"
                            {{ $cutoffPeriod == 'September 2nd Cut-off' ? 'selected' : '' }}>September 2nd Cut-off
                        </option>
                        <option value="September - October 1st Cut-off"
                            {{ $cutoffPeriod == 'September - October 1st Cut-off' ? 'selected' : '' }}>September -
                            October 1st Cut-off</option>
                        <option value="October 2nd Cut-off"
                            {{ $cutoffPeriod == 'October 2nd Cut-off' ? 'selected' : '' }}>October 2nd Cut-off</option>
                        <option value="October - November 1st Cut-off"
                            {{ $cutoffPeriod == 'October - November 1st Cut-off' ? 'selected' : '' }}>October - November
                            1st Cut-off</option>
                        <option value="November 2nd Cut-off"
                            {{ $cutoffPeriod == 'November 2nd Cut-off' ? 'selected' : '' }}>November 2nd Cut-off
                        </option>
                        <option value="November - December 1st Cut-off"
                            {{ $cutoffPeriod == 'November - December 1st Cut-off' ? 'selected' : '' }}>November -
                            December 1st Cut-off</option>
                        <option value="December 2nd Cut-off"
                            {{ $cutoffPeriod == 'December 2nd Cut-off' ? 'selected' : '' }}>December 2nd Cut-off
                        </option>
                    </select>
                    <label class="focus-label">Cut-off Period</label>
                </div>
            </div>

            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="year">
                        <option value="">--Select Year--</option>
                        <option value="{{ $selectedYear }}" selected>{{ $selectedYear }}</option>
                        <!-- Add more options if needed -->
                    </select>
                    <label class="focus-label">Year</label>
                </div>

            </div>
            <div class="col-sm-6 col-md-4"><button type="submit" class="btn btn-primary btn-block ml-2">Search</button>
            </div>
        </div>
    </form>
    <!-- /Search Filter -->

    <!-- Bulk Action Dropdown -->
    <div class="d-flex align-items-center mb-3">
        <div class="me-2">

            <select id="bulk-action-dropdown" class="select floating">
                <option value="" disabled selected>Select Bulk Action</option>
                <option value="Approved">Approve</option>
                <option value="Revision">Revision</option>
                <option value="Declined">Decline</option>
            </select>
        </div>
        <div>
            <button type="button" id="apply-bulk-action" class="btn btn-primary btn-sm">Apply</button>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-hover table-nowrap mb-0 datatable">
                    <thead class="thead-light">
                        <tr>
                            <th><input type="checkbox" name="" id="select_all_ids"></th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Month</th>
                            <th>Cut-Off</th>
                            <th>Total Hours</th>
                            <th>Net Pay</th>
                            <th>Payslip</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payslip as $pay)
                        <tr>
                            <td><input type="checkbox" name="ids" class="checkbox_ids" id="" value="{{ $pay->id }}">
                            </td>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="{{ url('hr/employee/edit/'.$pay->user->id) }}" class="avatar">
                                        @if ($pay->user->image)
                                        <img src="{{ asset('images/' . $pay->user->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{
                                            asset('images/default.png')
                                        }}" alt="Profile Image" />
                                        @endif</a>
                                    <a href="{{ url('hr/employee/edit/'.$pay->user->id) }}">
                                        @if($pay->user->fName || $pay->user->mName || $pay->user->lName)
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
                            <td>{{ $pay->totalHours }}</td>

                            <td>₱{{ number_format($pay->netPayTotal, 2) }}</td>
                            <td>

                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        @if($pay->status == 'New')
                                        <i class="fa fa-dot-circle-o text-purple"></i> New
                                        @elseif($pay->status == 'pending')
                                        <i class="fa fa-dot-circle-o text-info"></i> Pending
                                        @elseif($pay->status == 'Approved')
                                        <i class="fa fa-dot-circle-o text-success"></i> Approved
                                        @elseif($pay->status == 'Revision')
                                        <i class="fa fa-dot-circle-o text-warning"></i> Revision
                                        @elseif($pay->status == 'Revised')
                                        <i class="fa fa-dot-circle-o text-dark"></i> Revised
                                        @elseif($pay->status == 'Declined')
                                        <i class="fa fa-dot-circle-o text-danger"></i> Declined
                                        @else
                                        <i class="fa fa-dot-circle-o text-info"></i> Pending
                                        @endif
                                    </a>

                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ url('hr/payslip/edit/'.$pay->id) }}"><i
                                                class="fa fa-pencil m-r-5"></i>View & Edit</a>
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
    // Handle select all checkbox
    document.getElementById('select_all_ids').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.checkbox_ids');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Handle bulk action
    document.getElementById('apply-bulk-action').addEventListener('click', function () {
        const selectedAction = document.getElementById('bulk-action-dropdown').value;
        const selectedIds = Array.from(document.querySelectorAll('.checkbox_ids:checked')).map(checkbox =>
            checkbox.value);

        if (selectedIds.length === 0) {
            alert('Please select at least one item to apply the action.');
            return;
        }

        if (!selectedAction) {
            alert('Please select a bulk action to apply.');
            return;
        }

        // AJAX request to handle bulk action
        fetch(`/hr/processed/bulk-action`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    action: selectedAction,
                    ids: selectedIds
                })
            })
            .then(response => response.json())
            .then(data => {
                // Handle response
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Reload the page to see changes
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    });

</script>

@endsection
