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
                <form id="msform">
                    <!-- progressbar -->
                    <ul id="progressbar">
                        <li class="active" id="account"><strong>Account</strong></li>
                        <li id="personal"><strong>Personal</strong></li>
                        <li id="company"><strong>Company</strong></li> <!-- New step -->
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
                            <input type="email" name="email" placeholder="Email Id" required />
                            <label class="fieldlabels">Username: *</label>
                            <input type="text" name="uname" placeholder="UserName" />
                            <label class="fieldlabels">Password: *</label>
                            <div class="password-wrapper">
                                <input type="password" name="pwd" placeholder="Password" id="password" />
                                <span class="toggle-password" onclick="togglePasswordVisibility('password')">
                                    <i class="fa fa-eye" id="password-icon"></i>
                                </span>
                            </div>
                            <label class="fieldlabels">Confirm Password: *</label>
                            <div class="password-wrapper">
                                <input type="password" name="cpwd" placeholder="Confirm Password"
                                    id="confirm-password" />
                                <span class="toggle-password" onclick="togglePasswordVisibility('confirm-password')">
                                    <i class="fa fa-eye" id="confirm-password-icon"></i>
                                </span>
                            </div>
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
                            <label class="fieldlabels">First Name: *</label> <input type="text" name="fname"
                                placeholder="First Name" />
                            <label class="fieldlabels">Last Name: *</label> <input type="text" name="lname"
                                placeholder="Last Name" />
                            <label class="fieldlabels">Contact No.: *</label> <input type="text" name="phno"
                                placeholder="Contact No." />
                            <label class="fieldlabels">Alternate Contact No.: *</label> <input type="text" name="phno_2"
                                placeholder="Alternate Contact No." />
                        </div>
                        <input type="button" name="next" class="next action-button" value="Next" />
                        <input type="button" name="previous" class="previous action-button-previous" value="Previous" />
                    </fieldset>

                    <!-- New Address fieldset -->
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
                            <label class="fieldlabels">Address Line 1: *</label> <input type="text" name="address1"
                                placeholder="Address Line 1" />
                            <label class="fieldlabels">Address Line 2:</label> <input type="text" name="address2"
                                placeholder="Address Line 2" />
                            <label class="fieldlabels">City: *</label> <input type="text" name="city"
                                placeholder="City" />
                            <label class="fieldlabels">Postal Code: *</label> <input type="text" name="postal"
                                placeholder="Postal Code" />
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
                            <label class="fieldlabels">Upload Your Photo:</label> <input type="file" name="pic"
                                accept="image/*">
                            <label class="fieldlabels">Upload Signature Photo:</label> <input type="file" name="pic"
                                accept="image/*">
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
                            <label class="fieldlabels">Upload Your Photo:</label> <input type="file" name="pic"
                                accept="image/*">
                            <label class="fieldlabels">Upload Signature Photo:</label> <input type="file" name="pic"
                                accept="image/*">
                        </div>
                        <input type="button" name="next" class="next action-button" value="Next" />
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

        setProgressBar(current);

        $(".next").click(function () {
            current_fs = $(this).parent();
            next_fs = $(this).parent().next();

            // Add Class Active
            $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

            // Show the next fieldset
            next_fs.show();
            // Hide the current fieldset with style
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
        });

        $(".previous").click(function () {
            current_fs = $(this).parent();
            previous_fs = $(this).parent().prev();

            // Remove Class Active
            $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

            // Show the previous fieldset
            previous_fs.show();

            // Hide the current fieldset with style
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
        })
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


@endsection
