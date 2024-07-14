@extends('layouts.app')

@section('title', 'Create')
@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.css') }}" />
    <style>
        #map {
            height: 100%;
            width: 100%;
        }

        .control-panel {
            width: 100%;
            max-width: 600px;
            z-index: 1;
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .control-panel input,
        .control-panel select {
            margin-bottom: 5px;
        }

        .control-panel h3 {
            font-size: 24px;
            color: #000;
        }

        .control-panel input,
        .control-panel select {
            padding: 10px;
            width: 100%;
        }

        .control-panel label {
            display: block;
        }
    </style>
@endsection
@section('content')
    <form id="addData" action="{{ route('admin.station.store') }}" method="POST" enctype="multipart/form-data">
        <div class="row justify-content-center p-4 align-items-center">
            @csrf
            <div class="control-panel col-lg-6">
                <h3>Add Station</h3>
                <label for="station_level" class="form-label">Station Level</label>
                <select id="station_level" name="station_level" class="form-control form-select">
                    <option value="">Select Station Level</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>

                <div id="parent_station_div" class="d-none">
                    <label for="station-type">Parent Station</label>
                    <select id="parent_id" class="form-control form-select" name="parent_id">
                        <option>Select Parent Station</option>
                    </select>
                </div>
                <div>
                    <label for="location-name">Station Name: </label>
                    <input type="text" id="location-name" name="name">
                </div>
                <di>
                    <label for="location-name">Capacity</label>
                    <input type="number" id="capacity" name="capacity">
                </di>
                <div>
                    <label for="location-name">Starting Date</label>
                    <input type="date" id="starting-date" name="start_date">
                </div>
                <div>
                    <label for="latitude">Latitude:</label>
                    <input type="text" id="latitude" name="latitude">
                </div>
                <div>
                    <label for="longitude">Longitude:</label>
                    <input type="text" id="longitude" name="longitude">
                </div>
                <div>
                    <label for="lineman">Lineman Name</label>
                    <input type="text" id="lineman" name="line_man_name">
                </div>
                <div>
                    <label for="manager">Manager Name</label>
                    <input type="text" id="manager" name="manager">
                </div>
                <button type="submit" class="btn btn-primary">Add Station</button>
            </div>
            <div class="col-lg-6">
                <div class="main_div" style="width: 100% ;height: 500px" onload="initMap()">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjjf_h1Kin_CeaJiT8VanhcNz0-4lhdNQ&libraries=places" async
        defer></script>
    <script>
        $(document).ready(function() {
            let map;
            let markers = [];
            let lines = [];
            let lastParentLocation;
            let rootLatLng = new google.maps.LatLng({{ app('siteSetting')->latitude }},
                {{ app('siteSetting')->longitude }});

            let mapOptions = {
                center: rootLatLng,
                zoom: 12
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // Initialize the root marker (non-draggable)
            let rootMarker = new google.maps.Marker({
                position: rootLatLng,
                map: map,
                draggable: false,
                icon: {
                    url: "{{ asset('assets/map/root.png') }}",
                    scaledSize: new google.maps.Size(60, 60)
                },
                title: "Root Station"
            });

            // Initialize the movable marker
            let movableMarker = new google.maps.Marker({
                position: rootLatLng,
                map: map,
                draggable: true,
                title: "Movable Marker",
                visible: false
            });

            let polyline = new google.maps.Polyline({
                map: map,
                strokeColor: "#0000FF",
                strokeOpacity: 1.0,
                strokeWeight: 2
            });

            let input = document.getElementById('pac-input');
            let searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            map.addListener('bounds_changed', function() {
                searchBox.setBounds(map.getBounds());
            });

            google.maps.event.addListener(movableMarker, 'dragend', function(event) {
                let lat = movableMarker.getPosition().lat();
                let lng = movableMarker.getPosition().lng();
                $('#latitude').val(lat);
                $('#longitude').val(lng);
                updatePolyline();
            });

            google.maps.event.addListener(map, 'click', function(event) {
                let lat = event.latLng.lat();
                let lng = event.latLng.lng();
                movableMarker.setPosition(event.latLng);
                movableMarker.setVisible(true); // Show the marker on map click
                $('#latitude').val(lat);
                $('#longitude').val(lng);
                updatePolyline();
            });

            searchBox.addListener('places_changed', function() {
                let places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                let bounds = new google.maps.LatLngBounds();
                places.forEach(function(place) {
                    if (!place.geometry) {
                        console.log("Returned place contains no geometry");
                        return;
                    }

                    movableMarker.setPosition(place.geometry.location);
                    $('#latitude').val(place.geometry.location.lat());
                    $('#longitude').val(place.geometry.location.lng());
                    updatePolyline();

                    if (place.geometry.viewport) {
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });

            function updatePolyline() {
                let path = [];
                if (lastParentLocation) {
                    path.push(lastParentLocation);
                }
                path.push(movableMarker.getPosition());
                polyline.setPath(path);
            }

            $('#station_level').on('change', function() {
                let level = $(this).val();

                if (level == 1) {
                    $('#parent_station_div').addClass('d-none');
                    $('#parent_id').empty();
                    lastParentLocation = null;
                } else {
                    $('#parent_station_div').removeClass('d-none');

                    $.ajax({
                        url: "{{ route('admin.station.getSubStation') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            level: level
                        },
                        success: function(response) {
                            $('#parent_id').empty();
                            $('#parent_id').append('<option>Select Parent Station</option>');
                            $.each(response.data, function(key, value) {
                                $('#parent_id').append('<option data-latitude="' + value
                                    .latitude + '" data-longitude="' + value
                                    .longitude + '" value="' + value.id + '">' +
                                    value.name + '</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                        }
                    });
                }
            });

            $('#parent_id').on('change', function() {
                let selectedOption = $(this).find(':selected');
                let latitude = parseFloat(selectedOption.data('latitude'));
                let longitude = parseFloat(selectedOption.data('longitude'));

                lastParentLocation = new google.maps.LatLng(latitude, longitude);
                addMarker(lastParentLocation, true);
                drawLine(rootLatLng, lastParentLocation);
                updatePolyline();
            });

            function addMarker(location, isParentStation = false) {
                let icon = null;

                if (isParentStation) {
                    icon = {
                        url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png', // URL to the blue marker icon
                        scaledSize: new google.maps.Size(60, 60) // Adjust size if necessary
                    };
                }

                let marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    icon: icon
                });

                markers.push(marker);
            }

            function drawLine(start, end) {
                let line = new google.maps.Polyline({
                    path: [start, end],
                    geodesic: true,
                    strokeColor: '#FF0000',
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                });
                line.setMap(map);
                lines.push(line);
            }
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
    </script>
@endsection
