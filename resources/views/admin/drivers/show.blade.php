@extends('layouts.admin') {{-- Nama layout Anda --}}

@section('title', 'Driver Detail: ' . ($driver->user->name ?? 'N/A'))

{{-- (Opsional) Push CSS untuk Peta Leaflet.js --}}
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Styling untuk Leaflet map container */
    #driverMap {
        height: 400px;
        width: 100%;
        border-radius: 0.5rem; /* rounded-lg */
    }
    /* Mengatasi konflik styling Leaflet dengan Tailwind */
    .leaflet-pane {
        z-index: 10 !important;
    }
    .leaflet-top, .leaflet-bottom {
        z-index: 100 !important;
    }
</style>
@endpush


@section('content')
<div class="p-6 md:p-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Driver Detail</h1>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap mengenai driver.</p>
        </div>
        <a href="{{ url('admin/drivers') }}" class="mt-4 md:mt-0 inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Back to List</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex flex-col items-center text-center">
                    <img class="h-24 w-24 rounded-full object-cover shadow-md"
                         src="{{ $driver->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($driver->user->name ?? 'D').'&color=7F9CF5&background=EBF4FF&size=128' }}"
                         alt="Profile picture">
                    <h3 class="mt-4 text-xl font-semibold text-gray-900">{{ $driver->user->name ?? 'N/A' }}</h3>
                    <div class="mt-2 flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $driver->status == 'online' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            <svg class="mr-1.5 h-2 w-2 {{ $driver->status == 'online' ? 'text-green-400' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                            {{ ucfirst($driver->status) }}
                        </span>
                         <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $driver->is_verified ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                            <i class="fas fa-{{ $driver->is_verified ? 'check-circle' : 'exclamation-triangle' }} mr-1.5"></i>
                            {{ $driver->is_verified ? 'Verified' : 'Not Verified' }}
                        </span>
                    </div>
                </div>
                <div class="mt-6 border-t border-gray-200 pt-6">
                    <dl class="space-y-4">
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Rating</dt>
                            <dd class="text-gray-900 font-medium flex items-center text-yellow-500"><i class="fas fa-star mr-1"></i>{{ number_format($driver->rating, 1) }} / 5.0</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Email</dt>
                            <dd class="text-gray-900">{{ $driver->user->email ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Phone</dt>
                            <dd class="text-gray-900">{{ $driver->user->phone ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-white p-4 rounded-lg shadow flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg w-12 h-12 flex items-center justify-center">
                        <i class="fas fa-route text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Total Trips</p>
                        <p class="text-lg font-bold text-gray-900">{{ $driver->total_trips ?? 0 }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg w-12 h-12 flex items-center justify-center">
                        <i class="fas fa-wallet text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Balance</p>
                        <p class="text-lg font-bold text-gray-900">Rp {{ number_format($driver->balance, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow flex items-center">
                    <div class="flex-shrink-0 bg-gray-100 rounded-lg w-12 h-12 flex items-center justify-center">
                        <i class="far fa-clock text-gray-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Last Active</p>
                        <p class="text-lg font-bold text-gray-900">{{ $driver->last_active_at ? $driver->last_active_at->diffForHumans() : 'Never' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div x-data="{ activeTab: 'vehicle' }" class="bg-white rounded-lg shadow">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-6 px-6">
                        <button @click="activeTab = 'vehicle'" :class="{'border-blue-500 text-blue-600': activeTab === 'vehicle', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'vehicle'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Vehicle
                        </button>
                        <button @click="activeTab = 'documents'" :class="{'border-blue-500 text-blue-600': activeTab === 'documents', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'documents'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Documents
                        </button>
                        <button @click="activeTab = 'orders'" :class="{'border-blue-500 text-blue-600': activeTab === 'orders', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'orders'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Order History
                        </button>
                         <button @click="activeTab = 'location'" :class="{'border-blue-500 text-blue-600': activeTab === 'location', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'location'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Location
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <div x-show="activeTab === 'vehicle'" class="space-y-6">
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">Vehicle Information</h4>
                            <dl class="mt-4 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 text-sm">
                                <div class="sm:col-span-1"><dt class="text-gray-500">Vehicle Type</dt><dd class="mt-1 text-gray-900 font-medium">{{ $driver->vehicleType->name ?? ucfirst($driver->vehicle_type) }}</dd></div>
                                <div class="sm:col-span-1"><dt class="text-gray-500">Brand & Model</dt><dd class="mt-1 text-gray-900 font-medium">{{ $driver->vehicle_brand }} {{ $driver->vehicle_model }}</dd></div>
                                <div class="sm:col-span-1"><dt class="text-gray-500">Year</dt><dd class="mt-1 text-gray-900 font-medium">{{ $driver->vehicle_year }}</dd></div>
                                <div class="sm:col-span-1"><dt class="text-gray-500">License Plate</dt><dd class="mt-1 text-gray-900 font-mono font-bold tracking-wider inline-block bg-gray-800 text-white px-2 py-1 rounded">{{ $driver->vehicle_plate }}</dd></div>
                                <div class="sm:col-span-2"><dt class="text-gray-500">License Number (SIM)</dt><dd class="mt-1 text-gray-900 font-medium">{{ $driver->license_number }}</dd></div>
                            </dl>
                        </div>
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-medium text-gray-900">Emergency Contact</h4>
                             <dl class="mt-4 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 text-sm">
                                <div class="sm:col-span-1"><dt class="text-gray-500">Name</dt><dd class="mt-1 text-gray-900 font-medium">{{ $driver->emergency_contact_name ?? 'N/A' }}</dd></div>
                                <div class="sm:col-span-1"><dt class="text-gray-500">Phone</dt><dd class="mt-1 text-gray-900 font-medium">{{ $driver->emergency_contact_phone ?? 'N/A' }}</dd></div>
                            </dl>
                        </div>
                    </div>

                    <div x-show="activeTab === 'documents'">
                    <div class="overflow-x-auto">
    <table class="min-w-full text-sm divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left font-medium text-gray-500">Type</th>
                <th class="px-4 py-2 text-left font-medium text-gray-500">Status</th>
                <th class="px-4 py-2 text-left font-medium text-gray-500">Image</th>
                <th class="px-4 py-2 text-left font-medium text-gray-500">Uploaded</th>
                <th class="px-4 py-2 text-left font-medium text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($driver->documents as $document)
            <tr>
                <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $document->type)) }}</td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $document->is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $document->is_verified ? 'Verified' : 'Pending' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    @if($document->file_path && file_exists(public_path('storage/' . $document->file_path)))
                        <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank">
                            <img src="{{ asset('storage/' . $document->file_path) }}" alt="{{ $document->type }}" class="h-10 w-16 object-cover rounded">
                        </a>
                    @else
                        <span class="text-gray-400">N/A</span>
                    @endif
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $document->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3 whitespace-nowrap space-x-2">
                    @if(!$document->is_verified)
                        <button class="text-green-600 hover:text-green-900" title="Approve"><i class="fas fa-check"></i></button>
                        <button class="text-red-600 hover:text-red-900" title="Reject"><i class="fas fa-times"></i></button>
                    @else
                        <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900" title="View"><i class="fas fa-eye"></i></a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-8 text-gray-500">No documents uploaded.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
                    </div>

                    <div x-show="activeTab === 'orders'">
                    <div class="overflow-x-auto">
    <table class="min-w-full text-sm divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left font-medium text-gray-500">Order ID</th>
                <th class="px-4 py-2 text-left font-medium text-gray-500">Status</th>
                <th class="px-4 py-2 text-left font-medium text-gray-500">Amount</th>
                <th class="px-4 py-2 text-left font-medium text-gray-500">Date</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($driver->orders->sortByDesc('created_at')->take(10) as $order)
            <tr>
                <td class="px-4 py-3 whitespace-nowrap font-medium text-blue-600 hover:underline"><a href="#">#{{ $order->id }}</a></td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-8 text-gray-500">This driver has no order history.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
                    </div>

                    <div x-show="activeTab === 'location'">
                        @if($driver->current_latitude && $driver->current_longitude)
                            <div id="driverMap" class="z-0"></div>
                        @else
                            <div class="text-center py-12 bg-gray-50 rounded-lg">
                                <i class="fas fa-map-marker-slash fa-3x text-gray-400"></i>
                                <p class="mt-4 text-sm font-medium text-gray-600">Location data not available.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Alpine.js untuk fungsionalitas Tab --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
{{-- Leaflet.js untuk Peta --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        // Cek jika map container ada dan data lokasi tersedia
        @if($driver->current_latitude && $driver->current_longitude)
            const mapElement = document.getElementById('driverMap');
            if (mapElement) {
                const lat = {{ $driver->current_latitude }};
                const lng = {{ $driver->current_longitude }};
                const driverName = "{{ addslashes($driver->user->name ?? 'Driver') }}";

                // Inisialisasi peta
                const map = L.map('driverMap').setView([lat, lng], 15);

                // Tile Layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Marker
                L.marker([lat, lng]).addTo(map)
                    .bindPopup(`<b>${driverName}'s Location</b>`)
                    .openPopup();
            }
        @endif
    });
</script>
@endpush