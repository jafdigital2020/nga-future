@extends('layouts.master') @section('title', 'Training') @section('content')
@include('sweetalert::alert')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Training</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                    <li class="breadcrumb-item active">Training</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_training"><i
                        class="fa fa-plus"></i> Add New </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th>Training Type</th>
                            <th>Trainer</th>
                            <th>Employee</th>
                            <th>Time Duration</th>
                            <th>Description </th>
                            <th>Cost </th>
                            <th>Status </th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Git Training</td>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="profile.html" class="avatar"><img alt=""
                                            src="assets/img/profiles/avatar-02.jpg"></a>
                                    <a href="profile.html">John Doe </a>
                                </h2>
                            </td>
                            <td>
                                <ul class="team-members">
                                    <li>
                                        <a href="#" title="Bernardo Galaviz" data-toggle="tooltip"><img alt=""
                                                src="assets/img/profiles/avatar-10.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" title="Richard Miles" data-toggle="tooltip"><img alt=""
                                                src="assets/img/profiles/avatar-09.jpg"></a>
                                    </li>
                                    <li class="dropdown avatar-dropdown">
                                        <a href="#" class="all-users dropdown-toggle" data-toggle="dropdown"
                                            aria-expanded="false">+15</a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <div class="avatar-group">
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-02.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-09.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-10.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-05.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-11.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-12.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-13.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-01.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-16.jpg">
                                                </a>
                                            </div>
                                            <div class="avatar-pagination">
                                                <ul class="pagination">
                                                    <li class="page-item">
                                                        <a class="page-link" href="#" aria-label="Previous">
                                                            <span aria-hidden="true">«</span>
                                                            <span class="sr-only">Previous</span>
                                                        </a>
                                                    </li>
                                                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                    <li class="page-item">
                                                        <a class="page-link" href="#" aria-label="Next">
                                                            <span aria-hidden="true">»</span>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </td>
                            <td>7 May 2019 - 10 May 2019</td>
                            <td>Lorem ipsum dollar</td>
                            <td>$400</td>
                            <td>
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-dot-circle-o text-success"></i> Active
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#"><i
                                                class="fa fa-dot-circle-o text-success"></i> Active</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-danger"></i>
                                            Inactive</a>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#edit_training"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#delete_training"><i class="fa fa-trash-o m-r-5"></i>
                                            Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Swift Training</td>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="profile.html" class="avatar"><img alt=""
                                            src="assets/img/profiles/avatar-09.jpg"></a>
                                    <a href="profile.html">Richard Miles</a>
                                </h2>
                            </td>
                            <td>
                                <ul class="team-members">
                                    <li>
                                        <a href="#" title="John Doe" data-toggle="tooltip"><img alt=""
                                                src="assets/img/profiles/avatar-02.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" title="Richard Miles" data-toggle="tooltip"><img alt=""
                                                src="assets/img/profiles/avatar-09.jpg"></a>
                                    </li>
                                    <li class="dropdown avatar-dropdown">
                                        <a href="#" class="all-users dropdown-toggle" data-toggle="dropdown"
                                            aria-expanded="false">+15</a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <div class="avatar-group">
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-02.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-09.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-10.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-05.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-11.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-12.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-13.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-01.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-16.jpg">
                                                </a>
                                            </div>
                                            <div class="avatar-pagination">
                                                <ul class="pagination">
                                                    <li class="page-item">
                                                        <a class="page-link" href="#" aria-label="Previous">
                                                            <span aria-hidden="true">«</span>
                                                            <span class="sr-only">Previous</span>
                                                        </a>
                                                    </li>
                                                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                    <li class="page-item">
                                                        <a class="page-link" href="#" aria-label="Next">
                                                            <span aria-hidden="true">»</span>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </td>
                            <td>7 May 2019 - 10 May 2019</td>
                            <td>Lorem ipsum dollar</td>
                            <td>$800</td>
                            <td>
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-dot-circle-o text-danger"></i> Inactive
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#"><i
                                                class="fa fa-dot-circle-o text-success"></i> Active</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-danger"></i>
                                            Inactive</a>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#edit_training"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#delete_training"><i class="fa fa-trash-o m-r-5"></i>
                                            Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Node Training</td>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="profile.html" class="avatar"><img alt=""
                                            src="assets/img/profiles/avatar-02.jpg"></a>
                                    <a href="profile.html">John Doe </a>
                                </h2>
                            </td>
                            <td>
                                <ul class="team-members">
                                    <li>
                                        <a href="#" title="Bernardo Galaviz" data-toggle="tooltip"><img alt=""
                                                src="assets/img/profiles/avatar-10.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" title="Richard Miles" data-toggle="tooltip"><img alt=""
                                                src="assets/img/profiles/avatar-09.jpg"></a>
                                    </li>
                                    <li class="dropdown avatar-dropdown">
                                        <a href="#" class="all-users dropdown-toggle" data-toggle="dropdown"
                                            aria-expanded="false">+15</a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <div class="avatar-group">
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-02.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-09.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-10.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-05.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-11.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-12.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-13.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-01.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-16.jpg">
                                                </a>
                                            </div>
                                            <div class="avatar-pagination">
                                                <ul class="pagination">
                                                    <li class="page-item">
                                                        <a class="page-link" href="#" aria-label="Previous">
                                                            <span aria-hidden="true">«</span>
                                                            <span class="sr-only">Previous</span>
                                                        </a>
                                                    </li>
                                                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                    <li class="page-item">
                                                        <a class="page-link" href="#" aria-label="Next">
                                                            <span aria-hidden="true">»</span>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </td>
                            <td>7 May 2019 - 10 May 2019</td>
                            <td>Lorem ipsum dollar</td>
                            <td>$400</td>
                            <td>
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-dot-circle-o text-success"></i> Active
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#"><i
                                                class="fa fa-dot-circle-o text-success"></i> Active</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-danger"></i>
                                            Inactive</a>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#edit_training"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#delete_training"><i class="fa fa-trash-o m-r-5"></i>
                                            Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Angular Training</td>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="profile.html" class="avatar"><img alt=""
                                            src="assets/img/profiles/avatar-05.jpg"></a>
                                    <a href="profile.html">Mike Litorus </a>
                                </h2>
                            </td>
                            <td>
                                <ul class="team-members">
                                    <li>
                                        <a href="#" title="Bernardo Galaviz" data-toggle="tooltip"><img alt=""
                                                src="assets/img/profiles/avatar-10.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" title="Richard Miles" data-toggle="tooltip"><img alt=""
                                                src="assets/img/profiles/avatar-09.jpg"></a>
                                    </li>
                                    <li class="dropdown avatar-dropdown">
                                        <a href="#" class="all-users dropdown-toggle" data-toggle="dropdown"
                                            aria-expanded="false">+15</a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <div class="avatar-group">
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-02.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-09.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-10.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-05.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-11.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-12.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-13.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-01.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-16.jpg">
                                                </a>
                                            </div>
                                            <div class="avatar-pagination">
                                                <ul class="pagination">
                                                    <li class="page-item">
                                                        <a class="page-link" href="#" aria-label="Previous">
                                                            <span aria-hidden="true">«</span>
                                                            <span class="sr-only">Previous</span>
                                                        </a>
                                                    </li>
                                                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                    <li class="page-item">
                                                        <a class="page-link" href="#" aria-label="Next">
                                                            <span aria-hidden="true">»</span>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </td>
                            <td>7 May 2019 - 10 May 2019</td>
                            <td>Lorem ipsum dollar</td>
                            <td>$400</td>
                            <td>
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-dot-circle-o text-success"></i> Active
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#"><i
                                                class="fa fa-dot-circle-o text-success"></i> Active</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-danger"></i>
                                            Inactive</a>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#edit_training"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#delete_training"><i class="fa fa-trash-o m-r-5"></i>
                                            Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Git Training</td>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="profile.html" class="avatar"><img alt=""
                                            src="assets/img/profiles/avatar-11.jpg"></a>
                                    <a href="profile.html">Wilmer Deluna </a>
                                </h2>
                            </td>
                            <td>
                                <ul class="team-members">
                                    <li>
                                        <a href="#" title="Bernardo Galaviz" data-toggle="tooltip"><img alt=""
                                                src="assets/img/profiles/avatar-10.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" title="Richard Miles" data-toggle="tooltip"><img alt=""
                                                src="assets/img/profiles/avatar-09.jpg"></a>
                                    </li>
                                    <li class="dropdown avatar-dropdown">
                                        <a href="#" class="all-users dropdown-toggle" data-toggle="dropdown"
                                            aria-expanded="false">+15</a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <div class="avatar-group">
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-02.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-09.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-10.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-05.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-11.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-12.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-13.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-01.jpg">
                                                </a>
                                                <a class="avatar avatar-xs" href="#">
                                                    <img alt="" src="assets/img/profiles/avatar-16.jpg">
                                                </a>
                                            </div>
                                            <div class="avatar-pagination">
                                                <ul class="pagination">
                                                    <li class="page-item">
                                                        <a class="page-link" href="#" aria-label="Previous">
                                                            <span aria-hidden="true">«</span>
                                                            <span class="sr-only">Previous</span>
                                                        </a>
                                                    </li>
                                                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                    <li class="page-item">
                                                        <a class="page-link" href="#" aria-label="Next">
                                                            <span aria-hidden="true">»</span>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </td>
                            <td>7 May 2019 - 10 May 2019</td>
                            <td>Lorem ipsum dollar</td>
                            <td>$400</td>
                            <td>
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-dot-circle-o text-success"></i> Active
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#"><i
                                                class="fa fa-dot-circle-o text-success"></i> Active</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-danger"></i>
                                            Inactive</a>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#edit_training"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#delete_training"><i class="fa fa-trash-o m-r-5"></i>
                                            Delete</a>
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

