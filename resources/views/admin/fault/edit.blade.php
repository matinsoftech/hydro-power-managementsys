@extends('layouts.app')

@section('title', 'Update Fault')

@section('content')
<head>
    <style>
        .media-container {
            margin-bottom: 15px;
        }
        .media-display {
            max-width: 100%;
            height: auto;
        }
        .form-control {
            margin-bottom: 15px;
        }
    </style>
</head>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Update Fault
                        <a href="{{ route('admin.fault.index') }}" class="btn btn-sm btn-primary float-right">Back</a>
                    </h4>

                    <!-- Success and error messages -->
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @elseif (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="card card-primary">
                        <div class="card-body">
                            <form id="addData" action="{{ route('admin.fault.update', $fault->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>Fault Time</label>
                                        <input type="time" name="fault_time" class="form-control" value="{{ old('fault_time', $fault->fault_time) }}" />
                                        <span id="error_fault_time"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Reason</label>
                                        <input type="text" name="reason" class="form-control" value="{{ old('reason', $fault->reason) }}" />
                                        <span id="error_reason"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Photo</label>
                                        @if($fault->photo)
                                            <div class="media-container">
                                                <img src="{{ asset('FaultPhoto/' . $fault->photo) }}" alt="Current Photo" class="media-display" />
                                            </div>
                                        @endif
                                        <input type="file" name="photo" class="form-control" />
                                        <span id="error_photo"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Video</label>
                                        @if($fault->video)
                                            <div class="media-container">
                                                <video controls class="media-display">
                                                    <source src="{{ asset('FaultVideo/' . $fault->video) }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                        @endif
                                        <input type="file" name="video" class="form-control" />
                                        <span id="error_video"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="Solved" {{ old('status', $fault->status) == 'Solved' ? 'selected' : '' }}>Solved</option>
                                            <option value="Unsolved" {{ old('status', $fault->status) == 'Unsolved' ? 'selected' : '' }}>Unsolved</option>
                                        </select>
                                        <span id="error_status"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Solved By</label>
                                        <select name="solved_by" class="form-control" required>
                                            <option value="">Select Solved By</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        <span id="error_solved_by"></span>
                                    </div>
                                    <div class="col-sm-12">
                                        <button class="btn btn-sm btn-primary float-right p-10 mt-10">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusSelect = document.querySelector('select[name="status"]');
        const solvedBySelect = document.querySelector('select[name="solved_by"]');

        // Function to toggle enabled/disabled based on status
        function toggleSolvedBy() {
            if (statusSelect.value === 'Solved') {
                solvedBySelect.disabled = false;
            } else {
                solvedBySelect.disabled = true;
            }
        }

        // Initial check
        toggleSolvedBy();

        // Add event listener to status select element
        statusSelect.addEventListener('change', toggleSolvedBy);
    });
</script>

@endsection
