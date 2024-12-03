@extends('layouts.settings') @section('title', 'Location Setting')

@section('content')


<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Location</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Location</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_location"><i
                        class="fa fa-plus"></i> Add Location</a>
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
                            <th>Location Name</th>
                            <th>Location Address</th>
                            <th>Created By</th>
                            <th>Edit By</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $loc)
                        <tr>
                            <td>{{ $loc->location_name }}</td>
                            <td>{{ $loc->location_address }}</td>
                            <td>@if($loc->locationCreatedBy)
                                @if($loc->locationCreatedBy->fName || $loc->locationCreatedBy->lName)
                                {{ $loc->locationCreatedBy->fName }} {{ $loc->locationCreatedBy->lName }}
                                @else
                                {{ $loc->locationCreatedBy->name }}
                                @endif
                                @else
                                N/A
                                @endif
                            </td>
                            <td>@if($loc->locationEditBy)
                                {{ $loc->locationEditBy->fName }} {{ $loc->locationEditBy->lname }}
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="#" class="btn btn-success btn-sm btn-edit" data-id="{{ $loc->id }}"
                                    data-name="{{ $loc->location_name }}" data-address="{{ $loc->location_address }}">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a href="#" class="btn btn-danger btn-sm btn-delete" data-id="{{ $loc->id }}">
                                    <i class="fa fa-trash-o"></i>
                                </a>
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

<!-- ADD LOCATION MODAL -->
<div class="modal custom-modal fade" id="add_location" role="dialog">
    <div class="modal-dialog modal modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Location</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('settings.locationCreate') }}" method="POST">
                    @csrf
                    <!-- Location Name -->
                    <div class="mb-3">
                        <label for="location_name" class="form-label fw-bold">Location Name<span
                                class="text-danger">*</span></label>
                        <input type="text" name="location_name" class="form-control" placeholder="Enter location name"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="location_address" class="form-label fw-bold">Location Address<span
                                class="text-danger">*</span></label>
                        <textarea name="location_address" class="form-control"
                            placeholder="Enter location address"></textarea>
                    </div>
                    <!-- Submit Section -->
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /ADD LOCATION MODAL -->

<!-- EDIT LOCATION -->
<div id="edit_location" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Location</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editLocationForm" method="POST">
                    @csrf
                    <input type="hidden" name="location_id" id="location_id">
                    <div class="mb-3">
                        <label for="location_name" class="form-label fw-bold">Location Name<span
                                class="text-danger">*</span></label>
                        <input type="text" name="location_name" id="location_name" class="form-control"
                            placeholder="Enter location name" required>
                    </div>
                    <div class="mb-3">
                        <label for="location_address" class="form-label fw-bold">Location Address<span
                                class="text-danger">*</span></label>
                        <textarea name="location_address" id="location_address" class="form-control"
                            placeholder="Enter location address"></textarea>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Location Modal -->

<!-- DELETE LOCATION MODAL -->
<div class="modal custom-modal fade" id="delete_location" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Location</h3>
                    <p>Are you sure you want to cancel this location?</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-5">
                            <form id="deleteLocationForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="location_id" id="location_id">
                                <button class="btn add-btn" type="submit">Delete</button>
                            </form>
                        </div>
                        <div class="col-6">
                            <a href="javascript:void(0);" data-dismiss="modal" class="btn add-btn">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete location Modal -->

@endsection

@section('scripts')
<script>
    // Edit location request
    $('.btn-edit').on('click', function () {
        var locationId = $(this).data('id');
        var locationName = $(this).data('name');
        var locationAddress = $(this).data('address');

        $('#location_id').val(locationId);
        $('#location_name').val(locationName);
        $('#location_address').val(locationAddress);

        $('#editLocationForm').attr('action', '/admin/settings/location/edit/' +
            locationId);
        $('#edit_location').modal('show');
    });

    // Delete Earning request
    $('.btn-delete').on('click', function () {
        var locationId = $(this).data('id');

        $('#location_id').val(locationId);
        $('#deleteLocationForm').attr('action', '/admin/settings/location/delete/' + locationId);
        $('#delete_location').modal('show');
    });

</script>
@endsection
