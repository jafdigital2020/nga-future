@extends('layouts.hrmaster') @section('title', 'User Deduction')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">User Deduction</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('hr/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('hr/deduction/user') }}">User Deduction</a></li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn m-r-5" data-toggle="modal" data-target="#assign_deduction"> Assign
                    Deductions</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Content Starts -->
    <!-- Search Filter -->
    <form action="{{ route('hr.userDeductionsIndex') }}" method="GET">
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
                    <select class="select floating" name="deduction">
                        <option value="" disabled selected>Select Deduction</option>
                        @foreach ($deductions as $deduct)
                        <option value="{{ $deduct->id }}">{{ $deduct->name }}</option>
                        @endforeach
                    </select>
                    <label class="focus-label">Deduction</label>
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
                            <th>Deduction Name</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Inclusion Count</th>
                            <th>Every Payroll?</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userDeductions as $userDeduction)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="{{ url('hr/employee/edit/'. $userDeduction->user->id) }}"
                                        class="avatar"><img alt=""
                                            src="{{ asset('images/' . $userDeduction->user->image) }}"></a>
                                    <a href="{{ url('hr/employee/edit/'. $userDeduction->user->id) }}">{{ $userDeduction->user->fName }}
                                        {{ $userDeduction->user->lName }}
                                        <span>{{ $userDeduction->user->position }}</span></a>
                                </h2>
                            </td>
                            <td>{{ $userDeduction->user->department }}</td>
                            <td>{{ $userDeduction->deductionList->name }}</td>
                            <td>{{ $userDeduction->deductionList->amount }}</td>
                            <td>{{ $userDeduction->deductionList->type }}</td>
                            <td>
                                @if($userDeduction->deductionList->inclusion_limit)
                                {{ $userDeduction->inclusion_count }} out of
                                {{ $userDeduction->deductionList->inclusion_limit }}
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                {{ $userDeduction->deductionList->is_every_payroll ? 'Yes' : 'No' }}

                            </td>
                            <td>
                                <span class="badge {{ $userDeduction->active ? 'badge-warning' : 'badge-success' }}">
                                    {{ $userDeduction->active ? 'Active' : 'Completed' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-danger delete-deduction" data-id="{{ $userDeduction->id }}">
                                    <i class="fa fa-trash"></i>
                                </button>
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

<!-- Assign Shift -->
<div id="assign_deduction" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Deduction</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('hr.storeUserDeduction') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label">Department <span class="text-danger">*</span></label>
                        <select class="form-control" name="department" id="department-select">
                            <option value="" disabled selected>Select Department</option>
                            <option value="all">Select All Departments</option>
                            @foreach ($departments as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Employee Name <span class="text-danger">*</span></label>
                        <select class="form-control picker" name="users_id[]" id="employee-select" multiple>
                        </select>
                    </div>
                    <div id="deductions-container">
                        <div class="row row-sm deduction-row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Deduction</label>
                                    <select name="deduction_id[]" class="form-control deduction-select" required>
                                        <option value="">Select Deduction</option>
                                        @foreach($deductions as $deduct)
                                        <option value="{{ $deduct->id }}" data-amount="{{ $deduct->amount }}"
                                            data-type="{{ $deduct->type }}"
                                            data-inclusion-limit="{{ $deduct->inclusion_limit }}">
                                            {{ $deduct->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input class="form-control amount-input" type="text" name="amount[]" readonly>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Fixed / %</label>
                                    <input class="form-control type-input" type="text" name="type[]" readonly>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Limit</label>
                                    <input class="form-control inclusion-limit" type="number" name="inclusion_count[]"
                                        min="1" readonly>
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
                        <button type="submit" class="btn btn-primary">Assign Deductions</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Assign Shift -->

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('deductions-container');
        let selectedDeductions = new Set(); // To track selected deduction IDs

        // Function to create a new deduction row as a DOM element
        function createDeductionRow() {
            const row = document.createElement('div');
            row.classList.add('row', 'row-sm', 'deduction-row');
            row.innerHTML = `
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Deduction</label>
                            <select name="deduction_id[]" class="form-control deduction-select" required>
                                <option value="">Select Deduction</option>
                                    @foreach($deductions as $deduct)
                                        <option value="{{ $deduct->id }}" data-amount="{{ $deduct->amount }}"
                                            data-type="{{ $deduct->type }}"
                                            data-inclusion-limit="{{ $deduct->inclusion_limit }}">
                                        {{ $deduct->name }}</option>
                                    @endforeach
                            </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Amount</label>
                        <input class="form-control amount-input" type="text" name="amount[]" readonly>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label>Fixed / %</label>
                        <input class="form-control type-input" type="text" name="type[]" readonly>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label>Limit</label>
                        <input class="form-control inclusion-limit" type="number" name="inclusion_count[]"
                            min="1" readonly>
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

        // Function to handle deduction change and populate amount and type
        function handleDeductionChange(select) {
            const selectedOption = select.options[select.selectedIndex];

            // Get the corresponding input fields in the same row
            const deductionRow = select.closest('.deduction-row');
            const amountInput = deductionRow.querySelector('.amount-input');
            const typeInput = deductionRow.querySelector('.type-input');
            const inclusionLimitInput = deductionRow.querySelector('.inclusion-limit');

            if (selectedOption) {
                amountInput.value = selectedOption.getAttribute('data-amount') || '';
                typeInput.value = selectedOption.getAttribute('data-type') || '';
                inclusionLimitInput.value = selectedOption.getAttribute('data-inclusion-limit') ||
                    '';
            }

            // Update the selected deductions list and update all dropdowns
            updateSelectedDeductions();
            updateDropdownOptions();
        }

        // Function to track selected deductions
        function updateSelectedDeductions() {
            selectedDeductions.clear(); // Reset the set of selected deductions

            const selectElements = container.querySelectorAll('.deduction-select');
            selectElements.forEach(select => {
                const value = select.value;
                if (value) {
                    selectedDeductions.add(value); // Add selected value to the set
                }
            });
        }

        // Function to update dropdown options based on selected deductions
        function updateDropdownOptions() {
            const selectElements = container.querySelectorAll('.deduction-select');
            selectElements.forEach(select => {
                const currentValue = select.value;

                // Loop through options and disable if it's already selected elsewhere
                Array.from(select.options).forEach(option => {
                    if (selectedDeductions.has(option.value) && option.value !== currentValue) {
                        option.disabled = true; // Disable option if it's already selected
                    } else {
                        option.disabled = false; // Enable option if it's not selected elsewhere
                    }
                });
            });
        }

        // Function to disable or enable the inclusion limit based on every payroll selection
        function handleEveryPayrollChange(select) {
            const row = select.closest('.deduction-row');
            const inclusionLimit = row.querySelector('.inclusion-limit');
            inclusionLimit.disabled = (select.value === "1"); // Disable if 'Yes' is selected
        }

        // Function to apply the disable logic to existing rows
        function applyPayrollLogicToExistingRows() {
            const payrollSelects = container.querySelectorAll('.is-every-payroll');
            payrollSelects.forEach(select => {
                handleEveryPayrollChange(select);
                select.addEventListener('change', function () {
                    handleEveryPayrollChange(this);
                });
            });
        }

        // Function to create a new row and apply payroll logic
        function addNewRow() {
            const newRow = createDeductionRow();
            container.appendChild(newRow);
            updateDropdownOptions(); // Update the dropdown to reflect already selected deductions
            applyPayrollLogicToExistingRows(); // Apply every payroll logic to new rows
        }


        // Event delegation for adding and removing rows
        container.addEventListener('click', function (event) {
            if (event.target.closest('.add-more')) {
                // Add the new row
                addNewRow();
            }

            if (event.target.closest('.remove-row')) {
                // Remove the row
                const deductionRow = event.target.closest('.deduction-row');
                if (deductionRow) {
                    deductionRow.remove();
                    // Update the selected deductions and dropdown options after removal
                    updateSelectedDeductions();
                    updateDropdownOptions();
                }
            }
        });

        // Event delegation for detecting deduction select changes
        container.addEventListener('change', function (event) {
            if (event.target.classList.contains('deduction-select')) {
                handleDeductionChange(event.target);
            }
        });

        // Apply payroll logic to existing rows when the page loads
        applyPayrollLogicToExistingRows();
    });

</script>

<script>
    $(document).ready(function () {
        // When a department is selected
        $('#department-select').change(function () {
            var department = $(this).val(); // Get selected department

            // Clear previous employee options
            $('#employee-select').empty().append('');

            // If "Select All Departments" is chosen, it will send 'all'
            if (department) {
                $.ajax({
                    url: '{{ route("HRgetEmployeesByDepartmentDeduction") }}',
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


<script>
    $(document).ready(function () {
        $('.delete-deduction').on('click', function () {
            var deductionId = $(this).data('id');
            if (confirm('Are you sure you want to delete this deduction?')) {
                $.ajax({
                    url: '/hr/deduction/user/delete/' + deductionId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}' // Include CSRF token
                    },
                    success: function (response) {
                        if (response.success) {
                            // Optionally, remove the row from the table
                            $('button[data-id="' + deductionId + '"]').closest('tr')
                                .remove();
                            alert(response.message); // Show success message
                        } else {
                            alert('Error deleting deduction: ' + response.message);
                        }
                    },
                    error: function (xhr) {
                        alert('Error deleting deduction: ' + xhr.responseText);
                    }
                });
            }
        });
    });

</script>


@endsection
