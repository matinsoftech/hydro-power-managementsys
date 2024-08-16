@extends('layouts.app')

@section('title', 'Faults')

@section('content')

    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            Fault List
                            <a href="{{ route('admin.fault.create') }}" class="btn btn-sm btn-primary float-right">Add Fault</a>
                        </h4>
                               <!-- Date Filter -->
                               <br>
                    <div class="form-group">
                        <label for="start_date">Start Date:</label>
                        <input type="date" id="start_date" class="form-control">
                        <label for="end_date">End Date:</label>
                        <input type="date" id="end_date" class="form-control">
                        <button id="filter" class="btn btn-primary mt-2">Filter</button>
                    </div>
                    <br>
                        <div class="table-responsive">
                            <table id="myTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fault Time</th>
                                        <th>Reason</th>
                                        <th>Photo</th>
                                        <th>Video</th>
                                        <th>Status</th>
                                        <th>Solved By</th>
                                        <th>Found By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="deleteModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="deleteForm" action="#" method="POST">
                @method('DELETE')
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Are you sure you want to delete this item?</h5>
                        <button type="button" class="close btn btn-danger" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        var table = $("#myTable").DataTable({
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
                url: "{{ route('admin.fault.index') }}",
                data: function(d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [{
                    name: "DT_RowIndex",
                    data: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    name: "fault_time",
                    data: "fault_time"
                },
                {
                    name: "reason",
                    data: "reason"
                },
                {
                    name: "photo",
                    data: "photo"
                },
                {
                    name: "video",
                    data: "video"
                },
                {
                    name: "status",
                    data: "status"
                },
                {
                    name: "solved_by",
                    data: "solved_by"
                },
                {
                    name: "found_by",
                    data: "found_by"
                },
                {
                    name: "action",
                    data: "action"
                },
            ]
        });

        // Apply the filter on button click
        $('#filter').click(function() {
            table.ajax.reload(); // Reload data with new filters
        });

        // Optionally, reset the filter
        $('#reset').click(function() {
            $('#start_date').val('');
            $('#end_date').val('');
            table.ajax.reload(); // Reload data without filters
        });
    });
</script>
@endsection
