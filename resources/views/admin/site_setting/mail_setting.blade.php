@extends('layouts.app')

@section('title', 'Mail Setting')

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Mail Setup</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="index.html" class="text-muted">Apps</a></li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">Mail Setup</li>
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
                        <form action="{{ route('admin.mail-setting.update',$mailSetting->id) }}" id="addData" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                        <div class="row mt-2">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="driver" class="text-label">Driver</label>
                                    <input type="text" class="form-control form-bottom-primary" id="driver" name="driver" value="{{ $mailSetting->driver }}">
                                    <span id="error_driver" class="error-message"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="host" class="text-label">Host</label>
                                    <input type="text" class="form-control form-bottom-primary" id="host" name="host" value="{{ $mailSetting->host }}">
                                    <span id="error_host" class="error-message"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="port" class="text-label">Port</label>
                                    <input type="port" class="form-control form-bottom-primary" id="port" name="port" value="{{ $mailSetting->port }}">
                                    <span id="error_port" class="error-message"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="encryption" class="text-label">Encryption</label>
                                    <input type="text" class="form-control form-bottom-primary" id="encryption" name="encryption" value="{{ $mailSetting->encryption }}">
                                    <span id="error_encryption" class="error-message"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="username" class="text-label">User Name</label>
                                    <input type="text" class="form-control form-bottom-primary" id="username" name="username"
                                        value="{{ $mailSetting->username }}">
                                    <span id="error_username" class="error-message"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="password" class="text-label">Password</label>
                                    <input type="text" class="form-control form-bottom-primary" id="password" name="password"
                                        value="{{ $mailSetting->password }}">
                                    <span id="error_password" class="error-message"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="from_address" class="text-label">From Address</label>
                                    <input type="text" class="form-control form-bottom-primary" id="from_address" name="from_address"
                                        value="{{ $mailSetting->from_address }}">
                                    <span id="error_from_address" class="error-message"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="from_name" class="text-label">From Name</label>
                                    <input type="text" class="form-control form-bottom-primary" id="from_name" name="from_name"
                                        value="{{ $mailSetting->from_name }}">
                                    <span id="error_from_name" class="error-message"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="timeout" class="text-label">Time Out</label>
                                    <input type="text" class="form-control form-bottom-primary" id="timeout" name="timeout"
                                        value="{{ $mailSetting->timeout }}">
                                    <span id="error_timeout" class="error-message"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="local_domain" class="text-label">Local Domain</label>
                                    <input type="text" class="form-control form-bottom-primary" id="local_domain" name="local_domain"
                                        value="{{ $mailSetting->local_domain }}">
                                    <span id="error_local_domain" class="error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary float-right">Submit</button>
                            </div>
                        </div>
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

            $('#logo').on('change', function (e) {
                var input = e.target;
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#avatarPreview').attr('src', e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });
        });
    </script>
@endsection
