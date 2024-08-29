<!DOCTYPE html>
<html lang="en">

<head>
    <title>One JAF</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />

    <link href="{{ asset('assets/css/login.css') }}" rel="stylesheet" />
    <link rel="icon" href="{{ url('assets/img/jaffavicon.png') }}" />
    <link href="{{ asset('assets/css/line-awesome.min.css') }}" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/loginmain.js') }}"></script>
</head>

<body>
    <div class='box'>
        <div class='box-form'>
            <div class='box-login-tab'></div>
            <div class='box-login-title'>
                <div class='i i-login'></div>
                <h2>LOGIN</h2>
            </div>
            <div class='box-login'>
                <div class='fieldset-body' id='login_form'>
                    <button onclick="openLoginInfo();" class='b b-form i i-more'></button>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <img src="../assets/img/jaflogo.png" class="logo" width="70%">
                        <p class='field'>
                            <label for='user'>E-MAIL</label>
                            <input type='text' name="email" value="{{ old('email') }}" id="email" type="email"
                                class="form-control @error('email') is-invalid @enderror" placeholder="Email" required
                                autocomplete="email" autofocus />

                            <span class='validation' id='user_validation' class='i i-warning'></span>
                        </p>

                        <p class='field'>
                            <label for='pass'>PASSWORD</label>
                            <input required autocomplete="current-password" name="password"
                                class="form-control @error('password') is-invalid @enderror" id="password"
                                type="password" placeholder="Password" required />
                        </p>

                        <label class='checkbox'>
                            <input type='checkbox' id="show-password" /> Show Password
                        </label>
                        <input type="submit" value="Get Started" class="submit">

                    </form>
                </div>
            </div>
        </div>
        <div class='box-info'>
            <p><button onclick="closeLoginInfo();" class='b b-info i i-left' title='Back to Sign In'></button>
                <h3>Need Help?</h3>
            </p>
            <div class='line-wh'></div>
            <button onclick="" class='b-support' title='Forgot Password?'> Forgot Password?</button>
            <button onclick="" class='b-support' title='Contact Support'> Contact Support</button>
            <div class='line-wh'></div>
        </div>
    </div>

    <div class='icon-credits'>©2020 – 2024 <a href="https://jafdigital.co/" title="JAF Digital">JAF Digital Marketing &
            IT Services</a> Powered By:
        <a href="https://conesolution.co/" title="Cone IT Solutions">Cone IT Solutions</a></div>
</body>
<script src="{{ asset('assets/js/loginmain.js') }}"></script>

<script>
    document.getElementById('show-password').addEventListener('change', function () {
        var passwordField = document.getElementById('password');
        if (this.checked) {
            passwordField.setAttribute('type', 'text');
        } else {
            passwordField.setAttribute('type', 'password');
        }
    });

</script>


</html>
