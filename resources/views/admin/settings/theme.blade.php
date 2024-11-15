@extends('layouts.settings') @section('title', 'One JAF')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')


<!-- Page Content -->
<div class="content container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Theme Settings</h3>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <form action="{{ route('theme.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Website Name</label>
                    <div class="col-lg-9">
                        <input name="webName" class="form-control" value="{{ $theme ? $theme->webName : '' }}"
                            type="text">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Payslip Logo</label>
                    <div class="col-lg-7">
                        <input type="file" class="form-control" name="logo" id="logo" required>
                        <span class="form-text text-muted">Recommended image size is 150px x 150px</span>
                    </div>
                    <div class="col-lg-2">
                        <div class="img-thumbnail float-right"><img alt="" width="960" id="previewImage" height="540">
                        </div>
                    </div>
                </div>

                <div class="submit-section">
                    <button type="submit" class="btn btn-primary submit-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Page Content -->

@endsection

@section('scripts')

<script>
    document.getElementById("logo").addEventListener("change", function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                document.getElementById("previewImage").setAttribute("src", event.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

</script>

<script>
    document.getElementById("favicon").addEventListener("change", function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                document.getElementById("previewImage2").setAttribute("src", event.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

</script>
@endsection
