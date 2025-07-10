@extends('layouts.app')

@section('title', 'Dashboard Customer - AnterinAja')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="h-16 w-16 rounded-full">
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold text-gray-900">Selamat datang, {{ $user->name }}!</h1>
                        <p class="text-gray-600">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <a href="{{ route('customer.book') }}" class="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-lg shadow-lg transition transform hover:scale-105">
                <div class="flex items-center">
                    <i class="fas fa-motorcycle text-3xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-semibold">Pesan Ojek</h3>
                        <p class="text-blue-100">Pesan ojek sekarang</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('customer.orders') }}" class="bg-green-600 hover:bg-green-700 text-white p-6 rounded-lg shadow-lg transition transform hover:scale-105">
                <div class="flex items-center">
                    <i class="fas fa-list text-3xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-semibold">Riwayat Pesanan</h3>
                        <p class="text-green-100">Lihat pesanan Anda</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('customer.drivers') }}" class="bg-purple-600 hover:bg-purple-700 text-white p-6 rounded-lg shadow-lg transition transform hover:scale-105">
                <div class="flex items-center">
                    <i class="fas fa-map-marker-alt text-3xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-semibold">Cari Driver</h3>
                        <p class="text-purple-100">Driver terdekat</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shopping-cart text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Pesanan</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $totalOrders }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pesanan Selesai</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $completedOrders }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-2xl text-yellow-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pesanan Aktif</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $activeOrders }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Pesanan Terbaru</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">5 pesanan terakhir Anda</p>
            </div>
            
            @if($recentOrders->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($recentOrders as $order)
                        <li>
                            <a href="{{ route('customer.orders.show', $order) }}" class="block hover:bg-gray-50">
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                @if($order->status === 'completed')
                                                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                                @elseif($order->status === 'cancelled')
                                                    <i class="fas fa-times-circle text-red-500 text-xl"></i>
                                                @else
                                                    <i class="fas fa-clock text-yellow-500 text-xl"></i>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $order->pickup_address }} → {{ $order->destination_address }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $order->created_at->format('d M Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($order->status === 'completed') bg-green-100 text-green-800
                                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                            <span class="ml-2 text-sm font-medium text-gray-900">
                                                Rp {{ number_format($order->total_fare, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="px-4 py-12 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Belum ada pesanan.</p>
                    <a href="{{ route('customer.book') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Pesan Sekarang
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
