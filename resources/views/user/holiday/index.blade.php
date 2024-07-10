@extends('layouts.app')

@section('title', 'Holidays')
@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.css') }}"/>
@endsection
@section('content')

    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-body b-l calender-sidebar">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ asset('assets/libs/moment/min/moment.min.js') }}" ></script>
    <script src="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.js') }}" ></script>
    <script>
        $(function(){
            let eventToDelete;

            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: false,
                events: function(start, end, timezone, callback) {
                    $.ajax({
                        url: '{{ route("admin.holiday.index") }}',
                        dataType: 'json',
                        data: {
                            // Send the start and end dates for the currently viewed calendar range
                            start: start.format(),
                            end: end.format()
                        },
                        success: function(data) {
                            callback(data);
                        }
                    });
                }
            });
        });

    </script>
@endsection
