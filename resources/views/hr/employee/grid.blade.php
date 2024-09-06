@extends('layouts.hrmaster') @section('title', 'Employees') @section('content')
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
                        <a href="{{ url('hr/dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Employee</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="{{ url('hr/employee/create') }}" class="btn add-btn"></i> Add Employee</a>
                <div class="view-icons">
                    <a href="{{ url('hr/employee-grid') }}" class="grid-view btn btn-link"><i class="fa fa-th"></i></a>
                    <a href="{{ url('hr/employee') }}" class="list-view btn btn-link active"><i
                            class="fa fa-bars"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    <form action="{{ route('hr.employeegrid') }}" method="GET">
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
                    <input type="text" class="form-control floating" name="name" />
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
                        <a href="{{ url('hr/employee/edit/'.$user->id) }}" class="avatar">
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
                            <a class="dropdown-item" href="{{ url('hr/employee/edit/'.$user->id) }}">
                                <i class="fa fa-pencil m-r-5"></i> Edit
                            </a>
                            <a class="dropdown-item" href="#" data-toggle="modal"
                                data-target="#delete_employee_{{ $user->id }}">
                                <i class="fa fa-trash-o m-r-5"></i> Delete
                            </a>
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

    @endsection
