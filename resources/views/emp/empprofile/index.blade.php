@extends('layouts.empmaster') @section('title', 'Attendance')
<link href="{{ asset('assets/css/profile.css') }}" rel="stylesheet" />
@section('content')

<div class="container2">
    <div class="row ng-scope">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <form
                        action="upload.php"
                        method="post"
                        enctype="multipart/form-data"
                    >
                        <label for="fileToUpload">
                            <div
                                class="profile-pic"
                                style="background-image: url('{{ $user->image }}');"
                            >
                                <span class="glyphicon glyphicon-camera"></span>
                                <span>Change Image</span>
                            </div>
                        </label>
                        <input
                            type="File"
                            name="fileToUpload"
                            id="fileToUpload"
                        />
                    </form>
                    <h3 class="m0 text-bold">Audrey Hunt</h3>
                    <div class="mv-lg">
                        <p>Hello! {{ $user->name }}</p>
                    </div>
                    <div class="text-center">
                        <a
                            class="btn btn-primary"
                            href="{{ url('emp/changepassword') }}"
                        >
                            <i class="fa fa-edit"></i> Change Password</a
                        >
                    </div>
                </div>
            </div>
            <div class="panel panel-default hidden-xs hidden-sm">
                <div class="panel-heading">
                    <div class="panel-title text-center">Recent contacts</div>
                </div>
                <div class="panel-body">
                    <div class="media">
                        <div class="media-left media-middle">
                            <a href="#"
                                ><img
                                    class="media-object img-circle img-thumbnail thumb48"
                                    src="https://bootdey.com/img/Content/avatar/avatar2.png"
                                    alt="Contact"
                            /></a>
                        </div>
                        <div class="media-body pt-sm">
                            <div class="text-bold">
                                Joshua Soriano
                                <div class="text-sm text-muted">12m ago</div>
                            </div>
                        </div>
                    </div>
                    <div class="media">
                        <div class="media-left media-middle">
                            <a href="#"
                                ><img
                                    class="media-object img-circle img-thumbnail thumb48"
                                    src="https://bootdey.com/img/Content/avatar/avatar3.png"
                                    alt="Contact"
                            /></a>
                        </div>
                        <div class="media-body pt-sm">
                            <div class="text-bold">
                                Kristia Mei Antiporda
                                <div class="text-sm text-muted">2h ago</div>
                            </div>
                        </div>
                    </div>
                    <div class="media">
                        <div class="media-left media-middle">
                            <a href="#"
                                ><img
                                    class="media-object img-circle img-thumbnail thumb48"
                                    src="https://bootdey.com/img/Content/avatar/avatar5.png"
                                    alt="Contact"
                            /></a>
                        </div>
                        <div class="media-body pt-sm">
                            <div class="text-bold">
                                Just Quem Albiso
                                <div class="text-sm text-muted">1 hour ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="pull-right">
                        <div class="btn-group dropdown" uib-dropdown="dropdown">
                            <button
                                class="btn btn-link dropdown-toggle"
                                uib-dropdown-toggle=""
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                <em
                                    class="fa fa-ellipsis-v fa-lg text-muted"
                                ></em>
                            </button>
                            <ul
                                class="dropdown-menu dropdown-menu-right animated fadeInLeft"
                                role="menu"
                            >
                                <li>
                                    <a href=""><span>Send by message</span></a>
                                </li>
                                <li>
                                    <a href=""><span>Share contact</span></a>
                                </li>
                                <li>
                                    <a href=""><span>Block contact</span></a>
                                </li>
                                <li>
                                    <a href=""
                                        ><span class="text-warning"
                                            >Delete contact</span
                                        ></a
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="h4 text-center">Contact Information</div>
                    <div class="row pv-lg">
                        <div class="col-lg-2"></div>
                        <div class="col-lg-8">
                            <form class="form-horizontal ng-pristine ng-valid">
                                <div class="form-group">
                                    <label
                                        class="col-sm-2 control-label"
                                        for="inputContact1"
                                        >Name</label
                                    >
                                    <div class="col-sm-10">
                                        <input
                                            class="form-control"
                                            id="inputContact1"
                                            type="text"
                                            placeholder=""
                                            value="{{ $user->name }}"
                                            readonly
                                        />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-sm-2 control-label"
                                        for="inputContact2"
                                        >Email</label
                                    >
                                    <div class="col-sm-10">
                                        <input
                                            class="form-control"
                                            id="inputContact2"
                                            type="email"
                                            value="{{ $user->email }}"
                                            readonly
                                        />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-sm-4 control-label"
                                        for="inputContact3"
                                        >Employee Number</label
                                    >
                                    <div class="col-sm-10">
                                        <input
                                            class="form-control"
                                            id="inputContact3"
                                            type="text"
                                            value="{{ $user->empNumber }}"
                                            readonly
                                        />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-sm-2 control-label"
                                        for="inputContact4"
                                        >Position</label
                                    >
                                    <div class="col-sm-10">
                                        <input
                                            class="form-control"
                                            id="inputContact4"
                                            type="text"
                                            value="{{ $user->position }}"
                                            readonly
                                        />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-sm-3 control-label"
                                        for="inputContact5"
                                        >Date Hired</label
                                    >
                                    <div class="col-sm-10">
                                        <input
                                            class="form-control"
                                            id="inputContact5"
                                            type="text"
                                            value="{{ $user->dateHired }}"
                                            readonly
                                        />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-sm-2 control-label"
                                        for="inputContact6"
                                        >Address</label
                                    >
                                    <div class="col-sm-10">
                                        <textarea
                                            class="form-control"
                                            id="inputContact6"
                                            row="4"
                                            readonly
                                        >
                                        {{ $user->completeAddress }}</textarea
                                        >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button
                                            class="btn btn-info"
                                            type="submit"
                                        >
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
