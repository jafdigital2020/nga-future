@extends('layouts.master') @section('title', 'Employee Record')


@section('content')
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
            <!-- <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_shift"><i class="fa fa-plus"></i>
                    Shift Schedule</a>
            </div> -->
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card mb-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="profile-view">
                        <div class="profile-img-wrap">
                            <div class="profile-img">
                                <a href="#" class="avatar">
                                    @if ($user->image)
                                    <img src="{{ asset('images/' . $user->image) }}" alt="Profile Image" />
                                    @else
                                    <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                    @endif
                                </a>
                            </div>
                        </div>
                        <div class="profile-basic">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="profile-info-left">
                                        <h3 class="user-name m-t-0 mb-0">{{ $user->lName }}, {{ $user->fName }}
                                            {{ $user->mName }} ({{ $user->name }})</h3>
                                        <h6 class="text-muted">{{ $user->position }}</h6>
                                        <div class="staff-id">Employee ID : {{ $user->empNumber }}</div>
                                        <div class="staff-id">Department :
                                            {{ $user->department ?? 'No department record' }}</div>
                                        <div class="staff-id">Reporting to :
                                            @if ($supervisor === 'Management')

                                            <strong>Management</strong>
                                            @elseif ($supervisor)
                                            <strong>
                                                @if ($supervisor->fName || $supervisor->lName)
                                                {{ $supervisor->fName }} {{ $supervisor->lName }}
                                                @else
                                                {{ $supervisor->name }}
                                                @endif
                                            </strong>
                                            @else
                                            <strong>No supervisor assigned.</strong>
                                            @endif</div>

                                        <div class="staff-msg">
                                            <a class="btn btn-danger" href="#" data-toggle="modal"
                                                data-target="#change_password">Change Password</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <ul class="personal-info">
                                        <li>
                                            <div class="title">Phone:</div>
                                            <div class="text"><a href="tel:{{ $user->phoneNumber }}"
                                                    style="color:red;">{{ $user->phoneNumber  ?? 'N/A'}}</a></div>
                                        </li>
                                        <li>
                                            <div class="title">Email:</div>
                                            <div class="text"><a href="mailto:{{ $user->email }}"
                                                    style="color:red;">{{ $user->email }}</a></div>
                                        </li>
                                        <li>
                                            <div class="title">Birthday:</div>
                                            <div class="text">{{ $user->birthday ?? 'N/A' }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Address:</div>
                                            <div class="text">{{ $user->completeAddress ?? 'N/A' }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Type of contract:</div>
                                            <div class="text">{{ $user->typeOfContract ?? 'N/A' }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Date Hired:</div>
                                            <div class="text">{{ $user->dateHired ?? 'N/A' }}</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="pro-edit">
                            <a data-target="#profile_info" data-toggle="modal" class="edit-icon" href="#"><i
                                    class="fa fa-pencil"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card tab-box">
        <div class="row user-tabs">
            <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
                <ul class="nav nav-tabs nav-tabs-bottom">
                    <li class="nav-item"><a href="#emp_profile" data-toggle="tab" class="nav-link active">Profile</a>
                    </li>
                    <!-- <li class="nav-item"><a href="#emp_projects" data-toggle="tab" class="nav-link">Projects</a>
                    </li> -->
                    <li class="nav-item"><a href="#emp_record" data-toggle="tab" class="nav-link">Employee Record
                            <small class="text-danger">(Admin/Manager Only)</small></a></li>
                    <li class="nav-item"><a href="#emp_salary" data-toggle="tab" class="nav-link">Employee Salary Record
                            <small class="text-danger">(Admin/Manager Only)</small></a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="tab-content">

        <!-- Profile Info Card -->
        <div id="emp_profile" class="pro-overview tab-pane fade show active">
            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Personal Informations <a href="#" class="edit-icon"
                                    data-toggle="modal" data-target="#personal_info_modal"><i
                                        class="fa fa-pencil"></i></a></h3>
                            @if($user->personalInformation->isNotEmpty())
                            @foreach($user->personalInformation as $info)
                            <ul class="personal-info">
                                <li>
                                    <div class="title">Religion</div>
                                    <div class="text">{{ $info->religion }}</div>
                                </li>
                                <li>
                                    <div class="title">Education</div>
                                    <div class="text">{{ $info->education }}</div>
                                </li>
                                <li>
                                    <div class="title">Nationality</div>
                                    <div class="text">{{ $info->nationality }}</div>
                                </li>
                                <li>
                                    <div class="title">Marital status</div>
                                    <div class="text">{{ $info->mStatus }}</div>
                                </li>
                                <li>
                                    <div class="title">No. of children</div>
                                    <div class="text">{{ $info->numChildren }}</div>
                                </li>
                                <li>
                                    <div class="title">Personal Email Address</div>
                                    <div class="text">{{ $info->personalEmail }}</div>
                                </li>
                            </ul>
                            @endforeach
                            @else
                            <p>No Personal Info available.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Emergency Contact <a href="#" class="edit-icon" data-toggle="modal"
                                    data-target="#emergency_contact_modal"><i class="fa fa-pencil"></i></a></h3>
                            @if($user->contactEmergency->isNotEmpty())
                            <h5 class="section-title">Primary</h5>
                            @foreach($user->contactEmergency as $contact)
                            <ul class="personal-info">
                                <li>
                                    <div class="title">Name</div>
                                    <div class="text">{{ $contact->primaryName }}</div>
                                </li>
                                <li>
                                    <div class="title">Relationship</div>
                                    <div class="text">{{ $contact->primaryRelation }}</div>
                                </li>
                                <li>
                                    <div class="title">Phone </div>
                                    <div class="text">{{ $contact->primaryPhone }}</div>
                                </li>
                            </ul>
                            <hr>
                            <h5 class="section-title">Secondary</h5>
                            <ul class="personal-info">
                                <li>
                                    <div class="title">Name</div>
                                    <div class="text">{{ $contact->secondName }}</div>
                                </li>
                                <li>
                                    <div class="title">Relationship</div>
                                    <div class="text">{{ $contact->secondRelation }}</div>
                                </li>
                                <li>
                                    <div class="title">Phone </div>
                                    <div class="text">{{ $contact->secondPhone }}</div>
                                </li>
                            </ul>
                            @endforeach
                            @else
                            <p>No emergency contacts available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Government Mandates <a href="#" class="edit-icon" data-toggle="modal"
                                    data-target="#family_info_modal"><i class="fa fa-pencil"></i></a></h3>
                            <ul class="personal-info">
                                <li>
                                    <div class="title">SSS</div>
                                    <div class="text">{{ $user->sss }}</div>
                                </li>
                                <li>
                                    <div class="title">Pag-ibig</div>
                                    <div class="text">{{ $user->pagIbig }}</div>
                                </li>
                                <li>
                                    <div class="title">PhilHealth</div>
                                    <div class="text">{{ $user->philHealth }}</div>
                                </li>
                                <li>
                                    <div class="title">Tin</div>
                                    <div class="text">{{ $user->tin }}</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Bank Information<a href="#" class="edit-icon" data-toggle="modal"
                                    data-target="#bank_info"><i class="fa fa-pencil"></i></a></h3>
                            @if($user->bankInfo->isNotEmpty())
                            @foreach ($user->bankInfo as $bank)
                            <ul class="personal-info">
                                <li>
                                    <div class="title">Bank name</div>
                                    <div class="text">{{ $bank->bankName }}</div>
                                </li>
                                <li>
                                    <div class="title">Bank Account Name</div>
                                    <div class="text">{{ $bank->bankAccName }}</div>
                                </li>
                                <li>
                                    <div class="title">Bank Account Number</div>
                                    <div class="text">{{ $bank->bankAccNumber }}</div>
                                </li>

                            </ul>
                            @endforeach
                            @else
                            <p>No bank details available.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Leave Credits<a href="#" class="edit-icon" data-toggle="modal"
                                    data-target="#leave_credits"><i class="fa fa-pencil"></i></a></h3>
                            @if($leaveTypes->isNotEmpty())
                            @foreach ($leaveTypes as $leaveType)
                            <ul class="personal-info">
                                <li>
                                    <div class="title">{{ $leaveType->leaveType  }}</div>
                                    <div class="text">
                                        {{ optional($user->leaveCredits->where('leave_type_id', $leaveType->id)->first())->remaining_credits ?? 0 }}
                                    </div>
                                </li>
                            </ul>
                            @endforeach
                            @else
                            <p>No leave assign.</p>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Profile Info Tab -->

        <!-- Projects Tab -->
        <div class="tab-pane fade" id="emp_projects">
            <div class="row">
                <div class="col-lg-4 col-sm-6 col-md-4 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="dropdown profile-action">
                                <a aria-expanded="false" data-toggle="dropdown" class="action-icon dropdown-toggle"
                                    href="#"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a data-target="#edit_project" data-toggle="modal" href="#" class="dropdown-item"><i
                                            class="fa fa-pencil m-r-5"></i> Edit</a>
                                    <a data-target="#delete_project" data-toggle="modal" href="#"
                                        class="dropdown-item"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                </div>
                            </div>
                            <h4 class="project-title"><a href="project-view.html">Office Management</a></h4>
                            <small class="block text-ellipsis m-b-15">
                                <span class="text-xs">1</span> <span class="text-muted">open tasks, </span>
                                <span class="text-xs">9</span> <span class="text-muted">tasks completed</span>
                            </small>
                            <p class="text-muted">Lorem Ipsum is simply dummy text of the printing and
                                typesetting industry. When an unknown printer took a galley of type and
                                scrambled it...
                            </p>
                            <div class="pro-deadline m-b-15">
                                <div class="sub-title">
                                    Deadline:
                                </div>
                                <div class="text-muted">
                                    17 Apr 2019
                                </div>
                            </div>
                            <div class="project-members m-b-15">
                                <div>Project Leader :</div>
                                <ul class="team-members">
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="Jeffery Lalor"><img alt=""
                                                src="assets/img/profiles/avatar-16.jpg"></a>
                                    </li>
                                </ul>
                            </div>
                            <div class="project-members m-b-15">
                                <div>Team :</div>
                                <ul class="team-members">
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="John Doe"><img alt=""
                                                src="assets/img/profiles/avatar-02.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="Richard Miles"><img alt=""
                                                src="assets/img/profiles/avatar-09.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="John Smith"><img alt=""
                                                src="assets/img/profiles/avatar-10.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="Mike Litorus"><img alt=""
                                                src="assets/img/profiles/avatar-05.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" class="all-users">+15</a>
                                    </li>
                                </ul>
                            </div>
                            <p class="m-b-5">Progress <span class="text-success float-right">40%</span></p>
                            <div class="progress progress-xs mb-0">
                                <div style="width: 40%" title="" data-toggle="tooltip" role="progressbar"
                                    class="progress-bar bg-success" data-original-title="40%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-md-4 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="dropdown profile-action">
                                <a aria-expanded="false" data-toggle="dropdown" class="action-icon dropdown-toggle"
                                    href="#"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a data-target="#edit_project" data-toggle="modal" href="#" class="dropdown-item"><i
                                            class="fa fa-pencil m-r-5"></i> Edit</a>
                                    <a data-target="#delete_project" data-toggle="modal" href="#"
                                        class="dropdown-item"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                </div>
                            </div>
                            <h4 class="project-title"><a href="project-view.html">Project Management</a></h4>
                            <small class="block text-ellipsis m-b-15">
                                <span class="text-xs">2</span> <span class="text-muted">open tasks, </span>
                                <span class="text-xs">5</span> <span class="text-muted">tasks completed</span>
                            </small>
                            <p class="text-muted">Lorem Ipsum is simply dummy text of the printing and
                                typesetting industry. When an unknown printer took a galley of type and
                                scrambled it...
                            </p>
                            <div class="pro-deadline m-b-15">
                                <div class="sub-title">
                                    Deadline:
                                </div>
                                <div class="text-muted">
                                    17 Apr 2019
                                </div>
                            </div>
                            <div class="project-members m-b-15">
                                <div>Project Leader :</div>
                                <ul class="team-members">
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="Jeffery Lalor"><img alt=""
                                                src="assets/img/profiles/avatar-16.jpg"></a>
                                    </li>
                                </ul>
                            </div>
                            <div class="project-members m-b-15">
                                <div>Team :</div>
                                <ul class="team-members">
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="John Doe"><img alt=""
                                                src="assets/img/profiles/avatar-02.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="Richard Miles"><img alt=""
                                                src="assets/img/profiles/avatar-09.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="John Smith"><img alt=""
                                                src="assets/img/profiles/avatar-10.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="Mike Litorus"><img alt=""
                                                src="assets/img/profiles/avatar-05.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" class="all-users">+15</a>
                                    </li>
                                </ul>
                            </div>
                            <p class="m-b-5">Progress <span class="text-success float-right">40%</span></p>
                            <div class="progress progress-xs mb-0">
                                <div style="width: 40%" title="" data-toggle="tooltip" role="progressbar"
                                    class="progress-bar bg-success" data-original-title="40%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-md-4 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="dropdown profile-action">
                                <a aria-expanded="false" data-toggle="dropdown" class="action-icon dropdown-toggle"
                                    href="#"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a data-target="#edit_project" data-toggle="modal" href="#" class="dropdown-item"><i
                                            class="fa fa-pencil m-r-5"></i> Edit</a>
                                    <a data-target="#delete_project" data-toggle="modal" href="#"
                                        class="dropdown-item"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                </div>
                            </div>
                            <h4 class="project-title"><a href="project-view.html">Video Calling App</a></h4>
                            <small class="block text-ellipsis m-b-15">
                                <span class="text-xs">3</span> <span class="text-muted">open tasks, </span>
                                <span class="text-xs">3</span> <span class="text-muted">tasks completed</span>
                            </small>
                            <p class="text-muted">Lorem Ipsum is simply dummy text of the printing and
                                typesetting industry. When an unknown printer took a galley of type and
                                scrambled it...
                            </p>
                            <div class="pro-deadline m-b-15">
                                <div class="sub-title">
                                    Deadline:
                                </div>
                                <div class="text-muted">
                                    17 Apr 2019
                                </div>
                            </div>
                            <div class="project-members m-b-15">
                                <div>Project Leader :</div>
                                <ul class="team-members">
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="Jeffery Lalor"><img alt=""
                                                src="assets/img/profiles/avatar-16.jpg"></a>
                                    </li>
                                </ul>
                            </div>
                            <div class="project-members m-b-15">
                                <div>Team :</div>
                                <ul class="team-members">
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="John Doe"><img alt=""
                                                src="assets/img/profiles/avatar-02.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="Richard Miles"><img alt=""
                                                src="assets/img/profiles/avatar-09.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="John Smith"><img alt=""
                                                src="assets/img/profiles/avatar-10.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="Mike Litorus"><img alt=""
                                                src="assets/img/profiles/avatar-05.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" class="all-users">+15</a>
                                    </li>
                                </ul>
                            </div>
                            <p class="m-b-5">Progress <span class="text-success float-right">40%</span></p>
                            <div class="progress progress-xs mb-0">
                                <div style="width: 40%" title="" data-toggle="tooltip" role="progressbar"
                                    class="progress-bar bg-success" data-original-title="40%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-md-4 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="dropdown profile-action">
                                <a aria-expanded="false" data-toggle="dropdown" class="action-icon dropdown-toggle"
                                    href="#"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a data-target="#edit_project" data-toggle="modal" href="#" class="dropdown-item"><i
                                            class="fa fa-pencil m-r-5"></i> Edit</a>
                                    <a data-target="#delete_project" data-toggle="modal" href="#"
                                        class="dropdown-item"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                </div>
                            </div>
                            <h4 class="project-title"><a href="project-view.html">Hospital Administration</a></h4>
                            <small class="block text-ellipsis m-b-15">
                                <span class="text-xs">12</span> <span class="text-muted">open tasks, </span>
                                <span class="text-xs">4</span> <span class="text-muted">tasks completed</span>
                            </small>
                            <p class="text-muted">Lorem Ipsum is simply dummy text of the printing and
                                typesetting industry. When an unknown printer took a galley of type and
                                scrambled it...
                            </p>
                            <div class="pro-deadline m-b-15">
                                <div class="sub-title">
                                    Deadline:
                                </div>
                                <div class="text-muted">
                                    17 Apr 2019
                                </div>
                            </div>
                            <div class="project-members m-b-15">
                                <div>Project Leader :</div>
                                <ul class="team-members">
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="Jeffery Lalor"><img alt=""
                                                src="assets/img/profiles/avatar-16.jpg"></a>
                                    </li>
                                </ul>
                            </div>
                            <div class="project-members m-b-15">
                                <div>Team :</div>
                                <ul class="team-members">
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="John Doe"><img alt=""
                                                src="assets/img/profiles/avatar-02.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="Richard Miles"><img alt=""
                                                src="assets/img/profiles/avatar-09.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="John Smith"><img alt=""
                                                src="assets/img/profiles/avatar-10.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="tooltip" title="Mike Litorus"><img alt=""
                                                src="assets/img/profiles/avatar-05.jpg"></a>
                                    </li>
                                    <li>
                                        <a href="#" class="all-users">+15</a>
                                    </li>
                                </ul>
                            </div>
                            <p class="m-b-5">Progress <span class="text-success float-right">40%</span></p>
                            <div class="progress progress-xs mb-0">
                                <div style="width: 40%" title="" data-toggle="tooltip" role="progressbar"
                                    class="progress-bar bg-success" data-original-title="40%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Projects Tab -->

        <!-- Employee Record Tab -->
        <div class="tab-pane fade" id="emp_record">
            <div class="card">
                <div class="card-body">
                    <!-- Add/Edit Employment Record Button -->
                    <div class="col-auto float-right ml-auto mb-3">
                        <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_employee_record">
                            <i class="fa fa-plus"></i> Add Memo
                        </a>
                    </div>

                    <!-- Assigned Assets Section -->
                    <h3 class="card-title">Assigned Assets</h3>
                    @if($user->userAssets->isNotEmpty())
                    <div class="row">
                        @foreach ($user->userAssets as $userAsset)
                        <div class="col-md-6 col-lg-4">
                            <div class="card asset-card mb-3 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $userAsset->asset->name ?? 'N/A' }}</h5>
                                    <p class="mb-1"><strong>Serial Number:</strong>
                                        {{ $userAsset->asset->serial_number ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Assignment Date:</strong>
                                        {{ \Carbon\Carbon::parse($userAsset->assign_date)->format('F j, Y') }}</p>
                                    <p class="mb-1"><strong>Return Date:</strong>
                                        {{ $userAsset->return_date ? \Carbon\Carbon::parse($userAsset->return_date)->format('F j, Y') : 'Not Returned' }}
                                    </p>
                                    @if($userAsset->note)
                                    <p class="mb-1"><strong>Notes:</strong> {{ $userAsset->note }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted">No Assigned Assets.</p>
                    @endif

                    <!-- Memo Records Section -->
                    <h3 class="card-title mt-4">Memo Records</h3>
                    @if($user->memos->isNotEmpty())
                    <div class="row">
                        @foreach ($user->memos as $memo)
                        <div class="col-md-6 col-lg-4">
                            <div class="card memo-card mb-3 shadow-sm position-relative">
                                <div class="card-body">
                                    <h5 class="card-title">Memo Issued</h5>
                                    <p class="mb-1"><strong>Date Issued:</strong>
                                        {{ \Carbon\Carbon::parse($memo->date_issue)->format('F j, Y') }}</p>
                                    <p>
                                        @if($memo->attached_file)
                                        <a href="{{ asset('storage/' . $memo->attached_file) }}" target="_blank"
                                            class="btn btn-sm btn-danger mt-2">
                                            <i class="fa fa-file"></i> View Attachment
                                        </a>
                                        @else
                                        <span class="text-muted">No Attachment</span>
                                        @endif
                                    </p>
                                </div>

                                <!-- Edit Button Icon in the Red Circle -->
                                <div class="position-absolute top-0 end-0 p-2">
                                    <a href="#" data-toggle="modal" data-target="#editMemoModal-{{ $memo->id }}"
                                        class="text-danger">
                                        <i class="fa fa-edit fa-lg"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Memo Modal -->
                        <div class="modal fade" id="editMemoModal-{{ $memo->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="editMemoModalLabel-{{ $memo->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editMemoModalLabel-{{ $memo->id }}">Edit Memo</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.updateMemo', $memo->id) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf


                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="date_issue">Date Issued</label>
                                                <input type="text" class="datetimepicker form-control" name="date_issue"
                                                    value="{{ $memo->date_issue }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="attached_file">Attached File</label>
                                                <input type="file" name="attached_file" class="form-control">
                                                @if($memo->attached_file)
                                                <small class="form-text text-muted">Current File: <a
                                                        href="{{ asset('storage/' . $memo->attached_file) }}"
                                                        target="_blank">View</a></small>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                        <!-- /Edit Memo Modal -->

                        @endforeach
                    </div>
                    @else
                    <p class="text-muted">No Memo Records.</p>
                    @endif
                </div>
            </div>
        </div>
        <!-- /Employee Record Tab -->



        <!-- Employment Record Modal -->
        <div id="add_employee_record" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Employment Record</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.createMemo',  $user->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Employee Name</label>
                                        <input type="text" class="form-control"
                                            value="{{ $user->fName ?? '' }} {{ $user->lName ?? '' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Position</label>
                                        <input type="text" class="form-control" value="{{ $user->position }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Department</label>
                                        <input type="text" class="form-control" value="{{ $user->department }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">Date</label>
                                    <div class="form-group form-focus">
                                        <div class="cal-icon">
                                            <input type="text" class="datetimepicker form-control floating"
                                                name="date_issue"
                                                value="{{ $user->memos->isNotEmpty() ? $user->memos->first()->date_issue : '' }}">
                                        </div>
                                        <label class="focus-label">Select Date</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Attach File</label>
                                        <input type="file" name="attached_file" class="form-control">
                                    </div>
                                </div>

                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn" type="submit">Save</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Employment Record Modal -->


        <!-- Emp Salary Tab -->
        <div class="tab-pane fade" id="emp_salary">
            <div class="card">
                <div class="card-body">
                    <div class="col-auto float-right ml-auto">
                        <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_employee_salary"><i
                                class="fa fa-plus"></i> Add Employment Salary Record</a>
                        <!-- <div class="view-icons">
                            <a href="employees.html" class="grid-view btn btn-link"><i class="fa fa-th"></i></a>
                            <a href="employees-list.html" class="list-view btn btn-link active"><i
                                    class="fa fa-bars"></i></a>
                        </div> -->
                    </div>
                    <h3 class="card-title">Employee Salary Record</h3>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="datatable table table-stripped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Annual Salary</th>
                                                    <th>Salary Frequency Monthly</th>
                                                    <th>Salary Rate</th>
                                                    <th>Currency</th>
                                                    <th>Proposal Reason</th>
                                                    <th>Proposed By:</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($user->employmentSalary as $sarecord)
                                                <tr>
                                                    <td>{{ $sarecord->name }}</td>
                                                    <td>{{ $sarecord->annSalary }}</td>
                                                    <td>{{ $sarecord->salFreqMonthly }}</td>
                                                    <td>{{ $sarecord->salRate }}</td>
                                                    <td>{{ $sarecord->currency }}</td>
                                                    <td>{{ $sarecord->proposalReason }}</td>
                                                    <td>{{ $sarecord->proBy }}</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employment Salary Modal -->
        <div id="add_employee_salary" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Employment Record</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('admin/employee/update-salary/'. $user->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Annual Salary</label>
                                        <input type="text" name="annSalary" id="annSalary" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Salary Frequency Monthly</label>
                                        <input type="text" name="salFreqMonthly" id="salFreqMonthly"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Salary Rate</label>
                                        <input type="text" name="salRate" id="salRate" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Currency</label>
                                        <input type="text" name="currency" id="currency" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Proposal Reason</label>
                                        <textarea class="form-control" name="proposalReason"
                                            id="proposalReason"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="submit-section">
                                <button class="btn btn-primary submit-btn" type="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Employment Salary Modal -->
        <!-- /Emp Salary Tab -->

    </div>
</div>
<!-- /Page Content -->

<!-- Profile Modal -->
<div id="profile_info" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profile Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('admin/employee/update/'. $user->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-img-wrap edit-img">
                                @if ($user->image)
                                <img class="inline-block" src="{{ asset('images/' . $user->image) }}" alt="Employee"
                                    id="previewImage">
                                @else
                                <img class="inline-block" src="{{ asset('images/default.png') }}" alt="Employee"
                                    id="previewImage">
                                @endif
                                <div class="fileupload btn">
                                    <span class="btn-text">Edit</span>
                                    <input class="upload" type="file" name="image" id="imageInput">
                                </div>
                            </div>
                            <div class="row">
                                <!-- First Name -->
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="name">First Name<span class="text-danger">*</span></label>
                                        <input type="text" name="fName" id="fName" class="form-control"
                                            value="{{ $user->fName }}" required />
                                    </div>
                                </div>
                                <!-- Middle Name -->
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="name">Middle Name<span class="text-danger">*</span></label>
                                        <input type="text" name="mName" id="mName" class="form-control"
                                            value="{{ $user->mName }}" required />

                                    </div>
                                </div>
                                <!-- last Name -->
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="name">Last Name<span class="text-danger">*</span></label>
                                        <input type="text" name="lName" id="lName" class="form-control"
                                            value="{{ $user->lName }}" required />

                                    </div>
                                </div>
                                <!-- Suffix Name -->
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="suffix">Suffix</label>
                                        <input type="text" name="suffix" id="suffix" value="{{ $user->suffix }}"
                                            class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Professional Name</label>
                                        <input type="text" name="name" id="name" class="form-control"
                                            value="{{ $user->name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Employee Number</label>
                                        <input type="text" name="empNumber" id="empNumber" class="form-control"
                                            value="{{ $user->empNumber }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Birth Date</label>
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" type="text"
                                                value="{{ $user->birthday }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Joining Date</label>
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" type="text" name="dateHired"
                                                id="dateHired" value="{{ $user->dateHired }}">
                                        </div>
                                    </div>
                                </div>
                                <!-- Position -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Position</label>
                                        <input type="text" class="form-control" name="position" id="position"
                                            value="{{ $user->position ? $user->position : '' }}" required>
                                    </div>
                                </div>
                                <!-- Department -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <input type="text" class="form-control" name="department" id="department"
                                            value="{{ $user->department ? $user->department : '' }}" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Type Of Contract</label>
                                        <select class="form-control" name="typeOfContract" id="typeOfContract">
                                            <option value="Regular"
                                                {{ $user->typeOfContract == 'Regular' ? 'selected' : '' }}>Regular
                                            </option>
                                            <option value="Contractual"
                                                {{ $user->typeOfContract == 'Contractual' ? 'selected' : '' }}>
                                                Contractual</option>
                                            <option value="Probationary"
                                                {{ $user->typeOfContract == 'Probationary' ? 'selected' : '' }}>
                                                Probationary
                                            </option>
                                            <option value="Intern"
                                                {{ $user->typeOfContract == 'Intern' ? 'selected' : '' }}>Intern
                                            </option>
                                        </select>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" class="form-control" value="{{ $user->completeAddress }}"
                                    name="completeAddress" id="completeAddress">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="text" name="email" id="email" class="form-control"
                                    value="{{ $user->email }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> Phone Number </label>
                                <input type="text" name="phoneNumber" id="phoneNumber" class="form-control"
                                    value="{{ $user->phoneNumber }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> Hourly Rate </label>
                                <input type="text" name="hourly_rate" id="hourly_rate" class="form-control"
                                    value="{{ $user->hourly_rate }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Role</label>
                                <select class="form-control" name="role_as" id="role_as">
                                    <option value="1" {{ $user->role_as == '1' ? 'selected' : '' }}>
                                        Admin
                                    </option>
                                    <option value="2" {{ $user->role_as == '2' ? 'selected' : '' }}>
                                        HR</option>
                                    <option value="3" {{ $user->role_as == '3' ? 'selected' : '' }}>Employee
                                    </option>
                                    <option value="4" {{ $user->role_as == '4' ? 'selected' : '' }}> Manager
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Reporting to</label>
                                <select class="form-control" name="reporting_to" id="reporting_to">
                                    <option value="">--Select Supervisor--</option>
                                    @foreach ($users as $supervisor)
                                    <option value="{{ $supervisor->id }}"
                                        {{ $user->reporting_to == $supervisor->id ? 'selected' : '' }}>
                                        {{ $supervisor->fName }} {{ $supervisor->lName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn" type="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Profile Modal -->

<!-- Personal Info Modal -->
<div id="personal_info_modal" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Personal Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('admin/employee/personal-info/'. $user->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Religion</label>
                                <input type="text" class="form-control" name="religion" id="religion"
                                    value="{{ $info->religion ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Education</label>
                                <input class="form-control" type="text" name="education" id="education"
                                    value="{{ $info->education ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nationality</label>
                                <input class="form-control" type="text" name="nationality" id="nationality"
                                    value="{{ $info->nationality ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Marital status <span class="text-danger">*</span></label>
                                <select class="form-control" name="mStatus" id="mStatus" required>
                                    <option>-</option>
                                    <option value="Single" {{ ($info->mStatus ?? '') == 'Single' ? 'selected' : '' }}>
                                        Single</option>
                                    <option value="Married" {{ ($info->mStatus ?? '') == 'Married' ? 'selected' : '' }}>
                                        Married</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>No. of children </label>
                                <input class="form-control" type="text" name="numChildren" id="numChildren"
                                    value="{{ $info->numChildren ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Personal Email Adress </label>
                                <input class="form-control" type="email" name="personalEmail" id="personalEmail"
                                    value="{{ $info->personalEmail ?? '' }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Personal Info Modal -->

<!-- Government Mandate Info Modal -->
<div id="family_info_modal" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Government Mandates</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('admin/employee/update/mandates/'. $user->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>SSS</label>
                                        <input type="text" name="sss" id="sss" class="form-control"
                                            value="{{ $user->sss }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pag-Ibig</label>
                                        <input type="text" name="pagIbig" id="pagIbig" class="form-control"
                                            value="{{ $user->pagIbig }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>PhilHealth</label>
                                        <input type="text" name="philHealth" id="philHealth" class="form-control"
                                            value="{{ $user->philHealth }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tin</label>
                                        <input type="text" name="tin" id="tin" class="form-control"
                                            value="{{ $user->tin }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn" type="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Family Info Modal -->

<!-- Emergency Contact Modal -->
<div id="emergency_contact_modal" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Emergency Contact</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('admin/employee/contact/'. $user->id) }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Primary Contact</h3>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Name <span class="text-danger">*</span></label>
                                        <input type="text" name="primaryName" id="primaryName" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Relationship <span class="text-danger">*</span></label>
                                        <input class="form-control" name="primaryRelation" id="primaryRelation"
                                            type="text" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Phone <span class="text-danger">*</span></label>
                                        <input class="form-control" name="primaryPhone" id="primaryPhone" type="text"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Secondary Contact</h3>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" name="secondName" id="secondName" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Relationship </label>
                                        <input class="form-control" name="secondRelation" id="secondRelation"
                                            type="text">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input class="form-control" name="secondPhone" id="secondPhone" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Emergency Contact Modal -->

<!-- Bank Information Modal -->
<div id="bank_info" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Bank Informations</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('admin/employee/bank/' .$user->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Bank Name</label>
                                        <input type="text" name="bankName" id="bankName" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Bank Account Name</label>
                                        <input type="text" name="bankAccName" id="bankAccName" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Bank Account Number</label>
                                        <input type="text" name="bankAccNumber" id="bankAccNumber" class="form-control"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn" type="submit"> Submit </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Bank Information Modal -->

<!-- Changepassword Info Modal -->
<div id="change_password" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('admin/employee/changepassword/'. $user->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>New Password</label>
                                        <div class="input-group">
                                            <input type="password" name="password" id="password" class="form-control"
                                                required />
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="toggle-new-password">
                                                    <i class="la la-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation"
                                                id="password_confirmation" class="form-control" required />
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="toggle-confirm-password">
                                                    <i class="la la-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn" type="submit">Save Changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<!-- /Changepassword Info Modal -->

<!-- Leave Credits Modal -->
<div id="leave_credits" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Leave Credits</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('admin/employee/leave-credits/' .$user->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                @foreach($leaveTypes as $leaveType)
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>{{ $leaveType->leaveType }}</label>
                                        <input type="number" class="form-control"
                                            name="leave_credits[{{ $leaveType->id }}]"
                                            id="leave_credits_{{ $leaveType->id }}"
                                            value="{{ optional($user->leaveCredits->where('leave_type_id', $leaveType->id)->first())->remaining_credits ?? 0 }}"
                                            required>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn" type="submit"> Submit </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Leave Credits -->



@endsection

@section('scripts')

<script>
    document.getElementById("imageInput").addEventListener("change", function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                document.getElementById("previewImage").setAttribute("src", event.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to get the URL parameter
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            const results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        // Get the tab parameter from the URL
        const tab = getUrlParameter('tab');

        if (tab) {
            // Find the tab link with the corresponding href attribute
            const tabLink = document.querySelector(`a[href="#${tab}"]`);

            if (tabLink) {
                // Activate the tab using Bootstrap's tab plugin
                $(tabLink).tab('show');
            }
        }
    });

</script>

<script>
    document.getElementById('toggle-new-password').addEventListener('click', function () {
        var passwordField = document.getElementById('password');
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
        var passwordField = document.getElementById('password_confirmation');
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

<script>
    const timeInput = document.getElementById('shiftStart');

    timeInput.addEventListener('input', function () {
        let [hours, minutes] = this.value.split(':');
        const suffix = hours >= 12 ? "PM" : "AM";
        hours = (hours % 12) || 12;
        console.log(`${hours}:${minutes} ${suffix}`);
    });

</script>

<script>
    function toggleShiftFields(checkbox) {
        // Get the current shift ID from the checkbox's ID
        const shiftId = $(checkbox).attr('id').replace('flexibleTime', '');

        // Determine the input fields related to this shift
        const shiftStart = $('#shiftStart' + shiftId);
        const lateThreshold = $('#lateThreshold' + shiftId);
        const shiftEnd = $('#shiftEnd' + shiftId);

        // Toggle the disabled property based on the checkbox state
        const isDisabled = $(checkbox).is(':checked');
        shiftStart.prop('disabled', isDisabled);
        lateThreshold.prop('disabled', isDisabled);
        shiftEnd.prop('disabled', isDisabled);

        // Optionally, clear the values of the disabled fields
        if (isDisabled) {
            shiftStart.val('');
            lateThreshold.val('');
            shiftEnd.val('');
        }
    }

</script>
<script>
    document.getElementById('allowedHours').addEventListener('input', function (e) {
        let input = e.target.value;

        // Remove all non-digit characters
        input = input.replace(/[^0-9]/g, '');

        // Insert colons after every 2 digits
        if (input.length >= 3 && input.length <= 4) {
            input = input.slice(0, 2) + ':' + input.slice(2);
        }
        if (input.length >= 5) {
            input = input.slice(0, 2) + ':' + input.slice(2, 4) + ':' + input.slice(4, 6);
        }

        e.target.value = input;
    });

</script>


@endsection
