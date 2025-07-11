@extends('layouts.app')

@section('title', 'Pesan Ride')

@section('content')
<!-- Add CSRF token meta tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Pesan Ride</h1>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('customer.orders.create') }}" method="POST" id="orderForm">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column - Form -->
                    <div class="space-y-6">
                        <!-- Service Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Layanan</label>
                            <div class="grid grid-cols-1 gap-4">
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="service_type" value="motorcycle" class="form-radio" required>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Motor</div>
                                        <div class="text-xs text-gray-500">Cepat & Ekonomis</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="service_type" value="car" class="form-radio" required>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Mobil</div>
                                        <div class="text-xs text-gray-500">Nyaman & Aman</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Pickup Location -->
                        <div>
                            <label for="pickup_address" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Pickup</label>
                            <textarea name="pickup_address" id="pickup_address" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Masukkan alamat pickup..." required>{{ old('pickup_address') }}</textarea>
                            <input type="hidden" name="pickup_latitude" id="pickup_latitude" value="{{ old('pickup_latitude') }}">
                            <input type="hidden" name="pickup_longitude" id="pickup_longitude" value="{{ old('pickup_longitude') }}">
                            <div class="mt-2 space-x-2">
                                <button type="button" onclick="getCurrentLocation('pickup')" class="text-sm text-blue-600 hover:text-blue-800">📍 Gunakan Lokasi Saat Ini</button>
                                <button type="button" onclick="setLocationMode('pickup')" class="text-sm text-green-600 hover:text-green-800">🗺️ Pilih di Peta</button>
                            </div>
                        </div>

                        <!-- Destination -->
                        <div>
                            <label for="destination_address" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Tujuan</label>
                            <textarea name="destination_address" id="destination_address" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Masukkan alamat tujuan..." required>{{ old('destination_address') }}</textarea>
                            <input type="hidden" name="destination_latitude" id="destination_latitude" value="{{ old('destination_latitude') }}">
                            <input type="hidden" name="destination_longitude" id="destination_longitude" value="{{ old('destination_longitude') }}">
                            <div class="mt-2">
                                <button type="button" onclick="setLocationMode('destination')" class="text-sm text-green-600 hover:text-green-800">🗺️ Pilih di Peta</button>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                            <textarea name="notes" id="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Catatan tambahan untuk driver...">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Fare Estimation -->
                        <div id="fareEstimation" class="hidden bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-medium text-blue-800 mb-2">Estimasi Tarif</h3>
                            <div id="fareDetails"></div>
                        </div>
                    </div>

                    <!-- Right Column - Map -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-medium text-gray-800 mb-2">Peta Lokasi</h3>
                            <div id="mapInstructions" class="text-sm text-gray-600 mb-3">
                                Klik tombol "Pilih di Peta" untuk menentukan lokasi pickup atau tujuan
                            </div>
                            <div id="map" style="height: 400px; width: 100%;" class="rounded-lg border"></div>
                        </div>
                        
                        <!-- Map Controls -->
                        <div id="mapControls" class="hidden bg-yellow-50 p-3 rounded-lg">
                            <p class="text-sm text-yellow-800 mb-2">
                                <span id="mapMode">Mode:</span> - Klik pada peta untuk menentukan lokasi
                            </p>
                            <button type="button" onclick="confirmLocation()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm mr-2">
                                Konfirmasi Lokasi
                            </button>
                            <button type="button" onclick="cancelLocationMode()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-6">
                    <a href="{{ route('customer.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Kembali
                    </a>
                    <div class="space-x-2">
                        <button type="button" onclick="calculateFare()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Hitung Tarif
                        </button>
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Pesan Sekarang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Leaflet Polyline Encoded -->
<script src="https://cdn.jsdelivr.net/npm/polyline-encoded@0.0.9/Polyline.encoded.js"></script>

<script>
// Map variables
let map;
let pickupMarker = null;
let destinationMarker = null;
let routeControl = null;
let currentLocationMode = null;
let tempMarker = null;

// Initialize map
function initMap() {
    // Default center (Jakarta)
    map = L.map('map').setView([-6.2088, 106.8456], 13);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Add click event to map
    map.on('click', function(e) {
        if (currentLocationMode) {
            handleMapClick(e);
        }
    });
}

// Handle map click
function handleMapClick(e) {
    const lat = e.latlng.lat;
    const lng = e.latlng.lng;
    
    // Remove temp marker if exists
    if (tempMarker) {
        map.removeLayer(tempMarker);
    }
    
    // Add temporary marker
    tempMarker = L.marker([lat, lng], {
        icon: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        })
    }).addTo(map);
    
    // Store coordinates
    window.selectedLat = lat;
    window.selectedLng = lng;
    
    // Get address using reverse geocoding
    reverseGeocode(lat, lng, currentLocationMode);
}

