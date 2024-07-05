@extends('layouts.app')

@section('title', 'Users')

@section('content')

    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Users List
                            <a href="{{ route('admin.user.create') }}" class="btn btn-sm btn-primary float-right">Add User</a>
                        </h4>
                        <div class="table-responsive">
                            <table id="myTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
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
                url: "{{ route('admin.user.index') }}",
            },
            columns: [{
                    name: "DT_RowIndex",
                    data: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    name: "name",
                    data: "name"
                },
                {
                    name: "email",
                    data: "email"
                },
                {
                    name: "action",
                    data: "action"
                },
            ]
        });
    </script>
@endsection
