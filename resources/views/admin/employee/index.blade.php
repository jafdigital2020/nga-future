@extends('layouts.master') @section('title', 'Employees') @section('content')
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
    <form action="{{ route('admin.search') }}" method="GET">
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
                    <select class="select floating" name="department">
                        <option>--Select Department--</option>
                        <option value="Website Development">Website Development</option>
                        <option value="SEO">SEO</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Graphics">Graphics</option>
                        <option value="Content">Content</option>
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
                        @foreach ($emp as $employee)
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
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item"
                                            href="{{ url('admin/employee/edit/'.$employee->id) }}"><i
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
                <form action="{{ url('admin/employee') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- First Name -->
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="name">First Name<span class="text-danger">*</span></label>
                                <input type="text" name="fName" class="form-control" required />
                            </div>
                        </div>
                        <!-- Middle Name -->
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="name">Middle Name<span class="text-danger">*</span></label>
                                <input type="text" name="mName" class="form-control" required />

                            </div>
                        </div>
                        <!-- last Name -->
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="name">Last Name<span class="text-danger">*</span></label>
                                <input type="text" name="lName" class="form-control" required />

                            </div>
                        </div>
                        <!-- Suffix Name -->
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="suffix">Suffix</label>
                                <input type="text" name="suffix" class="form-control" />
                            </div>
                        </div>

                        <!-- Professional / Nick Name -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name">Professional Name</label>
                                <input type="text" name="name" class="form-control" />
                            </div>
                        </div>
                        <!-- Employee Number -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Employee Number<span class="text-danger">*</span></label>
                                <input type="text" name="empNumber" class="form-control" required />
                                <span style="color: red">@error('empNumber'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>

                        <!-- Type Of Contract -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Type Of Contract<span class="text-danger">*</span></label>
                                <select name="typeOfContract" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="Regular">Regular</option>
                                    <option value="Contractual">Contractual</option>
                                    <option value="Probationary">Probationary</option>
                                    <option value="Intern">Intern</option>
                                </select>
                                <span style="color: red">@error('typeOfContract'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>
                        <!-- Position -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Position<span class="text-danger">*</span></label>
                                <input type="text" name="position" class="form-control" required />
                            </div>
                        </div>

                        <!-- Department -->

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Department<span class="text-danger">*</span></label>
                                <select name="department" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Website Development">Website Development</option>
                                    <option value="SEO">SEO</option>
                                    <option value="IT">IT</option>
                                    <option value="Content">Content</option>
                                    <option value="Graphics">Graphics</option>
                                    <option value="HR">HR</option>
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
                                        placeholder="-- Select Date --" required />
                                    <span style="color: red">@error('dateHired'){{
                                        $message
                                    }}@enderror</span>
                                </div>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Contact Number<span class="text-danger">*</span></label>
                                <input type="text" name="phoneNumber" class="form-control" required />
                                <span style="color: red">@error('phoneNumber'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>

                        <!-- Birthday -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Birthday
                                    <span class="text-danger">*</span></label>
                                <div class="cal-icon">
                                    <input type="text" name="birthday" class="form-control datetimepicker"
                                        placeholder="-- Select Date --" required />
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
                                <textarea name="completeAddress" class="form-control" cols="30" rows="3"
                                    required></textarea>

                                <span style="color: red">@error('completeAddress'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Email address<span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required />
                                @error('email')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputPassword1">Password<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control"
                                        id="exampleInputPassword1" maxlength="10" />
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="toggle-password">
                                            <i class="la la-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <span style="color: red">@error('password'){{ $message }}@enderror</span>
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Select Role<span class="text-danger">*</span></label>
                                <select name="role_as" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="3">Employee</option>
                                    <option value="1">Admin</option>
                                    <option value="2">HR</option>
                                    <option value="4">Operations Manager</option>
                                    <option value="5">IT Manager</option>
                                    <option value="6">Marketing Manager</option>
                                </select>
                                <span style="color: red">@error('role_as'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>

                        <!-- Salary -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Monthly Salary<span class="text-danger">*</span></label>
                                <input type="number" name="mSalary" class="form-control" required />
                                <span style="color: red">@error('hourlyRate'){{
                                        $message
                                    }}@enderror</span>
                            </div>
                        </div>

                        <!-- Leave Credits -->

                        <div class="col-sm-12">
                            <div class="goverment">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    Leave Credits
                                </h5>
                            </div>
                        </div>

                        <!-- Vacation Leave -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">Vacation Leave <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="vacLeave" required>
                            </div>
                        </div>
                        <!-- /Vacation Leave -->

                        <!-- Sick leave -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">Sick Leave <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="sickLeave" required>
                            </div>
                        </div>
                        <!-- /Sick leave -->

                        <!-- Bday leave -->
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">Birthday Leave <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="bdayLeave" required>
                            </div>
                        </div>
                        <!-- /Bday leave -->


                        <!-- GOVERMENT MANDATES -->
                        <div class="col-sm-12">
                            <div class="goverment">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    Government Mandates
                                </h5>
                            </div>
                        </div>
                    </div>
                    <!-- SSS -->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>SSS<span class="text-danger">*</span></label>
                                <input type="text" name="sss" class="form-control" required />
                                <span style="color: red">@error('sss'){{ $message }}@enderror</span>
                            </div>
                        </div>
                        <!-- PAG-IBIG -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Pag-Ibig<span class="text-danger">*</span></label>
                                <input type="text" name="pagIbig" class="form-control" required />
                                <span style="color: red">@error('pagIbig'){{ $message }}@enderror</span>
                            </div>
                        </div>
                        <!-- Phil Health -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phil Health<span class="text-danger">*</span></label>
                                <input type="text" name="philHealth" class="form-control" required />
                                <span style="color: red">@error('philHealth'){{ $message }}@enderror</span>
                            </div>
                        </div>

                        <!-- Tin Number -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Tax Identification Number<span class="text-danger">*</span></label>
                                <input type="text" name="tin" class="form-control" required />
                                <span style="color: red">@error('philHealth'){{ $message }}@enderror</span>
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

<!-- Delete User Modal -->
<div class="modal custom-modal fade" id="delete_user" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ url('admin/employee/delete') }}" method="POST">
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

<script>
    document.getElementById('toggle-password').addEventListener('click', function () {
        var passwordField = document.getElementById('exampleInputPassword1');
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
