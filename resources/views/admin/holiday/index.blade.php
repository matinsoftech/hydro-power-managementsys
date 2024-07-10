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

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Holiday</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this holiday?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
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
                },
                eventRender: function(event, element) {
                    element.find('.fc-title').append('<span class="delete-icon" style="cursor:pointer; float:right;">&times;</span>');
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

        $(document).on('click', '.delete-icon', function() {
            // Get the event data
            eventToDelete = $('#calendar').fullCalendar('clientEvents', $(this).closest('.fc-event').data('event-id'))[0];
            // Open the modal
            $('#deleteModal').modal('show');
        });

        $('#confirmDelete').on('click', function() {
            // Perform the delete action via AJAX
            $.ajax({
                url: '/admin/holiday/' + eventToDelete.id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Close the modal
                    $('#deleteModal').modal('hide');
                    // Remove the event from the calendar
                    $('#calendar').fullCalendar('removeEvents', eventToDelete.id);
                },
                error: function(xhr, status, error) {
                    alert('There was an error deleting the event.');
                }
            });
        });
    </script>
@endsection
