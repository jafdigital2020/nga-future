@extends('layouts.master') @section('title', 'Asset Assign') @section('content')
@include('sweetalert::alert')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Assign Asset to User</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Assign Asset</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#view_asset"><i class="fa fa-eye"></i>
                    View Available Asset</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Enhanced Modal for Viewing Available and Deployed Assets -->
    <div id="view_asset" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="viewAssetLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="viewAssetLabel">
                        <i class="fa fa-boxes mr-2"></i> Asset Overview
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Available Assets Section -->
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <i class="fa fa-check-circle"></i> Available Assets
                            <span class="badge badge-dark float-right">{{ $availableAssets->sum('count') }} Total
                                Available</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-borderless table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Asset Name</th>
                                            <th class="text-center">Quantity Available</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($availableAssets as $asset)
                                        <tr>
                                            <td>{{ $asset->name }}</td>
                                            <td class="text-center"><span
                                                    class="badge badge-success">{{ $asset->count }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Deployed Assets Section -->
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <i class="fa fa-truck-loading"></i> Deployed Assets
                            <span class="badge badge-dark float-right">{{ $deployedAssets->sum('count') }} Total
                                Deployed</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-borderless table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Asset Name</th>
                                            <th class="text-center">Quantity Deployed</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($deployedAssets as $asset)
                                        <tr>
                                            <td>{{ $asset->name }}</td>
                                            <td class="text-center"><span
                                                    class="badge badge-warning">{{ $asset->count }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Enhanced Modal -->


    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Assign Asset to User</h4>

            <!-- Form for assigning an asset to a user -->
            <form action="{{ route('admin.assignAsset') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <!-- Select Asset -->
                        <div class="form-group">
                            <label for="asset_id">Select Asset</label>
                            <select class="form-control" name="asset_id" id="asset_id" required>
                                <option value=""> -- Select Asset -- </option>
                                @foreach($assets as $asset)
                                <option value="{{ $asset->id }}">
                                    {{ $asset->name }} (Serial: {{ $asset->serial_number }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Select User -->
                        <div class="form-group">
                            <label for="user_id">Select User</label>
                            <select class="form-control" name="user_id" id="user_id" required>
                                <option value=""> -- Select User -- </option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Additional Fields -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="assignment_date">Assignment Date</label>
                            <input type="date" class="form-control" name="assignment_date" id="assignment_date"
                                required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea class="form-control" name="notes" id="notes" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="submit-section text-right">
                    <button type="submit" class="btn btn-primary">Assign Asset</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assigned Assets Table -->
    <div class="card mt-4">
        <div class="card-body">
            <h4 class="card-title">Assigned Assets</h4>

            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0">
                    <thead>
                        <tr>
                            <th>Asset Name</th>
                            <th>Serial Number</th>
                            <th>Assigned To</th>
                            <th>Assignment Date</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assignedAssets as $assigned)
                        <tr>
                            <td>{{ $assigned->asset->name }}</td>
                            <td>{{ $assigned->asset->serial_number }}</td>
                            <td>{{ $assigned->user->name }} ({{ $assigned->user->email }})</td>
                            <td>{{ $assigned->assign_date }}</td>
                            <td><span class="badge badge-success">{{ $assigned->asset->status }}</span></td>
                            <td>{{ $assigned->note }}</td>
                            <td class="text-right">
                                <!-- Add any actions needed, like returning the asset -->
                                <form action="{{ route('admin.returnAsset', $assigned->asset_id) }}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Return Asset</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')

@endsection
