@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-gray-600 mt-1">Welcome back, {{ Auth::user()->name }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500">
                        {{ now()->format('l, F j, Y') }}
                    </div>
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_users']) }}</p>
                        <p class="text-sm text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>
                            +12% from last month
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Drivers -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Drivers</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['active_drivers']) }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            of {{ number_format($stats['total_drivers']) }} total
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-motorcycle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Today's Orders -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Today's Orders</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['today_orders']) }}</p>
                        <p class="text-sm text-blue-600 mt-1">
                            {{ number_format($stats['pending_orders']) }} pending
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">Rp {{ number_format($stats['total_revenue']) }}</p>
                        <p class="text-sm text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>
                            +8% from last month
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Orders -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
                        <a href="{{ route('admin.orders') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            View All
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recent_orders as $order)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">{{ substr($order->customer->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $order->customer->name }}</p>
                                    <p class="text-sm text-gray-600">{{ Str::limit($order->pickup_address, 30) }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($order->status === 'completed') bg-green-100 text-green-800
                                    @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ $order->status_label }}
                                </span>
                                <p class="text-sm font-medium text-gray-900 mt-1">Rp {{ number_format($order->fare_amount) }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-gray-400 text-3xl mb-3"></i>
                            <p class="text-gray-500">No recent orders</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Pending Approvals</h2>
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ $stats['pending_driver_approvals'] }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($pending_drivers as $driver)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 text-sm font-medium">{{ substr($driver->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">{{ $driver->user->name }}</p>
                                    <p class="text-xs text-gray-600">{{ $driver->vehicle_type }}</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <form action="{{ route('admin.drivers.approve', $driver) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-600 hover:text-green-700">
                                        <i class="fas fa-check text-sm"></i>
                                    </button>
                                </form>
                                <a href="{{ route('admin.drivers.show', $driver) }}" class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-6">
                            <i class="fas fa-check-circle text-green-400 text-2xl mb-2"></i>
                            <p class="text-gray-500 text-sm">All caught up!</p>
                        </div>
                        @endforelse
                    </div>
                    @if($stats['pending_driver_approvals'] > 0)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.drivers') }}" class="block text-center text-blue-600 hover:text-blue-700 text-sm font-medium">
                            View All Pending Drivers
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.users') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-200">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Manage Users</p>
                        <p class="text-sm text-gray-600">View and edit users</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.drivers') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors duration-200">
                        <i class="fas fa-motorcycle text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Manage Drivers</p>
                        <p class="text-sm text-gray-600">Approve and verify</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.orders') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors duration-200">
                        <i class="fas fa-shopping-bag text-orange-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">View Orders</p>
                        <p class="text-sm text-gray-600">Monitor all orders</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.reports') }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors duration-200">
                        <i class="fas fa-chart-bar text-purple-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Reports</p>
                        <p class="text-sm text-gray-600">Analytics & insights</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
