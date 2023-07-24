<!DOCTYPE html>
<html lang="en">
    <head>
        <title>One JAF</title>
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <link
            href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap"
            rel="stylesheet"
        />

        <link
            rel="stylesheet"
            href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
        />

        <link href="{{ asset('assets/css/login.css') }}" rel="stylesheet" />
    </head>
    <body>
        <section class="ftco-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6 text-center mb-5">
                        <h2 class="heading-section"></h2>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-5">
                        <div class="login-wrap p-4 p-md-5">
                            <div
                                class="icon d-flex align-items-center justify-content-center"
                            >
                                <span
                                    ><img
                                        src="../assets/img/jaflogo.png"
                                        class="img-fluid"
                                /></span>
                            </div>
                            <h3 class="text-center mb-4">Welcome to One JAF</h3>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <input
                                        name="email"
                                        value="{{ old('email') }}"
                                        id="email"
                                        type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="Email"
                                        required
                                        autocomplete="email"
                                        autofocus
                                    />
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group d-flex">
                                    <input
                                        required
                                        autocomplete="current-password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        id="password"
                                        type="password"
                                        class="form-control rounded-left"
                                        placeholder="Password"
                                        required
                                    />
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group d-md-flex">
                                    <div class="w-50">
                                        <label
                                            class="checkbox-wrap checkbox-primary"
                                            for="remember"
                                            >Remember Me <input type="checkbox"
                                            checked class="form-check-input"
                                            name="remember" id="remember"
                                            {{
                                                old("remember") ? "checked" : ""
                                            }}
                                            />
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="w-50 text-md-right">
                                        <div
                                            class="popup"
                                            onclick="myFunction()"
                                        >
                                            Forgot Password
                                            <span class="popuptext" id="myPopup"
                                                >Please contact
                                                marisolv@jafdigital.co</span
                                            >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button
                                        type="submit"
                                        class="btn btn-primary rounded submit p-3 px-5"
                                    >
                                        Get Started
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
