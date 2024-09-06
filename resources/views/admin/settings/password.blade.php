@extends('layouts.settings') @section('title', 'One JAF')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="content container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Change Password</h3>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
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

            <form action="{{ route('settings.changepass') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Old password</label>
                    <input type="password" name="current_password" class="form-control" id="current_password" />
                    <span class="position-absolute" id="toggle-current-password"
                        style="right: 25px; top: 29%; transform: translateY(-50%); cursor: pointer;">
                        <i class="la la-eye"></i>
                    </span>
                </div>
                <div class="form-group">
                    <label>New password</label>
                    <input type="password" name="password" class="form-control" id="new_password" />
                    <span class="position-absolute" id="toggle-new-password"
                        style="right: 25px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <i class="la la-eye"></i>
                    </span>
                </div>
                <div class="form-group">
                    <label>Confirm password</label>
                    <input type="password" name="password_confirmation" class="form-control" id="confirm_password" />
                    <span class="position-absolute" id="toggle-confirm-password"
                        style="right: 25px; top: 72%; transform: translateY(-50%); cursor: pointer;">
                        <i class="la la-eye"></i>
                    </span>
                </div>
                <div class="submit-section">
                    <button type="submit" class="btn btn-primary submit-btn">Update Password</button>
                </div>
            </form>
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
