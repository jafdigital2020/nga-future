@extends('layouts.managermaster') @section('title', 'Employee Record') @section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Employee Profile</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('manager/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">View Employee</li>
                </ul>
            </div>
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
                                        <h3 class="user-name m-t-0 mb-0">{{ $user->lName }}, {{ $user->fNamme }}
                                            {{ $user->mName }} ({{ $user->name }})</h3>
                                        <h6 class="text-muted">{{ $user->position }}</h6>
                                        <div class="staff-id">Employee ID : {{ $user->empNumber }}</div>
                                        <div class="staff-id">Department :
                                            {{ $user->department ?? 'No department record' }}</div>
                                        <div class="staff-id">Reporting to : {{ $supervisor->name }}</div>
                                        <div class="staff-id">Date Hired : {{ $user->dateHired }}</div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <ul class="personal-info">
                                        <li>
                                            <div class="title">Phone:</div>
                                            <div class="text"><a href="tel:{{ $user->phoneNumber }}"
                                                    style="color:red;">{{ $user->phoneNumber }}</a></div>
                                        </li>
                                        <li>
                                            <div class="title">Email:</div>
                                            <div class="text"><a href="mailto:{{ $user->email }}"
                                                    style="color:red;">{{ $user->email }}</a></div>
                                        </li>
                                        <li>
                                            <div class="title">Birthday:</div>
                                            <div class="text">{{ $user->birthday }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Address:</div>
                                            <div class="text">{{ $user->completeAddress }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Type of contract:</div>
                                            <div class="text">{{ $user->typeOfContract }}</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
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

        <!-- Profile Info Tab -->
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
                                    <div class="title">Personal Email</div>
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

                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Government Mandates</h3>
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
                                    <div class="title">TIN</div>
                                    <div class="text">{{ $user->tin }}</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
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

        <!-- Emp Tab Tab -->
        <div class="tab-pane fade" id="emp_record">
            <div class="card">
                <div class="card-body">
                    <div class="col-auto float-right ml-auto">
                        <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_employee_record"><i
                                class="fa fa-plus"></i> Add/Edit Employement Record</a>
                        <!-- <div class="view-icons">
                            <a href="employees.html" class="grid-view btn btn-link"><i class="fa fa-th"></i></a>
                            <a href="employees-list.html" class="list-view btn btn-link active"><i
                                    class="fa fa-bars"></i></a>
                        </div> -->
                    </div>
                    <h3 class="card-title">Employee Record</h3>
                    @if($user->employmentRecord->isNotEmpty())
                    @foreach ($user->employmentRecord as $record)
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Hired Date</label>
                                <input type="text" name="eHired" id="eHired" class="form-control"
                                    value="{{ $record->hiredDate }}" readonly>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Job Title</label>
                                <input type="text" name="ePosition" id="ePosition" class="form-control"
                                    value="{{ $record->jobTitle }}" readonly>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Department</label>
                                <input type="text" name="eDepartment" id="eDepartment" class="form-control"
                                    value="{{ $record->department }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Immediate Supervisor</label>
                                <input type="text" name="iSupervisor" id="iSupervisor" class="form-control"
                                    value="{{ $record->supervisor }}" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Location</label>
                                <input type="text" name="location" id="location" class="form-control"
                                    value="{{ $record->location }}" readonly>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p>No Employment Record.</p>
                    @endif
                </div>
            </div>
        </div>

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
                        <form action="{{ url('manager/department-record/update-record/'. $user->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Hired Date</label>
                                        <input type="text" name="eHired" id="eHired" class="form-control"
                                            value="{{ $user->dateHired }}" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Job Title</label>
                                        <input type="text" name="ePosition" id="ePosition" class="form-control"
                                            value="{{ $user->position }}" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Department</label>
                                        <input type="text" name="eDepartment" id="eDepartment" class="form-control"
                                            value="{{ $user->department }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Immediate Supervisor</label>
                                        <input type="text" name="iSupervisor" id="iSupervisor" class="form-control"
                                            value="{{ $supervisor->name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Location</label>
                                        <input type="text" name="location" id="location" class="form-control" required>
                                    </div>
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
        <!-- /Emp Tab -->

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
                        <form action="{{ url('manager/department-record/update-salary/'. $user->id) }}" method="POST"
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


@endsection
