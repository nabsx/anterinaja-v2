{{-- show.blade.php - Fixed version --}}
@extends('layouts.admin')

@section('title', 'Driver Detail: ' . ($driver->user->name ?? 'N/A'))

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #driverMap {
        height: 400px;
        width: 100%;
        border-radius: 0.5rem;
    }
    .leaflet-pane {
        z-index: 10 !important;
    }
    .leaflet-top, .leaflet-bottom {
        z-index: 100 !important;
    }
    .document-image {
        transition: transform 0.2s;
    }
    .document-image:hover {
        transform: scale(1.1);
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
        <div class="mt-4 md:mt-0 flex space-x-2">
            @if(!$driver->is_verified)
                <form action="{{ route('admin.drivers.approve', $driver) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="inline-flex items-center space-x-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-check"></i>
                        <span>Approve Driver</span>
                    </button>
                </form>
            @endif
            <a href="{{ url('admin/drivers') }}" class="inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Back to List</span>
            </a>
        </div>
    </div>

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Profile Card -->
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex flex-col items-center text-center">
                    @php
                        $profilePhoto = $driver->documents->where('document_type', 'photo')->first();
                        $profilePhotoUrl = $profilePhoto && $profilePhoto->document_path 
                            ? Storage::url($profilePhoto->document_path)
                            : 'https://ui-avatars.com/api/?name='.urlencode($driver->user->name ?? 'D').'&color=7F9CF5&background=EBF4FF&size=128';
                    @endphp
                    <img class="h-24 w-24 rounded-full object-cover shadow-md"
                         src="{{ $profilePhotoUrl }}"
                         alt="Profile picture"
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($driver->user->name ?? 'D') }}&color=7F9CF5&background=EBF4FF&size=128'">
                    <h3 class="mt-4 text-xl font-semibold text-gray-900">{{ $driver->user->name ?? 'N/A' }}</h3>
                    <div class="mt-2 flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $driver->is_online ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            <svg class="mr-1.5 h-2 w-2 {{ $driver->is_online ? 'text-green-400' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                            {{ $driver->is_online ? 'Online' : 'Offline' }}
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
                            <dd class="text-gray-900 font-medium flex items-center text-yellow-500">
                                <i class="fas fa-star mr-1"></i>{{ number_format($driver->rating ?? 0, 1) }} / 5.0
                            </dd>
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

            <!-- Stats Cards -->
            <div class="space-y-4">
                <div class="bg-white p-4 rounded-lg shadow flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg w-12 h-12 flex items-center justify-center">
                        <i class="fas fa-route text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Total Trips</p>
                        <p class="text-lg font-bold text-gray-900">{{ $driver->orders->where('status', 'completed')->count() }}</p>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-lg shadow flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg w-12 h-12 flex items-center justify-center">
                        <i class="fas fa-wallet text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Balance</p>
                        <p class="text-lg font-bold text-gray-900">Rp {{ number_format($driver->balance ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-lg shadow flex items-center">
                    <div class="flex-shrink-0 bg-gray-100 rounded-lg w-12 h-12 flex items-center justify-center">
                        <i class="far fa-clock text-gray-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Last Active</p>
                        <p class="text-lg font-bold text-gray-900">{{ $driver->updated_at ? $driver->updated_at->diffForHumans() : 'Never' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="lg:col-span-2">
            <div x-data="{ activeTab: 'vehicle' }" class="bg-white rounded-lg shadow">
                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-6 px-6">
                        <button @click="activeTab = 'vehicle'" 
                                :class="{'border-blue-500 text-blue-600': activeTab === 'vehicle', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'vehicle'}" 
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Vehicle
                        </button>
                        <button @click="activeTab = 'documents'" 
                                :class="{'border-blue-500 text-blue-600': activeTab === 'documents', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'documents'}" 
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Documents
                        </button>
                        <button @click="activeTab = 'orders'" 
                                :class="{'border-blue-500 text-blue-600': activeTab === 'orders', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'orders'}" 
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Order History
                        </button>
                        <button @click="activeTab = 'location'" 
                                :class="{'border-blue-500 text-blue-600': activeTab === 'location', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'location'}" 
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Location
                        </button>
                    </nav>
                </div>

                <!-- Tabs Content -->
                <div class="p-6">
                    <!-- Vehicle Tab -->
                    <div x-show="activeTab === 'vehicle'" class="space-y-6">
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">Vehicle Information</h4>
                            <dl class="mt-4 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 text-sm">
                                <div class="sm:col-span-1">
                                    <dt class="text-gray-500">Vehicle Type</dt>
                                    <dd class="mt-1 text-gray-900 font-medium">{{ $driver->vehicleType->name ?? ucfirst($driver->vehicle_type ?? 'N/A') }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-gray-500">Brand & Model</dt>
                                    <dd class="mt-1 text-gray-900 font-medium">{{ $driver->vehicle_brand ?? 'N/A' }} {{ $driver->vehicle_model ?? '' }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-gray-500">Year</dt>
                                    <dd class="mt-1 text-gray-900 font-medium">{{ $driver->vehicle_year ?? 'N/A' }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-gray-500">License Plate</dt>
                                    <dd class="mt-1 text-gray-900 font-mono font-bold tracking-wider inline-block bg-gray-800 text-white px-2 py-1 rounded">{{ $driver->vehicle_plate ?? 'N/A' }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-gray-500">License Number (SIM)</dt>
                                    <dd class="mt-1 text-gray-900 font-medium">{{ $driver->license_number ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-medium text-gray-900">Emergency Contact</h4>
                            <dl class="mt-4 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 text-sm">
                                <div class="sm:col-span-1">
                                    <dt class="text-gray-500">Name</dt>
                                    <dd class="mt-1 text-gray-900 font-medium">{{ $driver->emergency_contact_name ?? 'N/A' }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-gray-500">Phone</dt>
                                    <dd class="mt-1 text-gray-900 font-medium">{{ $driver->emergency_contact_phone ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div x-show="activeTab === 'documents'">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @php
                                $documentTypes = ['ktp', 'sim', 'stnk', 'photo'];
                                $documentLabels = [
                                    'ktp' => 'KTP (Kartu Tanda Penduduk)',
                                    'sim' => 'SIM (Surat Izin Mengemudi)',
                                    'stnk' => 'STNK (Surat Tanda Nomor Kendaraan)',
                                    'photo' => 'Foto Profile'
                                ];
                            @endphp

                            @foreach($documentTypes as $docType)
                                @php
                                    $document = $driver->documents->where('document_type', $docType)->first();
                                @endphp
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="font-semibold text-gray-800">{{ $documentLabels[$docType] }}</h3>
                                        @if($document)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $document->status == 'approved' ? 'bg-green-100 text-green-800' : ($document->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $document->status == 'approved' ? 'Approved' : ($document->status == 'rejected' ? 'Rejected' : 'Pending') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Not Uploaded
                                            </span>
                                        @endif
                                    </div>

                                    @if($document && $document->document_path)
                                        <div class="mb-3">
                                            <div class="relative group">
                                                @php
                                                    $imagePath = Storage::url($document->document_path);
                                                @endphp
                                                <img src="{{ $imagePath }}" 
                                                     alt="{{ $documentLabels[$docType] }}" 
                                                     class="w-full h-40 object-cover rounded border document-image cursor-pointer"
                                                     onclick="openImageModal('{{ $imagePath }}', '{{ $documentLabels[$docType] }}')"
                                                     onerror="this.parentNode.innerHTML='<div class=\'w-full h-40 bg-gray-100 rounded border flex items-center justify-center\'><i class=\'fas fa-image text-gray-400 text-2xl\'></i></div>'">
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                    <i class="fas fa-search-plus text-white text-2xl"></i>
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Uploaded: {{ $document->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>

                                        @if($document->status == 'pending')
                                            <div class="flex space-x-2">
                                                <form action="{{ route('admin.drivers.documents.approve',[$driver, $document]) }}" method="POST" class="flex-1">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm transition-colors">
                                                        Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.drivers.documents.reject', [$driver, $document]) }}" method="POST" class="flex-1">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm transition-colors"
                                                            onclick="return confirm('Are you sure you want to reject this document?')">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @else
                                        <div class="h-40 bg-gray-50 rounded border flex items-center justify-center">
                                            <div class="text-center">
                                                <i class="fas fa-upload text-gray-400 text-2xl mb-2"></i>
                                                <p class="text-sm text-gray-500">Document not uploaded</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div x-show="activeTab === 'orders'">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500">Order ID</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500">Customer</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500">Status</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500">Amount</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500">Date</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($driver->orders->sortByDesc('created_at')->take(10) as $order)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap font-medium text-blue-600">
                                            #{{ $order->order_code ?? $order->id }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            {{ $order->customer->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900">
                                            Rp {{ number_format($order->fare_amount ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-500">
                                            {{ $order->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-8 text-gray-500">
                                            This driver has no order history.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Location Tab -->
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

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="max-w-4xl max-h-full p-4">
        <div class="bg-white rounded-lg p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold"></h3>
                <button onclick="closeImageModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <img id="modalImage" src="" alt="" class="max-w-full max-h-96 mx-auto">
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Image Modal Functions
    function openImageModal(imageSrc, title) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });

    document.addEventListener('alpine:init', () => {
        @if($driver->current_latitude && $driver->current_longitude)
            const mapElement = document.getElementById('driverMap');
            if (mapElement) {
                const lat = {{ $driver->current_latitude }};
                const lng = {{ $driver->current_longitude }};
                const driverName = "{{ addslashes($driver->user->name ?? 'Driver') }}";

                setTimeout(() => {
                    // Initialize map
                    const map = L.map('driverMap').setView([lat, lng], 15);

                    // Tile Layer
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    // Marker
                    L.marker([lat, lng]).addTo(map)
                        .bindPopup(`<b>${driverName}'s Location</b>`)
                        .openPopup();

                    // Invalidate size when tab becomes active
                    setTimeout(() => {
                        map.invalidateSize();
                    }, 100);
                }, 100);
            }
        @endif
    });
</script>
@endpush