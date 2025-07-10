@extends('layouts.app')

@section('title', 'Pendapatan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Pendapatan</h1>
            
            <!-- Earnings Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-blue-800 mb-2">Hari Ini</h3>
                    <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($todayEarnings, 0, ',', '.') }}</p>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-green-800 mb-2">Minggu Ini</h3>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($thisWeekEarnings, 0, ',', '.') }}</p>
                </div>
                
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-yellow-800 mb-2">Bulan Ini</h3>
                    <p class="text-2xl font-bold text-yellow-600">Rp {{ number_format($thisMonthEarnings, 0, ',', '.') }}</p>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-purple-800 mb-2">Total</h3>
                    <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Driver Balance -->
            <div class="bg-gray-50 p-6 rounded-lg mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Saldo Tersedia</h3>
                        <p class="text-3xl font-bold text-gray-900 mt-2">Rp {{ number_format($driver->balance, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        @if($driver->balance >= 100000)
                            <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Tarik Saldo
                            </button>
                        @else
                            <span class="text-sm text-gray-500">Minimum penarikan Rp 100.000</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Total Trip</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $driver->total_trips }}</p>
                </div>
                
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Rating</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($driver->rating, 1) }}/5.0</p>
                </div>
                
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Status</h3>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $driver->is_online ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $driver->is_online ? 'Online' : 'Offline' }}
                    </span>
                </div>
            </div>

            <!-- Earnings Tips -->
            <div class="bg-blue-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-3">Tips Meningkatkan Pendapatan</h3>
                <ul class="list-disc list-inside text-blue-700 space-y-1">
                    <li>Aktif pada jam sibuk (07:00-09:00 dan 17:00-19:00)</li>
                    <li>Jaga rating dengan memberikan pelayanan terbaik</li>
                    <li>Lengkapi profil dan verifikasi dokumen</li>
                    <li>Respon cepat terhadap pesanan masuk</li>
                    <li>Gunakan aplikasi secara optimal</li>
                </ul>
            </div>

            <div class="mt-6">
                <a href="{{ route('driver.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
