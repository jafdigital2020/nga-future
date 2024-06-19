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
                        <a href="index.html">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Employee</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_employee"><i
                        class="fa fa-plus"></i> Add Employee</a>
                <!-- <div class="view-icons">
                    <a href="employees.html" class="grid-view btn btn-link"><i class="fa fa-th"></i></a>
                    <a href="employees-list.html" class="list-view btn btn-link active"><i class="fa fa-bars"></i></a>
                </div> -->
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    <form action="{{ route('employee.search') }}" method="GET">
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
                    <select class="select floating">
                        <option>--Select Position--</option>


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
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table datatable" id="dataTable">
                    <thead>
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
                        @foreach ($emp as $employee)
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
                                    <a href="profile.html">{{ $employee->name }}
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
                                        {{ $employee->position }}
                                    </a>
                                </div>
                            </td>
                            <td>{{ $employee->typeOfContract }}</td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ url('hr/employee/edit/'.$employee->id) }}"><i
                                                class="fa fa-pencil m-r-5"></i> Edit</a>

                                        <!-- <a class="dropdown-item edit-employee" data-id="'.$employee->id.'" href="#"
                                            data-toggle="modal" data-target="#edit_user"><i
                                                class="fa fa-pencil m-r-5"></i> Edit</a> -->

                                        <button type="button" data-toggle="modal" data-target="#delete_user"
                                            class="dropdown-item deleteBtn" value="{{ $employee->id }}"><i
                                                class="fa fa-trash-o m-r-5"></i>Delete</button>
                                    </div>
                                </div>
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

