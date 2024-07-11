
@extends('layouts.app')

@section('title', 'Create')
@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.css') }}"/>
<style>
    .main_div{
        position: relative;
    }
    #map {
        height: 100%;
        width: 100%;
    }
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
    .control-panel {
        width: 100%;
        max-width: 600px;
        z-index: 1;
        background-color: white;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .control-panel input,
    .control-panel select {
        margin-bottom: 5px;
    }
    .control-panel h3{
      font-size: 24px;
      color: #000;
    }

    .control-panel input,
    .control-panel select{
     padding: 10px;
     width: 100%;
    }
    .control-panel label{
        display: block;
    }
</style>

@endsection
@section('content')



<!-- Control panel to add new station -->

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
        <label for="latitude">Lineman Name</label>
        <input type="text" id="lineman">
    </div>
    <div>
        <label for="latitude">Manager Name</label>
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
    <script src="{{ asset('assets/libs/moment/min/moment.min.js') }}" ></script>
    <script src="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.js') }}" ></script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjjf_h1Kin_CeaJiT8VanhcNz0-4lhdNQ&callback=initMap" async defer></script>
    <script>
        var map;
        var rootStation = {lat: 40.730610, lng: -73.935242};
        var subStations = [
            {lat: 40.740610, lng: -73.925242, name: 'Substation 1'},
            {lat: 40.720610, lng: -73.945242, name: 'Substation 2'},
            {lat: 40.730610, lng: -73.955242, name: 'Substation 3'},
            {lat: 40.750610, lng: -73.935242, name: 'Substation 4'}
        ];
        var subSubStations = [
            [],
            [],
            [],
            []
        ];
        var subStationMarkers = [];

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                center: rootStation
            });

            var rootMarker = new google.maps.Marker({
                position: rootStation,
                map: map,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: '#ffffff',
                    fillOpacity: 1,
                    strokeWeight: 3,
                    strokeColor: 'red'
                },
                title: 'Root Station'
            });

            for (var i = 0; i < subStations.length; i++) {
                addSubStationMarker(subStations[i], i);
            }
        }

        function addSubStationMarker(location, index) {
            var marker = new google.maps.Marker({
                position: location,
                map: map,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 7,
                    fillColor: '#615dff',
                    fillOpacity: 1,
                    strokeWeight: 2,
                    strokeColor: '#fff'
                },
                title: location.name  // Set the tooltip content to the station name
            });

            var line = new google.maps.Polyline({
                path: [rootStation, location],
                geodesic: true,
                strokeColor: '#ff3333',
                strokeOpacity: 1.0,
                strokeWeight: 4
            });
            line.setMap(map);

            // Add click event listener to show tooltip
            marker.addListener('click', function() {
                new google.maps.InfoWindow({
                    content: '<b>' + location.name + '</b>'
                }).open(map, marker);
            });

            subStationMarkers.push(marker);
            updateSubStationOptions();
        }

        function addSubStation() {
            var locationName = document.getElementById('location-name').value;
            var latitude = parseFloat(document.getElementById('latitude').value);
            var longitude = parseFloat(document.getElementById('longitude').value);

            if (isNaN(latitude) || isNaN(longitude)) {
                alert('Please enter valid coordinates.');
                return;
            }

            var newSubStation = {lat: latitude, lng: longitude, name: locationName};
            subStations.push(newSubStation);
            subSubStations.push([]); // Ensure subSubStations array is updated
            addSubStationMarker(newSubStation, subStations.length - 1);

            // Clear input fields after adding the marker
            document.getElementById('location-name').value = '';
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
        }

        function addSubSubStation() {
            var subStationIndex = document.getElementById('substation-select').value;
            var locationName = document.getElementById('location-name').value;
            var latitude = parseFloat(document.getElementById('latitude').value);
            var longitude = parseFloat(document.getElementById('longitude').value);

            if (isNaN(latitude) || isNaN(longitude)) {
                alert('Please enter valid coordinates.');
                return;
            }

            var newSubSubStation = {lat: latitude, lng: longitude, name: locationName};
            subSubStations[subStationIndex].push(newSubSubStation);
            addSubSubStationMarker(newSubSubStation, subStationIndex);

            // Clear input fields after adding the marker
            document.getElementById('location-name').value = '';
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
        }

        function addSubSubStationMarker(location, subStationIndex) {
            var marker = new google.maps.Marker({
                position: location,
                map: map,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 5,
                    fillColor: '#0000FF',
                    fillOpacity: 1,
                    strokeWeight: 2,
                    strokeColor: '#0000FF'
                },
                title: location.name  // Set the tooltip content to the station name
            });

            var line = new google.maps.Polyline({
                path: [subStations[subStationIndex], location],
                geodesic: true,
                strokeColor: '#0000FF',
                strokeOpacity: 1.0,
                strokeWeight: 2
            });
            line.setMap(map);

            // Add click event listener to show tooltip
            marker.addListener('click', function() {
                new google.maps.InfoWindow({
                    content: '<b>' + location.name + '</b>'
                }).open(map, marker);
            });
        }

        function updateSubStationOptions() {
            var select = document.getElementById('substation-select');
            select.innerHTML = '';
            for (var i = 0; i < subStations.length; i++) {
                var option = document.createElement('option');
                option.value = i;
                option.text = subStations[i].name;
                select.add(option);
            }
        }
    </script>

    <script>
        document.getElementById('station-type').addEventListener('change', function() {
            var substationSelection = document.getElementById('substation-selection');
            if (this.value === 'subsubstation') {
                substationSelection.style.display = 'block';
            } else {
                substationSelection.style.display = 'none';
            }
        });

        function handleAddStation() {
            var stationType = document.getElementById('station-type').value;
            if (stationType === 'substation') {
                addSubStation();
            } else if (stationType === 'subsubstation') {
                addSubSubStation();
            }
        }

        window.initMap = initMap;  // Ensure initMap is in the global scope
    </script>
<!--This page JavaScript -->

@endsection
