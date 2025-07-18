@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-3xl font-bold text-gray-900">Order Details</h1>
            <nav class="flex mt-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <a href="{{ route('admin.orders') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Orders</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $order->order_code }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.orders') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Orders
            </a>
        </div>
    </div>

    <!-- Order Status Alert -->
    <div class="mb-6">
        @php
            $statusColors = [
                'pending' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
                'accepted' => 'bg-blue-50 border-blue-200 text-blue-800',
                'on_the_way' => 'bg-indigo-50 border-indigo-200 text-indigo-800',
                'completed' => 'bg-green-50 border-green-200 text-green-800',
                'cancelled' => 'bg-red-50 border-red-200 text-red-800'
            ];
            $statusColor = $statusColors[$order->status] ?? 'bg-gray-50 border-gray-200 text-gray-800';
        @endphp
        <div class="border-l-4 p-4 {{ $statusColor }}">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm">
                        <strong>Order Status:</strong> {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        @if($order->status === 'cancelled' && $order->cancellation_reason)
                            <br><span class="text-xs">Reason: {{ $order->cancellation_reason }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Order Info -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Order Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Order Code:</span>
                                <span class="text-sm text-gray-900">{{ $order->order_code }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Order Type:</span>
                                <span class="text-sm text-gray-900">{{ ucfirst($order->order_type) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Vehicle Type:</span>
                                <span class="text-sm text-gray-900">{{ ucfirst($order->vehicle_type) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Distance:</span>
                                <span class="text-sm text-gray-900">{{ $order->distance_km }} km</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Duration:</span>
                                <span class="text-sm text-gray-900">{{ $order->duration_minutes }} minutes</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Created:</span>
                                <span class="text-sm text-gray-900">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            @if($order->scheduled_at)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Scheduled:</span>
                                <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($order->scheduled_at)->format('d M Y, H:i') }}</span>
                            </div>
                            @endif
                            @if($order->accepted_at)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Accepted:</span>
                                <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($order->accepted_at)->format('d M Y, H:i') }}</span>
                            </div>
                            @endif
                            @if($order->picked_up_at)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Picked Up:</span>
                                <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($order->picked_up_at)->format('d M Y, H:i') }}</span>
                            </div>
                            @endif
                            @if($order->completed_at)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Completed:</span>
                                <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($order->completed_at)->format('d M Y, H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($order->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Notes:</h4>
                        <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Location Information -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Location Details</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <h4 class="text-sm font-medium text-green-700">Pickup Location</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">{{ $order->pickup_address }}</p>
                            <p class="text-xs text-gray-400">
                                Lat: {{ $order->pickup_latitude }}, Lng: {{ $order->pickup_longitude }}
                            </p>
                        </div>
                        <div>
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/>
                                </svg>
                                <h4 class="text-sm font-medium text-red-700">Destination</h4>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">{{ $order->destination_address }}</p>
                            <p class="text-xs text-gray-400">
                                Lat: {{ $order->destination_latitude }}, Lng: {{ $order->destination_longitude }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fare Breakdown -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Fare Breakdown</h3>
                </div>
                <div class="px-6 py-4">
                    @if($order->fare_breakdown)
                        @php $breakdown = is_string($order->fare_breakdown) ? json_decode($order->fare_breakdown, true) : $order->fare_breakdown; @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <div class="flex justify-between py-1">
                                    <span class="text-sm text-gray-600">Base Fare:</span>
                                    <span class="text-sm font-medium text-gray-900">Rp {{ number_format($breakdown['base_fare'] ?? 0) }}</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-sm text-gray-600">Distance Fare:</span>
                                    <span class="text-sm font-medium text-gray-900">Rp {{ number_format($breakdown['distance_fare'] ?? 0) }}</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-sm text-gray-600">Time Fare:</span>
                                    <span class="text-sm font-medium text-gray-900">Rp {{ number_format($breakdown['time_fare'] ?? 0) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-t border-gray-200">
                                    <span class="text-sm font-medium text-gray-900">Subtotal:</span>
                                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($breakdown['subtotal'] ?? 0) }}</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between py-1">
                                    <span class="text-sm text-gray-600">Platform Commission:</span>
                                    <span class="text-sm font-medium text-gray-900">Rp {{ number_format($order->platform_commission) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-t border-gray-200">
                                    <span class="text-sm font-medium text-gray-900">Total Fare:</span>
                                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($order->fare_amount) }}</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-sm font-medium text-green-600">Driver Earning:</span>
                                    <span class="text-sm font-bold text-green-600">Rp {{ number_format($order->driver_earning) }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="space-y-2">
                            <div class="flex justify-between py-1">
                                <span class="text-sm text-gray-600">Total Fare:</span>
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($order->fare_amount) }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-sm text-gray-600">Platform Commission:</span>
                                <span class="text-sm font-medium text-gray-900">Rp {{ number_format($order->platform_commission) }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-sm font-medium text-green-600">Driver Earning:</span>
                                <span class="text-sm font-bold text-green-600">Rp {{ number_format($order->driver_earning) }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Tracking -->
            @if($order->tracking && $order->tracking->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Order Tracking</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach($order->tracking->sortBy('created_at') as $index => $track)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 capitalize">{{ $track->status }}</p>
                                                @if($track->notes)
                                                <p class="text-sm text-gray-500">{{ $track->notes }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $track->created_at->format('d M Y, H:i:s') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- Ratings -->
            @if($order->ratings && $order->ratings->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Ratings & Reviews</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    @foreach($order->ratings as $rating)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-sm font-medium text-gray-900">
                                {{ $rating->rater_type === 'customer' ? 'Customer Rating' : 'Driver Rating' }}
                            </h4>
                            <div class="flex items-center">
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $rating->rating)
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="ml-2 text-sm text-gray-600">{{ $rating->rating }}/5</span>
                            </div>
                        </div>
                        @if($rating->comment)
                        <p class="text-sm text-gray-600 mb-2">{{ $rating->comment }}</p>
                        @endif
                        <p class="text-xs text-gray-400">{{ $rating->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Customer Information -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Customer Information</h3>
                </div>
                <div class="px-6 py-4">
                    @if($order->customer)
                    <div class="text-center mb-4">
                        <div class="mx-auto h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center text-white text-xl font-bold mb-2">
                            {{ strtoupper(substr($order->customer->name, 0, 2)) }}
                        </div>
                        <h4 class="text-lg font-medium text-gray-900">{{ $order->customer->name }}</h4>
                        <p class="text-sm text-gray-500">Customer</p>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm text-gray-900">{{ $order->customer->email }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-sm text-gray-900">{{ $order->customer->phone }}</span>
                        </div>
                        @if($order->customer->address)
                        <div class="flex items-start">
                            <svg class="w-4 h-4 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-sm text-gray-900">{{ $order->customer->address }}</span>
                        </div>
                        @endif
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-9 0v10a2 2 0 002 2h8a2 2 0 002-2V7H7z"/>
                            </svg>
                            <span class="text-sm text-gray-900">Joined {{ $order->customer->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.users.show', $order->customer->id) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            View Profile
                        </a>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Customer information not available</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Driver Information -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Driver Information</h3>
                </div>
                <div class="px-6 py-4">
                    @if($order->driver)
                    <div class="text-center mb-4">
                        <div class="mx-auto h-16 w-16 rounded-full bg-green-500 flex items-center justify-center text-white text-xl font-bold mb-2">
                            {{ strtoupper(substr($order->driver->user->name, 0, 2)) }}
                        </div>
                        <h4 class="text-lg font-medium text-gray-900">{{ $order->driver->user->name }}</h4>
                        <p class="text-sm text-gray-500">Driver</p>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm text-gray-900">{{ $order->driver->user->email }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-sm text-gray-900">{{ $order->driver->user->phone }}</span>
                        </div>
                        @if($order->driver->license_number)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                            <span class="text-sm text-gray-900">{{ $order->driver->license_number }}</span>
                        </div>
                        @endif
                        @if($order->driver->vehicle_plate)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                            <span class="text-sm text-gray-900">{{ $order->driver->vehicle_plate }}</span>
                        </div>
                        @endif
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-sm text-gray-900">{{ number_format($order->driver->rating, 1) }}/5.0</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.drivers.show', $order->driver->id) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            View Profile
                        </a>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No driver assigned</p>
                        @if($order->status === 'pending')
                        <p class="text-xs text-gray-400">Waiting for driver acceptance</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    @if($order->status === 'pending')
                    <button onclick="updateOrderStatus('accepted')" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Mark as Accepted
                    </button>
                    <button onclick="cancelOrder()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancel Order
                    </button>
                    @elseif($order->status === 'accepted')
                    <button onclick="updateOrderStatus('on_the_way')" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Mark as On The Way
                    </button>
                    @elseif($order->status === 'on_the_way')
                    <button onclick="updateOrderStatus('completed')" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Mark as Completed
                    </button>
                    @endif
                    
                    <button onclick="printOrder()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print Order
                    </button>
                    <button onclick="exportOrder()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Actions -->
<script>
function updateOrderStatus(status) {
    if (confirm('Are you sure you want to update the order status to ' + status + '?')) {
        fetch(`/admin/orders/{{ $order->id }}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating order status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating order status');
        });
    }
}

function cancelOrder() {
    const reason = prompt('Please enter cancellation reason:');
    if (reason) {
        fetch(`/admin/orders/{{ $order->id }}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error cancelling order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error cancelling order');
        });
    }
}

function printOrder() {
    window.print();
}

function exportOrder() {
    window.location.href = `/admin/orders/{{ $order->id }}/export`;
}
</script>
@endsection
