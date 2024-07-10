@extends('layouts.app')

@section('title', 'Meter Reading Entry')

@section('content')

    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Meter Reading Entry ( {{ date('Y-m-d') }} )
                            <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-primary float-right">Back</a>
                        </h4>
                        <div class="card card-primary">
                            <div class="card-body">
                                <form id="addData" action="{{ route('admin.meter-reading.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Time</th>
                                                        <th>Main Meter</th>
                                                        <th>Show Meter</th>
                                                        <th>Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($i = 0; $i <= 23; $i++)
                                                        @php
                                                            $time = sprintf('%02d:00:00', $i);
                                                            $reading = $meterReadings->get($time);
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                @if(\Carbon\Carbon::parse($time)->addHour()->format('H:i') == '00:00')
                                                                    24:00
                                                                @else
                                                                {{ \Carbon\Carbon::parse($time)->addHour()->format('H:i') }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="time[{{ $i }}]"
                                                                    value="{{ $time }}">
                                                                <input type="number" name="main_meter[{{ $i }}]"
                                                                    class="form-control"
                                                                    value="{{ $reading->main_meter ?? '' }}">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="show_meter[{{ $i }}]"
                                                                    class="form-control"
                                                                    value="{{ $reading->show_meter ?? '' }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="remarks[{{ $i }}]"
                                                                    class="form-control"
                                                                    value="{{ $reading->remarks ?? '' }}">
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-sm-12">
                                            <button class="btn btn-sm btn-primary float-right p-10 mt-10">Save</button>
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
