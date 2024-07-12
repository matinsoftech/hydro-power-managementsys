@extends('layouts.app')

@section('title', 'Site Setting')

@section('styles')
    <style>
        .main_div {
            position: relative;
        }

        #map {
            height: 100%;
            width: 100%;
        }

        #pac-input {
            margin-top: 10px;
            width: 300px;
            height: 30px;
            padding: 5px;
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Site Setup</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="index.html" class="text-muted">Apps</a></li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">Site Setup</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-5 align-self-center">
                <div class="customize-input float-end">
                    <span class="text-primary">
                        {{ now()->format('jS M, Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <!-- basic table -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            @include('admin.site_setting.partials.setting_nav')
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.site-setting.update', $siteSetting->id) }}" id="addData"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <div class="row mt-2">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-4 text-center">
                                    <div class="image-div">
                                        <label for="logo">
                                            @if ($siteSetting)
                                                <img id="avatarPreview" src="{{ asset($siteSetting->logo) }}"
                                                    class="site-logo" alt="profile">
                                            @else
                                                <img id="avatarPreview" src="{{ asset('assets/images/avatar.svg') }}"
                                                    class="site-logo" alt="profile">
                                            @endif
                                        </label>
                                        <input type="file" name="logo" id="logo" class="d-none">
                                    </div>
                                    <span class="text-info">Click on image to change picture...</span>
                                    <span id="error_logo" class="error-message"></span>
                                </div>
                                <div class="col-sm-4"></div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="name" class="text-label">Name</label>
                                        <input type="text" class="form-control form-bottom-primary" id="name"
                                            name="name" value="{{ $siteSetting->name }}">
                                        <span id="error_name" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="email" class="text-label">Email</label>
                                        <input type="email" class="form-control form-bottom-primary" id="email"
                                            name="email" value="{{ $siteSetting->email }}">
                                        <span id="error_email" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="phone" class="text-label">Phone</label>
                                        <input type="text" class="form-control form-bottom-primary" id="phone"
                                            name="phone" value="{{ $siteSetting->phone }}">
                                        <span id="error_phone" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="address" class="text-label">Address</label>
                                        <input type="text" class="form-control form-bottom-primary" id="address"
                                            name="address" value="{{ $siteSetting->address }}">
                                        <span id="error_address" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="description" class="text-label">Description</label>
                                        <input type="text" class="form-control form-bottom-primary" id="description"
                                            name="description" value="{{ $siteSetting->description }}">
                                        <span id="error_description" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="keywords" class="text-label">Keywords</label>
                                        <input type="text" class="form-control form-bottom-primary" id="keywords"
                                            name="keywords" value="{{ $siteSetting->keywords }}">
                                        <span id="error_keywords" class="error-message"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="unit_price" class="text-label">Unit Price</label>
                                        <input type="text" class="form-control form-bottom-primary" id="unit_price"
                                            name="unit_price" value="{{ $siteSetting->unit_price }}">
                                        <span id="error_unit_price" class="error-message"></span>
                                    </div>
                                </div>
                            </div>
                            {{-- Sation Map Start --}}

                            <div>

                                {{-- lat and lng --}}
                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">
                                {{-- lat and lng --}}


                                <div class="d-flex align-items-center gap-3 mt-5 mb-3">
                                    <label for="map" class="d-block text-label ">Change Root Station</label>
                                    <a tabindex="0" class="map_help" role="button" data-bs-toggle="popover"
                                        data-bs-trigger="focus" data-bs-title="Help"
                                        data-bs-content="Click on the root station on the map, then click 'Change Root Location' and select the root station!"><i
                                            class="fa-solid fa-question"></i></a>
                                </div>
                                <div class="main_div" style="width: 100% ;height: 500px">
                                    <input id="pac-input" class="controls" type="text" placeholder="Search Box" />
                                    <div id="map"></div>
                                </div>


                                <!-- Control panel to add new station -->
                                <div class="control-panel d-none">

                                    <input type="text" id="latitude"><br>
                                    <input type="text" id="longitude"><br>
                                </div>


                            </div>

                            {{-- Sation Map End --}}
                            <div class="row mt-2">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary float-right">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjjf_h1Kin_CeaJiT8VanhcNz0-4lhdNQ&libraries=places" async
        defer></script>
    <script>
        $(document).ready(function() {
            // Initialize the map
            var mapOptions = {
                center: new google.maps.LatLng( {{ $siteSetting->latitude? $siteSetting->latitude : 26.48631280  }}, {{ $siteSetting->longitude? $siteSetting->longitude : 87.27068650 }}), // Default center (San Francisco)
                zoom: 12
            };
            var map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // Initialize the marker
            var marker = new google.maps.Marker({
                position: mapOptions.center,
                map: map,
                draggable: true // Make the marker draggable
            });

            // Create the search box and link it to the UI element.
            var input = document.getElementById('pac-input');
            var searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            // Bias the SearchBox results towards current map's viewport.
            map.addListener('bounds_changed', function() {
                searchBox.setBounds(map.getBounds());
            });

            // Event listener for marker click
            google.maps.event.addListener(marker, 'click', function(event) {
                var lat = marker.getPosition().lat();
                var lng = marker.getPosition().lng();
                $('#latitude').val(lat);
                $('#longitude').val(lng);
            });

            // Event listener for marker drag end
            google.maps.event.addListener(marker, 'dragend', function(event) {
                var lat = marker.getPosition().lat();
                var lng = marker.getPosition().lng();
                $('#latitude').val(lat);
                $('#longitude').val(lng);
            });

            // Event listener for map click
            google.maps.event.addListener(map, 'click', function(event) {
                var lat = event.latLng.lat();
                var lng = event.latLng.lng();
                marker.setPosition(event.latLng);
                $('#latitude').val(lat);
                $('#longitude').val(lng);
            });

            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener('places_changed', function() {
                var places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                // Clear out the old markers.
                marker.setMap(null);

                // Get the icon, name and location of the place.
                var bounds = new google.maps.LatLngBounds();
                places.forEach(function(place) {
                    if (!place.geometry) {
                        console.log("Returned place contains no geometry");
                        return;
                    }

                    // Create a new marker for the place.
                    marker = new google.maps.Marker({
                        map: map,
                        draggable: true, // Make the new marker draggable
                        title: place.name,
                        position: place.geometry.location
                    });

                    $('#latitude').val(place.geometry.location.lat());
                    $('#longitude').val(place.geometry.location.lng());

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });


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

        });
    </script>
@endsection
