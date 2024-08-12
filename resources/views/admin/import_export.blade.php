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
</style>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Excel Import/Export</h4>
        </div>
        <div class="card-body">
            <!-- Import Form -->
            <form id="importForm" action="{{ route('admin.import_file') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="importFile">Import Excel File:</label>
                    <input type="file" class="form-control-file" id="importFile" name="import_file" accept=".xlsx, .xls">
                </div>
                <div class="btn-container">
                    <input type="submit" value="Import Data" class="btn btn-custom">
                   <a href="{{ route('admin.export_file') }}" class="btn btn-custom">Export Data</a>

            </form>
        </div>
    </div>
</div>
@endsection
