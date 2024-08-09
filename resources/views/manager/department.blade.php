@extends('layouts.managermaster') @section('title', 'Department')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Department</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ url('manager/dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Employee</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->


    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table datatable" id="dataTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Employee ID</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th class="text-nowrap">Hired Date</th>
                            <th>Department</th>
                            <th class="text-right no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $employee)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="profile.html" class="avatar">
                                        @if ($employee->image)
                                        <img src="{{ asset('images/' . $employee->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{
                                                asset('images/default.png')
                                            }}" alt="Profile Image" />
                                        @endif</a>
                                    <a href="{{ url('manager/department-record/'.$employee->id) }}">
                                        @if ( $employee->fName || $employee->lName )
                                        {{ $employee->fName }} {{ $employee->lName }}
                                        @else
                                        {{ $employee->name }}
                                        @endif
                                        <span>@if($employee->position)
                                            {{ $employee->position }}
                                            @else
                                            Please set the position.
                                            @endif</span></a>
                                </h2>
                            </td>
                            <td>{{ $employee->empNumber }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->phoneNumber }}</td>
                            <td>{{ $employee->dateHired }}</td>
                            <td>{{ $employee->department }}</td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item"
                                            href="{{ url('manager/department-record/'.$employee->id) }}?tab=emp_record"><i
                                                class="fa fa-address-book m-r-5"></i>Employee
                                            Record</a>
                                        <a class="dropdown-item"
                                            href="{{ url('manager/department-record/'.$employee->id) }}?tab=emp_salary"><i
                                                class="fa fa-address-book m-r-5"></i>Employee
                                            Salary</a>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>


@endsection
