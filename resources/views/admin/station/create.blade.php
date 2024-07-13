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
    <div class="row justify-content-center p-4 align-items-center">
        <div class="control-panel col-lg-6">
            <h3>Add Station</h3>
            <label for="station-type">Station Type:</label>
            <select id="station-type">
                <option value="substation">Substation</option>
                <option value="subsubstation">Sub-substation</option>
            </select>
            <div id="substation-selection" style="display:none;">
                <label for="substation-select">Parent Substation:</label>
                <select id="substation-select"></select>
            </div>
            <div>
                <label for="location-name">Location Name: </label>
                <input type="text" id="location-name">
            </div>
            <di>
                <label for="location-name">Capacity</label>
                <input type="number" id="capacity">
            </di>
            <div>
                <label for="location-name">Starting Date</label>
                <input type="date" id="starting-date">
            </div>
            <div>
                <label for="latitude">Latitude:</label>
                <input type="text" id="latitude">
            </div>
            <div>
                <label for="longitude">Longitude:</label>
                <input type="text" id="longitude">
            </div>
            <div>
                <label for="lineman">Lineman Name</label>
                <input type="text" id="lineman">
            </div>
            <div>
                <label for="manager">Manager Name</label>
                <input type="text" id="manager">
            </div>
            <button onclick="handleAddStation()" class="btn  btn-primary">Add Station</button>
        </div>
        <div class="col-lg-6">
            @include('admin.station.map')
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/libs/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.js') }}"></script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjjf_h1Kin_CeaJiT8VanhcNz0-4lhdNQ&libraries=places" async
        defer></script>
    <script>
        $(document).ready(function() {
            // Initialize the map
            var rootLatLng = new google.maps.LatLng({{ app('siteSetting')->latitude }},
                {{ app('siteSetting')->longitude }});
            var mapOptions = {
                center: rootLatLng,
                zoom: 12
            };
            var map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // Initialize the root marker (non-draggable)
            var rootMarker = new google.maps.Marker({
                position: rootLatLng,
                map: map,
                draggable: false,
                icon: {
                    url: "{{ asset('assets/map/root.png') }}",
                    scaledSize: new google.maps.Size(60, 60) // Scale the icon to 30x30 pixels
                },
                title: "Root Station"
            });

            // Initialize the movable marker
            var movableMarker = new google.maps.Marker({
                position: rootLatLng,
                map: map,
                draggable: true,
                title: "Movable Marker",
                visible : false,
            });

            // Create the search box and link it to the UI element.
            var input = document.getElementById('pac-input');
            var searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            // Bias the SearchBox results towards current map's viewport.
            map.addListener('bounds_changed', function() {
                searchBox.setBounds(map.getBounds());
            });

            // Event listener for movable marker drag end
            google.maps.event.addListener(movableMarker, 'dragend', function(event) {
                var lat = movableMarker.getPosition().lat();
                var lng = movableMarker.getPosition().lng();
                $('#latitude').val(lat);
                $('#longitude').val(lng);
            });

            // Event listener for map click to move the movable marker
            google.maps.event.addListener(map, 'click', function(event) {
                var lat = event.latLng.lat();
                var lng = event.latLng.lng();
                movableMarker.setPosition(event.latLng);
                movableMarker.setVisible(true); // Show the marker on map click
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

                // Get the icon, name and location of the place.
                var bounds = new google.maps.LatLngBounds();
                places.forEach(function(place) {
                    if (!place.geometry) {
                        console.log("Returned place contains no geometry");
                        return;
                    }

                    // Move the movable marker to the new place
                    movableMarker.setPosition(place.geometry.location);
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
        });
    </script>
@endsection
