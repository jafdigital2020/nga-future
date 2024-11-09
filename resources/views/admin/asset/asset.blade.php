@extends('layouts.master') @section('title', 'Asset') @section('content')
@include('sweetalert::alert')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Assets</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Assets</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_asset"><i class="fa fa-plus"></i>
                    Add Asset</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    <form action="{{ route('admin.asset') }}" method="GET">
        <div class="row filter-row">
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="status">
                        <option value=""> -- Select -- </option>
                        @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                        @endforeach
                    </select>
                    <label class="focus-label">Status</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="model">
                        <option value=""> -- Select -- </option>
                        @foreach($models as $model)
                        <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>
                            {{ ucfirst($model) }}
                        </option>
                        @endforeach
                    </select>
                    <label class="focus-label">Model</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="manufacturer">
                        <option value=""> -- Select -- </option>
                        @foreach($manufacturers as $manufacturer)
                        <option value="{{ $manufacturer }}"
                            {{ request('manufacturer') == $manufacturer ? 'selected' : '' }}>
                            {{ ucfirst($manufacturer) }}
                        </option>
                        @endforeach
                    </select>
                    <label class="focus-label">Manufacturer</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <button type="submit" class="btn btn-success btn-block">Search</button>
            </div>
        </div>
    </form>

    <!-- /Search Filter -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th>Asset Name</th>
                            <th>Model</th>
                            <th>Manufacturer</th>
                            <th>Serial Number</th>
                            <th>Purchase Date</th>
                            <th>Condition</th>
                            <th>Amount</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assets as $asset)
                        <tr>
                            <td>{{ $asset->name }}</td>
                            <td>{{ $asset->model }}</td>
                            <td>{{ $asset->manufacturer }}</td>
                            <td>{{ $asset->serial_number }}</td>
                            <td>{{ $asset->purchase_date }}</td>
                            <td>{{ $asset->condition }}</td>
                            <td>{{ $asset->value }}</td>
                            <td>{{ $asset->status }}</td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item edit-asset" href="#" data-id="{{ $asset->id }}"
                                            data-name="{{ $asset->name }}" data-model="{{ $asset->model }}"
                                            data-manufacturer="{{ $asset->manufacturer }}"
                                            data-serial_number="{{ $asset->serial_number }}"
                                            data-purchase_date="{{ $asset->purchase_date }}"
                                            data-condition="{{ $asset->condition }}" data-value="{{ $asset->value }}"
                                            data-status="{{ $asset->status }}">
                                            <i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item delete-asset" href="#" data-id="{{ $asset->id }}">
                                            <i class="fa fa-trash-o m-r-5"></i> Delete</a>
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

<!-- Add Asset Modal -->
<div id="add_asset" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Asset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.assetStore') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Asset Name</label>
                                <input class="form-control" type="text" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Purchase Date</label>
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text"
                                        name="purchase_date">
                                </div>
                                <label class="focus-label">Date</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Model</label>
                                <input class="form-control" type="text" name="model">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Manufacturer</label>
                                <input class="form-control" type="text" name="manufacturer">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Serial Number</label>
                                <input class="form-control" type="text" name="serial_number" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Condition</label>
                                <select class="form-control" name="condition" required>
                                    <option value=""> -- Select -- </option>
                                    <option value="New"> New </option>
                                    <option value="Good"> Good </option>
                                    <option value="Damaged"> Damaged </option>
                                    <option value="Under Maintenance">Under Maintenance</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Status</label>
                                <select class="form-control" name="status" required>
                                    <option value=""> -- Select -- </option>
                                    <option value="0"> Available </option>
                                    <option value="1"> Deployed </option>
                                    <option value="2"> Returned </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Value</label>
                                <input class="form-control" type="text" name="value">
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn" type="submit">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<!-- /Add Asset Modal -->

<!-- Edit Asset Modal -->
<div id="edit_asset" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Asset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editAssetForm" method="POST">
                    @csrf
                    <input type="hidden" name="asset_id" id="asset_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Asset Name</label>
                                <input class="form-control" type="text" name="name" id="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Purchase Date</label>
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text" name="purchase_date"
                                        id="purchase_date">
                                </div>
                                <label class="focus-label">Date</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Model</label>
                                <input class="form-control" type="text" name="model" id="model">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Manufacturer</label>
                                <input class="form-control" type="text" name="manufacturer" id="manufacturer">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Serial Number</label>
                                <input class="form-control" type="text" name="serial_number" id="serial_number"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Condition</label>
                                <select class="form-control" name="condition" id="condition" required>
                                    <option value=""> -- Select -- </option>
                                    <option value="New"> New </option>
                                    <option value="Good"> Good </option>
                                    <option value="Damaged"> Damaged </option>
                                    <option value="Under Maintenance">Under Maintenance</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Status</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value=""> -- Select -- </option>
                                    <option value="Available"> Available </option>
                                    <option value="Deployed"> Deployed </option>
                                    <option value="Returned"> Returned </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Value</label>
                                <input class="form-control" type="text" name="value" id="value">
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Asset Modal -->

<!-- Delete Earning Modal -->
<div class="modal custom-modal fade" id="delete_asset" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Asset</h3>
                    <p>Are you sure you want to delete this asset?</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-5">
                            <form id="deleteAssetForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="asset_id" id="asset_id">
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
<!-- /Delete Earning Modal -->

@endsection

@section('scripts')

<script>
    // Edit Earning request
    $('.edit-asset').on('click', function () {
        var assetId = $(this).data('id');
        var name = $(this).data('name');
        var model = $(this).data('model');
        var manufacturer = $(this).data('manufacturer');
        var serialNumber = $(this).data('serial_number');
        var purchaseDate = $(this).data('purchase_date');
        var condition = $(this).data('condition');
        var status = $(this).data('status');
        var value = $(this).data('value');

        $('#asset_id').val(assetId);
        $('#name').val(name);
        $('#model').val(model);
        $('#manufacturer').val(manufacturer);
        $('#serial_number').val(serialNumber);
        $('#purchase_date').val(purchaseDate);
        $('#condition').val(condition);
        $('#status').val(status);
        $('#value').val(value);


        $('#editAssetForm').attr('action', '/admin/asset/edit/' +
            assetId);
        $('#edit_asset').modal('show');
    });

    // Delete Earning request
    $('.delete-asset').on('click', function () {
        var assetId = $(this).data('id');

        $('#asset_id').val(assetId);
        $('#deleteAssetForm').attr('action', '/admin/asset/delete/' + assetId);
        $('#delete_asset').modal('show');
    });

</script>

@endsection
