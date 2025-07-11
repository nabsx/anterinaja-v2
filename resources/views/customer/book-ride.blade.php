@extends('layouts.app')

@section('title', 'Book Your Ride')

@section('content')
<!-- Add CSRF token meta tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pesan Perjalanan Anda</h1>
            <p class="text-gray-600">Pilih layanan dan tentukan lokasi penjemputan serta tujuan Anda.</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Please fix the following errors:</span>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('customer.orders.create') }}" method="POST" id="orderForm" class="space-y-6">
            @csrf
            
            <!-- Service Type Selection -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-5 w-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900">Jenis Layanan</h2>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-center space-x-3 p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                        <input type="radio" name="service_type" value="motorcycle" class="form-radio text-blue-600" required onchange="checkAutoCalculate()">
                        <div class="flex items-center gap-3 flex-1">
                            <div class="p-2 bg-green-100 rounded-full">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold">Motor</div>
                                <div class="text-sm text-gray-500">Cepat & Ekonomis</div>
                                <span class="inline-block mt-1 px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Mulai dari Rp 9,000</span>
                            </div>
                        </div>
                    </label>

                    <label class="flex items-center space-x-3 p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                        <input type="radio" name="service_type" value="car" class="form-radio text-blue-600" required onchange="checkAutoCalculate()">
                        <div class="flex items-center gap-3 flex-1">
                            <div class="p-2 bg-blue-100 rounded-full">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold">Mobil</div>
                                <div class="text-sm text-gray-500">Nyaman & Aman</div>
                                <span class="inline-block mt-1 px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Mulai dari Rp 11,000</span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Location Selection -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Pickup Location -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <h2 class="text-xl font-semibold text-gray-900">Lokasi Penjemputan</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div id="pickupLocationDisplay" class="hidden p-3 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="font-medium text-green-800">Selected Location</p>
                                    <p class="text-sm text-green-600 mt-1" id="pickupLocationText"></p>
                                </div>
                                <button type="button" class="text-green-600 hover:text-green-800" onclick="clearLocation('pickup')">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div id="pickupLocationInput">
                            <textarea name="pickup_address" id="pickup_address" rows="3" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                placeholder="Masukkan alamat penjemputan..." required>{{ old('pickup_address') }}</textarea>
                            
                            <input type="hidden" name="pickup_latitude" id="pickup_latitude" value="{{ old('pickup_latitude') }}">
                            <input type="hidden" name="pickup_longitude" id="pickup_longitude" value="{{ old('pickup_longitude') }}">
                            
                            <div class="flex flex-col sm:flex-row gap-2 mt-3">
                                <button type="button" onclick="getCurrentLocation('pickup')" 
                                    class="flex-1 flex items-center justify-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span id="currentLocationText">Gunakan Lokasi Saat Ini</span>
                                </button>
                                <button type="button" onclick="setLocationMode('pickup')" 
                                    class="flex-1 flex items-center justify-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                    </svg>
                                    Pilih dari Peta
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Destination Location -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <h2 class="text-xl font-semibold text-gray-900">Lokasi Tujuan</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div id="destinationLocationDisplay" class="hidden p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="font-medium text-red-800">Selected Location</p>
                                    <p class="text-sm text-red-600 mt-1" id="destinationLocationText"></p>
                                </div>
                                <button type="button" class="text-red-600 hover:text-red-800" onclick="clearLocation('destination')">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div id="destinationLocationInput">
                            <textarea name="destination_address" id="destination_address" rows="3" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                placeholder="Masukkan alamat tujuan..." required>{{ old('destination_address') }}</textarea>
                            
                            <input type="hidden" name="destination_latitude" id="destination_latitude" value="{{ old('destination_latitude') }}">
                            <input type="hidden" name="destination_longitude" id="destination_longitude" value="{{ old('destination_longitude') }}">
                            
                            <div class="mt-3">
                                <button type="button" onclick="setLocationMode('destination')" 
                                    class="w-full flex items-center justify-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                    </svg>
                                    Pilih dari Peta
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-5 w-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900">Peta Rute</h2>
                </div>
                
                <div id="mapInstructions" class="text-sm text-gray-600 mb-3 p-3 bg-blue-50 rounded-lg">
                    <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Klik tombol “Pilih dari Peta” untuk menentukan lokasi penjemputan dan tujuan Anda.
                </div>
                
                <div id="map" style="height: 400px; width: 100%;" class="rounded-lg border border-gray-200"></div>
                
                <!-- Map Controls -->
                <div id="mapControls" class="hidden mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800 mb-3 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <span id="mapMode">Mode:</span> - Klik pada peta untuk menentukan lokasi
                    </p>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <button type="button" onclick="confirmLocation()" 
                            class="flex-1 bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Konfirmasi Lokasi
                        </button>
                        <button type="button" onclick="cancelLocationMode()" 
                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Batalkan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Fare Estimation -->
            <div id="fareEstimation" class="hidden bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-5 w-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900">Estimasi Tarif</h2>
                </div>
                <div id="fareDetails"></div>
            </div>

            <!-- Notes -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-5 w-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900">Catatan Tambahan</h2>
                </div>
                <textarea name="notes" id="notes" rows="3" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                    placeholder="Tuliskan instruksi khusus untuk driver, jika ada...">{{ old('notes') }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <a href="{{ route('customer.dashboard') }}" 
                    class="flex-1 bg-white border border-gray-300 text-gray-700 font-medium py-3 px-6 rounded-lg hover:bg-gray-50 transition-colors text-center">
                    <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Beranda
                </a>
                <button type="button" onclick="calculateFare()" 
                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                    <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Hitung Estimasi Tarif
                </button>
                <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium py-3 px-6 rounded-lg transition-all transform hover:scale-105">
                    <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Pesan Sekarang
                </button>
            </div>
        </form>
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
let autoCalculateEnabled = true;

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
    document.getElementById('mapMode').textContent = `Mode: Select ${type === 'pickup' ? 'pickup' : 'destination'} location`;
    document.getElementById('mapInstructions').innerHTML = `
        <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
        Click on the map to set your ${type === 'pickup' ? 'pickup' : 'destination'} location
    `;
}

