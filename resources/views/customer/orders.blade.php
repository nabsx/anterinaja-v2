@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 mb-4 sm:mb-0">My Orders</h1>
        
        <!-- Filter/Search (Optional) -->
        <div class="flex flex-col sm:flex-row gap-2">
            <select class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="accepted">Accepted</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    @if($orders->isEmpty())
    <div class="text-center py-12">
        <div class="mx-auto h-24 w-24 text-gray-400">
            <i class="fas fa-receipt text-6xl"></i>
        </div>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No orders found</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating your first order.</p>
        <div class="mt-6">
            <a href="{{ route('customer.orders.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i>
                New Order
            </a>
        </div>
    </div>
    @else
    
    <!-- Desktop Table View -->
    <div class="hidden lg:block">
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Order Info
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Route
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fare
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->order_code }}</div>
                            <div class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $order->order_type_display }}</div>
                            <div class="text-sm text-gray-500">{{ $order->vehicle_type_display }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ Str::limit($order->pickup_address ?? $order->pickup_location, 30) }}</div>
                            <div class="text-sm text-gray-500">ke {{ Str::limit($order->destination_address ?? $order->destination_location, 30) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'accepted') bg-blue-100 text-blue-800
                                @elseif($order->status === 'in_progress') bg-purple-100 text-purple-800
                                @elseif($order->status === 'completed') bg-green-100 text-green-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            Rp {{ number_format($order->fare_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('customer.orders.show', $order) }}" 
                               class="text-blue-600 hover:text-blue-900">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-4">
        @foreach($orders as $order)
        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            <!-- Card Header -->
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-{{ $order->vehicle_type === 'motorcycle' ? 'motorcycle' : 'car' }} text-blue-600 text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">#{{ $order->order_code }}</div>
                            <div class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status === 'accepted') bg-blue-100 text-blue-800
                        @elseif($order->status === 'in_progress') bg-purple-100 text-purple-800
                        @elseif($order->status === 'completed') bg-green-100 text-green-800
                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $order->status_label }}
                    </span>
                </div>
            </div>

            <!-- Card Body -->
            <div class="px-4 py-4">
                <!-- Service Type -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clipboard-list text-gray-400 text-sm"></i>
                        <span class="text-sm text-gray-600">Layanan:</span>
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ $order->order_type_display }}</span>
                </div>

                <!-- Vehicle Type -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-motorcycle text-gray-400 text-sm"></i>
                        <span class="text-sm text-gray-600">Kendaraan:</span>
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ $order->vehicle_type_display }}</span>
                </div>

                <!-- Route -->
                <div class="mb-3">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $order->pickup_address ?? $order->pickup_location }}
                            </p>
                            <p class="text-xs text-gray-500">Penjemputan</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center ml-3 my-1">
                        <div class="flex-1 border-l-2 border-dashed border-gray-300 h-3"></div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $order->destination_address ?? $order->destination_location }}
                            </p>
                            <p class="text-xs text-gray-500">Tujuan</p>
                        </div>
                    </div>
                </div>

                <!-- Distance and Duration -->
                <div class="flex items-center justify-between mb-3 text-xs text-gray-500">
                    <div class="flex items-center space-x-4">
                        <span><i class="fas fa-route mr-1"></i>{{ number_format($order->distance_km, 1) }} km</span>
                        <span><i class="fas fa-clock mr-1"></i>{{ $order->duration_minutes }} min</span>
                    </div>
                </div>

                <!-- Fare -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-money-bill-wave text-gray-400 text-sm"></i>
                        <span class="text-sm text-gray-600">Tarif:</span>
                    </div>
                    <span class="text-lg font-bold text-blue-600">Rp {{ number_format($order->fare_amount, 0, ',', '.') }}</span>
                </div>

                <!-- Actions -->
                <div class="flex space-x-3">
                    <a href="{{ route('customer.orders.show', $order) }}" 
                       class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md text-sm font-medium hover:bg-blue-700 transition">
                        <i class="fas fa-eye mr-2"></i>Detail
                    </a>
                    
                    @if($order->status === 'pending')
                    <form method="POST" action="{{ route('customer.orders.cancel', $order) }}" class="flex-1">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="w-full bg-red-600 text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-red-700 transition"
                                onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
    <div class="mt-8">
        {{ $orders->links() }}
    </div>
    @endif
    
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Auto refresh for orders with active status
    @if($orders->whereIn('status', ['pending', 'accepted', 'in_progress'])->count() > 0)
    setTimeout(function() {
        location.reload();
    }, 60000); // Refresh every 60 seconds
    @endif
</script>
@endpush