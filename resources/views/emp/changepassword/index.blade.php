@extends('layouts.empmaster') @section('title', 'Change Password')
@section('content')

<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Change Password</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('main.emp') }}">Dashboard</a></li>
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
                            <form action="{{ url('emp/changepassword') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label>Current Password</label>
                                    <div class="position-relative">
                                        <input type="password" name="current_password" class="form-control"
                                            id="current_password" />
                                        <span class="position-absolute" id="toggle-current-password"
                                            style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                            <i class="la la-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>New Password</label>
                                    <div class="position-relative">
                                        <input type="password" name="password" class="form-control" id="new_password" />
                                        <span class="position-absolute" id="toggle-new-password"
                                            style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                            <i class="la la-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Confirm Password</label>
                                    <div class="position-relative">
                                        <input type="password" name="password_confirmation" class="form-control"
                                            id="confirm_password" />
                                        <span class="position-absolute" id="toggle-confirm-password"
                                            style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                            <i class="la la-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    Update Password
                                </button>
                                <a href="{{ route('emp.profile') }}" class="btn btn-success float-end"
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

@section('scripts')

<script>
    document.getElementById('toggle-current-password').addEventListener('click', function () {
        var passwordField = document.getElementById('current_password');
        var passwordFieldType = passwordField.getAttribute('type');
        if (passwordFieldType === 'password') {
            passwordField.setAttribute('type', 'text');
            this.innerHTML = '<i class="la la-eye-slash"></i>';
        } else {
            passwordField.setAttribute('type', 'password');
            this.innerHTML = '<i class="la la-eye"></i>';
        }
    });

    document.getElementById('toggle-new-password').addEventListener('click', function () {
        var passwordField = document.getElementById('new_password');
        var passwordFieldType = passwordField.getAttribute('type');
        if (passwordFieldType === 'password') {
            passwordField.setAttribute('type', 'text');
            this.innerHTML = '<i class="la la-eye-slash"></i>';
        } else {
            passwordField.setAttribute('type', 'password');
            this.innerHTML = '<i class="la la-eye"></i>';
        }
    });

    document.getElementById('toggle-confirm-password').addEventListener('click', function () {
        var passwordField = document.getElementById('confirm_password');
        var passwordFieldType = passwordField.getAttribute('type');
        if (passwordFieldType === 'password') {
            passwordField.setAttribute('type', 'text');
            this.innerHTML = '<i class="la la-eye-slash"></i>';
        } else {
            passwordField.setAttribute('type', 'password');
            this.innerHTML = '<i class="la la-eye"></i>';
        }
    });

</script>


@endsection
