@extends('layouts.app')

@section('title', 'Meter Reading Detail By Date')

@section('content')

    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Meter Reading Detail By Date </h4>

                        <form action="{{ route('admin.meter-reading.detail') }}" method="GET">
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="date" name="date" id="date" class="form-control" value="{{ isset($date) ? $date : '' }}">
                                </div>
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </form>

                    </div>

                    @if(isset($date))
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Main Meter</th>
                                        <th>Show Meter</th>
                                        <th>Accuracy</th> <!-- Added column -->
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
                                                {{ $reading->main_meter ?? '' }}
                                            </td>
                                            <td>
                                                {{ $reading->show_meter ?? '' }}
                                            </td>
                                            <td>
                                                {{ $reading->accuracy ?? '' }} <!-- Added column -->
                                            </td>
                                            <td>
                                                {{ $reading->remarks ?? '' }}
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var myTable = $("#myTable").DataTable({
            serverSide: true,
            processing: true,
            "language": {
                "paginate": {
                    "previous": "<",
                    "next": ">"
                },
                "search": "", // Remove search box
                "searchPlaceholder": "Search...", // Optional placeholder text
                "lengthMenu": "_MENU_",
            },
            lengthMenu: [
                [10, 25, 50, 100, 250, 500],
                [10, 25, 50, 100, 250, 500]
            ],
            ajax: {
                url: "{{ route('admin.meter-reading.index') }}",
            },
            columns: [
                {
                    name: "DT_RowIndex",
                    data: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    name: "date",
                    data: "date"
                },
                {
                    name: "time",
                    data: "time"
                },
                {
                    name: "main_meter",
                    data: "main_meter"
                },
                {
                    name: "show_meter",
                    data: "show_meter"
                },
                {
                    name: "accuracy", // Added column
                    data: "accuracy"
                },
                {
                    name: "remarks",
                    data: "remarks"
                },
                {
                    name: "created_by",
                    data: "created_by"
                },
                {
                    name: "action",
                    data: "action"
                },
            ]
        });
    </script>
@endsection
