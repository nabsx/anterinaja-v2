@extends('layouts.app')

@section('title', 'Pesan Ride')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
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
                
                <div class="space-y-6">
                    <!-- Service Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Layanan</label>
                        <div class="grid grid-cols-2 gap-4">
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
                        <button type="button" onclick="getCurrentLocation('pickup')" class="mt-2 text-sm text-blue-600 hover:text-blue-800">📍 Gunakan Lokasi Saat Ini</button>
                    </div>

                    <!-- Destination -->
                    <div>
                        <label for="destination_address" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Tujuan</label>
                        <textarea name="destination_address" id="destination_address" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Masukkan alamat tujuan..." required>{{ old('destination_address') }}</textarea>
                        <input type="hidden" name="destination_latitude" id="destination_latitude" value="{{ old('destination_latitude') }}">
                        <input type="hidden" name="destination_longitude" id="destination_longitude" value="{{ old('destination_longitude') }}">
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

<script>
function getCurrentLocation(type) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            document.getElementById(type + '_latitude').value = lat;
            document.getElementById(type + '_longitude').value = lng;
            
            // Reverse geocoding to get address
            fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=YOUR_MAPBOX_TOKEN`)
                .then(response => response.json())
                .then(data => {
                    if (data.features && data.features.length > 0) {
                        document.getElementById(type + '_address').value = data.features[0].place_name;
                    }
                })
                .catch(error => {
                    console.log('Geocoding error:', error);
                    document.getElementById(type + '_address').value = `Lat: ${lat}, Lng: ${lng}`;
                });
        }, function(error) {
            alert('Error getting location: ' + error.message);
        });
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

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
    
    // Calculate distance (simple haversine formula)
    const R = 6371; // Earth's radius in km
    const dLat = (destLat - pickupLat) * Math.PI / 180;
    const dLng = (destLng - pickupLng) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(pickupLat * Math.PI / 180) * Math.cos(destLat * Math.PI / 180) *
              Math.sin(dLng/2) * Math.sin(dLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c;
    
    // Simple fare calculation
    const baseFare = serviceType === 'motorcycle' ? 5000 : 8000;
    const perKmRate = serviceType === 'motorcycle' ? 2000 : 3000;
    const totalFare = baseFare + (distance * perKmRate);
    const estimatedDuration = Math.ceil(distance * 3); // Rough estimation
    
    // Display fare estimation
    document.getElementById('fareDetails').innerHTML = `
        <p><span class="font-medium">Jarak:</span> ${distance.toFixed(1)} km</p>
        <p><span class="font-medium">Estimasi Waktu:</span> ${estimatedDuration} menit</p>
        <p><span class="font-medium">Tarif Dasar:</span> Rp ${baseFare.toLocaleString()}</p>
        <p><span class="font-medium">Tarif per KM:</span> Rp ${perKmRate.toLocaleString()}</p>
        <p class="text-lg font-bold text-blue-800"><span class="font-medium">Total Estimasi:</span> Rp ${Math.ceil(totalFare).toLocaleString()}</p>
    `;
    document.getElementById('fareEstimation').classList.remove('hidden');
}
</script>
@endsection
