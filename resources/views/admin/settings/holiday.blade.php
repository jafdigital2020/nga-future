@extends('layouts.settings') @section('title', 'One JAF')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
</script>
@section('content')


<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Holidays</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Holidays</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_holiday"><i
                        class="fa fa-plus"></i> Add Holiday</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0">
                    <thead>
                        <tr>
                            <th>Title </th>
                            <th>Holiday Date</th>
                            <th>Day</th>
                            <th>Type</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($holiday as $day)
                        <tr class="holiday-upcoming">
                            <td>{{ $day->title }}</td>
                            <td>{{ $day->holidayDate }}</td>
                            <td>{{ $day->holidayDay }}</td>
                            <td>{{ $day->type }}</td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#edit_holiday"><i class="fa fa-pencil m-r-5"></i>
                                            Edit</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#delete_holiday"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->

<!-- Add Holiday Modal -->
<div class="modal custom-modal fade" id="add_holiday" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Holiday</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('settings.holidayStore') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Holiday Name <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="title" id="title" required>
                    </div>
                    <div class="form-group">
                        <label>Holiday Date <span class="text-danger">*</span></label>
                        <div class="cal-icon"><input class="form-control datetimepicker" type="text" name="holidayDate"
                                id="holidayDate" required></div>
                    </div>
                    <div class="form-group">
                        <label>Holiday Day <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="holidayDay" id="holidayDay" readonly>
                    </div>

                    <div class="form-group">
                        <label>Holiday Type <span class="text-danger">*</span></label>
                        <select class="form-control" name="type" id="type" required>
                            <option value="">--Select Type--</option>
                            <option value="Regular">Regular</option>
                            <option value="Special">Special</option>
                        </select>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Holiday Modal -->

<!-- Edit Holiday Modal -->
<div class="modal custom-modal fade" id="edit_holiday" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Holiday</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label>Holiday Name <span class="text-danger">*</span></label>
                        <input class="form-control" value="New Year" type="text">
                    </div>
                    <div class="form-group">
                        <label>Holiday Date <span class="text-danger">*</span></label>
                        <div class="cal-icon"><input class="form-control datetimepicker" value="01-01-2019" type="text">
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Holiday Modal -->

<!-- Delete Holiday Modal -->
<div class="modal custom-modal fade" id="delete_holiday" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Holiday</h3>
                    <p>Are you sure want to delete?</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-6">
                            <a href="javascript:void(0);" class="btn btn-primary continue-btn">Delete</a>
                        </div>
                        <div class="col-6">
                            <a href="javascript:void(0);" data-dismiss="modal"
                                class="btn btn-primary cancel-btn">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Holiday Modal -->


@endsection

@section('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize the datetimepicker
        $('#holidayDate').datetimepicker({
            format: 'YYYY-MM-DD',
            useCurrent: false
        });

        // Event listener for date change
        $('#holidayDate').on('dp.change', function (e) {
            var selectedDate = moment(e.date).format('dddd');
            document.getElementById('holidayDay').value = selectedDate;
        });
    });

</script>

@endsection
