@extends('layouts.app')

@section('title', 'Dashboard Driver - AnterinAja')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Header with Online Status -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <img src="{{ $user->avatar_url }}" alt="Avatar" class="h-16 w-16 rounded-full">
                        <div class="ml-4">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                            <p class="text-gray-600">Driver {{ $driver->vehicle_info }}</p>
                            <div class="flex items-center mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($driver->is_verified) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                    @if($driver->is_verified) 
                                        <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                                    @else 
                                        <i class="fas fa-times-circle mr-1"></i> Belum Terverifikasi
                                    @endif
                                </span>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($driver->is_online) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                                    @if($driver->is_online) 
                                        <i class="fas fa-circle mr-1 text-green-500"></i> Online
                                    @else 
                                        <i class="fas fa-circle mr-1 text-gray-500"></i> Offline
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Online Toggle -->
                    <div class="flex items-center space-x-4">
                        @if($driver->is_verified)
                            <form method="POST" action="{{ route('driver.status.update') }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_online" value="{{ $driver->is_online ? '0' : '1' }}">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white 
                                    @if($driver->is_online) bg-red-600 hover:bg-red-700 @else bg-green-600 hover:bg-green-700 @endif">
                                    @if($driver->is_online)
                                        <i class="fas fa-pause mr-2"></i> Go Offline
                                    @else
                                        <i class="fas fa-play mr-2"></i> Go Online
                                    @endif
                                </button>
                            </form>
                        @else
                            <a href="{{ route('driver.documents') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-upload mr-2"></i> Lengkapi Dokumen
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <a href="{{ route('driver.available.orders') }}" class="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-lg shadow-lg transition transform hover:scale-105">
                <div class="flex items-center">
                    <i class="fas fa-list-alt text-3xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-semibold">Pesanan Tersedia</h3>
                        <p class="text-blue-100">Lihat pesanan baru</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('driver.orders') }}" class="bg-green-600 hover:bg-green-700 text-white p-6 rounded-lg shadow-lg transition transform hover:scale-105">
                <div class="flex items-center">
                    <i class="fas fa-history text-3xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-semibold">Riwayat</h3>
                        <p class="text-green-100">Pesanan saya</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('driver.earnings') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white p-6 rounded-lg shadow-lg transition transform hover:scale-105">
                <div class="flex items-center">
                    <i class="fas fa-money-bill-wave text-3xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-semibold">Pendapatan</h3>
                        <p class="text-yellow-100">Lihat earning</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('driver.documents') }}" class="bg-purple-600 hover:bg-purple-700 text-white p-6 rounded-lg shadow-lg transition transform hover:scale-105">
                <div class="flex items-center">
                    <i class="fas fa-file-alt text-3xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-semibold">Dokumen</h3>
                        <p class="text-purple-100">Upload dokumen</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Trip</dt>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Trip Selesai</dt>
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
                            <i class="fas fa-star text-2xl text-yellow-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Rating</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $driver->formatted_rating }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-wallet text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Saldo</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $driver->formatted_balance }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Earnings -->
        <div class="bg-gradient-to-r from-green-400 to-blue-500 rounded-lg shadow-lg p-6 mb-6">
            <div class="text-white">
                <h3 class="text-lg font-semibold mb-2">Pendapatan Hari Ini</h3>
                <div class="text-3xl font-bold">Rp {{ number_format($todayEarnings, 0, ',', '.') }}</div>
                <p class="text-green-100 mt-1">Keep up the good work!</p>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Trip Terbaru</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">5 trip terakhir Anda</p>
            </div>
            
            @if($recentOrders->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($recentOrders as $order)
                        <li>
                            <a href="{{ route('driver.orders.show', $order) }}" class="block hover:bg-gray-50">
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
                                                    {{ $order->customer->name ?? 'Customer' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
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
                                                Rp {{ number_format($order->driver_earning ?? $order->total_fare, 0, ',', '.') }}
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
                    <i class="fas fa-car text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Belum ada trip.</p>
                    @if($driver->is_verified)
                        <a href="{{ route('driver.available.orders') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Cari Pesanan
                        </a>
                    @else
                        <a href="{{ route('driver.documents') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Lengkapi Dokumen
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
