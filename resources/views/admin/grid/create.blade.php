@extends('layouts.app')

@section('title', 'Create Grid')

@section('content')

    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Create Grid
                            <a href="{{ route('admin.grid.index') }}" class="btn btn-sm btn-primary float-right">Back</a>
                        </h4>
                        <div class="card card-primary">
                            <div class="card-body">
                                <form id="addData" action="{{ route('admin.grid.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label>Gone Time</label>
                                            <input type="time" name="gone_time" class="form-control" />
                                            <span id="error_gone_time"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Charge Time</label>
                                            <input type="time" name="charge_time" class="form-control" />
                                            <span id="error_charge_time"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Unit 1 Sync Time</label>
                                            <input type="time" name="unit_one_sync_time" class="form-control" />
                                            <span id="error_unit_one_sync_time"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Unit 2 Sync Time</label>
                                            <input type="time" name="unit_two_sync_time" class="form-control" />
                                            <span id="error_unit_two_sync_time"></span>
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
