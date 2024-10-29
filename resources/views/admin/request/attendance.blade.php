@extends('layouts.master') @section('title', 'Request Attendance')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Attendance Certificate</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Attendance Certificate</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_policy"><i class="fa fa-plus"></i>
                    Request Attendance</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Overtime Statistics -->
    <div class="row">
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
            <div class="stats-info">
                <h6>Total Request</h6>
                <h4><span>this month</span></h4>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
            <div class="stats-info">
                <h6>Pending Request</h6>
                <h4></h4>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
            <div class="stats-info">
                <h6>Rejected</h6>
                <h4></h4>
            </div>
        </div>
    </div>
    <!-- /Overtime Statistics -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Total Hours</th>
                            <th>Reason</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                            <th>Approved By</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /Page Content -->

    @endsection

    @section('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var approveButtons = document.querySelectorAll('.approve-button');
            var declineButtons = document.querySelectorAll('.decline-button');

            approveButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var otId = button.getAttribute('data-ot-id');
                    confirmApproval(otId);
                });
            });

            declineButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var otId = button.getAttribute('data-ot-id');
                    confirmDecline(otId);
                });
            });
        });

        function confirmApproval(otId) {
            var form = document.getElementById('approve-form-' + otId);
            var confirmAction = confirm("Are you sure you want to approve this OT request?");
            if (confirmAction) {
                form.submit();
            }
        }

        function confirmDecline(otId) {
            var form = document.getElementById('decline-form-' + otId);
            var confirmAction = confirm("Are you sure you want to decline this OT request?");
            if (confirmAction) {
                form.submit();
            }
        }

    </script>


    @endsection
