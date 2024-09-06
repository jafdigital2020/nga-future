@extends('layouts.master') @section('title', 'Employees')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
@include('sweetalert::alert')

<div class="content container-fluid">

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
                                <option value="4">Operations Manager</option>
                                <option value="5">IT Manager</option>
                                <option value="6">Marketing Manager</option>
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
                                    <label class="fieldlabels">Monthly Salary: *</label> <input type="text"
                                        name="mSalary" />
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
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="fieldlabels">Flexible Time:</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" onchange="toggleShiftFields(this)"
                                                class="custom-control-input" id="flexibleTime" name="flexibleTime"
                                                value="1"> <!-- Default is off -->
                                            <label class="custom-control-label" for="flexibleTime"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="fieldlabels">Shift Start: *</label>
                                        <input type="time" name="shiftStart" id="shiftStart" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="fieldlabels">Late Threshold: *</label>
                                        <input type="time" name="lateThreshold" id="lateThreshold" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="fieldlabels">Shift End: *</label>
                                        <input type="time" name="shiftEnd" id="shiftEnd">
                                    </div>
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
                                    <h2 class="fs-title">Leave Credits:</h2>
                                </div>
                                <div class="col-5">
                                    <h2 class="steps">Step 5 - 5</h2> <!-- Updated step count -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <label class="fieldlabels">Vacation Leave: *</label> <input type="number"
                                        name="vacLeave" />
                                </div>
                                <div class="col-sm-4">
                                    <label class="fieldlabels">Sick Leave: *</label> <input type="number"
                                        name="sickLeave" />
                                </div>
                                <div class="col-sm-4">
                                    <label class="fieldlabels">Birthday Leave: *</label> <input type="number"
                                        name="bdayLeave" />
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


@endsection
