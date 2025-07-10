@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detail Pesanan #{{ $order->id }}</h1>
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'accepted' => 'bg-blue-100 text-blue-800',
                        'picking_up' => 'bg-purple-100 text-purple-800',
                        'in_progress' => 'bg-indigo-100 text-indigo-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                    ];
                @endphp
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($order->status) }}
                </span>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Driver Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Driver</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        @if($order->driver)
                            <p><span class="font-medium">Nama:</span> {{ $order->driver->user->name }}</p>
                            <p><span class="font-medium">Telepon:</span> {{ $order->driver->user->phone }}</p>
                            <p><span class="font-medium">Kendaraan:</span> {{ $order->driver->vehicle_brand }} {{ $order->driver->vehicle_model }} ({{ $order->driver->vehicle_plate }})</p>
                            <p><span class="font-medium">Rating:</span> ⭐ {{ number_format($order->driver->rating, 1) }}/5.0</p>
                        @else
                            <p class="text-gray-500">Belum ada driver yang mengambil pesanan</p>
                        @endif
                    </div>
                </div>

                <!-- Order Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Pesanan</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p><span class="font-medium">Jenis Layanan:</span> {{ ucfirst($order->service_type) }}</p>
                        <p><span class="font-medium">Jarak:</span> {{ number_format($order->distance, 1) }} km</p>
                        <p><span class="font-medium">Estimasi Waktu:</span> {{ $order->estimated_duration }} menit</p>
                        <p><span class="font-medium">Tarif:</span> Rp {{ number_format($order->estimated_fare, 0, ',', '.') }}</p>
                        @if($order->total_amount)
                            <p><span class="font-medium">Total Bayar:</span> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pickup & Destination -->
            <div class="mt-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800">Lokasi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-medium text-blue-800 mb-2">Pickup</h4>
                        <p class="text-sm text-blue-700">{{ $order->pickup_address }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="font-medium text-green-800 mb-2">Destination</h4>
                        <p class="text-sm text-green-700">{{ $order->destination_address }}</p>
                    </div>
                </div>
            </div>

            @if($order->notes)
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Catatan</h3>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <p class="text-yellow-800">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Rating Form -->
            @if($order->status === 'completed' && !$order->rating)
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Berikan Rating</h3>
                    <form action="{{ route('customer.orders.rate', $order) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rating (1-5 bintang)</label>
                            <select name="rating" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Pilih Rating</option>
                                <option value="1">⭐ 1 - Sangat Buruk</option>
                                <option value="2">⭐⭐ 2 - Buruk</option>
                                <option value="3">⭐⭐⭐ 3 - Cukup</option>
                                <option value="4">⭐⭐⭐⭐ 4 - Baik</option>
                                <option value="5">⭐⭐⭐⭐⭐ 5 - Sangat Baik</option>
                            </select>
                        </div>
                        <div>
                            <label for="comment" class="block text-sm font-medium text-gray-700">Komentar (opsional)</label>
                            <textarea name="comment" id="comment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Berikan komentar untuk driver..."></textarea>
                        </div>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Kirim Rating
                        </button>
                    </form>
                </div>
            @endif

            <div class="mt-6 flex justify-between">
                <a href="{{ route('customer.orders') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali ke Riwayat
                </a>
                
                @if(in_array($order->status, ['pending', 'accepted']))
                    <form action="{{ route('customer.orders.cancel', $order) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Yakin ingin membatalkan pesanan?')">
                            Batalkan Pesanan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
