@extends('layouts.app')

@section('title', 'Grid List')

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
                            Grid List
                            <a href="{{ route('admin.grid.create') }}" class="btn btn-sm btn-primary float-right">Add Grid</a>
                        </h4>
                             {{-- start filter --}}
                             <br>
                             <div class="row">
                                 <div class="col-md-4">
                                     <label for="startDate">Start Date</label>
                                     <input type="date" id="startDate" class="form-control">
                                 </div>
                                 <div class="col-md-4">
                                     <label for="endDate">End Date</label>
                                     <input type="date" id="endDate" class="form-control">
                                 </div>
                                 <div class="col-md-4">
                                     <button id="filterBtn" class="btn btn-primary mt-4">Filter</button>
                                 </div>
                             </div>
                             <br>
                             {{-- end filter --}}
                        <div class="table-responsive">
                            <table id="myTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Gone Time</th>
                                        <th>Charge Time</th>
                                        <th>Unit One Sync Time</th>
                                        <th>Unit Two Sync Time</th>
                                        <th>Entry By</th>
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
    var myTable = $("#myTable").DataTable({
        serverSide: true,
        processing: true,
        language: {
            paginate: {
                previous: "<",
                next: ">"
            },
            search: "", // Remove search box
            searchPlaceholder: "Search...", // Optional placeholder text
            lengthMenu: "_MENU_",
        },
        lengthMenu: [
            [10, 25, 50, 100, 250, 500],
            [10, 25, 50, 100, 250, 500]
        ],
        ajax: {
            url: "{{ route('admin.grid.index') }}",
            data: function(d) {
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
            },
            error: function(xhr, error, thrown) {
                console.error('Ajax error:', error, thrown);
                console.log(xhr.responseText);
            }
        },
        columns: [
            { name: "DT_RowIndex", data: "DT_RowIndex", orderable: false, searchable: false },
            { name: "date", data: "date" },
            { name: "gone_time", data: "gone_time" },
            { name: "charge_time", data: "charge_time" },
            { name: "unit_one_sync_time", data: "unit_one_sync_time" },
            { name: "unit_two_sync_time", data: "unit_two_sync_time" },
            { name: "created_by", data: "created_by" },
            { name: "action", data: "action" },
        ]
    });

    $('#filterBtn').on('click', function() {
        myTable.draw();
    });

    $('#deleteForm').on('submit', function(e) {
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

    $(document).on('click', '.btnDelete', function() {
        let url = $(this).data('url');
        $('#deleteForm').attr('action', url);
        $('#deleteModal').modal('show');
    });
</script>

@endsection