// Reverse geocoding using Nominatim (free)
function reverseGeocode(lat, lng, locationType) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
        .then(response => response.json())
        .then(data => {
            const address = data.display_name || `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
            
            if (locationType === 'pickup') {
                document.getElementById('pickup_address').value = address;
            } else if (locationType === 'destination') {
                document.getElementById('destination_address').value = address;
            }
        })
        .catch(error => {
            console.log('Reverse geocoding error:', error);
            const address = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
            if (locationType === 'pickup') {
                document.getElementById('pickup_address').value = address;
            } else if (locationType === 'destination') {
                document.getElementById('destination_address').value = address;
            }
        });
}

// Set location mode
function setLocationMode(type) {
    currentLocationMode = type;
    document.getElementById('mapControls').classList.remove('hidden');
    document.getElementById('mapMode').textContent = `Mode: Pilih lokasi ${type === 'pickup' ? 'pickup' : 'tujuan'}`;
    document.getElementById('mapInstructions').textContent = `Klik pada peta untuk menentukan lokasi ${type === 'pickup' ? 'pickup' : 'tujuan'}`;
}

// Confirm location
function confirmLocation() {
    if (!window.selectedLat || !window.selectedLng) {
        alert('Silakan pilih lokasi di peta terlebih dahulu');
        return;
    }
    
    const lat = window.selectedLat;
    const lng = window.selectedLng;
    
    // Remove temp marker
    if (tempMarker) {
        map.removeLayer(tempMarker);
    }
    
    if (currentLocationMode === 'pickup') {
        // Remove existing pickup marker
        if (pickupMarker) {
            map.removeLayer(pickupMarker);
        }
        
        // Add pickup marker
        pickupMarker = L.marker([lat, lng], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(map).bindPopup('Lokasi Pickup').openPopup();
        
        // Set coordinates
        document.getElementById('pickup_latitude').value = lat;
        document.getElementById('pickup_longitude').value = lng;
        
    } else if (currentLocationMode === 'destination') {
        // Remove existing destination marker
        if (destinationMarker) {
            map.removeLayer(destinationMarker);
        }
        
        // Add destination marker
        destinationMarker = L.marker([lat, lng], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(map).bindPopup('Lokasi Tujuan').openPopup();
        
        // Set coordinates
        document.getElementById('destination_latitude').value = lat;
        document.getElementById('destination_longitude').value = lng;
    }
    
    // Clear location mode
    cancelLocationMode();
    
    // Draw route if both locations are set
    drawRoute();
}

// Cancel location mode
function cancelLocationMode() {
    currentLocationMode = null;
    document.getElementById('mapControls').classList.add('hidden');
    document.getElementById('mapInstructions').textContent = 'Klik tombol "Pilih di Peta" untuk menentukan lokasi pickup atau tujuan';
    
    // Remove temp marker
    if (tempMarker) {
        map.removeLayer(tempMarker);
        tempMarker = null;
    }
    
    // Clear selected coordinates
    window.selectedLat = null;
    window.selectedLng = null;
}

// Draw route between pickup and destination
function drawRoute() {
    const pickupLat = document.getElementById('pickup_latitude').value;
    const pickupLng = document.getElementById('pickup_longitude').value;
    const destLat = document.getElementById('destination_latitude').value;
    const destLng = document.getElementById('destination_longitude').value;
    
    if (pickupLat && pickupLng && destLat && destLng) {
        // Get route from OSRM
        getOSRMRoute(pickupLat, pickupLng, destLat, destLng);
    }
}

// Get route from OSRM API
function getOSRMRoute(pickupLat, pickupLng, destLat, destLng) {
    const url = `https://router.project-osrm.org/route/v1/driving/${pickupLng},${pickupLat};${destLng},${destLat}?overview=full&geometries=polyline`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.routes && data.routes.length > 0) {
                drawOSRMRoute(data.routes[0].geometry);
            } else {
                // Fallback to simple line
                drawSimpleRoute(pickupLat, pickupLng, destLat, destLng);
            }
        })
        .catch(error => {
            console.log('OSRM route error:', error);
            // Fallback to simple line
            drawSimpleRoute(pickupLat, pickupLng, destLat, destLng);
        });
}

