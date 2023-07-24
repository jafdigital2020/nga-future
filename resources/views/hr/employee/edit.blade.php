@extends('layouts.hrmaster') @section('title', 'Edit Employee')
@section('content')
<br>
<div class="container">
    <div class="row">
        <div class="profile-nav col-md-3">
            <div class="panel">
                <div class="user-heading round">
                    <a href="#">
                        @if ($user->image)
                        <img
                            src="{{ asset('images/' . $user->image) }}"
                            alt="Profile Image"
                        />
                        @else
                        <img
                            src="{{
                                asset('images/default-profile-image.jpg')
                            }}"
                            alt="Profile Image"
                        />
                        @endif
                    </a>
                    <h1>{{ $user->name }}</h1>
                    <p>{{ $user->email }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-9 border-right">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-right">Edit Employee</h4>
                    <a
                    href="{{ url('hr/employee') }}"
                    class="btn btn-success float-end"
                    id="editBTN"
                    >Back</a
                >
                </div>
                <form
                            action="{{ url('hr/employee/update/'.$user->id) }}"
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
                                    <select name="position" class="form-control" value="{{ $user->position }}">
                                        <!-- Technical Marketing -->
                                        <option value="Digital Marketing Associate"  {{ old('position') == 'Digital Marketing Associate' ? 'selected' : '' }}>
                                            Digital Marketing Associate
                                        </option>
                                        <option
                                            value="Digital Marketing Specialist"  {{ old('position') == 'Digital Marketing Specialist' ? 'selected' : '' }}>
                                        
                                            Digital Marketing Specialist
                                        </option>
                                        <option
                                            value="Digital Marketing Senior Specialist" {{ old('position') == 'Digital Marketing Senior Specialist' ? 'selected' : '' }}>
                                        
                                            Digital Marketing Senior Specialist
                                        </option>
                                        <option value="Digital Marketing Expert" {{ old('position') == 'Digital Marketing Expert' ? 'selected' : '' }}>
                                            Digital Marketing Expert
                                        </option>
                                        <!-- IT Services -->
                                        <option value="IT Associate" {{ old('position') == 'IT Associate' ? 'selected' : '' }}>
                                            IT Associate
                                        </option>
                                        <option value="IT Specialist" {{ old('position') == 'IT Specialist' ? 'selected' : '' }}>
                                            IT Specialist
                                        </option>
                                        <option value="IT Senior Specialist" {{ old('position') == 'IT Senior Specialist' ? 'selected' : '' }}>
                                            IT Senior Specialist
                                        </option>
                                        <option value="IT Lead" {{ old('position') == 'IT Lead' ? 'selected' : '' }}>IT Lead</option>
                                        <!-- Web Developer -->
                                        <option value="Associate Website Developer" {{ old('position') == 'Associate Website Developer' ? 'selected' : '' }}>
                                            Associate Website Developer
                                        </option>
                                        <option value="Junior Website Developer" {{ old('position') == 'Junior Website Developer' ? 'selected' : '' }}>
                                            Junior Website Developer
                                        </option>
                                        <option value="Senior Website Developer" {{ old('position') == 'Senior Website Developer' ? 'selected' : '' }}>
                                            Senior Website Developer
                                        </option>
                                        <option value="Lead Website Developer" {{ old('position') == 'Lead Website Developer' ? 'selected' : '' }}>
                                            Lead Website Developer
                                        </option>
                                        <!-- Search Engine Optimization -->
                                        <option value="SEO Associate" {{ old('position') == 'SEO Associate' ? 'selected' : '' }}>
                                            SEO Associate
                                        </option>
                                        <option value="SEO Specialist" {{ old('position') == 'SEO Specialist' ? 'selected' : '' }}>
                                            SEO Specialist
                                        </option>
                                        <option value="SEO Senior Specialist" {{ old('position') == 'SEO Senior Specialist' ? 'selected' : '' }}>
                                            SEO Senior Specialist
                                        </option>
                                        <option value="SEO Expert" {{ old('position') == 'SEO Expert' ? 'selected' : '' }}>
                                            SEO Expert
                                        </option>
                                        <!-- Graphic Designer -->
                                        <option value="Associate Graphic Designer" {{ old('position') == 'Associate Graphic Designer' ? 'selected' : '' }}>
                                            SEO Expert
                                            Associate Graphic Designer
                                        </option>
                                        <option value="Junior Graphic Designer" {{ old('position') == 'Junior Graphic Designer' ? 'selected' : '' }}>
                                            Junior Graphic Designer
                                        </option>
                                        <option value="Senior Graphic Designer" {{ old('position') == 'Senior Graphic Designer' ? 'selected' : '' }}>
                                            Senior Graphic Designer
                                        </option>
                                        <option value="Lead Graphic Designer" {{ old('position') == 'Lead Graphic Designer' ? 'selected' : '' }}>
                                            Lead Graphic Designer
                                        </option>
                                        <!-- Content Writer -->
                                        <option value="Associate Content Writer" {{ old('position') == 'Associate Content Writer' ? 'selected' : '' }}>
                                            Associate Content Writer
                                        </option>
                                        <option value="Junior Content Writer" {{ old('position') == 'Junior Content Writer' ? 'selected' : '' }}>
                                            Junior Content Writer
                                        </option>
                                        <option value="Senior Content Writer" {{ old('position') == 'Senior Content Writer' ? 'selected' : '' }}>
                                            Senior Content Writer
                                        </option>
                                        <option value="Expert Content Writer" {{ old('position') == 'Expert Content Writer' ? 'selected' : '' }}>
                                            Expert Content Writer
                                        </option>
                                        <!-- DATA ENTRY -->
                                        <option value="Associate Data Entry" {{ old('position') == 'Associate Data Entry' ? 'selected' : '' }}> 
                                            Associate Data Entry
                                        </option>
                                        <option value="Junior Data Entry" {{ old('position') == 'Junior Data Entry' ? 'selected' : '' }}> 
                                            Junior Data Entry
                                        </option>
                                        <option value="Senior Data Entry" {{ old('position') == 'Senior Data Entry' ? 'selected' : '' }}>  
                                            Senior Data Entry
                                        </option>
                                        <option value="Expert Data Entry" {{ old('position') == 'Expert Data Entry' ? 'selected' : '' }}>
                                            Expert Data Entry
                                        </option>
                                        <!-- LOAN PROCESSOR -->
                                        <option value="Associate Loan Processor" {{ old('position') == 'Associate Loan Processor' ? 'selected' : '' }}>
                                            Associate Loan Processor
                                        </option>
                                        <option value="Junior Loan Processor" {{ old('position') == 'Junior Loan Processor' ? 'selected' : '' }}>
                                            Junior Loan Processor
                                        </option>
                                        <option value="Senior Loan Processor" {{ old('position') == 'Senior Loan Processor' ? 'selected' : '' }}>
                                            Senior Loan Processor
                                        </option>
                                        <option value="Lead Loan Processor" {{ old('position') == 'Lead Loan Processor' ? 'selected' : '' }}>
                                            Lead Loan Processor
                                        </option>
                                        <!-- Admin HR -->
                                        <option value="Associate HR Generalist" {{ old('position') == 'Associate HR Generalist' ? 'selected' : '' }}>
                                            Associate HR Generalist
                                        </option>
                                        <option value="Junior HR Generalist" {{ old('position') == 'Junior HR Generalist' ? 'selected' : '' }}>
                                            Junior HR Generalist
                                        </option>
                                        <option value="Senior HR Generalist" {{ old('position') == 'Senior HR Generalist' ? 'selected' : '' }}>
                                            Senior HR Generalist
                                        </option>
                                        <option value="Supervisor Generalist" {{ old('position') == 'Supervisor Generalist' ? 'selected' : '' }}>
                                            Supervisor Generalist
                                        </option>
                                        <!-- Management -->
                                        <option value="Manager" {{ old('position') == 'Manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="Senior Manager" {{ old('position') == 'Senior Manager' ? 'selected' : '' }}>
                                            Senior Manager
                                        </option>
                                        <option value="Director" {{ old('position') == 'Director' ? 'selected' : '' }}>Director</option>
                                        <option value="Managing Director" {{ old('position') == 'Managing Director' ? 'selected' : '' }}>
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
                                        <div class="col-md-6">
                                            <label for="exampleInputPassword1">Password</label>
                                            <input type="password"
                                                   name="password"
                                                   class="form-control"
                                                   id="exampleInputPassword1"
                                                   value="{{ old('password') }}"
                                            />
                                            <span style="color: red">@error('password'){{ $message }}@enderror</span>
                                        </div>
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
                <div class="col-md-6"><label for="">Hourly Rate</label>
                    <input type="number" name="hourlyRate" id="hourlyRate" class="form-control"></div>
                    <div class="col-md-6">
                    <button class="btn btn-primary profile-button" type="submit">Update</button>
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
