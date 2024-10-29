@extends('layouts.master') @section('title', 'Loan') @section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Loan</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Loan</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#create_loan"><i
                        class="fa fa-plus"></i>
                    Create Loan</a>
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
                            <th>Employee</th>
                            <th>Loan Name</th>
                            <th>Amount</th>
                            <th>Payable in Cutoffs</th>
                            <th>Payable Amount per Cutoffs</th>
                            <th>Status</th>
                            <th>Date Applied</th>
                            <th>Date Completed</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $loan)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="{{ url('admin/employee/edit/'. $loan->user->id) }}" class="avatar"><img
                                            alt="" src="{{ asset('images/' . $loan->user->image) }}"></a>
                                    <a href="{{ url('admin/employee/edit/'. $loan->user->id) }}">{{ $loan->user->fName }}
                                        {{ $loan->user->lName }}
                                        <span>{{ $loan->user->position }}</span></a>
                                </h2>
                            </td>
                            <td>{{ $loan->loan_name }}</td>
                            <td>{{ $loan->amount }}</td>
                            <td class="text-center">{{ $loan->payable_in_cutoff }}</td>
                            <td class="text-center">{{ $loan->payable_amount_per_cutoff }}</td>
                            <td class="text-center">
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        @if($loan->status == 'Active')
                                        <i class="fa fa-dot-circle-o text-info"></i> Active
                                        @elseif($loan->status == 'Completed')
                                        <i class="fa fa-dot-circle-o text-success"></i> Completed
                                        @elseif($loan->status == 'Hold')
                                        <i class="fa fa-dot-circle-o text-danger"></i> Hold
                                        @else
                                        <i class="fa fa-dot-circle-o text-secondary"></i> Unknown Status
                                        @endif
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">

                                        <form id="active-form-{{ $loan->id }}"
                                            action="{{ url('admin/loan/active/' . $loan->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item active-button"
                                                data-loan-id="{{ $loan->id }}">
                                                <i class="fa fa-dot-circle-o text-info"></i> Active
                                            </button>
                                        </form>
                                        <form id="complete-form-{{ $loan->id }}"
                                            action="{{ url('admin/loan/complete/' . $loan->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item complete-button"
                                                data-loan-id="{{ $loan->id }}">
                                                <i class="fa fa-dot-circle-o text-success"></i> Completed
                                            </button>
                                        </form>
                                        <form id="hold-form-{{ $loan->id }}"
                                            action="{{ url('admin/loan/hold/' . $loan->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item hold-button"
                                                data-loan-id="{{ $loan->id }}">
                                                <i class="fa fa-dot-circle-o text-danger"></i> Hold
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>

                            <td>{{ $loan->created_at->toDateString() }}</td>
                            <td>
                                @if($loan->date_completed)
                                <span class="badge bg-success">{{ $loan->date_completed }}</span>
                                @else
                                <span class="badge bg-warning">In Progress</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item edit-loan" href="#" data-id="{{ $loan->id }}"
                                            data-loan_name="{{ $loan->loan_name }}" data-amount="{{ $loan->amount }}"
                                            data-payable_in_cutoff="{{ $loan->payable_in_cutoff }}"
                                            data-payable_amount_per_cutoff="{{ $loan->payable_amount_per_cutoff }}">
                                            <i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item delete-loan" href="#" data-id="{{ $loan->id }}">
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

<!-- Create Loan Modal -->
<div id="create_loan" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Loan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.storeloan') }}">
                    @csrf
                    <div class="form-group">
                        <label>Employee Name<span class="text-danger"> *</span></label>
                        <div class="form-group form-focus select-focus">
                            <select class="form-control" name="users_id" id="users_id" required>
                                <option value="">-</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->fName }} {{ $user->lName }}</option>
                                @endforeach
                            </select><label class="focus-label">Select Employee</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Loan Name<span class="text-danger"> *</span></label>
                        <input class="form-control" type="text" name="loan_name" id="loan_name" required>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Amount<span class="text-danger"> *</span></label>
                                <input class="form-control" type="text" name="amount" id="amount" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Payable in Cutoffs<span class="text-danger"> *</span></label>
                                <input class="form-control" type="number" name="payable_in_cutoff"
                                    id="payable_in_cutoff" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Payable amount per Cutoff<span class="text-danger"> *</span></label>
                                <input class="form-control" type="number" name="payable_amount_per_cutoff"
                                    id="payable_amount_per_cutoff" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Create Loan Modal -->

<!-- Edit Loan Modal -->
<div id="edit_loan" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Loan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="editLoanForm">
                    @csrf
                    <input type="hidden" name="loan_ide" id="loan_ide">
                    <div class="form-group">
                        <label>Loan Name<span class="text-danger"> *</span></label>
                        <input class="form-control" type="text" name="loan_namee" id="loan_namee" required>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Amount<span class="text-danger"> *</span></label>
                                <input class="form-control" type="text" name="amounte" id="amounte" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Payable in Cutoffs<span class="text-danger"> *</span></label>
                                <input class="form-control" type="number" name="payable_in_cutoffe"
                                    id="payable_in_cutoffe" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Payable amount per Cutoff<span class="text-danger"> *</span></label>
                                <input class="form-control" type="number" name="payable_amount_per_cutoffe"
                                    id="payable_amount_per_cutoffe" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Loan Modal -->

