@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detail Pesanan</h1>
                <span class="inline-block px-3 py-1 text-sm rounded-full font-medium
                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                       ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                       ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                       ($order->status === 'accepted' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800'))) }}">
                    @switch($order->status)
                        @case('pending')
                            Menunggu Driver
                            @break
                        @case('accepted')
                            Diterima Driver
                            @break
                        @case('in_progress')
                            Sedang Berlangsung
                            @break
                        @case('completed')
                            Selesai
                            @break
                        @case('cancelled')
                            Dibatalkan
                            @break
                        @default
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    @endswitch
                </span>
            </div>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <!-- Order Information Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column - Order Details -->
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">
                            <i class="fas fa-info-circle mr-2 text-blue-500"></i>Informasi Pesanan
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Kode Pesanan:</span>
                                <span class="font-medium text-gray-900">{{ $order->order_code }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Jenis Layanan:</span>
                                <span class="font-medium text-gray-900">
                                    @if($order->order_type === 'ride')
                                        <i class="fas fa-user mr-1"></i>Perjalanan
                                    @else
                                        <i class="fas fa-box mr-1"></i>{{ ucfirst($order->order_type) }}
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Jenis Kendaraan:</span>
                                <span class="font-medium text-gray-900">
                                    @switch($order->vehicle_type)
                                        @case('motorcycle')
                                            <i class="fas fa-motorcycle mr-1"></i>Motor
                                            @break
                                        @case('car')
                                            <i class="fas fa-car mr-1"></i>Mobil
                                            @break
                                        @case('van')
                                            <i class="fas fa-truck mr-1"></i>Van
                                            @break
                                        @case('truck')
                                            <i class="fas fa-truck mr-1"></i>Truk
                                            @break
                                        @default
                                            <i class="fas fa-motorcycle mr-1"></i>{{ ucfirst($order->vehicle_type ?? 'Motor') }}
                                    @endswitch
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Jarak:</span>
                                <span class="font-medium text-gray-900">
                                    <i class="fas fa-route mr-1"></i>{{ number_format($order->distance_km, 2) }} km
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Estimasi Durasi:</span>
                                <span class="font-medium text-gray-900">
                                    <i class="fas fa-clock mr-1"></i>{{ $order->duration_minutes }} menit
                                </span>
                            </div>
                            <div class="flex justify-between items-center border-t pt-3">
                                <span class="text-gray-900 font-semibold">Total Tarif:</span>
                                <span class="font-bold text-lg text-green-600">
                                    <i class="fas fa-money-bill-wave mr-1"></i>Rp {{ number_format($order->fare_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Driver Information -->
                <div class="space-y-4">
                    @if($order->driver)
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold mb-3 text-gray-800">
                                <i class="fas fa-user-tie mr-2 text-blue-500"></i>Informasi Driver
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Nama:</span>
                                    <span class="font-medium text-gray-900">{{ $order->driver->user->name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Telepon:</span>
                                    <span class="font-medium text-gray-900">
                                        <a href="tel:{{ $order->driver->user->phone }}" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-phone mr-1"></i>{{ $order->driver->user->phone }}
                                        </a>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Kendaraan:</span>
                                    <span class="font-medium text-gray-900">{{ ucfirst($order->driver->vehicle_type) }}</span>
                                </div>
                                @if($order->driver->vehicle_plate)
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Plat Nomor:</span>
                                        <span class="font-medium text-gray-900 bg-white px-2 py-1 rounded border">
                                            {{ $order->driver->vehicle_plate }}
                                        </span>
                                    </div>
                                @endif
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Rating:</span>
                                    <span class="font-medium text-gray-900">
                                        <span class="text-yellow-500">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($order->driver->rating))
                                                    <i class="fas fa-star"></i>
                                                @elseif($i - 0.5 <= $order->driver->rating)
                                                    <i class="fas fa-star-half-alt"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </span>
                                        <span class="ml-1">{{ number_format($order->driver->rating, 1) }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @elseif($order->status === 'cancelled')
                        <div class="bg-red-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold mb-3 text-gray-800">
                                <i class="fas fa-times-circle mr-2 text-red-500"></i>Pesanan Dibatalkan
                            </h3>
                            <div class="text-center py-4">
                                <i class="fas fa-ban text-3xl text-red-500 mb-3"></i>
                                <p class="text-gray-700 font-medium">Pesanan ini telah dibatalkan</p>
                                @if($order->cancellation_reason)
                                    <p class="text-sm text-red-600 mt-2">{{ $order->cancellation_reason }}</p>
                                @endif
                                @if($order->cancelled_at)
                                    <p class="text-xs text-gray-500 mt-1">{{ $order->cancelled_at->format('d M Y, H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    @elseif($order->status === 'completed')
                        <div class="bg-green-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold mb-3 text-gray-800">
                                <i class="fas fa-check-circle mr-2 text-green-500"></i>Pesanan Selesai
                            </h3>
                            <div class="text-center py-4">
                                <i class="fas fa-flag-checkered text-3xl text-green-500 mb-3"></i>
                                <p class="text-gray-700 font-medium">Perjalanan telah selesai</p>
                                @if($order->completed_at)
                                    <p class="text-xs text-gray-500 mt-1">{{ $order->completed_at->format('d M Y, H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold mb-3 text-gray-800">
                                <i class="fas fa-search mr-2 text-yellow-500"></i>Driver
                            </h3>
                            <div class="text-center py-4">
                                <i class="fas fa-spinner fa-spin text-2xl text-yellow-500 mb-2"></i>
                                <p class="text-gray-600">Mencari driver terdekat...</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Addresses -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">
                <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>Alamat
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border-2 border-green-200 rounded-lg p-4 bg-green-50">
                    <h4 class="font-medium text-green-700 mb-2 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2"></i>Lokasi Jemput
                    </h4>
                    <p class="text-gray-700">{{ $order->pickup_address }}</p>
                </div>
                <div class="border-2 border-red-200 rounded-lg p-4 bg-red-50">
                    <h4 class="font-medium text-red-700 mb-2 flex items-center">
                        <i class="fas fa-flag-checkered mr-2"></i>Lokasi Tujuan
                    </h4>
                    <p class="text-gray-700">{{ $order->destination_address }}</p>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($order->notes)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold mb-3 text-gray-800">
                    <i class="fas fa-sticky-note mr-2 text-blue-500"></i>Catatan
                </h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700">{{ $order->notes }}</p>
                </div>
            </div>
        @endif

        <!-- Timeline -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">
                <i class="fas fa-history mr-2 text-purple-500"></i>Timeline Pesanan
            </h3>
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="w-4 h-4 bg-blue-500 rounded-full flex-shrink-0"></div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">Pesanan Dibuat</p>
                        <p class="text-sm text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                
                @if($order->accepted_at)
                    <div class="flex items-center space-x-4">
                        <div class="w-4 h-4 bg-yellow-500 rounded-full flex-shrink-0"></div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">Diterima Driver</p>
                            <p class="text-sm text-gray-600">{{ $order->accepted_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                @endif
                
                @if($order->picked_up_at)
                    <div class="flex items-center space-x-4">
                        <div class="w-4 h-4 bg-purple-500 rounded-full flex-shrink-0"></div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">Penjemputan</p>
                            <p class="text-sm text-gray-600">{{ $order->picked_up_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                @endif
                
                @if($order->completed_at)
                    <div class="flex items-center space-x-4">
                        <div class="w-4 h-4 bg-green-500 rounded-full flex-shrink-0"></div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">Pesanan Selesai</p>
                            <p class="text-sm text-gray-600">{{ $order->completed_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if($order->cancelled_at)
                    <div class="flex items-center space-x-4">
                        <div class="w-4 h-4 bg-red-500 rounded-full flex-shrink-0"></div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">Pesanan Dibatalkan</p>
                            <p class="text-sm text-gray-600">{{ $order->cancelled_at->format('d M Y, H:i') }}</p>
                            @if($order->cancellation_reason)
                                <p class="text-sm text-red-600">Alasan: {{ $order->cancellation_reason }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('customer.orders') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Pesanan
                </a>
                
                @if($order->canBeCancelled())
                    <form method="POST" action="{{ route('customer.orders.cancel', $order->id) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')"
                                class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                            <i class="fas fa-times mr-2"></i>Batalkan Pesanan
                        </button>
                    </form>
                @endif

                @if($order->canBeRated())
                    <a href="{{ route('customer.orders.rate', $order->id) }}" 
                       class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                        <i class="fas fa-star mr-2"></i>Beri Rating
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto refresh page every 30 seconds if order is not completed
    @if(in_array($order->status, ['pending', 'accepted', 'in_progress']))
        setInterval(function() {
            location.reload();
        }, 30000);
    @endif
</script>
@endpush
