@extends('layouts.app')

@section('title', 'Pesanan Tersedia')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Pesanan Tersedia</h1>
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(isset($orders) && $orders->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($orders as $order)
                        <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="font-semibold text-gray-800">Pesanan #{{ $order->id }}</h3>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                    {{ ucfirst($order->service_type) }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div>
                                    <span class="font-medium">Customer:</span>
                                    <span>{{ $order->customer->name }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Pickup:</span>
                                    <p class="text-xs">{{ Str::limit($order->pickup_address, 50) }}</p>
                                </div>
                                <div>
                                    <span class="font-medium">Destination:</span>
                                    <p class="text-xs">{{ Str::limit($order->destination_address, 50) }}</p>
                                </div>
                                <div>
                                    <span class="font-medium">Jarak:</span>
                                    <span>{{ number_format($order->distance, 1) }} km</span>
                                </div>
                                <div>
                                    <span class="font-medium">Estimasi:</span>
                                    <span>{{ $order->estimated_duration }} menit</span>
                                </div>
                                <div class="text-green-600 font-semibold">
                                    Total: Rp {{ number_format($order->estimated_fare, 0, ',', '.') }}
                                </div>
                            </div>

                            @if($order->notes)
                                <div class="mb-4">
                                    <span class="font-medium text-sm">Catatan:</span>
                                    <p class="text-xs text-gray-600 bg-yellow-50 p-2 rounded">{{ $order->notes }}</p>
                                </div>
                            @endif

                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">
                                    {{ $order->created_at->diffForHumans() }}
                                </span>
                                <form action="{{ route('driver.orders.accept', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm" onclick="return confirm('Terima pesanan ini?')">
                                        Terima
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pesanan tersedia</h3>
                        <p class="mt-1 text-sm text-gray-500">Saat ini belum ada pesanan yang bisa Anda terima.</p>
                    </div>
                </div>
            @endif

            <div class="mt-6 flex justify-between">
                <a href="{{ route('driver.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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