<!-- Delete Earning Modal -->
<div class="modal custom-modal fade" id="delete_loan" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Loan</h3>
                    <p>Are you sure you want to cancel this loan??</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-5">
                            <form id="deleteLoanForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="loan_id" id="loan_id">
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
    // Edit Loan request
    $('.edit-loan').on('click', function () {
        var loanId = $(this).data('id');
        var loanName = $(this).data('loan_name');
        var amount = $(this).data('amount');
        var payableInCutoff = $(this).data('payable_in_cutoff');
        var payableAmountCutoff = $(this).data('payable_amount_per_cutoff');

        $('#loan_ide').val(loanId);
        $('#loan_namee').val(loanName);
        $('#amounte').val(amount);
        $('#payable_in_cutoffe').val(payableInCutoff);
        $('#payable_amount_per_cutoffe').val(payableAmountCutoff);

        $('#editLoanForm').attr('action', '/admin/loan/edit/' +
            loanId);
        $('#edit_loan').modal('show');
    });

    // Delete Loan request
    $('.delete-loan').on('click', function () {
        var loanId = $(this).data('id');

        $('#delete_loan_id').val(loanId);
        $('#deleteLoanForm').attr('action', '/admin/loan/delete/' + loanId);
        $('#delete_loan').modal('show');
    });

</script>

<!-- Create Loan Script -->

<script>
    // Function to calculate payable amount per cutoff
    function calculatePayableAmount() {
        var amount = parseFloat(document.getElementById('amount').value) || 0;
        var payableInCutoff = parseInt(document.getElementById('payable_in_cutoff').value) || 1;

        // Ensure we do not divide by zero
        if (payableInCutoff <= 0) {
            payableInCutoff = 1; // Set minimum cutoff to 1 to avoid division by zero
        }

        // Calculate the payable amount per cutoff
        var payableAmountPerCutoff = amount / payableInCutoff;

        // Set the value to the payable_amount_per_cutoff input
        document.getElementById('payable_amount_per_cutoff').value = payableAmountPerCutoff;
    }

    // Add event listeners to trigger calculation in real-time when amount or payable_in_cutoff changes
    document.getElementById('amount').addEventListener('input', calculatePayableAmount);
    document.getElementById('payable_in_cutoff').addEventListener('input', calculatePayableAmount);

</script>

<!-- Edit Loan Script -->

<script>
    // Function to calculate payable amount per cutoff
    function calculatePayableAmount() {
        var amount = parseFloat(document.getElementById('amounte').value) || 0;
        var payableInCutoff = parseInt(document.getElementById('payable_in_cutoffe').value) || 1;

        // Ensure we do not divide by zero
        if (payableInCutoff <= 0) {
            payableInCutoff = 1; // Set minimum cutoff to 1 to avoid division by zero
        }

        // Calculate the payable amount per cutoff
        var payableAmountPerCutoff = amount / payableInCutoff;

        // Set the value to the payable_amount_per_cutoff input
        document.getElementById('payable_amount_per_cutoffe').value = payableAmountPerCutoff;
    }

    // Add event listeners to trigger calculation in real-time when amount or payable_in_cutoff changes
    document.getElementById('amounte').addEventListener('input', calculatePayableAmount);
    document.getElementById('payable_in_cutoffe').addEventListener('input', calculatePayableAmount);

</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var completeButton = document.querySelectorAll('.complete-button');
        var holdButton = document.querySelectorAll('.hold-button');
        var activeButton = document.querySelectorAll('.active-button');

        completeButton.forEach(function (button) {
            button.addEventListener('click', function () {
                var loanId = button.getAttribute('data-loan-id');
                confirmComplete(loanId);
            });
        });

        activeButton.forEach(function (button) {
            button.addEventListener('click', function () {
                var loanId = button.getAttribute('data-loan-id');
                confirmActive(loanId);
            });
        });


        holdButton.forEach(function (button) {
            button.addEventListener('click', function () {
                var loanId = button.getAttribute('data-loan-id');
                confirmHold(loanId);
            });
        });
    });

    function confirmComplete(loanId) {
        var form = document.getElementById('complete-form-' + loanId);
        var confirmAction = confirm("Are you sure you want to complete this?");
        if (confirmAction) {

            form.submit();
        }
    }

    function confirmActive(loanId) {
        var form = document.getElementById('active-form-' + loanId);
        var confirmAction = confirm("Are you sure you want to change the status to 'Active' ?");
        if (confirmAction) {

            form.submit();
        }
    }

    function confirmHold(loanId) {
        var form = document.getElementById('hold-form-' + loanId);
        var confirmAction = confirm("Are you sure you want to hold this?");
        if (confirmAction) {
            form.submit();
        }
    }

</script>


@endsection