<!-- Add Training List Modal -->
<div id="add_training" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Training</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Training Type</label>
                                <select class="form-control">
                                    <option>Node Training</option>
                                    <option>Swift Training</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Trainer</label>
                                <select class="form-control">
                                    <option>Mike Litorus </option>
                                    <option>John Doe</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Employees</label>
                                <select class="form-control">
                                    <option>Bernardo Galaviz</option>
                                    <option>Jeffrey Warden</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Training Cost <span class="text-danger">*</span></label>
                                <input class="form-control" type="text">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Start Date <span class="text-danger">*</span></label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>End Date <span class="text-danger">*</span></label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text"></div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Status</label>
                                <select class="form-control">
                                    <option>Active</option>
                                    <option>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Training List Modal -->

<!-- Edit Training List Modal -->
<div id="edit_training" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Training List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Training Type</label>
                                <select class="select">
                                    <option selected>Node Training</option>
                                    <option>Swift Training</option>
                                    <option>Git Training</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Trainer</label>
                                <select class="select">
                                    <option>Mike Litorus </option>
                                    <option selected>John Doe</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Employees</label>
                                <select class="select">
                                    <option>Bernardo Galaviz</option>
                                    <option selected>Jeffrey Warden</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Training Cost <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" value="$400">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Start Date <span class="text-danger">*</span></label>
                                <div class="cal-icon"><input class="form-control datetimepicker" value="07-08-2019"
                                        type="text"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>End Date <span class="text-danger">*</span></label>
                                <div class="cal-icon"><input class="form-control datetimepicker" value="10-08-2019"
                                        type="text"></div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" rows="4">Lorem ipsum ismap</textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Status</label>
                                <select class="select">
                                    <option selected>Active</option>
                                    <option>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Training List Modal -->

<!-- Delete Training List Modal -->
<div class="modal custom-modal fade" id="delete_training" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Training List</h3>
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
<!-- /Delete Training List Modal -->

@endsection
