@extends('layouts.master') @section('title', 'Deductions') @section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Deductions</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Deductions</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#create_deduction"><i
                        class="fa fa-plus"></i>
                    Create Deduction</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-hover table-nowrap mb-0 datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Deduction Name</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Inclusion Limit</th>
                            <th>Every Payroll</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deductions as $deduct)
                        <tr>
                            <td>{{ $deduct->name }}</td>
                            <td>
                                @if($deduct->amount == 0)
                                {{ number_format($deduct->amount, 4) }}
                                @elseif(floor($deduct->amount) == $deduct->amount)
                                {{ number_format($deduct->amount, 2) }}
                                @else
                                {{ number_format($deduct->amount, 4) }}
                                @endif
                            </td>
                            <td>{{ $deduct->type }}</td>
                            <td>
                                @if($deduct->is_every_payroll)
                                -
                                @else
                                {{ $deduct->inclusion_limit ?? 'Unlimited' }}

                                @endif
                            </td>
                            <td>
                                {{ $deduct->is_every_payroll ? 'Yes' : 'No' }}

                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item edit-deduction" href="#" data-id="{{ $deduct->id }}"
                                            data-name="{{ $deduct->name }}" data-amount="{{ $deduct->amount }}"
                                            data-type="{{ $deduct->type }}"
                                            data-inclusion_limit="{{ $deduct->inclusion_limit }}"
                                            data-is_every_payroll="{{ $deduct->is_every_payroll }}">
                                            <i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item delete-deduction" href="#" data-id="{{ $deduct->id }}">
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

<!-- Create Deduction Modal -->
<div id="create_deduction" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Deduction</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.createDeduction') }}">
                    @csrf
                    <div class="form-group">
                        <label>Deduction Name<span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="name" id="name" required>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Amount<span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="amount" id="amount" required>

                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Type<span class="text-danger">*</span></label>
                                <div class="form-group form-focus select-focus">
                                    <select class="form-control" name="type">
                                        <option value="">-</option>
                                        <option value="fixed">Fixed</option>
                                        <option value="percentage">Percentage</option>
                                    </select> <label class="focus-label">Select Type</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="inclusion_limit">Inclusion Limit</label>
                                    <input type="number" class="form-control" id="inclusion_limit"
                                        name="inclusion_limit" min="1">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="is_every_payroll">Include in Every Payroll?</label>
                                    <select class="form-control" id="is_every_payroll" name="is_every_payroll" required>
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Create Deduction Modal -->

<!-- Edit Deduction Modal -->
<div id="edit_deduction" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Deduction</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="editDeductionForm">
                    @csrf
                    <input type="hidden" name="deduction_id" id="deduction_id">
                    <div class="form-group">
                        <label>Deduction Name<span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="namee" id="namee" required>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Amount<span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="amounte" id="amounte" required>

                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Type<span class="text-danger">*</span></label>
                                <div class="form-group form-focus select-focus">
                                    <select class="form-control" name="typee" id="typee">
                                        <option value="">-</option>
                                        <option value="fixed">Fixed</option>
                                        <option value="percentage">Percentage</option>
                                    </select> <label class="focus-label">Select Type</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="inclusion_limit">Inclusion Limit</label>
                                <input type="number" class="form-control" id="inclusion_limite" name="inclusion_limite"
                                    min="1">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="is_every_payroll">Include in Every Payroll?</label>
                                <select class="form-control" id="is_every_payrolle" name="is_every_payrolle" required>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Deduction Modal -->

<!-- Delete Deduction Modal -->
<div class="modal custom-modal fade" id="delete_deduction" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Deduction</h3>
                    <p>Are you sure you want to cancel this deduction?</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-5">
                            <form id="deleteDeductionForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="deduction_id" id="deduction_id">
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
<!-- /Delete Deduction Modal -->

@endsection

@section('scripts')

<script>
    // Edit deduction request
    $('.edit-deduction').on('click', function () {
        var deductId = $(this).data('id');
        var name = $(this).data('name');
        var amount = $(this).data('amount');
        var type = $(this).data('type');
        var limit = $(this).data('inclusion_limit');
        var everyPayroll = $(this).data('is_every_payroll');

        $('#deduction_ide').val(deductId);
        $('#namee').val(name);
        $('#amounte').val(amount);
        $('#typee').val(type);
        $('#inclusion_limite').val(limit);
        $('#is_every_payrolle').val(everyPayroll);

        $('#editDeductionForm').attr('action', '/admin/deduction/edit/' +
            deductId);
        $('#edit_deduction').modal('show');
    });

    // Delete deduction request
    $('.delete-deduction').on('click', function () {
        var deductId = $(this).data('id');

        $('#delete_deduction_id').val(deductId);
        $('#deleteDeductionForm').attr('action', '/admin/deduction/delete/' + deductId);
        $('#delete_deduction').modal('show');
    });

</script>

<script>
    // JavaScript to disable the inclusion limit when "Every Payroll" is selected
    document.getElementById('is_every_payroll').addEventListener('change', function () {
        var inclusionLimit = document.getElementById('inclusion_limit');
        if (this.value == 1) { // If "Yes" is selected (value=1)
            inclusionLimit.disabled = true;
            inclusionLimit.value = ''; // Clear the value
        } else {
            inclusionLimit.disabled = false;
        }
    });

</script>

<script>
    // JavaScript to disable the inclusion limit when "Every Payroll" is selected
    document.getElementById('is_every_payrolle').addEventListener('change', function () {
        var inclusionLimit = document.getElementById('inclusion_limite');
        if (this.value == 1) { // If "Yes" is selected (value=1)
            inclusionLimit.disabled = true;
            inclusionLimit.value = ''; // Clear the value
        } else {
            inclusionLimit.disabled = false;
        }
    });

</script>


@endsection
