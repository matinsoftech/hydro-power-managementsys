@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Profile Setup</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="index.html" class="text-muted">Apps</a></li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">Profile Setup</li>
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
                        <form action="{{ route('update.profile') }}" id="addData" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <div class="row mt-2">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-4 text-center">
                                    <div class="image-div">
                                        <label for="avatar">
                                            @if (auth()->user()->avatar)
                                                <img id="avatarPreview" src="{{ asset(auth()->user()->avatar) }}"
                                                    class="rounded-circle avatar" alt="profile">
                                            @else
                                                <img id="avatarPreview" src="{{ asset('assets/images/avatar.svg') }}"
                                                    class="rounded-circle avatar" alt="profile">
                                            @endif
                                        </label>
                                        <input type="file" name="avatar" id="avatar" class="d-none">
                                    </div>
                                    <span class="text-info">Click on image to change picture...</span>
                                    <span id="error_avatar" class="error-message"></span>
                                </div>
                                <div class="col-sm-4"></div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name" class="text-label">Name</label>
                                        <input type="text" class="form-control form-bottom-primary" id="name"
                                            name="name" value="{{ auth()->user()->name }}">
                                        <span id="error_name" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email" class="text-label">Email</label>
                                        <input type="email" class="form-control form-bottom-primary" id="email"
                                            name="email" value="{{ auth()->user()->email }}">
                                        <span id="error_email" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="password" class="text-label">Password</label>
                                        <input type="password" class="form-control form-bottom-primary" id="password"
                                            name="password" />
                                        <span id="error_password" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="confirm_password" class="text-label">Confirm Password</label>
                                        <input type="password" class="form-control form-bottom-primary"
                                            id="confirm_password" name="confirm_password" />
                                        <span id="error_confirm_password" class="error-message"></span>
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

            $('#avatar').on('change', function(e) {
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
