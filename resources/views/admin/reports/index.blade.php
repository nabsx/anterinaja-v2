@extends('layouts.admin')

@section('title', 'Reports & Analytics')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
                    <p class="text-gray-600 mt-1">Comprehensive insights and performance metrics</p>
                </div>
                <div class="flex items-center space-x-4">
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>Last 7 days</option>
                        <option>Last 30 days</option>
                        <option>Last 90 days</option>
                        <option>Custom range</option>
                    </select>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-download mr-2"></i>Export All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Report Categories -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <a href="{{ route('admin.reports.orders') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Order Reports</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($order_stats['total_orders']) }}</p>
                        <p class="text-sm text-blue-600 mt-1">Total orders this month</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-200">
                        <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.reports.drivers') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Driver Reports</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($driver_stats['active_drivers']) }}</p>
                        <p class="text-sm text-green-600 mt-1">Active drivers</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors duration-200">
                        <i class="fas fa-motorcycle text-green-600 text-xl"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.reports.customers') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Customer Reports</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($driver_stats['new_drivers']) }}</p>
                        <p class="text-sm text-purple-600 mt-1">New customers</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors duration-200">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.reports.financial') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Financial Reports</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ number_format($financial_stats['revenue']) }}</p>
                        <p class="text-sm text-orange-600 mt-1">Monthly revenue</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors duration-200">
                        <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Performance Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Order Performance -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Performance</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Completed Orders</p>
                                <p class="text-sm text-gray-600">Successfully finished</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-green-600">{{ number_format($order_stats['completed_orders']) }}</p>
                            <p class="text-sm text-gray-500">{{ number_format(($order_stats['completed_orders'] / $order_stats['total_orders']) * 100, 1) }}%</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-times text-red-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Cancelled Orders</p>
                                <p class="text-sm text-gray-600">Customer or driver cancelled</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-red-600">{{ number_format($order_stats['cancelled_orders']) }}</p>
                            <p class="text-sm text-gray-500">{{ number_format(($order_stats['cancelled_orders'] / $order_stats['total_orders']) * 100, 1) }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Breakdown -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Revenue Breakdown</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">Gross Revenue</p>
                            <p class="text-sm text-gray-600">Total customer payments</p>
                        </div>
                        <p class="text-xl font-bold text-blue-600">Rp {{ number_format($financial_stats['revenue']) }}</p>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">Platform Commission</p>
                            <p class="text-sm text-gray-600">15% of gross revenue</p>
                        </div>
                        <p class="text-xl font-bold text-purple-600">Rp {{ number_format($financial_stats['commission']) }}</p>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">Driver Earnings</p>
                            <p class="text-sm text-gray-600">85% of gross revenue</p>
                        </div>
                        <p class="text-xl font-bold text-green-600">Rp {{ number_format($financial_stats['revenue'] - $financial_stats['commission']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analytics -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Performance Analytics</h2>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-lg">Orders</button>
                    <button class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Revenue</button>
                    <button class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Drivers</button>
                </div>
            </div>
            <div class="h-80 bg-gray-50 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-bar text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500 text-lg">Interactive Analytics Chart</p>
                    <p class="text-sm text-gray-400">Chart.js or D3.js integration would display detailed analytics here</p>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Export Reports</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button class="flex items-center justify-center space-x-2 p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors duration-200">
                    <i class="fas fa-file-excel text-green-600"></i>
                    <span class="font-medium text-gray-700">Export to Excel</span>
                </button>
                <button class="flex items-center justify-center space-x-2 p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-red-500 hover:bg-red-50 transition-colors duration-200">
                    <i class="fas fa-file-pdf text-red-600"></i>
                    <span class="font-medium text-gray-700">Export to PDF</span>
                </button>
                <button class="flex items-center justify-center space-x-2 p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors duration-200">
                    <i class="fas fa-file-csv text-blue-600"></i>
                    <span class="font-medium text-gray-700">Export to CSV</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
