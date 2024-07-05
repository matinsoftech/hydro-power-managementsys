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
                        <h4 class="card-title">Holidays List
                            <a href="{{ route('admin.holiday.create') }}" class="btn btn-sm btn-primary float-right">Add Holiday</a>
                        </h4>
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
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: true,
                eventDrop: function(event, delta, revertFunc) {
                    updateHoliday(event , revertFunc);
                },
                eventResize: function(event, delta, revertFunc) {
                    updateHoliday(event, revertFunc);
                },
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

            function updateHoliday(event, revertFunc) {
                $.ajax({
                    url: '{{ asset("admin/holiday") }}/' + event.id, // Ensure you have the correct URL structure
                    method: 'PATCH',
                    data: {
                        start_date: event.start.format(),
                        end_date: event.end ? event.end.format() : event.start.format(),
                        _token: '{{ csrf_token() }}' // Include CSRF token
                    },
                    success: function(response) {
                        toastr['success']('Holiday updated successfully.');
                    },
                    error: function() {
                        toastr['error']('An error occurred while updating the holiday.');
                        revertFunc(); // Revert the event to its original position
                    }
                });
            }
        });
    </script>
@endsection
