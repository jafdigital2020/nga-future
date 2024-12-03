@extends('layouts.settings') @section('title', 'Geofencing')
<style>
    #map {
        height: 400px;
        width: 100%;
    }

    /* Ensure icon spacing */
    .btn i {
        margin-right: 0;
        /* Remove margin if not needed */
        font-size: 14px;
        /* Adjust icon size if needed */
    }

    /* Optional: Custom hover effects */
    .btn-success:hover {
        background-color: #45a049;
        /* Darker green */
    }

    .btn-danger:hover {
        background-color: #d9534f;
        /* Darker red */
    }

    .pac-container {
        z-index: 99999999999 !important;
    }

</style>
<!-- Load Google Maps and Places API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCoZSVkyGR645u4B_OOFmepLzrRBB8Hgmc&libraries=places">
</script>
@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Geofencing</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Geofencing</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#createGeofenceModal">
                    <i class="fa fa-plus"></i> Create Geofence
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Table -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0">
                    <thead>
                        <tr>
                            <th>Geofence Name </th>
                            <th>Address</th>
                            <th>Radius(meters)</th>
                            <th>Created By</th>
                            <th>Edit By</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($geofence as $geo)
                        <tr>
                            <td>{{ $geo->fencing_name }}</td>
                            <td>{{ $geo->fencing_address }}</td>
                            <td>{{ $geo->fencing_radius }}</td>
                            <td>
                                @if ($geo->geofenceCreatedBy)
                                @if ($geo->geofenceCreatedBy->fName || $geo->geofenceCreatedBy->lName)
                                {{ $geo->geofenceCreatedBy->fName }} {{ $geo->geofenceCreatedBy->lName }}
                                @else
                                {{ $geo->geofenceCreatedBy->name }}
                                @endif
                                @else
                                N/A
                                @endif
                            </td>

                            <td> @if($geo->geofenceEditBy)
                                {{ $geo->geofenceEditBy->fName }} {{ $geo->geofenceEditBy->lName }}
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="#" class="btn btn-success btn-sm btn-edit" data-id="{{ $geo->id }}"
                                    data-name="{{ $geo->fencing_name }}" data-address="{{ $geo->fencing_address }}"
                                    data-radius="{{ $geo->fencing_radius }}" data-latitude="{{ $geo->latitude }}"
                                    data-longitude="{{ $geo->longitude }}">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a href="#" class="btn btn-danger btn-sm btn-delete" data-id="{{ $geo->id }}">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Creating Geofence -->
    <div class="modal fade" id="createGeofenceModal" tabindex="-1" role="dialog"
        aria-labelledby="createGeofenceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createGeofenceModalLabel">Create Geofence</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Geofence Form Inside Modal -->
                    <form id="geofencingForm" action="{{ route('settings.createGeofence') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label for="nameInput" class="form-label">Geofence Name:</label>
                                    <input type="text" id="nameInput" name="fencing_name" class="form-control"
                                        placeholder="Enter name" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label for="addressInput" class="form-label">Address:</label>
                                    <input type="text" id="addressInput" name="fencing_address" class="form-control"
                                        placeholder="Enter address" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label for="radiusInput" class="form-label">Fencing Radius (meters):</label>
                                    <input type="number" id="radiusInput" class="form-control" name="fencing_radius"
                                        value="500" required>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                        <div id="map" style="height: 300px; margin-top: 20px;"></div>
                        <button type="submit" class="btn btn-primary mt-3">Save Geofence</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Geofence Modal -->
    <div class="modal fade" id="editGeofenceModal" tabindex="-1" role="dialog" aria-labelledby="editGeofenceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGeofenceModalLabel">Edit Geofence</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editGeofenceForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editGeofenceId">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="editFencingName">Geofence Name</label>
                                    <input type="text" name="fencing_namee" id="editFencingName" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="editFencingAddress">Address</label>
                                    <input type="text" name="fencing_addresse" id="editFencingAddress"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="editFencingRadius">Radius (meters)</label>
                                    <input type="number" name="fencing_radiuse" id="editFencingRadius"
                                        class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <!-- Map container for editing geofence location -->
                        <div id="editMap" style="height: 300px; width: 100%; margin-top: 20px;"></div>
                        <input type="hidden" name="latitudee" id="editLatitude">
                        <input type="hidden" name="longitudee" id="editLongitude">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Geofence Modal -->
    <div class="modal custom-modal fade" id="delete_geofence" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete Geofence</h3>
                        <p>Are you sure you want to cancel this Geofence?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <div class="row">
                            <div class="col-5">
                                <form id="deleteGeofenceForm" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="geofence_id" id="delete_geofence_id">
                                    <button class="btn add-btn" type="submit">Delete</button>
                                </form>
                            </div>
                            <div class="col-6">
                                <a href="javascript:void(0);" data-dismiss="modal" class="btn add-btn">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Geofence Modal -->


</div>


@endsection