// Confirm location
function confirmLocation() {
    if (!window.selectedLat || !window.selectedLng) {
        alert('Please select a location on the map first');
        return;
    }
    
    const lat = window.selectedLat;
    const lng = window.selectedLng;
    const address = currentLocationMode === 'pickup' ? 
        document.getElementById('pickup_address').value : 
        document.getElementById('destination_address').value;
    
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
        }).addTo(map).bindPopup('Pickup Location').openPopup();
        
        // Set coordinates
        document.getElementById('pickup_latitude').value = lat;
        document.getElementById('pickup_longitude').value = lng;
        
        // Update UI
        updateLocationDisplay('pickup', address);
        
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
        }).addTo(map).bindPopup('Destination Location').openPopup();
        
        // Set coordinates
        document.getElementById('destination_latitude').value = lat;
        document.getElementById('destination_longitude').value = lng;
        
        // Update UI
        updateLocationDisplay('destination', address);
    }
    
    // Clear location mode
    cancelLocationMode();
    
    // Draw route if both locations are set
    drawRoute();
    
    // Check if we can auto calculate fare
    checkAutoCalculate();
}

// Update location display
function updateLocationDisplay(type, address) {
    const displayElement = document.getElementById(`${type}LocationDisplay`);
    const inputElement = document.getElementById(`${type}LocationInput`);
    const textElement = document.getElementById(`${type}LocationText`);
    
    textElement.textContent = address;
    displayElement.classList.remove('hidden');
    inputElement.classList.add('hidden');
}

// Clear location
function clearLocation(type) {
    const displayElement = document.getElementById(`${type}LocationDisplay`);
    const inputElement = document.getElementById(`${type}LocationInput`);
    
    displayElement.classList.add('hidden');
    inputElement.classList.remove('hidden');
    
    // Clear form fields
    document.getElementById(`${type}_address`).value = '';
    document.getElementById(`${type}_latitude`).value = '';
    document.getElementById(`${type}_longitude`).value = '';
    
    // Remove marker
    if (type === 'pickup' && pickupMarker) {
        map.removeLayer(pickupMarker);
        pickupMarker = null;
    } else if (type === 'destination' && destinationMarker) {
        map.removeLayer(destinationMarker);
        destinationMarker = null;
    }
    
    // Remove route
    if (routeControl) {
        map.removeLayer(routeControl);
        routeControl = null;
    }
    
    // Hide fare estimation
    document.getElementById('fareEstimation').classList.add('hidden');
}

