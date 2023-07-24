@extends('layouts.master') @section('title', 'Edit Employee')
@section('content')
<br>
<div class="container">
    <div class="row">
        <div class="col-md-3 ">
            <div class="d-flex flex-column align-items-center text-center p-3 py-5">@if ($user->image)
                <img
                    src="{{ asset('images/' . $user->image) }}"
                    alt="Profile Image"
                    style="width: 200px"
                />
                @else
                <img
                    src="{{
                        asset('images/default-profile-image.jpg')
                    }}"
                    alt="Profile Image"
                />
                @endif<span class="font-weight-bold">{{ $user->name }}</span><span class="text-black-50">{{ $user->email }}</span><span> </span></div>
        </div>
        <div class="col-md-9 ">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-right">Edit Employee</h4>
                    <a
                    href="{{ url('admin/employees') }}"
                    class="btn btn-success float-end"
                    id="editBTN"
                    >Back</a
                >
                </div>
                <form
                            action="{{ url('admin/employees/update/'.$user->id) }}"
                            method="POST"
                            enctype="multipart/form-data"
                        >
                            @csrf @method('PUT')

                <div class="row mt-3">
                    <div class="col-md-6"><label>Name</label>
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ $user->name }}"
                        />
                        <span style="color: red"
                            >@error('name'){{ $message }}@enderror</span
                        >
                    </div>
                    <div class="col-md-6"><label>Employee Number</label>
                        <input
                            type="text"
                            name="empNumber"
                            class="form-control"
                            value="{{ $user->empNumber }}"
                        />
                        <span style="color: red"
                            >@error('empNumber'){{
                                $message
                            }}@enderror</span
                        >
                    </div>
                    <div class="col-md-6"><label>Type Of Contract</label>
                        <select
                            name="typeOfContract"
                            class="form-control"
                        >
                            <option value="Full-Time">Full-Time</option>
                            <option value="Part-Time">Part-Time</option>
                        </select>
                        <span style="color: red"
                            >@error('typeOfContract'){{
                                $message
                            }}@enderror</span
                        ></div>
                        <div class="col-md-6"><label>Phone Number</label>
                            <input
                                type="text"
                                name="phoneNumber"
                                class="form-control"
                                value="{{ $user->phoneNumber }}"
                            />
                            <span style="color: red"
                                >@error('phoneNumber'){{
                                    $message
                                }}@enderror</span
                            ></div>
                            <div class="col-md-12"><label>Complete Address</label>
                                <input
                                    type="text"
                                    name="completeAddress"
                                    class="form-control"
                                    value="{{ $user->completeAddress }}"
                                />
                                <span style="color: red"
                                    >@error('completeAddress'){{
                                        $message
                                    }}@enderror</span
                                ></div>
                                <div class="col-md-12"><label>Select Role</label>
                                    <select name="position" class="form-control">
                                        <!-- Technical Marketing -->
                                        <option value="Digital Marketing Associate">
                                            Digital Marketing Associate
                                        </option>
                                        <option
                                            value="Digital Marketing Specialist"
                                        >
                                            Digital Marketing Specialist
                                        </option>
                                        <option
                                            value="Digital Marketing Senior Specialist"
                                        >
                                            Digital Marketing Senior Specialist
                                        </option>
                                        <option value="Digital Marketing Expert">
                                            Digital Marketing Expert
                                        </option>
                                        <!-- IT Services -->
                                        <option value="IT Associate">
                                            IT Associate
                                        </option>
                                        <option value="IT Specialist">
                                            IT Specialist
                                        </option>
                                        <option value="IT Senior Specialist">
                                            IT Senior Specialist
                                        </option>
                                        <option value="IT Lead">IT Lead</option>
                                        <!-- Web Developer -->
                                        <option value="Associate Website Developer">
                                            Associate Website Developer
                                        </option>
                                        <option value="Junior Website Developer">
                                            Junior Website Developer
                                        </option>
                                        <option value="Senior Website Developer">
                                            Senior Website Developer
                                        </option>
                                        <option value="Lead Website Developer">
                                            Lead Website Developer
                                        </option>
                                        <!-- Search Engine Optimization -->
                                        <option value="SEO Associate">
                                            SEO Associate
                                        </option>
                                        <option value="SEO Specialist">
                                            SEO Specialist
                                        </option>
                                        <option value="SEO Senior Specialist">
                                            SEO Senior Specialist
                                        </option>
                                        <option value="SEO Expert">
                                            SEO Expert
                                        </option>
                                        <!-- Graphic Designer -->
                                        <option value="Associate Graphic Designer">
                                            Associate Graphic Designer
                                        </option>
                                        <option value="Junior Graphic Designer">
                                            Junior Graphic Designer
                                        </option>
                                        <option value="Senior Graphic Designer">
                                            Senior Graphic Designer
                                        </option>
                                        <option value="Lead Graphic Designer">
                                            Lead Graphic Designer
                                        </option>
                                        <!-- Content Writer -->
                                        <option value="Associate Content Writer">
                                            Associate Content Writer
                                        </option>
                                        <option value="Junior Content Writer">
                                            Junior Content Writer
                                        </option>
                                        <option value="Senior Content Writer">
                                            Senior Content Writer
                                        </option>
                                        <option value="Expert Content Writer">
                                            Expert Content Writer
                                        </option>
                                        <!-- DATA ENTRY -->
                                        <option value="Associate Data Entry">
                                            Associate Data Entry
                                        </option>
                                        <option value="Junior Data Entry">
                                            Junior Data Entry
                                        </option>
                                        <option value="Senior Data Entry">
                                            Senior Data Entry
                                        </option>
                                        <option value="Expert Data Entry">
                                            Expert Data Entry
                                        </option>
                                        <!-- LOAN PROCESSOR -->
                                        <option value="Associate Loan Processor">
                                            Associate Loan Processor
                                        </option>
                                        <option value="Junior Loan Processor">
                                            Junior Loan Processor
                                        </option>
                                        <option value="Senior Loan Processor">
                                            Senior Loan Processor
                                        </option>
                                        <option value="Lead Loan Processor">
                                            Lead Loan Processor
                                        </option>
                                        <!-- Admin HR -->
                                        <option value="Associate HR Generalist">
                                            Associate HR Generalist
                                        </option>
                                        <option value="Junior HR Generalist">
                                            Junior HR Generalist
                                        </option>
                                        <option value="Senior HR Generalist">
                                            Senior HR Generalist
                                        </option>
                                        <option value="Supervisor Generalist">
                                            Supervisor Generalist
                                        </option>
                                        <!-- Management -->
                                        <option value="Manager">Manager</option>
                                        <option value="Senior Manager">
                                            Senior Manager
                                        </option>
                                        <option value="Director">Director</option>
                                        <option value="Managing Director">
                                            Managing Director
                                        </option>
                                    </select>
                                    <span style="color: red"
                                        >@error('position'){{
                                            $message
                                        }}@enderror</span
                                    ></div>
                                    <div class="col-md-6"><label>Email address</label>
                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control"
                                            value="{{ $user->email }}"
                                        />
                                        <span style="color: red"
                                            >@error('email'){{
                                                $message
                                            }}@enderror</span
                                        ></div>
                                        <div class="col-md-6"><label for="exampleInputPassword1"
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
                                        ></div>
                                        <div class="col-md-6"><label>Select Role</label>
                                            <select name="role_as" class="form-control">
                                                <option value="3">Employee</option>
                                                <option value="1">Admin</option>
                                                <option value="2">HR</option>
                                            </select>
                                            <span style="color: red"
                                                >@error('role_as'){{
                                                    $message
                                                }}@enderror</span
                                            ></div>
                                            <div class="col-md-6"><label for="image">Image</label>
                                                <input
                                                    type="file"
                                                    name="image"
                                                    id="image"
                                                    class="form-control-file"
                                                />
                                                @if ($errors->has('image'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong
                                                        >{{ $errors->first('image') }}</strong
                                                    >
                                                </span>
                                                @endif</div>
                </div>
                <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit">Update</button></div>
                </form>  
            </div>   
        </div>
     </div>
    </div>
</div>
</div>
</div>

@endsection
