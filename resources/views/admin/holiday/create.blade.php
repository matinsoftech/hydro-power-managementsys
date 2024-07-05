@extends('layouts.app')

@section('title', 'Create Holiday')

@section('content')

    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Create Holiday
                            <a href="{{ route('admin.holiday.index') }}" class="btn btn-sm btn-primary float-right">Back</a>
                        </h4>
                        <div class="card card-primary">
                            <div class="card-body">
                                <form id="addData" action="{{ route('admin.holiday.store') }}" method="POST">
                                    @csrf

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label>Title</label>
                                            <input type="text" name="title" class="form-control" />
                                            <span id="error_title"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Description</label>
                                            <input type="text" name="description" class="form-control" />
                                            <span id="error_description"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Start Date</label>
                                            <input type="date" name="start_date" class="form-control" />
                                            <span id="error_start_date"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>End Date</label>
                                            <input type="date" name="end_date" class="form-control" />
                                            <span id="error_end_date"></span>
                                        </div>
                                        <div class="col-sm-12">
                                            <button class="btn btn-sm btn-primary float-right p-10 mt-10">Create</button>
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
