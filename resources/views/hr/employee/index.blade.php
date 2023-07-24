@extends('layouts.hrmaster') @section('title', 'Employees') @section('content')
@include('sweetalert::alert')
<style>
    td {
        font-size: 14px;
    }
    .pagination {
        font-size: 13px;
    }
    .dataTables_info {
        font-size: 13px;
    }
    #addBTN {
        position: absolute;
        left: 85%;
        top: 10px;
        background: red;
        color: #fff;
        font-size: 15px;
        padding-bottom: 5px;
    }
    #addBTN i {
        position: relative;
    }
    #addBTN span {
        position: relative;
        bottom: 5px;
        font-size: 13px;
    }
    .action i {
        font-size: 15px;
    }
    #editBTN {
        padding: 6px;
        padding-bottom: 6px;
        margin-right: 5px;
    }
    #viewBTN {
        padding: 6px;
        padding-bottom: 6px;
        margin-right: 5px;
    }
    #delBTN {
        padding: 6px;
        padding-bottom: 6px;
    }
</style>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card" style="min-height: 485px">
            @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session("success") }}
            </div>
            @endif
            <div class="card-header card-header-text">
                <h4 class="card-title">Employees Details</h4>
                <!-- Button trigger modal -->
                <button
                    id="addBTN"
                    type="button"
                    class="btn btn-primary"
                    data-toggle="modal"
                    data-target="#exampleModal"
                >
                    <i class="material-icons">person_add</i>
                    <span>New Employee</span>
                </button>
                <!-- Modal -->
                <div
                    class="modal fade"
                    id="exampleModal"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="exampleModalLabel"
                    aria-hidden="true"
                >
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    Add New Employee
                                </h5>
                                <button
                                    type="button"
                                    class="close"
                                    data-dismiss="modal"
                                    aria-label="Close"
                                >
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Modal Body Start -->
                                <form
                                    action="{{ url('hr/employee') }}"
                                    method="POST"
                                    enctype="multipart/form-data"
                                >
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input
                                                    type="text"
                                                    name="name"
                                                    class="form-control"
                                                />
                                                <span style="color: red"
                                                    >@error('name'){{
                                                        $message
                                                    }}@enderror</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label>Employee Number</label>
                                                <input
                                                    type="text"
                                                    name="empNumber"
                                                    class="form-control"
                                                />
                                                <span style="color: red"
                                                    >@error('empNumber'){{
                                                        $message
                                                    }}@enderror</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label>Type Of Contract</label>
                                                <select
                                                    name="typeOfContract"
                                                    class="form-control"
                                                >
                                                    <option value="Full-Time">
                                                        Full-Time
                                                    </option>
                                                    <option value="Part-Time">
                                                        Part-Time
                                                    </option>
                                                </select>
                                                <span style="color: red"
                                                    >@error('typeOfContract'){{
                                                        $message
                                                    }}@enderror</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label>Phone Number</label>
                                                <input
                                                    type="text"
                                                    name="phoneNumber"
                                                    class="form-control"
                                                />
                                                <span style="color: red"
                                                    >@error('phoneNumber'){{
                                                        $message
                                                    }}@enderror</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label>Date Hired</label>
                                                <input
                                                    type="date"
                                                    name="dateHired"
                                                    class="form-control"
                                                />
                                                <span style="color: red"
                                                    >@error('dateHired'){{
                                                        $message
                                                    }}@enderror</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label>Birthday</label>
                                                <input
                                                    type="date"
                                                    name="birthday"
                                                    class="form-control"
                                                />
                                                <span style="color: red"
                                                    >@error('birthday'){{
                                                        $message
                                                    }}@enderror</span
                                                >
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Complete Address</label>
                                                <input
                                                    type="text"
                                                    name="completeAddress"
                                                    class="form-control"
                                                />
                                                <span style="color: red"
                                                    >@error('completeAddress'){{
                                                        $message
                                                    }}@enderror</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label>Select Role</label>
                                                <select
                                                    name="position"
                                                    class="form-control"
                                                >
                                                    <!-- Technical Marketing -->
                                                    <option
                                                        value="Digital Marketing Associate"
                                                    >
                                                        Digital Marketing
                                                        Associate
                                                    </option>
                                                    <option
                                                        value="Digital Marketing Specialist"
                                                    >
                                                        Digital Marketing
                                                        Specialist
                                                    </option>
                                                    <option
                                                        value="Digital Marketing Senior Specialist"
                                                    >
                                                        Digital Marketing Senior
                                                        Specialist
                                                    </option>
                                                    <option
                                                        value="Digital Marketing Expert"
                                                    >
                                                        Digital Marketing Expert
                                                    </option>
                                                    <!-- IT Services -->
                                                    <option
                                                        value="IT Associate"
                                                    >
                                                        IT Associate
                                                    </option>
                                                    <option
                                                        value="IT Specialist"
                                                    >
                                                        IT Specialist
                                                    </option>
                                                    <option
                                                        value="IT Senior Specialist"
                                                    >
                                                        IT Senior Specialist
                                                    </option>
                                                    <option value="IT Lead">
                                                        IT Lead
                                                    </option>
                                                    <!-- Web Developer -->
                                                    <option
                                                        value="Associate Website Developer"
                                                    >
                                                        Associate Website
                                                        Developer
                                                    </option>
                                                    <option
                                                        value="Junior Website Developer"
                                                    >
                                                        Junior Website Developer
                                                    </option>
                                                    <option
                                                        value="Senior Website Developer"
                                                    >
                                                        Senior Website Developer
                                                    </option>
                                                    <option
                                                        value="Lead Website Developer"
                                                    >
                                                        Lead Website Developer
                                                    </option>
                                                    <!-- Search Engine Optimization -->
                                                    <option
                                                        value="SEO Associate"
                                                    >
                                                        SEO Associate
                                                    </option>
                                                    <option
                                                        value="SEO Specialist"
                                                    >
                                                        SEO Specialist
                                                    </option>
                                                    <option
                                                        value="SEO Senior Specialist"
                                                    >
                                                        SEO Senior Specialist
                                                    </option>
                                                    <option value="SEO Expert">
                                                        SEO Expert
                                                    </option>
                                                    <!-- Graphic Designer -->
                                                    <option
                                                        value="Associate Graphic Designer"
                                                    >
                                                        Associate Graphic
                                                        Designer
                                                    </option>
                                                    <option
                                                        value="Junior Graphic Designer"
                                                    >
                                                        Junior Graphic Designer
                                                    </option>
                                                    <option
                                                        value="Senior Graphic Designer"
                                                    >
                                                        Senior Graphic Designer
                                                    </option>
                                                    <option
                                                        value="Lead Graphic Designer"
                                                    >
                                                        Lead Graphic Designer
                                                    </option>
                                                    <!-- Content Writer -->
                                                    <option
                                                        value="Associate Content Writer"
                                                    >
                                                        Associate Content Writer
                                                    </option>
                                                    <option
                                                        value="Junior Content Writer"
                                                    >
                                                        Junior Content Writer
                                                    </option>
                                                    <option
                                                        value="Senior Content Writer"
                                                    >
                                                        Senior Content Writer
                                                    </option>
                                                    <option
                                                        value="Expert Content Writer"
                                                    >
                                                        Expert Content Writer
                                                    </option>
                                                    <!-- DATA ENTRY -->
                                                    <option
                                                        value="Associate Data Entry"
                                                    >
                                                        Associate Data Entry
                                                    </option>
                                                    <option
                                                        value="Junior Data Entry"
                                                    >
                                                        Junior Data Entry
                                                    </option>
                                                    <option
                                                        value="Senior Data Entry"
                                                    >
                                                        Senior Data Entry
                                                    </option>
                                                    <option
                                                        value="Expert Data Entry"
                                                    >
                                                        Expert Data Entry
                                                    </option>
                                                    <!-- LOAN PROCESSOR -->
                                                    <option
                                                        value="Associate Loan Processor"
                                                    >
                                                        Associate Loan Processor
                                                    </option>
                                                    <option
                                                        value="Junior Loan Processor"
                                                    >
                                                        Junior Loan Processor
                                                    </option>
                                                    <option
                                                        value="Senior Loan Processor"
                                                    >
                                                        Senior Loan Processor
                                                    </option>
                                                    <option
                                                        value="Lead Loan Processor"
                                                    >
                                                        Lead Loan Processor
                                                    </option>
                                                    <!-- Admin HR -->
                                                    <option
                                                        value="Associate HR Generalist"
                                                    >
                                                        Associate HR Generalist
                                                    </option>
                                                    <option
                                                        value="Junior HR Generalist"
                                                    >
                                                        Junior HR Generalist
                                                    </option>
                                                    <option
                                                        value="Senior HR Generalist"
                                                    >
                                                        Senior HR Generalist
                                                    </option>
                                                    <option
                                                        value="Supervisor Generalist"
                                                    >
                                                        Supervisor Generalist
                                                    </option>
                                                    <!-- Management -->
                                                    <option value="Manager">
                                                        Manager
                                                    </option>
                                                    <option
                                                        value="Senior Manager"
                                                    >
                                                        Senior Manager
                                                    </option>
                                                    <option value="Director">
                                                        Director
                                                    </option>
                                                    <option
                                                        value="Managing Director"
                                                    >
                                                        Managing Director
                                                    </option>
                                                </select>
                                                <span style="color: red"
                                                    >@error('role_as'){{
                                                        $message
                                                    }}@enderror</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label>Email address</label>
                                                <input
                                                    type="email"
                                                    name="email"
                                                    class="form-control"
                                                />
                                                <span style="color: red"
                                                    >@error('email'){{
                                                        $message
                                                    }}@enderror</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    for="exampleInputPassword1"
                                                    >Password</label
                                                >
                                                <input
                                                    type="password"
                                                    name="password"
                                                    class="form-control"
                                                    id="exampleInputPassword1"
                                                />
                                                <span style="color: red"
                                                    >@error('password'){{
                                                        $message
                                                    }}@enderror</span
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label>Hourly Rate</label>
                                                <input
                                                    type="text"
                                                    name="hourlyRate"
                                                    class="form-control"
                                                />
                                                <span style="color: red"
                                                    >@error('password'){{
                                                        $message
                                                    }}@enderror</span
                                                >
                                            </div>

                                            <div class="form-group">
                                                <label>Select Role</label>
                                                <select
                                                    name="role_as"
                                                    class="form-control"
                                                >
                                                    <option value="3">
                                                        Employee
                                                    </option>
                                                    <option value="1">
                                                        Admin
                                                    </option>
                                                    <option value="2">
                                                        HR
                                                    </option>
                                                </select>
                                                <span style="color: red"
                                                    >@error('role_as'){{
                                                        $message
                                                    }}@enderror</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>SSS</label>
                                        <input
                                            type="text"
                                            name="sss"
                                            class="form-control"
                                        />
                                        <span style="color: red"
                                            >@error('sss'){{
                                                $message
                                            }}@enderror</span
                                        >
                                    </div>
                                    <div class="form-group">
                                        <label>Pag-Ibig</label>
                                        <input
                                            type="text"
                                            name="pagIbig"
                                            class="form-control"
                                        />
                                        <span style="color: red"
                                            >@error('pagIbig'){{
                                                $message
                                            }}@enderror</span
                                        >
                                    </div>
                                    <div class="form-group">
                                        <label>Phil Health</label>
                                        <input
                                            type="text"
                                            name="philHealth"
                                            class="form-control"
                                        />
                                        <span style="color: red"
                                            >@error('philHealth'){{
                                                $message
                                            }}@enderror</span
                                        >
                                    </div>
                                    <div class="form-group">
                                        <label>Select Image</label>
                                        <input
                                            id="image"
                                            type="file"
                                            class="form-control @error('image') is-invalid @enderror"
                                            name="image"
                                            required
                                        />

                                        @error('image')
                                        <span
                                            class="invalid-feedback"
                                            role="alert"
                                        >
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="modal-footer">
                                        <button
                                            type="submit"
                                            class="btn btn-primary"
                                        >
                                            Add
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-secondary"
                                            data-dismiss="modal"
                                        >
                                            Close
                                        </button>
                                    </div>
                                </form>
                                <!-- Modal Body End -->
                            </div>
                        </div>
                    </div>
                </div>

                <p class="category"></p>
            </div>
            <div class="card-content table-responsive">
                <table class="table" id="example" style="width: 100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th>EmployeeNumber</th>
                            <th>Contract</th>
                            <th>Email</th>
                            <th>PhoneNumber</th>
                            <th>DateHired</th>
                            <th>Position</th>
                            <th>Modify</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($emp as $emp)
                        <tr>
                            <td>{{ $emp->name }}</td>
                            <td>{{ $emp->empNumber }}</td>
                            <td>{{ $emp->typeOfContract }}</td>
                            <td>{{ $emp->email }}</td>
                            <td>{{ $emp->phoneNumber }}</td>
                            <td>{{ $emp->dateHired }}</td>
                            <td>{{ $emp->position }}</td>
                            <td>
                                <div class="action">
                                    <div class="d-flex justify-content-between">
                                        <a
                                            href="{{ url('hr/employee/edit/'.$emp->id) }}"
                                            class="btn btn-success"
                                            id="editBTN"
                                        >
                                            <i class="material-icons"
                                                >mode_edit</i
                                            >
                                        </a>
                                        <a
                                            href="{{ url('hr/employee/viewemp/'.$emp->id) }}"
                                            class="btn btn-warning"
                                            id="viewBTN"
                                        >
                                            <i class="material-icons"
                                                >visibility</i
                                            >
                                        </a>
                                        <a
                                            href="{{ url('hr/click_delete/'.$emp->id) }}"
                                            class="btn btn-danger"
                                            id="delBTN"
                                            onclick="confirmation(event)"
                                        >
                                            <i class="material-icons">delete</i>
                                        </a>
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
    @endsection

    <script>
        function confirmation(ev) {
            ev.preventDefault();
            var urlToRedirect = ev.currentTarget.getAttribute("href");
            console.log(urlToRedirect);
            swal({
                title: "Are you to delete this data?",
                text: "You will not be able to revert this!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willCancel) => {
                if (willCancel) {
                    window.location.href = urlToRedirect;
                }
            });
        }
    </script>
</div>
