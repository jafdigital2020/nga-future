<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1"
        />
        <title>@yield('title')</title>
        <!-- Bootstrap CSS -->
        <link
            href="{{ asset('assets/css/jquery.dataTables.min.css') }}"
            rel="stylesheet"
        />
        <link
            href="{{ asset('assets/css/bootstrap.min.css') }}"
            rel="stylesheet"
        />
        <link
            href="{{ asset('assets/css/dataTables.bootstrap.min.css') }}"
            rel="stylesheet"
        />
        <!----css3---->
        <link rel="icon" href="{{ url('assets/img/jaflogo.png') }}" />
        <link
            href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/5.0.7/sweetalert2.min.css"
            rel="stylesheet"
        />
        <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />
        <!-- <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" /> -->
        <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->

        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap"
            rel="stylesheet"
        />

        <!--google material icon-->
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Icons"
            rel="stylesheet"
        />
        <link
            href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css"
            rel="stylesheet"
        />
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
            integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
        ></script>
    </head>
    <body onload="initClock()">
        <div class="wrapper">
            <div class="body-overlay"></div>

            <!-- Sidebar  -->
            <nav id="sidebar">
                <div class="sidebar-header">
                    <h3>
                        <img
                            src="../assets/img/jaflogo.png"
                            class="img-fluid"
                        /><span>One JAF</span>
                    </h3>
                </div>
                <ul class="list-unstyled components">
                    <li class="">
                        <a
                            href="{{ url('emp/dashboard') }}"
                            class="{{ Request::is('emp/dashboard') ? 'active':'' }}"
                            ><i class="material-icons">dashboard</i
                            ><span>Dashboard</span></a
                        >
                    </li>

                    <!-- <li class="dropdown">
                        <a
                            href="#homeSubmenu1"
                            data-toggle="collapse"
                            aria-expanded="false"
                            class="dropdown-toggle"
                        >
                            <i class="material-icons">people</i
                            ><span>Layouts</span></a
                        >
                        <ul
                            class="collapse list-unstyled menu"
                            id="homeSubmenu1"
                        >
                            <li>
                                <a href="#">Home 1</a>
                            </li>
                            <li>
                                <a href="#">Home 2</a>
                            </li>
                            <li>
                                <a href="#">Home 3</a>
                            </li>
                        </ul>
                    </li> -->

                    <!-- <li class="dropdown">
                        <a
                            href="#pageSubmenu2"
                            data-toggle="collapse"
                            aria-expanded="false"
                            class="dropdown-toggle"
                        >
                            <i class="material-icons">apps</i
                            ><span>widgets</span></a
                        >
                        <ul
                            class="collapse list-unstyled menu"
                            id="pageSubmenu2"
                        >
                            <li>
                                <a href="#">Page 1</a>
                            </li>
                            <li>
                                <a href="#">Page 2</a>
                            </li>
                            <li>
                                <a href="#">Page 3</a>
                            </li>
                        </ul>
                    </li> -->

                    <!-- <li class="dropdown">
                        <a
                            href="#pageSubmenu3"
                            data-toggle="collapse"
                            aria-expanded="false"
                            class="dropdown-toggle"
                        >
                            <i class="material-icons">equalizer</i>

                            <span>chart</span></a
                        >
                        <ul
                            class="collapse list-unstyled menu"
                            id="pageSubmenu3"
                        >
                            <li>
                                <a href="#">Page 1</a>
                            </li>
                            <li>
                                <a href="#">Page 2</a>
                            </li>
                            <li>
                                <a href="#">Page 3</a>
                            </li>
                        </ul>
                    </li> -->
                    <!-- <li class="dropdown">
                        <a
                            href="#pageSubmenu4"
                            data-toggle="collapse"
                            aria-expanded="false"
                            class="dropdown-toggle"
                        >
                            <i class="material-icons">extension</i
                            ><span>ui element</span></a
                        >
                        <ul
                            class="collapse list-unstyled menu"
                            id="pageSubmenu4"
                        >
                            <li>
                                <a href="#">Page 1</a>
                            </li>
                            <li>
                                <a href="#">Page 2</a>
                            </li>
                            <li>
                                <a href="#">Page 3</a>
                            </li>
                        </ul>
                    </li> -->

                    <!-- <li class="dropdown">
                        <a
                            href="#pageSubmenu5"
                            data-toggle="collapse"
                            aria-expanded="false"
                            class="dropdown-toggle"
                        >
                            <i class="material-icons">border_color</i
                            ><span>forms</span></a
                        >
                        <ul
                            class="collapse list-unstyled menu"
                            id="pageSubmenu5"
                        >
                            <li>
                                <a href="#">Page 1</a>
                            </li>
                            <li>
                                <a href="#">Page 2</a>
                            </li>
                            <li>
                                <a href="#">Page 3</a>
                            </li>
                        </ul>
                    </li> -->

                    <!-- <li class="dropdown">
                        <a
                            href="#pageSubmenu6"
                            data-toggle="collapse"
                            aria-expanded="false"
                            class="dropdown-toggle"
                        >
                            <i class="material-icons">grid_on</i
                            ><span>tables</span></a
                        >
                        <ul
                            class="collapse list-unstyled menu"
                            id="pageSubmenu6"
                        >
                            <li>
                                <a href="#">Page 1</a>
                            </li>
                            <li>
                                <a href="#">Page 2</a>
                            </li>
                            <li>
                                <a href="#">Page 3</a>
                            </li>
                        </ul>
                    </li> -->

                    <!-- <li class="dropdown">
                        <a
                            href="#pageSubmenu7"
                            data-toggle="collapse"
                            aria-expanded="false"
                            class="dropdown-toggle"
                        >
                            <i class="material-icons">date_range</i
                            ><span>Attendance</span></a
                        >
                        <ul
                            class="collapse list-unstyled menu"
                            id="pageSubmenu7"
                        >
                            <li>
                                <a
                                    href="{{ url('admin/attendance') }}"
                                    class="{{ Request::is('admin/attendance') ? 'active':'' }}"
                                    >Attendance Reports</a
                                >
                            </li>
                        </ul>
                    </li> -->

                    <li class="dropdown">
                        <a
                            href="#pageSubmenu7"
                            data-toggle="collapse"
                            aria-expanded="false"
                            class="dropdown-toggle"
                        >
                            <i class="material-icons">date_range</i
                            ><span>Attendance Page</span></a
                        >
                        <ul
                            class="collapse list-unstyled menu"
                            id="pageSubmenu7"
                        >
                            <li>
                                <a
                                    href="{{ url('emp/attendance') }}"
                                    class="{{ Request::is('emp/attendance') ? 'active':'' }}"
                                    >Attendance</a
                                >
                            </li>
                            <li>
                                <a
                                    href="{{ url('emp/attendance/report') }}"
                                    class="{{ Request::is('emp/attendance/report') ? 'active':'' }}"
                                    >Attendance Reports</a
                                >
                            </li>
                        </ul>
                    </li>

                    <li class="">
                        <a
                            href="{{ url('emp/calendar') }}"
                            class="{{ Request::is('emp/calendar') ? 'active':'' }}"
                            ><i class="material-icons">event</i
                            ><span>Calendar Activities</span></a
                        >
                    </li>

                    <li class="">
                        <a href="#"
                            ><i class="material-icons">insert_drive_file</i
                            ><span>Leave</span></a
                        >
                    </li>
                    <li class="">
                        <a href="{{ url('emp/payslip') }}"
                            ><i class="material-icons">payment</i
                            ><span>Payslip</span></a
                        >
                    </li>

                    <!-- <li class="">
                        <a href="#"
                            ><i class="material-icons">payment</i
                            ><span>Payroll</span></a
                        >
                    </li> -->
                </ul>
            </nav>

            <!-- Page Content  -->
            <div id="content">
                <div class="top-navbar">
                    <nav class="navbar navbar-expand-lg">
                        <div class="container-fluid">
                            <button
                                type="button"
                                id="sidebarCollapse"
                                class="d-xl-block d-lg-block d-md-mone d-none"
                            >
                                <span
                                    class="material-icons"
                                    style="
                                        margin-top: 0px;
                                        margin-left: 8px;
                                        padding: 10px 5px 10px 0px;
                                    "
                                    >arrow_back_ios</span
                                >
                            </button>

                            <a class="navbar-brand" href="#"> Dashboard</a>

                            <button
                                class="d-inline-block d-lg-none ml-auto more-button"
                                type="button"
                                data-toggle="collapse"
                                data-target="#navbarSupportedContent"
                                aria-controls="navbarSupportedContent"
                                aria-expanded="false"
                                aria-label="Toggle navigation"
                            >
                                <span class="material-icons">more_vert</span>
                            </button>

                            <!-- NAV BAR -->
                            <div
                                class="collapse navbar-collapse d-lg-block d-xl-block d-sm-none d-md-none d-none"
                                id="navbarSupportedContent"
                            >
                                <ul class="nav navbar-nav ml-auto">
                                    <li class="dropdown nav-item active">
                                        <a
                                            href="#"
                                            class="nav-link"
                                            data-toggle="dropdown"
                                        >
                                            <span class="material-icons"
                                                >notifications</span
                                            >
                                            <span class="notification">4</span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="#"
                                                    >You have 99 new messages</a
                                                >
                                            </li>
                                            <li>
                                                <a href="#"
                                                    >You're now friend with
                                                    Mike</a
                                                >
                                            </li>
                                            <li>
                                                <a href="#"
                                                    >Wish Mary on her
                                                    birthday!</a
                                                >
                                            </li>
                                            <li>
                                                <a href="#"
                                                    >5 warnings in Server
                                                    Console</a
                                                >
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">
                                            <span class="material-icons"
                                                >apps</span
                                            >
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">
                                            <span class="material-icons"
                                                >person</span
                                            >
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            class="nav-link"
                                            href="#"
                                            data-toggle="dropdown"
                                        >
                                            <span class="material-icons"
                                                >settings</span
                                            >
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a
                                                    href="{{
                                                        url('emp/empprofile')
                                                    }}"
                                                    >Profile</a
                                                >
                                            </li>
                                            <li>
                                                <a href="#">Settings</a>
                                            </li>
                                            <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="{{ route('logout') }}"
                                                    onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"
                                                >
                                                    {{ __("Logout") }}
                                                </a>
                                                <form
                                                    id="logout-form"
                                                    action="{{
                                                        route('logout')
                                                    }}"
                                                    method="POST"
                                                    class="d-none"
                                                >
                                                    @csrf
                                                </form>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
                <!-- Main Content -->
                <div class="main-content">@yield('content')</div>
            </div>
        </div>
        <script src="{{
                asset('assets/js/jquery-3.3.1.slim.min.js')
            }}"></script>
        <script src="{{ asset('assets/js/popper.min.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
        <script src="{{ asset('assets/js/main.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/js/custom.js') }}"></script>
        <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
        <script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                $("#sidebarCollapse").on("click", function () {
                    $("#sidebar").toggleClass("active");
                    $("#content").toggleClass("active");
                });

                $(".more-button,.body-overlay").on("click", function () {
                    $("#sidebar,.body-overlay").toggleClass("show-nav");
                });
            });
        </script>
    </body>
</html>
