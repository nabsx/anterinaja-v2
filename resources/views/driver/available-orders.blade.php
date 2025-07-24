@extends('layouts.app')

@section('title', 'Pesanan Tersedia')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pesanan Tersedia</h1>
        <a href="{{ route('driver.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
            Kembali ke Dashboard
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($orders->count() > 0)
        <div class="grid gap-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">
                                Pesanan #{{ $order->id }}
                                <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">
                                    {{ $order->status_label }}
                                </span>
                            </h3>
                            <p class="text-sm text-gray-600">
                                <strong>Customer:</strong> {{ $order->customer->name }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-green-600">
                                Total: {{ $order->formatted_fare }}
                            </p>
                            <p class="text-sm text-gray-600">
                                Anda terima: {{ $order->formatted_driver_earning }}
                            </p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Pickup:</p>
                            <p class="text-sm text-gray-600 mb-2">
                                <i class="fas fa-map-marker-alt text-green-500 mr-1"></i>
                                {{ Str::limit($order->pickup_address, 60) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Destination:</p>
                            <p class="text-sm text-gray-600 mb-2">
                                <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                                {{ Str::limit($order->destination_address, 60) }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-sm">
                        <div>
                            <p class="text-gray-600">Jarak:</p>
                            <p class="font-semibold">{{ $order->formatted_distance }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Estimasi:</p>
                            <p class="font-semibold">{{ $order->formatted_duration }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Kendaraan:</p>
                            <p class="font-semibold capitalize">{{ $order->vehicle_type }}</p>
                        </div>
                        @if(isset($order->distance_from_driver))
                        <div>
                            <p class="text-gray-600">Jarak dari Anda:</p>
                            <p class="font-semibold">{{ number_format($order->distance_from_driver, 1) }} km</p>
                        </div>
                        @endif
                    </div>

                    @if($order->notes)
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-700 mb-1">Catatan:</p>
                            <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                <p class="text-sm text-gray-700">{{ $order->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-between items-center">
                        <p class="text-xs text-gray-500">
                            {{ $order->created_at->diffForHumans() }}
                        </p>
                        
                        <form action="{{ route('driver.orders.accept', $order) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" 
                                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium transition duration-200"
                                    onclick="return confirm('Apakah Anda yakin ingin menerima pesanan ini?')">
                                Terima
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Auto refresh every 30 seconds -->
        <script>
            setTimeout(function() {
                window.location.reload();
            }, 30000);
        </script>
    @else
        <div class="bg-gray-50 rounded-lg p-8 text-center">
            <div class="mb-4">
                <i class="fas fa-inbox text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-800 mb-2">Tidak Ada Pesanan Tersedia</h3>
            <p class="text-gray-600 mb-4">
                Saat ini tidak ada pesanan yang tersedia di area Anda. 
                Pastikan status Anda online dan lokasi GPS aktif.
            </p>
            <div class="space-y-2 text-sm text-gray-500">
                <p>• Pastikan Anda sudah terverifikasi</p>
                <p>• Aktifkan lokasi GPS</p>
                <p>• Ubah status menjadi online</p>
            </div>
            
            <div class="mt-6">
                <button onclick="window.location.reload()" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
            </div>
        </div>
    @endif
</div>

<style>
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .grid {
        grid-template-columns: 1fr;
    }
    
    .text-lg {
        font-size: 1rem;
    }
}
</style>
@endsection
