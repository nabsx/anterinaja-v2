@extends('layouts.app')

@section('title', 'Cari Driver')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Driver Tersedia</h1>
            
            @if($drivers->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($drivers as $driver)
                        <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 font-bold">{{ substr($driver->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $driver->user->name }}</h3>
                                    <p class="text-sm text-gray-600">⭐ {{ number_format($driver->rating, 1) }}/5.0</p>
                                </div>
                                <div class="ml-auto">
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                        Online
                                    </span>
                                </div>
                            </div>
                            
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex justify-between">
                                    <span class="font-medium">Kendaraan:</span>
                                    <span>{{ ucfirst($driver->vehicle_type) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Total Trip:</span>
                                    <span>{{ $driver->total_trips }}</span>
                                </div>
                                @if($driver->vehicle_brand && $driver->vehicle_model)
                                    <div class="flex justify-between">
                                        <span class="font-medium">Detail:</span>
                                        <span>{{ $driver->vehicle_brand }} {{ $driver->vehicle_model }}</span>
                                    </div>
                                @endif
                                @if($driver->vehicle_plate)
                                    <div class="flex justify-between">
                                        <span class="font-medium">Plat:</span>
                                        <span>{{ $driver->vehicle_plate }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">
                                    @if($driver->is_verified)
                                        ✓ Terverifikasi
                                    @else
                                        ⚠ Belum Terverifikasi
                                    @endif
                                </span>
                                <div class="space-x-2">
                                    @if($driver->user->phone)
                                        <a href="tel:{{ $driver->user->phone }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                            📞 Telepon
                                        </a>
                                    @endif
                                    <a href="{{ route('customer.drivers') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Pesan
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada driver tersedia</h3>
                        <p class="mt-1 text-sm text-gray-500">Saat ini tidak ada driver yang online di area ini.</p>
                    </div>
                </div>
            @endif

            <div class="mt-6 flex justify-between">
                <a href="{{ route('customer.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali ke Dashboard
                </a>
                <button onclick="window.location.reload()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Refresh
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
