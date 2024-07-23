@extends('layouts.managermaster') @section('title', 'Change Password')
@section('content')

<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Change Password</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('manager/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Change Password</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="py-5">
        <div class="container2">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    @if (session('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session("message") }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> A <a href="#" class="alert-link">problem</a> has occurred while
                        submitting
                        your data:
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li class="text-danger">{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <div class="card shadow">
                        <div class="card-header bg-primary">
                            <h4 class="mb-0 text-white">Change Password</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('manager/profile/changepassword') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label>Current Password</label>
                                    <input type="password" name="current_password" class="form-control" />
                                </div>
                                <div class="mb-3">
                                    <label>New Password</label>
                                    <input type="password" name="password" class="form-control" />
                                </div>
                                <div class="mb-3">
                                    <label>Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" />
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    Update Password
                                </button>
                                <a href="{{{ url('manager/profile') }}}" class="btn btn-success float-end"
                                    id="editBTN">Back</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
