@extends('layouts.hrmaster') @section('title', 'Employees') @section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Employee Profile</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('hr/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Employee</li>
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
                                        <h3 class="user-name m-t-0 mb-0">{{ $user->lName }}, {{ $user->fName }}
                                            {{ $user->mName }} ({{ $user->name }})</h3>

                                        <h6 class="text-muted">{{ $user->position }}</h6>
                                        <div class="staff-id">Department:
                                            {{ $user->department ?? 'No department record' }}</div>
                                        <div class="staff-id">Employee ID: {{ $user->empNumber }}</div>
                                        <div class="staff-id">Date of Join: {{ $user->dateHired }}</div>
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
                    </li>
                    <li class="nav-item"><a href="#bank_statutory" data-toggle="tab" class="nav-link">Bank &
                            Statutory <small class="text-danger">(Admin Only)</small></a></li> -->
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

                <div class="col-md-6 d-flex">
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
                <form action="{{ url('hr/employee/update/'. $user->id) }}" method="POST" enctype="multipart/form-data">
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
                                        <select class="form-control" name="department" id="department">
                                            <option value="Website Development"
                                                {{ $user->department == 'Website Development' ? 'selected' : '' }}>
                                                Website Development
                                            </option>
                                            <option value="IT" {{ $user->department == 'IT' ? 'selected' : '' }}>
                                                IT
                                            </option>
                                            <option value="SEO" {{ $user->department == 'SEO' ? 'selected' : '' }}>
                                                SEO
                                            </option>
                                            <option value="Content"
                                                {{ $user->department == 'Content' ? 'selected' : '' }}>
                                                Content
                                            </option>
                                            <option value="Marketing"
                                                {{ $user->department == 'Marketing' ? 'selected' : '' }}>
                                                Marketing
                                            </option>
                                            <option value="HR" {{ $user->department == 'HR' ? 'selected' : '' }}>
                                                HR
                                            </option>
                                        </select>
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
                        <!-- Vac leave -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Vacation Leave</label>
                                <input type="number" class="form-control" name="vacLeave" id="vacLeave"
                                    value="{{ $user->vacLeave ?? 0 }}" required>
                            </div>
                        </div>


                        <!-- Sick leave -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Sick Leave</label>
                                <input type="number" class="form-control" name="sickLeave" id="sickLeave"
                                    value="{{ $user->sickLeave ?? 0 }}" required>
                            </div>
                        </div>

                        <!-- Bday Leave -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Bday Leave</label>
                                <input type="number" class="form-control" name="bdayLeave" id="bdayLeave"
                                    value="{{ $user->bdayLeave ?? 0 }}" required>
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
                                <label> Monthly Salary </label>
                                <input type="text" name="mSalary" id="mSalary" class="form-control"
                                    value="{{ $user->mSalary }}">
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
                                    <option value="4" {{ $user->role_as == '4' ? 'selected' : '' }}>Operations Manager
                                    </option>
                                    <option value="5" {{ $user->role_as == '4' ? 'selected' : '' }}>IT Manager
                                    </option>
                                    <option value="6" {{ $user->role_as == '4' ? 'selected' : '' }}>Marketing Manager
                                    </option>
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
                <form action="{{ url('hr/employee/personal-info/'. $user->id) }}" method="POST">
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
                <form action="{{ url('hr/employee/update/mandates/'. $user->id) }}" method="POST"
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
                <form action="{{ url('hr/employee/contact/'. $user->id) }}" method="POST">
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
                <form action="{{ url('hr/employee/bank/' .$user->id) }}" method="POST">
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
                <form action="{{ url('hr/employee/changepassword/'. $user->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
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
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control" required />
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="toggle-confirm-password">
                                            <i class="la la-eye"></i>
                                        </span>
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


@endsection
