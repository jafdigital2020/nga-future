@extends('layouts.master') @section('title', 'Expenses') @section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Expenses</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                    <li class="breadcrumb-item active">Expenses</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_expense"><i
                        class="fa fa-plus"></i> Add Expense</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    <div class="row filter-row">
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <div class="form-group form-focus">
                <input type="text" class="form-control floating">
                <label class="focus-label">Item Name</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <div class="form-group form-focus select-focus">
                <select class="select floating">
                    <option> -- Select -- </option>
                    <option>Loren Gatlin</option>
                    <option>Tarah Shropshire</option>
                </select>
                <label class="focus-label">Purchased By</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <div class="form-group form-focus select-focus">
                <select class="select floating">
                    <option> -- Select -- </option>
                    <option> Cash </option>
                    <option> Cheque </option>
                </select>
                <label class="focus-label">Paid By</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <div class="form-group form-focus">
                <div class="cal-icon">
                    <input class="form-control floating datetimepicker" type="text">
                </div>
                <label class="focus-label">From</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <div class="form-group form-focus">
                <div class="cal-icon">
                    <input class="form-control floating datetimepicker" type="text">
                </div>
                <label class="focus-label">To</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <a href="#" class="btn btn-success btn-block"> Search </a>
        </div>
    </div>
    <!-- /Search Filter -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Purchase From</th>
                            <th>Purchase Date</th>
                            <th>Purchased By</th>
                            <th>Amount</th>
                            <th>Paid By</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>Dell Laptop</strong>
                            </td>
                            <td>Amazon</td>
                            <td>5 Jan 2019</td>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="profile.html" class="avatar avatar-xs"><img
                                            src="assets/img/profiles/avatar-04.jpg" alt=""></a>
                                    <a href="profile.html">Loren Gatlin</a>
                                </h2>
                            </td>
                            <td>$1215</td>
                            <td>Cash</td>
                            <td class="text-center">
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-dot-circle-o text-danger"></i> Pending
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-danger"></i>
                                            Pending</a>
                                        <a class="dropdown-item" href="#"><i
                                                class="fa fa-dot-circle-o text-success"></i> Approved</a>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#edit_expense"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#delete_expense"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Mac System</strong>
                            </td>
                            <td>Amazon</td>
                            <td>5 Jan 2019</td>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="profile.html" class="avatar avatar-xs"><img
                                            src="assets/img/profiles/avatar-03.jpg" alt=""></a>
                                    <a href="profile.html">Tarah Shropshire</a>
                                </h2>
                            </td>
                            <td>$1215</td>
                            <td>Cheque</td>
                            <td class="text-center">
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-dot-circle-o text-success"></i> Approved
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-danger"></i>
                                            Pending</a>
                                        <a class="dropdown-item" href="#"><i
                                                class="fa fa-dot-circle-o text-success"></i> Approved</a>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#edit_expense"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#delete_expense"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->

<!-- Add Expense Modal -->
<div id="add_expense" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Name</label>
                                <input class="form-control" type="text">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Purchase From</label>
                                <input class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Purchase Date</label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Purchased By </label>
                                <select class="select">
                                    <option>Daniel Porter</option>
                                    <option>Roger Dixon</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Amount</label>
                                <input placeholder="$50" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Paid By</label>
                                <select class="select">
                                    <option>Cash</option>
                                    <option>Cheque</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="select">
                                    <option>Pending</option>
                                    <option>Approved</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Attachments</label>
                                <input class="form-control" type="file">
                            </div>
                        </div>
                    </div>
                    <div class="attach-files">
                        <ul>
                            <li>
                                <img src="assets/img/placeholder.jpg" alt="">
                                <a href="#" class="fa fa-close file-remove"></a>
                            </li>
                            <li>
                                <img src="assets/img/placeholder.jpg" alt="">
                                <a href="#" class="fa fa-close file-remove"></a>
                            </li>
                        </ul>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Expense Modal -->

<!-- Edit Expense Modal -->
<div id="edit_expense" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Name</label>
                                <input class="form-control" value="Dell Laptop" type="text">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Purchase From</label>
                                <input class="form-control" value="Amazon" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Purchase Date</label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Purchased By </label>
                                <select class="select">
                                    <option>Daniel Porter</option>
                                    <option>Roger Dixon</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Amount</label>
                                <input placeholder="$50" class="form-control" value="$10000" type="text">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Paid By</label>
                                <select class="select">
                                    <option>Cash</option>
                                    <option>Cheque</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="select">
                                    <option>Pending</option>
                                    <option>Approved</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Attachments</label>
                                <input class="form-control" type="file">
                            </div>
                        </div>
                    </div>
                    <div class="attach-files">
                        <ul>
                            <li>
                                <img src="assets/img/placeholder.jpg" alt="">
                                <a href="#" class="fa fa-close file-remove"></a>
                            </li>
                            <li>
                                <img src="assets/img/placeholder.jpg" alt="">
                                <a href="#" class="fa fa-close file-remove"></a>
                            </li>
                        </ul>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Expense Modal -->

<!-- Delete Expense Modal -->
<div class="modal custom-modal fade" id="delete_expense" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Expense</h3>
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
<!-- Delete Expense Modal -->


@endsection

@section('scripts')

@endsection
