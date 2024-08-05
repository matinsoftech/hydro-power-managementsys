@extends('layouts.app')

@section('title', 'Meter Reading List')

@section('content')

    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Meter Reading List
                            <a href="{{ route('admin.meter-reading.create') }}" class="btn btn-sm btn-primary float-right">Add Meter Reading</a>
                        </h4>
                        <div class="table-responsive">
                            <table id="myTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Main Meter</th>
                                        <th>Check Meter</th>
                                        <th>Remarks</th>
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
            columns: [{
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

        $(document).on('click','.btnDelete',function(){
            let url = $(this).data('url');
            $('#deleteForm').attr('action',url);
            $('#deleteModal').modal('show');
        });
    </script>
@endsection
