<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Map</title>
    <style>
        .main_div {
            position: relative;
        }

        #map {
            height: 100vh;
            width: 100%;
        }

        html,
        body {
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
</head>
<body>
    <div class="main_div" style="width: 100% ;height: 500px">
        <div id="map"></div>
    </div>


    <script src="https://hydropower.merogaditracker.com/assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjjf_h1Kin_CeaJiT8VanhcNz0-4lhdNQ&libraries=places" async
    defer></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let map;
        let rootLatLng = new google.maps.LatLng({{ app('siteSetting')->latitude }}, {{ app('siteSetting')->longitude }});

        let mapOptions = {
            center: rootLatLng,
            zoom: 12
        };

        map = new google.maps.Map(document.getElementById('map'), mapOptions);

        let rootMarker = new google.maps.Marker({
            position: rootLatLng,
            map: map,
            icon: {
                url: "{{ asset('assets/map/root.png') }}",
                scaledSize: new google.maps.Size(60, 60)
            },
            title: "Root Station"
        });

        function addMarker(location, station, isParentStation = false) {
            let icon = null;

            if (isParentStation) {
                icon = {
                    url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                    scaledSize: new google.maps.Size(60, 60)
                };
            }

            let marker = new google.maps.Marker({
                position: location,
                map: map,
                icon: icon
            });

            marker.addListener('click', function() {
                $('#stationName').text(station.name);
                $('#stationLevel').text(station.station_level);
                $('#stationManager').text(station.manager);
                $('#stationLineMan').text(station.line_man_name);
                $('#stationCapacity').text(station.capacity);
                $('#stationStartDate').text(station.start_date);
                $('#stationLatitude').text(station.latitude);
                $('#stationLongitude').text(station.longitude);
                $('#deleteStation').attr('action', '/admin/station/' + station.id);
                $('#stationModal').modal('show');
            });
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
        }

        function plotStations(stations, parentLatLng) {
            stations.forEach(station => {
                let stationLatLng = new google.maps.LatLng(station.latitude, station.longitude);
                addMarker(stationLatLng, station, true);
                drawLine(parentLatLng, stationLatLng);

                if (station.childs && station.childs.length > 0) {
                    plotStations(station.childs, stationLatLng);
                }
            });
        }

        // Initial plot with root station as the parent
        plotStations(@json($stations), rootLatLng);
    });
    </script>
</body>
</html>



