@extends('layouts.app')

@section('title', 'Meter Reading List')

@section('content')
<style>
    .container {
        margin-top: 30px;
    }
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .card-header {
        background-color: #007bff;
        color: white;
        padding: 20px;
        font-size: 1.5em;
        border-bottom: 1px solid #0056b3;
        text-align: center;
    }
    .card-body {
        padding: 30px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-control-file {
        border-radius: 5px;
    }
    .btn-custom {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        font-size: 1em;
        transition: background-color 0.3s ease;
        margin: 5px;
    }
    .btn-custom:hover {
        background-color: #0056b3;
    }
    .btn-container {
        display: flex;
        justify-content: flex-end; /* Align buttons to the right */
    }
    .btn-container button {
        margin-left: 10px; /* Space between buttons */
    }
    table {
        width: 100%;
        margin-bottom: 1rem;
        background-color: transparent;
    }
    thead th {
        background-color: #f8f9fa;
    }
</style>

<div class="container">
@if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if(session('deleted'))
        <div class="alert alert-success">
            {{ session('deleted') }}
        </div>
    @endif
    @if(session('deleted'))
        <div class="alert alert-success">
            {{ session('updated') }}
        </div>
    @endif
    <div class="card">
        <div class="card-header">
       
            <h4>CSV Import/Export</h4>
        </div>
        <div class="card-body">
            <!-- Import Form -->
            <form id="importForm" action="{{ route('admin.import_file') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="importFile">Import CSV File:</label>
                    <input type="file" class="form-control-file" id="importFile" name="import_file" required>
                </div>
                <div class="btn-container">
                    <input type="submit" value="Import Data" class="btn btn-custom">
                    <a href="{{ route('admin.export_file') }}" class="btn btn-custom">Export Data</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{--<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <!-- Date Filter Form -->
            <form action="{{ route('admin.import_export') }}" method="GET">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="startDate">Start Date:</label>
                            <input type="date" class="form-control" id="startDate" name="start_date" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="endDate">End Date:</label>
                            <input type="date" class="form-control" id="endDate" name="end_date" value="{{ request('end_date') }}">
                        </div>
                    </div>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn btn-custom">Filter</button>
                </div>
            </form>
        </div>
    </div>
</div> 
--}}

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4 >Meter Reading List</h4>
        </div>
        <!-- filter -->
        <div class="card">
        <div class="card-body">
            <!-- Date Filter Form -->
            <form action="{{ route('admin.import_export') }}" method="GET">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="startDate">Start Date:</label>
                            <input type="date" class="form-control" id="startDate" name="start_date" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="endDate">End Date:</label>
                            <input type="date" class="form-control" id="endDate" name="end_date" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                    <div class="btn-container">
                        <button type="submit" class="btn btn-custom w-100">Filter</button>
                    </div>
                    </div>
                  
                </div>
            </form>
        </div>
    </div>
        <!-- filter -->
        <div class="card-body">
            @if($datas->isEmpty())
                <p>No data available for the selected date range.</p>
            @else
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Main Meter</th>
                            <th>Check Meter</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $data)
                            <tr>
                            <td scope="row">{{ $loop->iteration + ($datas->currentPage() - 1) * $datas->perPage() }}</td>
                                <td>{{ $data->Date }}</td>
                                <td>{{ $data->Time_or_Hour }}</td>
                                <td>{{ $data->MainMeterReading_kWH }}</td>
                                <td>{{ $data->CheckMeterReading_kWH }}</td>
                                <td>
                                   <a href="{{ route('admin.view', $data->id) }}" class="btn btn-info btn-sm">View</a>
                                   <a href="{{ route('admin.edit', $data->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                   <a href="{{ route('admin.destroy', $data->id) }}" class="btn btn-danger btn-sm">Delete</a>
                                   
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
             <!-- Pagination Links -->
             <div class="mt-3">
                {{ $datas->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
