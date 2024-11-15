@extends('layouts.master') @section('title', 'Employees')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .container-btn-file {
        display: flex;
        position: relative;
        justify-content: center;
        align-items: center;
        background-color: #307750;
        color: #fff;
        border-style: none;
        padding: 1em 2em;
        border-radius: 0.5em;
        overflow: hidden;
        z-index: 1;
        box-shadow: 4px 8px 10px -3px rgba(0, 0, 0, 0.356);
        transition: all 250ms;
    }

    .container-btn-file input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .container-btn-file>svg {
        margin-right: 1em;
    }

    .container-btn-file::before {
        content: "";
        position: absolute;
        height: 100%;
        width: 0;
        border-radius: 0.5em;
        background-color: #469b61;
        z-index: -1;
        transition: all 350ms;
    }

    .container-btn-file:hover::before {
        width: 100%;
    }


    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100px;
        height: 120px;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        text-align: center;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .btn {
        margin: 10px;
        padding: 10px 20px;
    }

</style>
@section('content')
@include('sweetalert::alert')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Create Employee</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Create Employee</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <div class="col-auto float-right ml-auto">
                    <div class="btn-group btn-group-sm">
                        <button class="container-btn-file">
                            <svg fill="#fff" width="20" height="20" viewBox="0 0 50 50">
                                <path d="M28.8125 .03125L.8125 5.34375C.339844 
                              5.433594 0 5.863281 0 6.34375L0 43.65625C0 
                              44.136719 .339844 44.566406 .8125 44.65625L28.8125 
                              49.96875C28.875 49.980469 28.9375 50 29 50C29.230469 
                              50 29.445313 49.929688 29.625 49.78125C29.855469 49.589844 
                              30 49.296875 30 49L30 1C30 .703125 29.855469 .410156 29.625 
                              .21875C29.394531 .0273438 29.105469 -.0234375 28.8125 .03125ZM32 
                              6L32 13L34 13L34 15L32 15L32 20L34 20L34 22L32 22L32 27L34 27L34 
                              29L32 29L32 35L34 35L34 37L32 37L32 44L47 44C48.101563 44 49 
                              43.101563 49 42L49 8C49 6.898438 48.101563 6 47 6ZM36 13L44 
                              13L44 15L36 15ZM6.6875 15.6875L11.8125 15.6875L14.5 21.28125C14.710938 
                              21.722656 14.898438 22.265625 15.0625 22.875L15.09375 22.875C15.199219 
                              22.511719 15.402344 21.941406 15.6875 21.21875L18.65625 15.6875L23.34375 
                              15.6875L17.75 24.9375L23.5 34.375L18.53125 34.375L15.28125 
                              28.28125C15.160156 28.054688 15.035156 27.636719 14.90625 
                              27.03125L14.875 27.03125C14.8125 27.316406 14.664063 27.761719 
                              14.4375 28.34375L11.1875 34.375L6.1875 34.375L12.15625 25.03125ZM36 
                              20L44 20L44 22L36 22ZM36 27L44 27L44 29L36 29ZM36 35L44 35L44 37L36 37Z"></path>
                            </svg>
                            Bulk Upload
                            <input class="file" name="file" type="file" id="file-upload" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">File Upload</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="file-name"></p>
                    <p>Are you sure you want to upload this file?</p>
                </div>
                <form id="uploadForm" action="{{ route('users.bulkCreate') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="file_name" id="hidden-file-name">
                    <button type="submit" class="btn btn-primary">Yes</button>
                    <button type="button" class="btn btn-secondary" id="cancel-upload">No</button>
                </form>
            </div>
        </div>
    </div>


    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-10 col-lg-6 col-xl-5 text-center p-0 mt-3 mb-2">
            <div class="card2 px-0 pt-4 pb-0 mt-3 mb-3">
                <h2 id="heading">Create Account</h2>
                <p>Fill all form fields to go to the next step</p>
                <form id="msform" action="{{ route('admin.employeecreate') }}" method="POST">
                    @csrf
                    <!-- progressbar -->
                    <ul id="progressbar">
                        <li class="active" id="account"><strong>Account</strong></li>
                        <li id="personal"><strong>Personal</strong></li>
                        <li id="company"><strong>Company</strong></li>
                        <li id="government"><strong>Mandates</strong></li>
                        <li id="leave"><strong>Leave</strong></li>
                    </ul>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <br>
                    <!-- fieldsets -->
                    <fieldset>
                        <div class="form-card">
                            <div class="row">
                                <div class="col-7">
                                    <h2 class="fs-title">Account Information:</h2>
                                </div>
                                <div class="col-5">
                                    <h2 class="steps">Step 1 - 5</h2>
                                </div>
                            </div>

                            <label class="fieldlabels">Email: *</label>
                            <input type="email" class="form-control" name="email" placeholder="Email Id" required />
                            @error('email')
                            <div class="error-message">{{ $message }}</div>
                            @enderror
                            <label class="fieldlabels">Password: *</label>
                            <div class="password-wrapper">
                                <input type="password" name="password" id="password" placeholder="Password"
                                    id="password" />
                                <span class="toggle-password" onclick="togglePasswordVisibility('password')">
                                    <i class="fa fa-eye" id="password-icon"></i>
                                </span>
                                @error('password')
                                <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <label class="fieldlabels">Confirm Password: *</label>
                            <div class="password-wrapper">
                                <input type="password" name="cpassword" id="cpassword" placeholder="Confirm Password"
                                    required />
                                <span class="toggle-password" onclick="togglePasswordVisibility('cpassword')">
                                    <i class="fa fa-eye" id="cpassword-icon"></i>
                                </span>
                                @error('cpassword')
                                <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <label class="fieldlabels">Role: *</label>
                            <select name="role_as" class="form-control" required>
                                <option value="">-- Select --</option>
                                <option value="3">Employee</option>
                                <option value="1">Admin</option>
                                <option value="2">HR</option>
                                <option value="4">Manager</option>
                            </select>
                            @error('role_as')
                            <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <input type="button" name="next" class="next action-button" value="Next" />
                    </fieldset>

                    <fieldset>
                        <div class="form-card">
                            <div class="row">
                                <div class="col-7">
                                    <h2 class="fs-title">Personal Information:</h2>
                                </div>
                                <div class="col-5">
                                    <h2 class="steps">Step 2 - 5</h2> <!-- Updated step count -->
                                </div>
                            </div>
                            <label class="fieldlabels">First Name: *</label> <input type="text" name="fName" />
                            <label class="fieldlabels">Middle Name: *</label> <input type="text" name="mName" />
                            <label class="fieldlabels">Last Name: *</label> <input type="text" name="lName" />
                            <label class="fieldlabels">Professional Name: *</label> <input type="text" name="name" />
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="fieldlabels">Contact No.: *</label> <input type="text"
                                        name="phoneNumber" />
                                </div>
                                <div class="col-sm-6">
                                    <label class="fieldlabels">Birthday: *</label> <input class="datetimepicker"
                                        type="text" name="birthday" placeholder="-- Select Date --" required />
                                </div>
                            </div>
                            <label class="fieldlabels">Address: *</label> <textarea name="completeAddress" cols="30"
                                rows="3"></textarea>
                        </div>
                        <input type="button" name="next" class="next action-button" value="Next" />
                        <input type="button" name="previous" class="previous action-button-previous" value="Previous" />
                    </fieldset>

                    <!-- Company Details -->
                    <fieldset>
                        <div class="form-card">
                            <div class="row">
                                <div class="col-7">
                                    <h2 class="fs-title">Company Details:</h2>
                                </div>
                                <div class="col-5">
                                    <h2 class="steps">Step 3 - 5</h2> <!-- Updated step count -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="fieldlabels">Employee Number: *</label> <input type="text"
                                        name="empNumber" />
                                </div>
                                <div class="col-sm-6">
                                    <label class="fieldlabels">Date Hired: *</label> <input class="datetimepicker"
                                        type="text" name="dateHired" placeholder="-- Select Date --" required />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="fieldlabels">Type of Contract: *</label>
                                    <select name="typeOfContract" required>
                                        <option value="">-- Select --</option>
                                        <option value="Regular">Regular</option>
                                        <option value="Contractual">Contractual</option>
                                        <option value="Probationary">Probationary</option>
                                        <option value="Freelancer">Freelancer</option>
                                        <option value="Intern">Intern</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="fieldlabels">Hourly Rate: *</label> <input type="text"
                                        name="hourly_rate" />
                                </div>
                                <div class="col-sm-6">
                                    <label class="fieldlabels">Department : *</label> <input type="text"
                                        name="department" />
                                </div>
                                <div class="col-sm-6">
                                    <label class="fieldlabels">Position: *</label> <input type="text" name="position" />
                                </div>
                                <div class="col-sm-12">
                                    <label class="fieldlabels">Reporting To: *</label>
                                    <select name="reporting_to">
                                        <option value="">--Select Reporting To--</option>
                                        @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->fName }} {{ $user->lName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="button" name="next" class="next action-button" value="Next" />
                        <input type="button" name="previous" class="previous action-button-previous" value="Previous" />
                    </fieldset>

                    <fieldset>
                        <div class="form-card">
                            <div class="row">
                                <div class="col-7">
                                    <h2 class="fs-title">Government Mandates:</h2>
                                </div>
                                <div class="col-5">
                                    <h2 class="steps">Step 4 - 5</h2> <!-- Updated step count -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="fieldlabels">SSS: *</label> <input type="text" name="sss" />
                                </div>
                                <div class="col-sm-6">
                                    <label class="fieldlabels">TIN: *</label> <input type="text" name="tin" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="fieldlabels">Pag-ibig: *</label> <input type="text" name="pagIbig" />
                                </div>
                                <div class="col-sm-6">
                                    <label class="fieldlabels">PhilHealth: *</label> <input type="text"
                                        name="philHealth" />
                                </div>
                            </div>
                        </div>
                        <input type="button" name="next" class="next action-button" value="Next" />
                        <input type="button" name="previous" class="previous action-button-previous" value="Previous" />
                    </fieldset>

                    <fieldset>
                        <div class="form-card">
                            <div class="row">
                                <div class="col-7">
                                    <h2 class="fs-title">Assign Leave Credits:</h2>
                                </div>
                                <div class="col-5">
                                    <h2 class="steps">Step 5 - 5</h2> <!-- Updated step count -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="d-flex flex-wrap">
                                    @foreach($availableLeaveTypes as $leaveType)
                                    <div class="form-group mx-2">
                                        <div class="checkbox-wrapper">
                                            <input style="display: none;" type="checkbox" class="inp-cbx"
                                                name="leave_types[]" value="{{ $leaveType->id }}"
                                                id="leaveType{{ $leaveType->id }}" />
                                            <label for="leaveType{{ $leaveType->id }}" class="cbx">
                                                <span>
                                                    <svg viewBox="0 0 12 9" height="9px" width="12px">
                                                        <polyline points="1 5 4 8 11 1"></polyline>
                                                    </svg>
                                                </span>
                                                <span>{{ $leaveType->leaveType }} ({{ $leaveType->leaveDays }}
                                                    days)</span>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <input type="submit" class="action-button" value="Submit" />
                        <input type="button" name="previous" class="previous action-button-previous" value="Previous" />
                    </fieldset>

                </form>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@section('scripts')

<script>
    $(document).ready(function () {
        var current_fs, next_fs, previous_fs; // fieldsets
        var opacity;
        var current = 1;
        var steps = $("fieldset").length;
        var notificationOffset = 0; // Offset for stacking notifications

        setProgressBar(current);

        $(".next").click(function (e) {
            e.preventDefault();

            current_fs = $(this).parent();
            var step = $("fieldset").index(current_fs) + 1; // Determine the current step
            var formData = current_fs.find('input, select, textarea', )
                .serialize(); // Serialize input data

            $.ajax({
                url: step === 1 ? '/admin/employee/create/validate-step-1' : step === 2 ?
                    '/admin/employee/create/validate-step-2' : step === 3 ?
                    '/admin/employee/create/validate-step-3' : step === 4 ?
                    '/admin/employee/create/validate-step-4' :
                    '/admin/employee/create/validate-step-5',
                method: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        // Proceed to the next step if validation passes
                        next_fs = current_fs.next();

                        $("#progressbar li").eq($("fieldset").index(next_fs)).addClass(
                            "active");

                        next_fs.show();
                        current_fs.animate({
                            opacity: 0
                        }, {
                            step: function (now) {
                                opacity = 1 - now;
                                current_fs.css({
                                    'display': 'none',
                                    'position': 'relative'
                                });
                                next_fs.css({
                                    'opacity': opacity
                                });
                            },
                            duration: 500
                        });
                        setProgressBar(++current);
                    }
                },
                error: function (response) {
                    var errors = response.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        showErrorNotification(value[
                            0]); // Use the pop-up function to show errors
                    });
                }
            });
        });

        $(".previous").click(function () {
            current_fs = $(this).parent();
            previous_fs = current_fs.prev();

            $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

            previous_fs.show();
            current_fs.animate({
                opacity: 0
            }, {
                step: function (now) {
                    opacity = 1 - now;
                    current_fs.css({
                        'display': 'none',
                        'position': 'relative'
                    });
                    previous_fs.css({
                        'opacity': opacity
                    });
                },
                duration: 500
            });
            setProgressBar(--current);
        });

        function setProgressBar(curStep) {
            var percent = parseFloat(100 / steps) * curStep;
            percent = percent.toFixed();
            $(".progress-bar").css("width", percent + "%");
        }

        $(".submit").click(function () {
            return false;
        });

        // Setup CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function showErrorNotification(message) {
            // Create a notification div
            var notification = $('<div class="notification"></div>').text(message);

            // Adjust position based on the number of notifications
            notification.css('top', (20 + notificationOffset) + 'px');
            notificationOffset += 60; // Increment offset for next notification

            $('body').append(notification);

            // Show notification and fade it out after 5 seconds
            notification.fadeIn(300).delay(5000).fadeOut(300, function () {
                $(this).remove(); // Remove notification from the DOM
                notificationOffset -= 60; // Decrement offset for next notification
            });
        }
    });

</script>


<script>
    function togglePasswordVisibility(inputId) {
        const passwordField = document.getElementById(inputId);
        const icon = document.getElementById(inputId + '-icon');

        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

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
    // When the file is selected
    document.getElementById('file-upload').addEventListener('change', function () {
        const file = this.files[0];

        if (file) {
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
            modal.show();

            // Display the selected file name
            document.getElementById('file-name').textContent = `Selected file: ${file.name}`;

            // Create a hidden input with the file name (only the name is passed here for confirmation)
            document.getElementById('hidden-file-name').value = file.name;

            // Save the file data to submit
            const hiddenFileInput = document.createElement('input');
            hiddenFileInput.type = 'file';
            hiddenFileInput.name = 'file';
            hiddenFileInput.id = 'hidden-file';
            hiddenFileInput.style.display = 'none';
            hiddenFileInput.files = this.files; // Assign the selected file to this input

            // Append the hidden input to the form
            document.getElementById('uploadForm').appendChild(hiddenFileInput);
        }
    });

    // Close modal on 'No' button click
    document.getElementById('cancel-upload').addEventListener('click', function () {
        const modal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
        modal.hide();
    });

</script>


@endsection
