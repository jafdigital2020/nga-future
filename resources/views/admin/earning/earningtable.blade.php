@extends('layouts.master') @section('title', 'Earnings') @section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Earnings</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Earnings</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#create_earning"><i
                        class="fa fa-plus"></i>
                    Create Earning</a>
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
                            <th>Earning Name</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Inclusion Limit</th>
                            <th>Every Payroll</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($earnings as $earning)
                        <tr>
                            <td>{{ $earning->name }}</td>
                            <td>{{ $earning->amount }}</td>
                            <td>{{ $earning->type }}</td>
                            <td>
                                @if($earning->is_every_payroll)
                                -
                                @else
                                {{ $earning->inclusion_limit ?? 'Unlimited' }}

                                @endif
                            </td>
                            <td>
                                {{ $earning->is_every_payroll ? 'Yes' : 'No' }}

                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item edit-earning" href="#" data-id="{{ $earning->id }}"
                                            data-name="{{ $earning->name }}" data-amount="{{ $earning->amount }}"
                                            data-type="{{ $earning->type }}"
                                            data-inclusion_limit="{{ $earning->inclusion_limit }}"
                                            data-is_every_payroll="{{ $earning->is_every_payroll }}">
                                            <i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item delete-earning" href="#" data-id="{{ $earning->id }}">
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

<!-- Create Earning Modal -->
<div id="create_earning" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Earning</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.createEarning') }}">
                    @csrf
                    <div class="form-group">
                        <label>Earning Name<span class="text-danger">*</span></label>
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
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="inclusion_limit">Inclusion Limit</label>
                                <input type="number" class="form-control" id="inclusion_limit" name="inclusion_limit"
                                    min="1">
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
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Create Earning Modal -->

<!-- Edit Earning Modal -->
<div id="edit_earning" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Earning</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="editEarningForm">
                    @csrf
                    <input type="hidden" name="earning_id" id="earning_ide">
                    <div class="form-group">
                        <label>Earning Name<span class="text-danger">*</span></label>
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
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="inclusion_limit">Inclusion Limit</label>
                                    <input type="number" class="form-control" id="inclusion_limite"
                                        name="inclusion_limite" min="1">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="is_every_payroll">Include in Every Payroll?</label>
                                    <select class="form-control" id="is_every_payrolle" name="is_every_payrolle"
                                        required>
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
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
<!-- /Edit Earning Modal -->

<!-- Delete Earning Modal -->
<div class="modal custom-modal fade" id="delete_earning" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Earning</h3>
                    <p>Are you sure you want to cancel this earning?</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-5">
                            <form id="deleteEarningForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="earning_id" id="earning_id">
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
<!-- /Delete Earning Modal -->

@endsection

@section('scripts')

<script>
    // Edit Earning request
    $('.edit-earning').on('click', function () {
        var earningId = $(this).data('id');
        var name = $(this).data('name');
        var amount = $(this).data('amount');
        var type = $(this).data('type');
        var limit = $(this).data('inclusion_limit');
        var everyPayroll = $(this).data('is_every_payroll');

        $('#earning_ide').val(earningId);
        $('#namee').val(name);
        $('#amounte').val(amount);
        $('#typee').val(type);
        $('#inclusion_limite').val(limit);
        $('#is_every_payrolle').val(everyPayroll);


        $('#editEarningForm').attr('action', '/admin/earning/edit/' +
            earningId);
        $('#edit_earning').modal('show');
    });

    // Delete Earning request
    $('.delete-earning').on('click', function () {
        var earningId = $(this).data('id');

        $('#delete_earning_id').val(earningId);
        $('#deleteEarningForm').attr('action', '/admin/earning/delete/' + earningId);
        $('#delete_earning').modal('show');
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
