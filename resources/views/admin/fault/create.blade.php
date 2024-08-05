@extends('layouts.app')

@section('title', 'Create Fault')

@section('content')

    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Create Fault
                            <a href="{{ route('admin.fault.index') }}" class="btn btn-sm btn-primary float-right">Back</a>
                        </h4>
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
                                <form  action="{{ route('admin.fault.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label>Fault Time</label>
                                            <input type="time" name="fault_time" class="form-control" required />
                                            <span id="error_fault_time"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Reason</label>
                                            <input type="text" name="reason" class="form-control" required />
                                            <span id="error_reason"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Photo</label>
                                            <input type="file" name="photo" class="form-control" required/>
                                            <span id="error_photo"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Video</label>
                                            <input type="file" name="video" class="form-control" required/>
                                            <span id="error_video"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="">Select Status</option>
                                                <option value="Solved">Solved</option>
                                                <option value="Unsolved">Unsolved</option>
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
                                        <div class="col-sm-6">
                                            <label>Found By</label>
                                            <select name="found_by" class="form-control" required>
                                                <option value="">Select Solved By</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            <span id="error_solved_by"></span>
                                        </div>
                                        <div class="col-sm-12">
                                            {{-- <button class="btn btn-sm btn-primary float-right p-10 mt-10">Create</button> --}}
                                            <input type="submit" value="Create" class="btn btn-sm btn-primary float-right p-10 mt-10">
                                        </form>
                                        </div>
                                    </div>

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
    <script>
        $('#addData').on('submit', function(e) {
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
    </script>
@endsection
