@extends('layouts.master') @section('title', 'Inactive Employees')
@include('sweetalert::alert')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')


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
                    <li class="breadcrumb-item active">Inactive Employee</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-hover table-nowrap custom-table datatable" id="dataTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Employee ID</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th class="text-nowrap">Join Date</th>
                            <th>Department</th>
                            <th>Contract</th>

                            <th class="text-right no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inactiveUsers as $employee)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="#" class="avatar">
                                        @if ($employee->image)
                                        <img src="{{ asset('images/' . $employee->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{
                                                asset('images/default.png')
                                            }}" alt="Profile Image" />
                                        @endif</a>
                                    <a href="{{ url('admin/employee/edit/'.$employee->id) }}">{{ $employee->fName }}
                                        {{ $employee->lName }}
                                        <span>{{ $employee->position }}</span></a>
                                </h2>
                            </td>
                            <td>{{ $employee->empNumber }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->phoneNumber }}</td>
                            <td>{{ $employee->dateHired }}</td>
                            <td>
                                <div class="dropdown">
                                    <a href="" class="btn btn-white btn-sm btn-rounded" data-toggle="dropdown"
                                        aria-expanded="false">
                                        {{ $employee->department }}
                                    </a>
                                </div>
                            </td>
                            <td>{{ $employee->typeOfContract }}</td>
                            <td class="text-right">
                                <!-- Activate Button -->
                                <form action="{{ route('admin.activateUser', $employee->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success">Activate</button>
                                </form>
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


<!-- Inactive User Modal -->
<div class="modal custom-modal fade" id="delete_user" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ url('admin/employee/delete') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Deactivate User</h3>
                        <p>Are you sure you want to deactivate this user?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <div class="row">
                            <div class="col-5">
                                <input type="hidden" name="emp_delete_id" id="emp_id">
                                <button class="btn add-btn" type="submit">Deactivate</button>
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



@endsection
