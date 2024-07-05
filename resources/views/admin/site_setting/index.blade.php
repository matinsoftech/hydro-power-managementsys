@extends('layouts.app')

@section('title', 'Site Setting')

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Site Setup</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="index.html" class="text-muted">Apps</a></li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">Site Setup</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-5 align-self-center">
                <div class="customize-input float-end">
                    <span class="text-primary">
                        {{ now()->format('jS M, Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <!-- basic table -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            @include('admin.site_setting.partials.setting_nav')
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.site-setting.update', $siteSetting->id) }}" id="addData"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <div class="row mt-2">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-4 text-center">
                                    <div class="image-div">
                                        <label for="logo">
                                            @if ($siteSetting)
                                                <img id="avatarPreview" src="{{ asset($siteSetting->logo) }}"
                                                    class="site-logo" alt="profile">
                                            @else
                                                <img id="avatarPreview" src="{{ asset('assets/images/avatar.svg') }}"
                                                    class="site-logo" alt="profile">
                                            @endif
                                        </label>
                                        <input type="file" name="logo" id="logo" class="d-none">
                                    </div>
                                    <span class="text-info">Click on image to change picture...</span>
                                    <span id="error_logo" class="error-message"></span>
                                </div>
                                <div class="col-sm-4"></div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="name" class="text-label">Name</label>
                                        <input type="text" class="form-control form-bottom-primary" id="name"
                                            name="name" value="{{ $siteSetting->name }}">
                                        <span id="error_name" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="email" class="text-label">Email</label>
                                        <input type="email" class="form-control form-bottom-primary" id="email"
                                            name="email" value="{{ $siteSetting->email }}">
                                        <span id="error_email" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="phone" class="text-label">Phone</label>
                                        <input type="text" class="form-control form-bottom-primary" id="phone"
                                            name="phone" value="{{ $siteSetting->phone }}">
                                        <span id="error_phone" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="address" class="text-label">Address</label>
                                        <input type="text" class="form-control form-bottom-primary" id="address"
                                            name="address" value="{{ $siteSetting->address }}">
                                        <span id="error_address" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="description" class="text-label">Description</label>
                                        <input type="text" class="form-control form-bottom-primary" id="description"
                                            name="description" value="{{ $siteSetting->description }}">
                                        <span id="error_description" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="keywords" class="text-label">Keywords</label>
                                        <input type="text" class="form-control form-bottom-primary" id="keywords"
                                            name="keywords" value="{{ $siteSetting->keywords }}">
                                        <span id="error_keywords" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="unit_price" class="text-label">Unit Price</label>
                                        <input type="text" class="form-control form-bottom-primary" id="unit_price"
                                            name="unit_price" value="{{ $siteSetting->unit_price }}">
                                        <span id="error_unit_price" class="error-message"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary float-right">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('#addData').on('submit', function(e) {
                e.preventDefault();
                let url = $(this).attr('action');
                // Serialize the form data
                let formData = new FormData(this);

                // Check if the password and confirmPassword fields are empty
                if (formData.get('password') === '' && formData.get('confirm_password') === '') {
                    // Remove the password and confirmPassword fields from the FormData object
                    formData.delete('password');
                    formData.delete('confirmPassword');
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        console.log('ajax fired');
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr['success'](data.message);
                        } else {
                            toastr['error'](data.message);
                        }
                    },
                    error: function(xhr) {
                        var i = 0;
                        $('.help-block').remove();
                        $('.has-error').removeClass('has-error');
                        for (var error in xhr.responseJSON.errors) {
                            $('#add_' + error).removeClass('has-error');
                            $('#add_' + error).addClass('has-error');
                            $('#error_' + error).html(
                                '<span class="text-red">*' + xhr
                                .responseJSON.errors[
                                    error] + '</span>');
                            i++;
                        }
                    }
                });
            });

            $('#logo').on('change', function(e) {
                var input = e.target;
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#avatarPreview').attr('src', e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });
        });
    </script>
@endsection
