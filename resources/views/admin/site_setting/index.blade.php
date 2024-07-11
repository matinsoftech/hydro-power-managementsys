@extends('layouts.app')

@section('title', 'Site Setting')

@section('styles')
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
    #change-root-button {
        display: none;
        position: absolute;
        top: 50px;
        left: 10px;
        z-index: 1;
        background-color: white;
        padding: 10px;
        border: 1px solid #999;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .map_help{
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
        background: gray;
        width: 1.5rem;
        height: 1.5rem;
        color: white;
    }
    .map_help:hover{
        color: white !important;
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
                                <div class="d-flex align-items-center gap-3 mt-5 mb-3">
                                <label for="map" class="d-block text-label ">Change Root Station</label>
                                <a tabindex="0" class="map_help" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Help" data-bs-content="Click on the root station on the map, then click 'Change Root Location' and select the root station!"><i class="fa-solid fa-question"></i></a>
                                </div>
                                <div class="main_div" style="width: 100% ;height: 500px" onload="initMap()">
                                    <div id="map"></div>
                                    <!-- Button to change root location -->
                                    <div   id="change-root-button">
                                        <div class="btn btn-primary" onclick="enableRootChange()">Change Root Location</div>
                                    </div>
                                </div>


                                <!-- Control panel to add new station -->
                                <div class="control-panel d-none">
                                            <h3>Add Station</h3>
                                            <label for="station-type">Station Type:</label><br>
                                            <select id="station-type">
                                                <option value="substation">Substation</option>
                                                <option value="subsubstation">Sub-substation</option>
                                            </select><br>
                                            <div id="substation-selection" style="display:none;">
                                                <label for="substation-select">Parent Substation:</label><br>
                                                <select id="substation-select"></select><br>
                                            </div>
                                            <label for="location-name">Location Name:</label><br>
                                            <input type="text" id="location-name"><br>
                                            <label for="latitude">Latitude:</label><br>
                                            <input type="text" id="latitude"><br>
                                            <label for="longitude">Longitude:</label><br>
                                            <input type="text" id="longitude"><br>
                                            <button onclick="handleAddStation()">Add Station</button>
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

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjjf_h1Kin_CeaJiT8VanhcNz0-4lhdNQ&callback=initMap" async defer></script>
<script>
    var map;
    var rootMarker;
    var rootStation = {lat: 40.730610, lng: -73.935242};
    console.log(rootStation);

    // var subStations = [
    //     {lat: 40.740610, lng: -73.925242, name: 'Substation 1'},
    //     {lat: 40.720610, lng: -73.945242, name: 'Substation 2'},
    //     {lat: 40.730610, lng: -73.955242, name: 'Substation 3'},
    //     {lat: 40.750610, lng: -73.935242, name: 'Substation 4'}
    // ];
    // var subSubStations = [
    //     [],
    //     [],
    //     [],
    //     []
    // ];

    // var subStationMarkers = [];

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,
            center: rootStation
        });

        rootMarker = new google.maps.Marker({
            position: rootStation,
            map: map,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 10,
                fillColor: '#FF0000',
                fillOpacity: 1,
                strokeWeight: 2,
                strokeColor: '#FF0000'
            },
            title: 'Root Station'
        });

        rootMarker.addListener('click', function() {
            document.getElementById('change-root-button').style.display = 'block';
        });

        for (var i = 0; i < subStations.length; i++) {
            addSubStationMarker(subStations[i], i);
        }

        map.addListener('click', function(event) {
            if (document.getElementById('change-root-button').style.display === 'block') {
                setNewRootLocation(event.latLng);
            }
        });
    }

    function addSubStationMarker(location, index) {
        var marker = new google.maps.Marker({
            position: location,
            map: map,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 7,
                fillColor: '#00FF00',
                fillOpacity: 1,
                strokeWeight: 2,
                strokeColor: '#00FF00'
            },
            title: location.name  // Set the tooltip content to the station name
        });

        var line = new google.maps.Polyline({
            path: [rootStation, location],
            geodesic: true,
            strokeColor: '#00FF00',
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

        subStationMarkers.push(marker);
        updateSubStationOptions();
    }

    function setNewRootLocation(latLng) {
        rootStation = {lat: latLng.lat(), lng: latLng.lng()};
        rootMarker.setPosition(rootStation);
        map.setCenter(rootStation);

        for (var i = 0; i < subStationMarkers.length; i++) {
            var line = new google.maps.Polyline({
                path: [rootStation, subStations[i]],
                geodesic: true,
                strokeColor: '#00FF00',
                strokeOpacity: 1.0,
                strokeWeight: 2
            });
            line.setMap(map);
        }

        document.getElementById('change-root-button').style.display = 'none';
        console.log(rootStation);

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

    function enableRootChange() {
        document.getElementById('change-root-button').style.display = 'none';
        google.maps.event.addListenerOnce(map, 'click', function(event) {
            setNewRootLocation(event.latLng);
        });
    }

    window.initMap = initMap;  // Ensure initMap is in the global scope
</script>

    <script>
        $(function() {
            $('#addData').on('submit', function(e) {
                e.preventDefault();
                let url = $(this).attr('action');
                // Serialize the form data
                let formData = new FormData(this);

                // Check if the password and confirmPassword fields are empty
                if (formData.get('password') === '' && formData.get('confirm_password') === '') {
                    // Remove the password and confirmPassword fields from the FormData object
                    formData.delete('password');
                    formData.delete('confirmPassword');
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        console.log('ajax fired');
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr['success'](data.message);
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
                                '<span class="text-red">*' + xhr
                                .responseJSON.errors[
                                    error] + '</span>');
                            i++;
                        }
                    }
                });
            });

            $('#logo').on('change', function(e) {
                var input = e.target;
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#avatarPreview').attr('src', e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });
        });
    </script>
@endsection