// Cancel location mode
function cancelLocationMode() {
    currentLocationMode = null;
    document.getElementById('mapControls').classList.add('hidden');
    document.getElementById('mapInstructions').innerHTML = `
        <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Click "Select on Map" buttons to set your pickup and destination locations
    `;
    
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
    const button = document.getElementById('currentLocationText');
    button.innerHTML = `
        <svg class="animate-spin h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Getting Location...
    `;
    
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
                }).addTo(map).bindPopup('Current Pickup Location').openPopup();
            }
            
            // Reverse geocoding
            reverseGeocode(lat, lng, type);
            
            // Draw route if both locations are set
            drawRoute();
            
            // Check if we can auto calculate fare
            checkAutoCalculate();
            
            // Reset button text
            button.innerHTML = `
                <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Location Found
            `;
            
            setTimeout(() => {
                button.textContent = 'Use Current Location';
            }, 2000);
            
        }, function(error) {
            alert('Error getting location: ' + error.message);
            button.textContent = 'Use Current Location';
        });
    } else {
        alert('Geolocation is not supported by this browser.');
        button.textContent = 'Use Current Location';
    }
}

// Check if auto calculate should be triggered
function checkAutoCalculate() {
    if (!autoCalculateEnabled) return;
    
    const pickupLat = document.getElementById('pickup_latitude').value;
    const pickupLng = document.getElementById('pickup_longitude').value;
    const destLat = document.getElementById('destination_latitude').value;
    const destLng = document.getElementById('destination_longitude').value;
    const serviceType = document.querySelector('input[name="service_type"]:checked')?.value;
    
    // If all required data is available, auto calculate
    if (pickupLat && pickupLng && destLat && destLng && serviceType) {
        // Add a small delay to make it feel more natural
        setTimeout(() => {
            calculateFare(true); // Pass true to indicate this is auto calculation
        }, 500);
    }
}

// Calculate fare using OSRM - Modified to support auto calculation
function calculateFare(isAutoCalculation = false) {
    const pickupLat = document.getElementById('pickup_latitude').value;
    const pickupLng = document.getElementById('pickup_longitude').value;
    const destLat = document.getElementById('destination_latitude').value;
    const destLng = document.getElementById('destination_longitude').value;
    const serviceType = document.querySelector('input[name="service_type"]:checked')?.value;
    
    if (!pickupLat || !pickupLng || !destLat || !destLng || !serviceType) {
        if (!isAutoCalculation) {
            alert('Please complete all locations and select service type');
        }
        return;
    }
    
    // Show loading state
    document.getElementById('fareDetails').innerHTML = `
        <div class="flex items-center justify-center py-8">
            <svg class="animate-spin h-8 w-8 mr-3 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-blue-600">${isAutoCalculation ? 'Auto-calculating fare...' : 'Calculating fare...'}</span>
        </div>
    `;
    document.getElementById('fareEstimation').classList.remove('hidden');
    
    // Call Laravel route to get distance and duration from OSRM
    fetch('/customer/calculate-fare', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
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
            const fareData = data.data;
            const driverFare = fareData.total_fare - fareData.surcharges.commission;
            // Display modern fare estimation
            document.getElementById('fareDetails').innerHTML = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <svg class="h-5 w-5 mx-auto mb-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            <p class="text-sm text-gray-600">Jarak</p>
                            <p class="font-semibold">${fareData.distance} km</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <svg class="h-5 w-5 mx-auto mb-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-gray-600">Durasi</p>
                            <p class="font-semibold">${fareData.duration} min</p>
                        </div>
                        <div class="text-center p-3 bg-blue-50 rounded-lg col-span-2 sm:col-span-1">
                            <svg class="h-5 w-5 mx-auto mb-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <p class="text-sm text-blue-600">Tarif yang kamu bayar</p>
                            <p class="font-bold text-lg text-blue-700">Rp ${fareData.total_fare.toLocaleString()}</p>
                        </div>
                    </div>
                    
                    <hr class="border-gray-200">
                    
                    <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                    
                     <span class="text-gray-600">Tarif yang diterima driver:</span>
                        <span class="font-semibold text-green-600">
                            Rp ${(fareData.total_fare - fareData.surcharges.commission).toLocaleString()}
                        </span> 
                    </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Komisi:</span>
                            <span>Rp ${fareData.surcharges.commission.toLocaleString()}</span>
                        </div>
                    </div>
                    
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="h-4 w-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p>* Ini adalah estimasi tarif yang sudah termasuk komisi.</p>
                                ${data.approximated ? '<p>* Dihitung secara otomatis</p>' : ''}
                                ${isAutoCalculation ? '<p class="text-green-700">✓ Calculated automatically</p>' : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Draw route on map if polyline is available
            if (data.polyline) {
                drawOSRMRoute(data.polyline);
            }
        } else {
            document.getElementById('fareDetails').innerHTML = `
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-red-800">Failed to calculate fare: ${data.error || 'An error occurred'}</p>
                    </div>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error calculating fare:', error);
        document.getElementById('fareDetails').innerHTML = `
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-800">Failed to calculate fare. Please try again.</p>
                </div>
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
