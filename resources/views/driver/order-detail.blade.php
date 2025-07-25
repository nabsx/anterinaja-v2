@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Pesanan</h1>
                <p class="text-gray-600">{{ $order->order_code }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'accepted') bg-blue-100 text-blue-800
                    @elseif($order->status === 'driver_arrived') bg-purple-100 text-purple-800
                    @elseif($order->status === 'picked_up') bg-indigo-100 text-indigo-800
                    @elseif($order->status === 'completed') bg-green-100 text-green-800
                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                    @endif">
                    @if($order->status === 'pending') Menunggu Driver
                    @elseif($order->status === 'accepted') Driver Diterima
                    @elseif($order->status === 'driver_arrived') Sampai di Pickup
                    @elseif($order->status === 'picked_up') Dalam Perjalanan
                    @elseif($order->status === 'completed') Selesai
                    @elseif($order->status === 'cancelled') Dibatalkan
                    @endif
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Info -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Customer</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $order->customer->name }}</p>
                                <p class="text-sm text-gray-600">{{ $order->customer->phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Route Info -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Rute Perjalanan</h3>
                    <div class="space-y-4">
                        <!-- Pickup -->
                        <div class="flex items-start">
                            <div class="w-4 h-4 bg-green-500 rounded-full mt-1 mr-3 flex-shrink-0"></div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Lokasi Pickup</p>
                                <p class="text-sm text-gray-600">{{ $order->pickup_address }}</p>
                            </div>
                        </div>

                        <!-- Line -->
                        <div class="flex items-center">
                            <div class="w-4 flex justify-center mr-3">
                                <div class="w-0.5 h-8 bg-gray-300"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">{{ number_format($order->distance_km, 1) }} km • {{ number_format($order->duration_minutes) }} menit</p>
                            </div>
                        </div>

                        <!-- Destination -->
                        <div class="flex items-start">
                            <div class="w-4 h-4 bg-red-500 rounded-full mt-1 mr-3 flex-shrink-0"></div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Tujuan</p>
                                <p class="text-sm text-gray-600">{{ $order->destination_address }}</p>
                            </div>
                        </div>
                    </div>

                    @if($order->notes)
                    <div class="mt-4 p-3 bg-yellow-50 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <span class="font-medium">Catatan:</span> {{ $order->notes }}
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Status Update Buttons -->
                @if(in_array($order->status, ['accepted', 'driver_arrived', 'picked_up']))
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Status</h3>
                    <div class="space-y-3">
                        @if($order->status === 'accepted')
                        <form action="{{ route('driver.orders.update-status', $order) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="driver_arrived">
                            <button type="submit" class="w-full bg-purple-600 text-white px-4 py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors">
                                Sudah Sampai di Lokasi Pickup
                            </button>
                        </form>
                        @endif

                        @if($order->status === 'driver_arrived')
                        <form action="{{ route('driver.orders.update-status', $order) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="picked_up">
                            <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-3 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                                Mulai Perjalanan ke Tujuan
                            </button>
                        </form>
                        @endif

                        @if($order->status === 'picked_up')
                        <form action="{{ route('driver.orders.update-status', $order) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="w-full bg-green-600 text-white px-4 py-3 rounded-lg font-medium hover:bg-green-700 transition-colors">
                                Selesai - Sudah Sampai Tujuan
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jenis Kendaraan</span>
                            <span class="font-medium">
                                {{ $order->vehicle_type === 'motorcycle' ? 'Motor' : 'Mobil' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jarak</span>
                            <span class="font-medium">{{ number_format($order->distance_km, 1) }} km</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Estimasi Waktu</span>
                            <span class="font-medium">{{ number_format($order->duration_minutes) }} menit</span>
                        </div>
                        <hr>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Tarif</span>
                            <span class="font-medium">Rp {{ number_format($order->fare_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-green-600">
                            <span class="font-medium">Pendapatan Driver</span>
                            <span class="font-bold">Rp {{ number_format($order->driver_earning, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mt-1 mr-3 flex-shrink-0"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Pesanan Dibuat</p>
                                <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        @if($order->accepted_at)
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-green-500 rounded-full mt-1 mr-3 flex-shrink-0"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Driver Menerima</p>
                                <p class="text-xs text-gray-500">{{ $order->accepted_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($order->pickup_arrived_at || $order->status === 'driver_arrived')
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-purple-500 rounded-full mt-1 mr-3 flex-shrink-0"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Sampai di Pickup</p>
                                <p class="text-xs text-gray-500">{{ ($order->pickup_arrived_at ?? now())->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($order->started_at || $order->status === 'picked_up')
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-indigo-500 rounded-full mt-1 mr-3 flex-shrink-0"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Perjalanan Dimulai</p>
                                <p class="text-xs text-gray-500">{{ ($order->started_at ?? now())->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($order->completed_at)
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-green-600 rounded-full mt-1 mr-3 flex-shrink-0"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Pesanan Selesai</p>
                                <p class="text-xs text-gray-500">{{ $order->completed_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($order->cancelled_at)
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-red-500 rounded-full mt-1 mr-3 flex-shrink-0"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Pesanan Dibatalkan</p>
                                <p class="text-xs text-gray-500">{{ $order->cancelled_at->format('d M Y, H:i') }}</p>
                                @if($order->cancellation_reason)
                                <p class="text-xs text-gray-600 mt-1">Alasan: {{ $order->cancellation_reason }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-6">
            <a href="{{ route('driver.orders') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Pesanan
            </a>
        </div>
    </div>
</div>
@endsection