// Draw OSRM route using polyline
function drawOSRMRoute(polyline) {
    // Remove existing route
    if (routeControl) {
        map.removeLayer(routeControl);
    }
    
    // Decode polyline
    const coordinates = L.Polyline.fromEncoded(polyline).getLatLngs();
    
    // Draw route
    routeControl = L.polyline(coordinates, {
        color: '#3B82F6',
        weight: 4,
        opacity: 0.8
    }).addTo(map);
    
    // Fit map to route bounds
    if (coordinates.length > 0) {
        map.fitBounds(routeControl.getBounds(), { padding: [20, 20] });
    }
}

// Draw simple route (fallback)
function drawSimpleRoute(pickupLat, pickupLng, destLat, destLng) {
    // Remove existing route
    if (routeControl) {
        map.removeLayer(routeControl);
    }
    
    const latlngs = [
        [parseFloat(pickupLat), parseFloat(pickupLng)],
        [parseFloat(destLat), parseFloat(destLng)]
    ];
    
    routeControl = L.polyline(latlngs, {
        color: '#EF4444',
        weight: 3,
        opacity: 0.7,
        dashArray: '10, 5'
    }).addTo(map);
    
    map.fitBounds(routeControl.getBounds(), { padding: [20, 20] });
}

// Get current location
function getCurrentLocation(type) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            document.getElementById(type + '_latitude').value = lat;
            document.getElementById(type + '_longitude').value = lng;
            
            // Update map
            map.setView([lat, lng], 15);
            
            // Add marker
            if (type === 'pickup') {
                if (pickupMarker) {
                    map.removeLayer(pickupMarker);
                }
                pickupMarker = L.marker([lat, lng], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map).bindPopup('Lokasi Pickup Saat Ini').openPopup();
            }
            
            // Reverse geocoding
            reverseGeocode(lat, lng, type);
            
            // Draw route if both locations are set
            drawRoute();
            
        }, function(error) {
            alert('Error getting location: ' + error.message);
        });
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

// Calculate fare using OSRM
function calculateFare() {
    const pickupLat = document.getElementById('pickup_latitude').value;
    const pickupLng = document.getElementById('pickup_longitude').value;
    const destLat = document.getElementById('destination_latitude').value;
    const destLng = document.getElementById('destination_longitude').value;
    const serviceType = document.querySelector('input[name="service_type"]:checked')?.value;
    
    if (!pickupLat || !pickupLng || !destLat || !destLng || !serviceType) {
        alert('Mohon lengkapi semua lokasi dan pilih jenis layanan');
        return;
    }
    
    // Show loading state
    document.getElementById('fareDetails').innerHTML = `
        <div class="flex items-center justify-center py-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-blue-600">Menghitung tarif...</span>
        </div>
    `;
    document.getElementById('fareEstimation').classList.remove('hidden');
    
    // Call Laravel route to get distance and duration from OSRM
    fetch('/customer/calculate-fare', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json', // Tambahkan ini
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            pickup_latitude: parseFloat(pickupLat),
            pickup_longitude: parseFloat(pickupLng),
            destination_latitude: parseFloat(destLat),
            destination_longitude: parseFloat(destLng),
            service_type: serviceType
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log("RESPONSE:", data);
        if (data.success) {
            const fareData = data.data; // Ambil dari nested "data"
            // Display fare estimation
            document.getElementById('fareDetails').innerHTML = `
                <div class="space-y-2">
                    <p><strong>Jarak:</strong> ${fareData.distance} km</p>
                    <p><strong>Waktu:</strong> ${fareData.duration} menit</p>
                    <p><strong>Tarif Dasar:</strong> Rp ${fareData.base_fare.toLocaleString()}</p>
                    <p><strong>Tarif per KM:</strong> Rp ${fareData.distance_fare.toLocaleString()}</p>
                    <hr class="my-2">
                    <p><strong>Total Estimasi:</strong> Rp ${fareData.total_fare.toLocaleString()}</p>
                    ${data.approximated ? '<p class="text-xs text-yellow-600">* Estimasi berdasarkan jarak garis lurus</p>' : ''}
                </div>
            `;
            
            // Draw route on map if polyline is available
            if (data.polyline) {
                drawOSRMRoute(data.polyline);
            }
        } else {
            document.getElementById('fareDetails').innerHTML = `
                <div class="text-red-600">
                    <p>Gagal menghitung tarif: ${data.error || 'Terjadi kesalahan'}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error calculating fare:', error);
        document.getElementById('fareDetails').innerHTML = `
            <div class="text-red-600">
                <p>Gagal menghitung tarif. Silakan coba lagi.</p>
            </div>
        `;
    });
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    initMap();
});
</script>
@endsection