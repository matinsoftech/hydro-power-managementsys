@extends('layouts.app')

@section('title', 'Edit User')

@section('content')

    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit User
                            <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-primary float-right">Back</a>
                        </h4>
                        <div class="card card-primary">
                            <div class="card-body">
                                <form id="addData" action="{{ route('admin.user.update', $user->id) }}" method="POST">
                                    @csrf

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" />
                                            <span id="error_name"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Email Address</label>
                                            <input type="text" name="email" class="form-control" value="{{ $user->email }}" />
                                            <span id="error_email"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Password</label>
                                            <input type="password" name="password" class="form-control" />
                                            <span id="error_password"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Confirm Password</label>
                                            <input type="password" name="password_confirmation" class="form-control" />
                                            <span id="error_password_confirmation"></span>
                                        </div>
                                        <div class="col-sm-12">
                                            <button class="btn btn-sm btn-primary float-right p-10 mt-10">Update</button>
                                        </div>
                                    </div>
                                </form>
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
        $('#addData').on('submit', function(e) {
            e.preventDefault();
            let url = $(this).attr('action');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    console.log('ajax fired');
                },
                success: function(data) {
                    if (data.status == true) {
                        toastr['success'](data.message);
                        window.location.href = data.url;
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
                            '<span class="help-block ' + error + '">*' + xhr
                            .responseJSON.errors[
                                error] + '</span>');
                        i++;
                    }
                }
            });
        });
    </script>
@endsection
