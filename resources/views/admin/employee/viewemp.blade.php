@extends('layouts.master') @section('title', 'Employee Details')
@section('content')
<section>
    <div class="container py-10">
        <div class="row">
            <div class="col">
                <nav
                    aria-label="breadcrumb"
                    class="bg-light rounded-3 p-3 mb-4"
                >
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ url('admin/employees') }}">Back</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#">Employee View</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div
                        class="card-body text-center"
                        style="border: 2px solid black"
                    >
                        @if ($user->image)
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
                        @endif
                        <h5 class="my-3">{{ $user->name }}</h5>
                        <p class="text-muted mb-1">{{ $user->position }}</p>
                        <p class="text-muted mb-4"></p>
                        <div class="d-flex justify-content-center mb-2">
                            <button
                                type="button"
                                class="btn btn-outline-primary ms-1"
                            >
                                Edit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body" style="border: 2px solid black">
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Full Name :</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $user->name }}</p>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Email :</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">
                                    {{ $user->email }}
                                </p>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Phone :</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">
                                    {{ $user->phoneNumber }}
                                </p>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Emp Number :</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">
                                    {{ $user->empNumber }}
                                </p>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Address :</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">
                                    {{ $user->completeAddress }}
                                </p>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Contract :</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">
                                    {{ $user->typeOfContract }}
                                </p>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Date Hired :</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">
                                    {{ $user->dateHired }}
                                </p>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Birthday :</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">
                                    {{ $user->birthday }}
                                </p>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Position :</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">
                                    {{ $user->position }}
                                </p>
                            </div>
                        </div>
                        <hr />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
