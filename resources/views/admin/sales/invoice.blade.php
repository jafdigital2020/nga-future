@extends('layouts.master') @section('title', 'Invoices') @section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Invoices</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                    <li class="breadcrumb-item active">Invoices</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="create-invoice.html" class="btn add-btn"><i class="fa fa-plus"></i> Create Invoice</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    <div class="row filter-row">
        <div class="col-sm-6 col-md-3">
            <div class="form-group form-focus">
                <div class="cal-icon">
                    <input class="form-control floating datetimepicker" type="text">
                </div>
                <label class="focus-label">From</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group form-focus">
                <div class="cal-icon">
                    <input class="form-control floating datetimepicker" type="text">
                </div>
                <label class="focus-label">To</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group form-focus select-focus">
                <select class="select floating">
                    <option>Select Status</option>
                    <option>Pending</option>
                    <option>Paid</option>
                    <option>Partially Paid</option>
                </select>
                <label class="focus-label">Status</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <a href="#" class="btn btn-success btn-block"> Search </a>
        </div>
    </div>
    <!-- /Search Filter -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice Number</th>
                            <th>Client</th>
                            <th>Created Date</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><a href="invoice-view.html">#INV-0001</a></td>
                            <td>Global Technologies</td>
                            <td>11 Mar 2019</td>
                            <td>17 Mar 2019</td>
                            <td>$2099</td>
                            <td><span class="badge bg-inverse-success">Paid</span></td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="edit-invoice.html"><i
                                                class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item" href="invoice-view.html"><i
                                                class="fa fa-eye m-r-5"></i> View</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-file-pdf-o m-r-5"></i>
                                            Download</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td><a href="invoice-view.html">#INV-0002</a></td>
                            <td>Delta Infotech</td>
                            <td>11 Mar 2019</td>
                            <td>17 Mar 2019</td>
                            <td>$2099</td>
                            <td><span class="badge bg-inverse-info">Sent</span></td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="edit-invoice.html"><i
                                                class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item" href="invoice-view.html"><i
                                                class="fa fa-eye m-r-5"></i> View</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-file-pdf-o m-r-5"></i>
                                            Download</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td><a href="invoice-view.html">#INV-0003</a></td>
                            <td>Cream Inc</td>
                            <td>11 Mar 2019</td>
                            <td>17 Mar 2019</td>
                            <td>$2099</td>
                            <td><span class="badge bg-inverse-warning">Partially Paid</span></td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="edit-invoice.html"><i
                                                class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item" href="invoice-view.html"><i
                                                class="fa fa-eye m-r-5"></i> View</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-file-pdf-o m-r-5"></i>
                                            Download</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
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

@endsection

@section('scripts')

@endsection
