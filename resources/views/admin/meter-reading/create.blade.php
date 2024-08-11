@extends('layouts.app')

@section('title', 'Meter Reading Entry')
@section('content')

    <style>
        .table>:not(:last-child)>:last-child>*{
            min-width: 150px;
        }
    </style>

    <div class="container-fluid p-4">

        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body" style="overflow: scroll">
                        <h4 class="card-title d-flex align-items-center justify-content-between">Meter Reading Entry ( {{ date('Y-m-d') }} )
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
                                                        <th>Check Meter</th>
                                                        <th>Accuracy</th>
                                                        <th>Difference</th>
                                                        <th>Percentage Difference</th>
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
                                                                <input type="hidden" name="time[{{ $i }}]" value="{{ $time }}">
                                                                <input type="number" name="main_meter[{{ $i }}]" class="form-control main-meter" id="main_meter_{{ $i }}" value="{{ $reading->main_meter ?? '' }}">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="show_meter[{{ $i }}]" class="form-control show-meter" id="show_meter_{{ $i }}" value="{{ $reading->show_meter ?? '' }}">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="accuracy[{{ $i }}]" class="form-control" id="accuracy{{ $i }}" step="0.01" value="{{ $reading->accuracy ?? '' }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="difference[{{ $i }}]" class="form-control" id="difference_{{ $i }}" value="{{ isset($reading->main_meter) && isset($reading->show_meter) ? $reading->main_meter - $reading->show_meter : '' }}" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="percentage_difference[{{ $i }}]" class="form-control" id="percentage_difference_{{ $i }}" value="{{ isset($reading->main_meter) && $reading->main_meter != 0 ? number_format((($reading->main_meter - $reading->show_meter) * 100) / $reading->main_meter, 2) : '' }}" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="remarks[{{ $i }}]" class="form-control" value="{{ $reading->remarks ?? '' }}">
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
                    if (data.status === true) {
                        toastr['success'](data.message);
                        window.location.href = data.url;
                    } else {
                        toastr['error'](data.message);
                    }
                },
                error: function(xhr) {
                    $('.help-block').remove();
                    $('.has-error').removeClass('has-error');
                    for (var error in xhr.responseJSON.errors) {
                        $('#add_' + error).addClass('has-error');
                        $('#error_' + error).html(
                            '<span class="help-block">*' + xhr.responseJSON.errors[error] + '</span>'
                        );
                    }
                }
            });
        });

        $('.main-meter, .show-meter').on('input', function() {
            var index = $(this).attr('name').match(/\d+/)[0];
            var mainMeter = parseFloat($('#main_meter_' + index).val()) || 0;
            var checkMeter = parseFloat($('#show_meter_' + index).val()) || 0;

            var difference = mainMeter - checkMeter;
            var percentageDifference = mainMeter !== 0 ? ((difference * 100) / mainMeter) : 0;

            $('#difference_' + index).val(difference);
            $('#percentage_difference_' + index).val(percentageDifference.toFixed(2));
        });
    </script>
@endsection
