@extends('layouts.master') @section('title', 'One JAF')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Policies</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Policies</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_policy"><i class="fa fa-plus"></i>
                    Add Policy</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th>Policy Title</th>
                            <th>Policy Name </th>
                            <th>Description </th>
                            <th>Uploaded Document</th>
                            <th>Created </th>
                            <th>Uploaded By</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($policies as $policy)
                        <tr>
                            <td>{{ $policy->policyTitle }}</td>
                            <td>{{ $policy->policyName }}</td>
                            <td>{{ $policy->policyDescription }}</td>
                            <td>
                                @if ($policy->policyUpload)
                                <a href="{{ asset('storage/' . $policy->policyUpload) }}" target="_blank">
                                    View Document
                                </a>
                                @else
                                No document uploaded
                                @endif
                            </td>
                            <td>{{ $policy->created_at }}</td>
                            <td>{{ $policy->uploader->fName }} {{ $policy->uploader->lName }}</td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#"><i class="fa fa-download m-r-5"></i>
                                            Download</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#edit_policy"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                            data-target="#delete_policy"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
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

<!-- Add Policy Modal -->
<div id="add_policy" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Policy</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.policy.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Policy Title <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="policyTitle" required>
                    </div>
                    <div class="form-group">
                        <label>Policy Name <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="policyName" required>
                    </div>
                    <div class="form-group">
                        <label>Description <span class="text-danger">*</span></label>
                        <textarea name="policyDescription" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Upload Policy</span></label>
                        <div class="custom-file">
                            <input type="file" name="policyUpload" accept=".pdf,.doc,.docx,.ppt,.pptx"
                                class="custom-file-input" id="policy_upload">
                            <label class="custom-file-label" for="policy_upload">Choose file</label>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Policy Modal -->

<!-- Edit Policy Modal -->
<div id="edit_policy" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Policy</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label>Policy Name <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" value="Leave Policy">
                    </div>
                    <div class="form-group">
                        <label>Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Department</label>
                        <select class="select">
                            <option>All Departments</option>
                            <option>Web Development</option>
                            <option>Marketing</option>
                            <option>IT Management</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Upload Policy <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="edit_policy_upload">
                            <label class="custom-file-label" for="edit_policy_upload">Choose file</label>
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
<!-- /Edit Policy Modal -->

<!-- Delete Policy Modal -->
<div class="modal custom-modal fade" id="delete_policy" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Policy</h3>
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
<!-- /Delete Policy Modal -->

@endsection

@section('scripts')



@endsection
