@extends('layouts.master') @section('title', 'Asset Assign') @section('content')
@include('sweetalert::alert')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Returned Assets</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Returned Assets</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Returned Assets Table -->
    <div class="card mt-4">
        <div class="card-body">
            <h4 class="card-title">Returned Assets</h4>

            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0">
                    <thead>
                        <tr>
                            <th>Asset Name</th>
                            <th>Serial Number</th>
                            <th>Returned By</th>
                            <th>Assignment Date</th>
                            <th>Return Date</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($returnedAssets as $returned)
                        <tr>
                            <td>{{ $returned->asset->name }}</td>
                            <td>{{ $returned->asset->serial_number }}</td>
                            <td>{{ $returned->user->name }} ({{ $returned->user->email }})</td>
                            <td>{{ $returned->assign_date }}</td>
                            <td>{{ $returned->return_date }}</td>
                            <td>{{ $returned->note }}</td>
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