<!-- Add Employee Modal -->
<div id="add_employee" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('hr/employee') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Name -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name">Full Name<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" />
                                <span style="color: red">@error('name'){{
                                    $message
                                }}@enderror</span>
                            </div>
                        </div>
                        <!-- Employee Number -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Employee Number<span class="text-danger">*</span></label>
                                <input type="text" name="empNumber" class="form-control" />
                                <span style="color: red">@error('empNumber'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>
                        <!-- Type Of Contract -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Type Of Contract<span class="text-danger">*</span></label>
                                <select name="typeOfContract" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="Regular">Regular</option>
                                    <option value="Freelancer">Freelancer</option>
                                </select>
                                <span style="color: red">@error('typeOfContract'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>
                        <!-- Position -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Select Position<span class="text-danger">*</span></label>
                                <select name="position" class="form-control">
                                    <!-- Technical Marketing -->
                                    <option>-- Select --</option>
                                    <option value="CEO & Founder">
                                        CEO & Founder
                                    </option>
                                    <option value="Co-Founder">
                                        Co-Founder
                                    </option>

                                    <option value="Operations Supervisor">Operations Supervisor</option>
                                    <option value="IT Supervisor">IT Supervisor</option>
                                    <option value="Marketing Supervisor">
                                        Marketing Supervisor
                                    </option>
                                    <options value="HR">HR</options>
                                    <option value="Senior Website Developer">
                                        Senior Website Developer
                                    </option>
                                    <option value="Junior Website Developer">
                                        Junior Website Developer
                                    </option>
                                    <option value="Associate Website Developer">
                                        Associate Website Developer
                                    </option>
                                    <option value="Cloud Specialist">
                                        Cloud Specialist
                                    </option>
                                    <option value="SEO Specialist">
                                        SEO Specialist
                                    </option>
                                    <option value="Graphic Artist">
                                        Graphic Artist
                                    </option>
                                    <option value="Digital Marketing Associate">
                                        Digital Marketing Associate
                                    </option>
                                    <option value="Content Writer">
                                        Content Writer
                                    </option>
                                    <option value="Event Coordinator">
                                        Event Coordinator
                                    </option>
                                    <option value="Admin Staff">
                                        Admin Staff
                                    </option>
                                </select>
                                <span style="color: red">@error('role_as'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>

                        <!-- Date Hired -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Joining Date
                                    <span class="text-danger">*</span></label>
                                <div class="cal-icon">
                                    <input class="form-control datetimepicker" type="text" name="dateHired"
                                        placeholder="-- Select Date --" />
                                    <span style="color: red">@error('dateHired'){{
                                        $message
                                    }}@enderror</span>
                                </div>
                            </div>
                        </div>
                        <!-- Birthday -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Birthday
                                    <span class="text-danger">*</span></label>
                                <div class="cal-icon">
                                    <input type="text" name="birthday" class="form-control datetimepicker"
                                        placeholder="-- Select Date --" />
                                    <span style="color: red">@error('birthday'){{
                                        $message
                                    }}@enderror</span>
                                </div>
                            </div>
                        </div>
                        <!-- Complete Address -->
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Complete Address<span class="text-danger">*</span></label>
                                <textarea name="completeAddress" class="form-control" cols="30" rows="3"></textarea>

                                <span style="color: red">@error('completeAddress'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Email address<span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" />
                                @error('email')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputPassword1">Password<span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" id="exampleInputPassword1"
                                    maxlength="10" />
                                <span style="color: red">@error('password'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>
                        <!-- Phone Number -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phone Number<span class="text-danger">*</span></label>
                                <input type="text" name="phoneNumber" class="form-control" />
                                <span style="color: red">@error('phoneNumber'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>
                        <!-- Hourly Rate -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Hourly Rate<span class="text-danger">*</span></label>
                                <input type="number" name="hourlyRate" class="form-control" />
                                <span style="color: red">@error('hourlyRate'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>

                        <!-- GOVERMENT MANDATES -->
                        <div class="col-sm-6">
                            <div class="goverment">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    Goverment Mandates
                                </h5>
                            </div>
                        </div>
                    </div>
                    <!-- SSS -->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>SSS<span class="text-danger">*</span></label>
                                <input type="text" name="sss" class="form-control" />
                                <span style="color: red">@error('sss'){{ $message }}@enderror</span>
                            </div>
                        </div>
                        <!-- PAG-IBIG -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Pag-Ibig<span class="text-danger">*</span></label>
                                <input type="text" name="pagIbig" class="form-control" />
                                <span style="color: red">@error('pagIbig'){{ $message }}@enderror</span>
                            </div>
                        </div>
                        <!-- Phil Health -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phil Health<span class="text-danger">*</span></label>
                                <input type="text" name="philHealth" class="form-control" />
                                <span style="color: red">@error('philHealth'){{ $message }}@enderror</span>
                            </div>
                        </div>
                        <!-- PROFILE PICTURE -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Profile Picture</label>
                                <input id="image" type="file" class="form-control @error('image') is-invalid @enderror"
                                    name="image" />

                                @error('image')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Select Role<span class="text-danger">*</span></label>
                                <select name="role_as" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="3">Employee</option>
                                    <option value="1">Admin</option>
                                    <option value="2">HR</option>
                                </select>
                                <span style="color: red">@error('role_as'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary submit-btn">
                            Add
                        </button>
                        <button type="button" class="btn btn-secondary submit-btn" data-dismiss="modal"
                            id="submit-button">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Employee Modal -->

<!-- Edit Employee Modal -->
<div id="edit_user" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <!-- Name -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name">Full Name<span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ $employee->name }}"
                                    class="form-control" />
                                <span style="color: red">@error('name'){{
                                    $message
                                }}@enderror</span>
                            </div>
                        </div>
                        <!-- Employee Number -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Employee Number<span class="text-danger">*</span></label>
                                <input type="text" name="empNumber" id="empNumber" class="form-control" />
                                <span style="color: red">@error('empNumber'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>
                        <!-- Type Of Contract -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Type Of Contract<span class="text-danger">*</span></label>
                                <select name="typeOfContract" id="typeOfContract" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="Regular">Regular</option>
                                    <option value="Contractual">Contractual</option>
                                    <option value="Freelancer">Freelancer</option>
                                </select>
                                <span style="color: red">@error('typeOfContract'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>
                        <!-- Position -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Select Position<span class="text-danger">*</span></label>
                                <select name="position" id="position" class="form-control">
                                    <!-- Technical Marketing -->
                                    <option>-- Select --</option>
                                    <option value="CEO & Founder">
                                        CEO & Founder
                                    </option>
                                    <option value="Co-Founder">
                                        Co-Founder
                                    </option>

                                    <option value="Operations Supervisor">Operations Supervisor</option>
                                    <option value="IT Supervisor">IT Supervisor</option>
                                    <option value="Marketing Supervisor">
                                        Marketing Supervisor
                                    </option>
                                    <options value="HR">HR</options>
                                    <option value="Senior Website Developer">
                                        Senior Website Developer
                                    </option>
                                    <option value="Junior Website Developer">
                                        Junior Website Developer
                                    </option>
                                    <option value="Associate Website Developer">
                                        Associate Website Developer
                                    </option>
                                    <option value="Cloud Specialist">
                                        Cloud Specialist
                                    </option>
                                    <option value="SEO Specialist">
                                        SEO Specialist
                                    </option>
                                    <option value="Graphic Artist">
                                        Graphic Artist
                                    </option>
                                    <option value="Digital Marketing Associate">
                                        Digital Marketing Associate
                                    </option>
                                    <option value="Content Writer">
                                        Content Writer
                                    </option>
                                    <option value="Event Coordinator">
                                        Event Coordinator
                                    </option>
                                    <option value="Admin Staff">
                                        Admin Staff
                                    </option>
                                </select>
                                <span style="color: red">@error('role_as'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>

                        <!-- Date Hired -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Joining Date
                                    <span class="text-danger">*</span></label>
                                <div class="cal-icon">
                                    <input class="form-control datetimepicker" type="text" name="dateHired"
                                        id="dateHired" placeholder="-- Select Date --" />
                                    <span style="color: red">@error('dateHired'){{
                                        $message
                                    }}@enderror</span>
                                </div>
                            </div>
                        </div>
                        <!-- Birthday -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Birthday
                                    <span class="text-danger">*</span></label>
                                <div class="cal-icon">
                                    <input type="text" name="birthday" id="birthday" class="form-control datetimepicker"
                                        placeholder="-- Select Date --" />
                                    <span style="color: red">@error('birthday'){{
                                        $message
                                    }}@enderror</span>
                                </div>
                            </div>
                        </div>
                        <!-- Complete Address -->
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Complete Address<span class="text-danger">*</span></label>
                                <textarea name="completeAddress" id="completeAddress" class="form-control" cols="30"
                                    rows="3"></textarea>

                                <span style="color: red">@error('completeAddress'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Email address<span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" />
                                @error('email')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputPassword1">Password<span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" class="form-control"
                                    id="exampleInputPassword1" maxlength="10" />
                                <span style="color: red">@error('password'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>
                        <!-- Phone Number -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phone Number<span class="text-danger">*</span></label>
                                <input type="text" name="phoneNumber" id="phoneNumber" class="form-control" />
                                <span style="color: red">@error('phoneNumber'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>
                        <!-- Hourly Rate -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Hourly Rate<span class="text-danger">*</span></label>
                                <input type="number" name="hourlyRate" id="hourlyRate" class="form-control" />
                                <span style="color: red">@error('hourlyRate'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>

                        <!-- GOVERMENT MANDATES -->
                        <div class="col-sm-6">
                            <div class="goverment">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    Goverment Mandates
                                </h5>
                            </div>
                        </div>
                    </div>
                    <!-- SSS -->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>SSS<span class="text-danger">*</span></label>
                                <input type="text" name="sss" id="sss" class="form-control" />
                                <span style="color: red">@error('sss'){{ $message }}@enderror</span>
                            </div>
                        </div>
                        <!-- PAG-IBIG -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Pag-Ibig<span class="text-danger">*</span></label>
                                <input type="text" name="pagIbig" id="pagIbig" class="form-control" />
                                <span style="color: red">@error('pagIbig'){{ $message }}@enderror</span>
                            </div>
                        </div>
                        <!-- Phil Health -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phil Health<span class="text-danger">*</span></label>
                                <input type="text" name="philHealth" id="philHealth" class="form-control" />
                                <span style="color: red">@error('philHealth'){{ $message }}@enderror</span>
                            </div>
                        </div>
                        <!-- PROFILE PICTURE -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Profile Picture</label>
                                <input id="image" type="file" class="form-control" name="image" />
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Select Role<span class="text-danger">*</span></label>
                                <select name="role_as" class="form-control" id="role_as">
                                    <option value="">-- Select --</option>
                                    <option value="3">Employee</option>
                                    <option value="1">Admin</option>
                                    <option value="2">HR</option>
                                </select>
                                <span style="color: red">@error('role_as'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary submit-btn">
                            Save Changes
                        </button>
                        <button type="button" class="btn btn-secondary submit-btn" data-dismiss="modal"
                            id="submit-button">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Employee Modal -->

<!-- Delete User Modal -->
<div class="modal custom-modal fade" id="delete_user" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ url('hr/employee/delete') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete User</h3>
                        <p>Are you sure want to delete?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <div class="row">
                            <div class="col-5">
                                <input type="hidden" name="emp_delete_id" id="emp_id">
                                <button class="btn add-btn" type="submit">Delete</button>
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
<!-- /Delete User Modal -->
@endsection

@section('scripts')

<script>
    $(document).ready(function () {
        $('.deleteBtn').click(function (e) {
            e.preventDefault();

            var emp_id = $(this).val();
            $('#emp_id').val(emp_id);
            $('#delete_user').model('show');
        })
    })

</script>

<!-- <script>
    $(document).ready(function () {
        $('.editBtn').click(function (e) {
            e.preventDefault();

            var edit_emp_id = $(this).val();
            $('#edit_emp_id').val(edit_emp_id);
            $('#delete_user').model('show');
        })
    })

</script> -->

@endsection
