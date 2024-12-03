@extends('layouts.master') @section('title', 'Employees') @section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Employee</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ url('admin/dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Employee</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="{{ url('admin/employee/create') }}" class="btn add-btn"><i class="fa fa-plus"></i> Add
                    Employee</a>
                <div class="view-icons">
                    <a href="{{ url('admin/employee-grid') }}"
                        class="grid-view btn btn-link {{ Request::is('admin/employee-grid') ? 'active':'' }}"><i
                            class="fa fa-th"></i></a>
                    <a href="{{ url('admin/employee') }}"
                        class="list-view btn btn-link {{ Request::is('admin/employee') ? 'active':'' }}"><i
                            class="fa fa-bars"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    <form action="{{ route('admin.employeegrid') }}" method="GET">
        @csrf
        <div class="row filter-row">
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus">
                    <input type="text" class="form-control floating" name="empNumber" />
                    <label class="focus-label">Employee ID</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus">
                    <input type="text" class="form-control floating" name="name" list="userName" />
                    <datalist id="userName">
                        @foreach ($names as $name)
                        <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </datalist>
                    <label class="focus-label">Employee Name</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="department">
                        <option value="">--Select Department--</option>
                        @foreach ($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                    <label class="focus-label">Department</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <button type="submit" class="btn btn-danger btn-block">Search</button>
            </div>
        </div>
    </form>
    <!-- /Search Filter -->

    <div class="row">
        <div class="row staff-grid-row">
            @foreach($emp as $user)
            <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
                <div class="profile-widget">
                    <div class="profile-img">
                        <a href="{{ url('admin/employee/edit/'.$user->id) }}" class="avatar">
                            @if ($user->image)
                            <img src="{{ asset('images/' . $user->image) }}" alt="Profile Image" />
                            @else
                            <img src="{{
                                    asset('images/default.png')
                                }}" alt="Profile Image" />
                            @endif</a>
                    </div>
                    <div class="dropdown profile-action">
                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ url('admin/employee/edit/'.$user->id) }}">
                                <i class="fa fa-pencil m-r-5"></i> Edit
                            </a>
                            <button type="button" data-toggle="modal" data-target="#delete_user"
                                class="dropdown-item deleteBtn" value="{{ $user->id }}"><i
                                    class="fa fa-trash-o m-r-5"></i>Delete</button>
                        </div>
                    </div>
                    <h4 class="user-name m-t-10 mb-0 text-ellipsis">
                        <a href="#">{{ $user->fName }} {{ $user->lName }}</a>
                    </h4>
                    <div class="small text-muted">{{ $user->position }}</div>
                </div>
            </div>

            @endforeach
        </div>
    </div>


    <!-- Inactive User Modal -->
    <div class="modal custom-modal fade" id="delete_user" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ url('admin/employee/delete') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-header">
                            <h3>Deactivate User</h3>
                            <p>Are you sure you want to delete this user?</p>
                        </div>
                        <div class="modal-btn delete-action">
                            <div class="row">
                                <div class="col-5">
                                    <input type="hidden" name="emp_delete_id" id="emp_id">
                                    <button class="btn add-btn" type="submit">Delete</button>
                                </div>
                                <div class="col-6">
                                    <a href="javascript:void(0);" data-dismiss="modal" class="btn add-btn">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Inactive User Modal -->

    @endsection

    @section('scripts')
    <script>
        $(document).ready(function () {
            $('.deleteBtn').click(function (e) {
                e.preventDefault(); // Prevent default action

                var emp_id = $(this).val(); // Get the employee ID from the button's value
                $('#emp_id').val(emp_id); // Set the employee ID in the hidden input field
                $('#delete_user').modal('show'); // Corrected method to show the modal
            });
        });

    </script>
    @endsection
