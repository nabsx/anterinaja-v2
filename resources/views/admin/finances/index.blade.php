@extends('layouts.admin')

@section('title', 'Keuangan')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <h2 class="text-2xl font-semibold mb-6">Dashboard Keuangan</h2>

    <!-- Statistik Keuangan -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-blue-600 text-white rounded-xl p-4 shadow">
            <h5 class="text-sm">Total Pendapatan</h5>
            <h3 class="text-xl font-bold">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
        </div>
        <div class="bg-green-600 text-white rounded-xl p-4 shadow">
            <h5 class="text-sm">Total Komisi</h5>
            <h3 class="text-xl font-bold">Rp {{ number_format($stats['total_commission'], 0, ',', '.') }}</h3>
        </div>
        <div class="bg-cyan-600 text-white rounded-xl p-4 shadow">
            <h5 class="text-sm">Penghasilan Driver</h5>
            <h3 class="text-xl font-bold">Rp {{ number_format($stats['driver_earnings'], 0, ',', '.') }}</h3>
        </div>
        <div class="bg-yellow-500 text-white rounded-xl p-4 shadow">
            <h5 class="text-sm">Pending Payouts</h5>
            <h3 class="text-xl font-bold">Rp {{ number_format($stats['pending_payouts'], 0, ',', '.') }}</h3>
        </div>
        <div class="bg-gray-600 text-white rounded-xl p-4 shadow">
            <h5 class="text-sm">Pendapatan Hari Ini</h5>
            <h3 class="text-xl font-bold">Rp {{ number_format($stats['today_revenue'], 0, ',', '.') }}</h3>
        </div>
        <div class="bg-black text-white rounded-xl p-4 shadow">
            <h5 class="text-sm">Pendapatan Bulan Ini</h5>
            <h3 class="text-xl font-bold">Rp {{ number_format($stats['this_month_revenue'], 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Top Drivers & Transaksi Terbaru -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Top Driver -->
        <div class="bg-white rounded-xl shadow p-4">
            <h4 class="text-lg font-semibold mb-4">Top Driver (Penghasilan Tertinggi)</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="py-2 px-3">No</th>
                            <th class="py-2 px-3">Nama Driver</th>
                            <th class="py-2 px-3">Total Penghasilan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($top_drivers as $index => $driver)
                        <tr class="border-t">
                            <td class="py-2 px-3">{{ $index + 1 }}</td>
                            <td class="py-2 px-3">
                                <div class="font-semibold">{{ $driver->user->name }}</div>
                                <div class="text-gray-500 text-xs">{{ $driver->user->email }}</div>
                            </td>
                            <td class="py-2 px-3">
                                <span class="inline-block bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">
                                    Rp {{ number_format($driver->orders_sum_driver_earning ?? $driver->total_earnings ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">Tidak ada data driver</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transaksi Terbaru -->
        <div class="bg-white rounded-xl shadow p-4">
            <h4 class="text-lg font-semibold mb-4">Transaksi Terbaru</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="py-2 px-3">Kode Order</th>
                            <th class="py-2 px-3">Customer</th>
                            <th class="py-2 px-3">Driver</th>
                            <th class="py-2 px-3">Total</th>
                            <th class="py-2 px-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_transactions as $transaction)
                        <tr class="border-t">
                            <td class="py-2 px-3 font-semibold">{{ $transaction->order_code }}</td>
                            <td class="py-2 px-3">{{ $transaction->customer->name ?? 'N/A' }}</td>
                            <td class="py-2 px-3">{{ $transaction->driver->user->name ?? 'N/A' }}</td>
                            <td class="py-2 px-3">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">
                                    Rp {{ number_format($transaction->actual_fare, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="py-2 px-3">
                                {{ $transaction->completed_at ? $transaction->completed_at->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Tidak ada transaksi terbaru</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Ringkasan Keuangan -->
    <div class="mt-10">
        <h4 class="text-lg font-semibold mb-4">Ringkasan Keuangan</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-center bg-white rounded-xl shadow p-4">
                <div class="w-12 h-12 flex items-center justify-center bg-blue-100 text-blue-600 rounded-full mr-4">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Total Pendapatan</div>
                    <div class="text-base font-bold">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="flex items-center bg-white rounded-xl shadow p-4">
                <div class="w-12 h-12 flex items-center justify-center bg-green-100 text-green-600 rounded-full mr-4">
                    <i class="fas fa-percentage"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Komisi Platform</div>
                    <div class="text-base font-bold">Rp {{ number_format($stats['total_commission'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="flex items-center bg-white rounded-xl shadow p-4">
                <div class="w-12 h-12 flex items-center justify-center bg-cyan-100 text-cyan-600 rounded-full mr-4">
                    <i class="fas fa-car"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Penghasilan Driver</div>
                    <div class="text-base font-bold">Rp {{ number_format($stats['driver_earnings'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="flex items-center bg-white rounded-xl shadow p-4">
                <div class="w-12 h-12 flex items-center justify-center bg-yellow-100 text-yellow-600 rounded-full mr-4">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Pending Payouts</div>
                    <div class="text-base font-bold">Rp {{ number_format($stats['pending_payouts'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
