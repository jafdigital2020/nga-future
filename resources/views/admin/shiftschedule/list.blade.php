@extends('layouts.master') @section('title', 'Shift List')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Shift List</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('admin/employee') }}">Employees</a></li>
                    <li class="breadcrumb-item active">Shift List</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn m-r-5" data-toggle="modal" data-target="#add_shift">Add
                    Shifts</a>
                <a href="#" class="btn add-btn m-r-5" data-toggle="modal" data-target="#add_schedule">
                    Assign Shifts</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Content Starts -->
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Shift Name</th>
                            <th>Start Time</th>
                            <th>Late Threshold</th>
                            <th>End Time</th>
                            <th>Break Time</th>
                            <th class="text-center">Status</th>
                            <th class="text-right no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /Content End -->

</div>
<!-- /Page Content -->

</div>
<!-- /Page Wrapper -->

<!-- Add Shift Modal -->
<div id="add_shift" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Shift</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Shift Name <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Min Start Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Start Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Max Start Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Min End Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>End Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Max End Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Break Time (In Minutes) </label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck1">
                                <label class="custom-control-label" for="customCheck1">Recurring Shift</label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Repeat Every</label>
                                <select class="select">
                                    <option value="">1 </option>
                                    <option value="1">2</option>
                                    <option value="2">3</option>
                                    <option value="3">4</option>
                                    <option selected value="4">5</option>
                                    <option value="3">6</option>
                                </select>
                                <label class="col-form-label">Week(s)</label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group wday-box">
                                <label class="checkbox-inline"><input type="checkbox" value="monday"
                                        class="days recurring" checked=""><span class="checkmark">M</span></label>

                                <label class="checkbox-inline"><input type="checkbox" value="tuesday"
                                        class="days recurring" checked=""><span class="checkmark">T</span></label>

                                <label class="checkbox-inline"><input type="checkbox" value="wednesday"
                                        class="days recurring" checked=""><span class="checkmark">W</span></label>

                                <label class="checkbox-inline"><input type="checkbox" value="thursday"
                                        class="days recurring" checked=""><span class="checkmark">T</span></label>

                                <label class="checkbox-inline"><input type="checkbox" value="friday"
                                        class="days recurring" checked=""><span class="checkmark">F</span></label>

                                <label class="checkbox-inline"><input type="checkbox" value="saturday"
                                        class="days recurring"><span class="checkmark">S</span></label>

                                <label class="checkbox-inline"><input type="checkbox" value="sunday"
                                        class="days recurring"><span class="checkmark">S</span></label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">End On <span class="text-danger">*</span></label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck2">
                                <label class="custom-control-label" for="customCheck2">Indefinite</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Tag </label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Note </label>
                                <textarea class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Shift Modal -->

<!-- Edit Shift Modal -->
<div id="edit_shift" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Shift</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Shift Name <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Min Start Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Start Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Max Start Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Min End Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>End Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Max End Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Break Time (In Minutes) </label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck3">
                                <label class="custom-control-label" for="customCheck3">Recurring Shift</label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Repeat Every</label>
                                <select class="select">
                                    <option value="">1 </option>
                                    <option value="1">2</option>
                                    <option value="2">3</option>
                                    <option value="3">4</option>
                                    <option selected value="4">5</option>
                                    <option value="3">6</option>
                                </select>
                                <label class="col-form-label">Week(s)</label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group wday-box">
                                <label class="checkbox-inline"><input type="checkbox" value="monday"
                                        class="days recurring" checked=""><span class="checkmark">M</span></label>

                                <label class="checkbox-inline"><input type="checkbox" value="tuesday"
                                        class="days recurring" checked=""><span class="checkmark">T</span></label>

                                <label class="checkbox-inline"><input type="checkbox" value="wednesday"
                                        class="days recurring" checked=""><span class="checkmark">W</span></label>

                                <label class="checkbox-inline"><input type="checkbox" value="thursday"
                                        class="days recurring" checked=""><span class="checkmark">T</span></label>

                                <label class="checkbox-inline"><input type="checkbox" value="friday"
                                        class="days recurring" checked=""><span class="checkmark">F</span></label>

                                <label class="checkbox-inline"><input type="checkbox" value="saturday"
                                        class="days recurring"><span class="checkmark">S</span></label>

                                <label class="checkbox-inline"><input type="checkbox" value="sunday"
                                        class="days recurring"><span class="checkmark">S</span></label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">End On <span class="text-danger">*</span></label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck4">
                                <label class="custom-control-label" for="customCheck4">Indefinite</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Tag </label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Note </label>
                                <textarea class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Shift Modal -->

<!-- Add Schedule Modal -->
<div id="add_schedule" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Department <span class="text-danger">*</span></label>
                                <select class="select">
                                    <option value="">Select</option>
                                    <option value="">Development</option>
                                    <option value="1">Finance</option>
                                    <option value="2">Finance and Management</option>
                                    <option value="3">Hr & Finance</option>
                                    <option value="4">ITech</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Employee Name <span class="text-danger">*</span></label>
                                <select class="select">
                                    <option value="">Select </option>
                                    <option value="1">Richard Miles </option>
                                    <option value="2">John Smith</option>
                                    <option value="3">Mike Litorus </option>
                                    <option value="4">Wilmer Deluna</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Date</label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Shifts <span class="text-danger">*</span></label>
                                <select class="select">
                                    <option value="">Select </option>
                                    <option value="1">10'o clock Shift</option>
                                    <option value="2">10:30 shift</option>
                                    <option value="3">Daily Shift </option>
                                    <option value="4">New Shift</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Min Start Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Start Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Max Start Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Min End Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">End Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Max End Time <span class="text-danger">*</span></label>
                                <div class="input-group time timepicker">
                                    <input class="form-control"><span class="input-group-append input-group-addon"><span
                                            class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-form-label">Break Time <span class="text-danger">*</span></label>
                                <input class="form-control" type="text">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Accept Extra Hours </label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="customSwitch1" checked="">
                                    <label class="custom-control-label" for="customSwitch1"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Publish </label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="customSwitch2" checked="">
                                    <label class="custom-control-label" for="customSwitch2"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Schedule Modal -->

<!-- Delete Shift Modal -->
<div class="modal custom-modal fade" id="delete_employee" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Shift</h3>
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
<!-- /Delete Employee Modal -->

@endsection
