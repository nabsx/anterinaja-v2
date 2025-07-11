@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-start mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detail Pesanan</h1>
                <span class="inline-block px-3 py-1 text-sm rounded-full
                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                       ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                       ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informasi Pesanan -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Informasi Pesanan</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kode Pesanan:</span>
                                <span class="font-medium">{{ $order->order_code }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jenis Layanan:</span>
                                <span class="font-medium">{{ ucfirst($order->order_type) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jarak:</span>
                                <span class="font-medium">{{ $order->distance_km }} km</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Estimasi Waktu:</span>
                                <span class="font-medium">{{ $order->estimated_duration }} menit</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="text-gray-900 font-semibold">Total Tarif:</span>
                                <span class="font-semibold text-green-600">Rp {{ number_format($order->fare_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Driver -->
                @if($order->driver)
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Informasi Driver</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nama:</span>
                                    <span class="font-medium">{{ $order->driver->user->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Telepon:</span>
                                    <span class="font-medium">{{ $order->driver->user->phone }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Kendaraan:</span>
                                    <span class="font-medium">{{ ucfirst($order->driver->vehicle_type) }}</span>
                                </div>
                                @if($order->driver->vehicle_plate)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Plat Nomor:</span>
                                        <span class="font-medium">{{ $order->driver->vehicle_plate }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Rating:</span>
                                    <span class="font-medium">⭐ {{ number_format($order->driver->rating, 1) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Driver</h3>
                            <p class="text-gray-600">Mencari driver...</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Alamat -->
            <div class="mt-6 space-y-4">
                <div>
                    <h3 class="text-lg font-semibold mb-3">Alamat</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-green-600 mb-2">📍 Lokasi Jemput</h4>
                            <p class="text-gray-700">{{ $order->pickup_address }}</p>
                        </div>
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-red-600 mb-2">📍 Lokasi Tujuan</h4>
                            <p class="text-gray-700">{{ $order->destination_address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($order->notes)
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-3">Catatan</h3>
                    <p class="text-gray-700 bg-gray-50 p-3 rounded">{{ $order->notes }}</p>
                </div>
            @endif

            <!-- Timeline -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold mb-3">Timeline</h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Pesanan dibuat: {{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    @if($order->accepted_at)
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Pesanan diterima driver: {{ $order->accepted_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    
                    @if($order->picked_up_at)
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Anda dijemput: {{ $order->picked_up_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    
                    @if($order->completed_at)
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Pesanan selesai: {{ $order->completed_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('customer.orders') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali ke Pesanan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