@section('scripts')
<script>
    let map;
    let marker;
    let circle;
    let autocomplete;
    const defaultLocation = {
        lat: 14.5995,
        lng: 120.9842
    }; // Default location (e.g., Manila)

    function initMap() {
        // Initialize the map centered on the default location
        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultLocation,
            zoom: 15,
        });

        // Marker for the selected address
        marker = new google.maps.Marker({
            position: defaultLocation,
            map: map,
            draggable: true,
        });

        // Circle to represent the geofence radius
        circle = new google.maps.Circle({
            map: map,
            radius: parseFloat(document.getElementById('radiusInput').value), // Initial radius
            fillColor: '#AA0000',
            strokeColor: '#AA0000',
            fillOpacity: 0.3,
            strokeOpacity: 0.8,
            strokeWeight: 2,
        });
        circle.bindTo('center', marker, 'position');

        // Initialize autocomplete for address input
        autocomplete = new google.maps.places.Autocomplete(document.getElementById("addressInput"));
        autocomplete.bindTo("bounds", map);

        // When a place is selected, update the map and marker
        autocomplete.addListener("place_changed", function () {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                alert("No details available for the selected address.");
                return;
            }

            // Center the map and place the marker at the selected location
            map.setCenter(place.geometry.location);
            map.setZoom(15);
            marker.setPosition(place.geometry.location);
            updateLatLngInputs(place.geometry.location.lat(), place.geometry.location.lng());
        });

        // Update latitude and longitude when marker is dragged
        google.maps.event.addListener(marker, 'dragend', function () {
            updateLatLngInputs(marker.getPosition().lat(), marker.getPosition().lng());
        });

        // Update circle radius when radius input changes
        document.getElementById('radiusInput').addEventListener('input', function () {
            circle.setRadius(parseFloat(this.value));
        });

        // Set default latitude and longitude values in the form
        updateLatLngInputs(marker.getPosition().lat(), marker.getPosition().lng());
    }

    function updateLatLngInputs(lat, lng) {
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
    }

    // Initialize map after the page loads
    window.onload = initMap;

</script>

<!-- Edit -->

<script>
    $(document).ready(function () {
        // Initialize Google Places Autocomplete for the edit address input
        let autocomplete;

        function initEditAutocomplete() {
            const addressInput = document.getElementById("editFencingAddress");

            // Initialize Google Places Autocomplete for the edit address input without type restriction
            const autocomplete = new google.maps.places.Autocomplete(addressInput);

            // When a place is selected from the autocomplete suggestions, update the map and coordinates
            autocomplete.addListener("place_changed", function () {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    alert("No details available for the selected address.");
                    return;
                }

                // Set latitude and longitude based on selected address
                const latitude = place.geometry.location.lat();
                const longitude = place.geometry.location.lng();

                // Update the hidden latitude and longitude fields
                $('#editLatitude').val(latitude);
                $('#editLongitude').val(longitude);

                // Update the map with the new location
                editMap.setCenter({
                    lat: latitude,
                    lng: longitude
                });
                editMarker.setPosition({
                    lat: latitude,
                    lng: longitude
                });
            });
        }


        // Triggered when the edit button is clicked
        $('.btn-edit').on('click', function () {
            // Get the geofence data from data attributes
            const id = $(this).data('id');
            const name = $(this).data('name');
            const address = $(this).data('address');
            const radius = $(this).data('radius');
            const latitude = parseFloat($(this).data('latitude'));
            const longitude = parseFloat($(this).data('longitude'));

            // Populate the modal fields with data
            $('#editGeofenceId').val(id);
            $('#editFencingName').val(name);
            $('#editFencingAddress').val(address);
            $('#editFencingRadius').val(radius);
            $('#editLatitude').val(latitude);
            $('#editLongitude').val(longitude);

            // Initialize map with current coordinates and radius
            initEditMap(latitude, longitude, radius);

            // Initialize autocomplete only once
            if (!autocomplete) {
                initEditAutocomplete();
            }

            $('#editGeofenceForm').attr('action', '/admin/settings/geofencing/update/' + id);
            // Show the modal
            $('#editGeofenceModal').modal('show');
        });

        // Delete Geofence request
        $('.btn-delete').on('click', function () {
            var id = $(this).data('id');

            $('#delete_geofence_id').val(id);
            $('#deleteGeofenceForm').attr('action', '/admin/settings/geofencing/delete/' + id);
            $('#delete_geofence').modal('show');
        });
    });

    // Function to initialize the map with the current geofence data in the edit modal
    let editMap, editMarker, editCircle;

    function initEditMap(lat, lng, radius) {
        editMap = new google.maps.Map(document.getElementById("editMap"), {
            center: {
                lat: lat,
                lng: lng
            },
            zoom: 15,
        });

        editMarker = new google.maps.Marker({
            position: {
                lat: lat,
                lng: lng
            },
            map: editMap,
            draggable: true,
        });

        editCircle = new google.maps.Circle({
            map: editMap,
            radius: parseFloat(radius), // Initial radius from geofence data
            fillColor: '#AA0000',
            strokeColor: '#AA0000',
            fillOpacity: 0.3,
            strokeOpacity: 0.8,
            strokeWeight: 2,
        });
        editCircle.bindTo('center', editMarker, 'position');

        // Update hidden latitude and longitude when marker is dragged
        google.maps.event.addListener(editMarker, 'dragend', function () {
            $('#editLatitude').val(editMarker.getPosition().lat());
            $('#editLongitude').val(editMarker.getPosition().lng());
        });

        // Update circle radius when radius input changes
        $('#editFencingRadius').on('input', function () {
            editCircle.setRadius(parseFloat(this.value));
        });

        // Trigger resize when the modal is shown to render the map correctly
        $('#editGeofenceModal').on('shown.bs.modal', function () {
            google.maps.event.trigger(editMap, 'resize');
            editMap.setCenter(editMarker.getPosition());
        });
    }

</script>
@endsection
