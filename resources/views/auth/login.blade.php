<!DOCTYPE html>
<html lang="en">

<head>
    <title>One JAF</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />

    <link href="{{ asset('assets/css/login.css') }}" rel="stylesheet" />
</head>

<body>
    <img src="../assets/img/jaflogo.png" class="logo">
    @if (session('message'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <h3>Sign In</h3>
        <div class="inputbox">
            <label>Email</label>
            <input name="email" value="{{ old('email') }}" id="email" type="email"
                class="form-control @error('email') is-invalid @enderror" placeholder="Email" required
                autocomplete="email" autofocus />
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>


        <div class="inputbox">
            <label>Password</label>
            <input required autocomplete="current-password" name="password"
                class="form-control @error('password') is-invalid @enderror" id="password" type="password"
                class="form-control rounded-left" placeholder="Password" required />
            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>



        <input type="submit" value="sign in" class="submit">

        <div class="helpBox">
            <div class="checkBox">
                <input type="checkbox" checked class="form-check-input" name="remember" id="remember" {{
                                                    old("remember") ? "checked" : ""
                                                }} />
                <label for="rem">Remember me</label>
            </div>

            <div class="popup" onclick="myFunction()">
                Forgot Password
                <span class="popuptext" id="myPopup">Please contact
                    jpaguiap@jafdigital.co</span>
            </div>
        </div>



    </form>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/loginmain.js') }}"></script>
    <script>
        function myFunction() {
            var popup = document.getElementById("myPopup");
            popup.classList.toggle("show");
        }

    </script>

</body>

</html>
