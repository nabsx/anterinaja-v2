@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Welcome Card -->
            <div class="bg-blue-50 p-6 rounded-lg border-l-4 border-blue-500">
                <h3 class="text-lg font-semibold text-blue-800">Selamat Datang!</h3>
                <p class="text-blue-600">{{ Auth::user()->name }}</p>
                <p class="text-sm text-blue-500">{{ ucfirst(Auth::user()->role) }}</p>
            </div>
            
            <!-- Status Card -->
            <div class="bg-green-50 p-6 rounded-lg border-l-4 border-green-500">
                <h3 class="text-lg font-semibold text-green-800">Status Akun</h3>
                <p class="text-green-600">{{ Auth::user()->is_active ? 'Aktif' : 'Tidak Aktif' }}</p>
                <p class="text-sm text-green-500">Bergabung: {{ Auth::user()->created_at->format('d M Y') }}</p>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-purple-50 p-6 rounded-lg border-l-4 border-purple-500">
                <h3 class="text-lg font-semibold text-purple-800">Aksi Cepat</h3>
                @if(Auth::user()->role === 'customer')
                    <a href="{{ route('customer.dashboard') }}" class="inline-block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition duration-200">
                        Dashboard Customer
                    </a>
                @elseif(Auth::user()->role === 'driver')
                    <a href="{{ route('driver.dashboard') }}" class="inline-block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition duration-200">
                        Dashboard Driver
                    </a>
                @elseif(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="inline-block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition duration-200">
                        Dashboard Admin
                    </a>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Aktivitas Terakhir</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-white rounded border">
                    <div>
                        <p class="font-medium">Login Terakhir</p>
                        <p class="text-sm text-gray-600">{{ Auth::user()->last_login ? Auth::user()->last_login->format('d M Y H:i') : 'Belum pernah login' }}</p>
                    </div>
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Sukses</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-white rounded border">
                    <div>
                        <p class="font-medium">Akun Dibuat</p>
                        <p class="text-sm text-gray-600">{{ Auth::user()->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">Info</span>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @if(Auth::user()->role === 'customer')
                <a href="{{ route('customer.profile') }}" class="block p-4 bg-white border rounded-lg hover:shadow-md transition duration-200">
                    <h4 class="font-semibold text-gray-800">Profil Saya</h4>
                    <p class="text-sm text-gray-600">Kelola informasi profil Anda</p>
                </a>
                <a href="{{ route('customer.orders') }}" class="block p-4 bg-white border rounded-lg hover:shadow-md transition duration-200">
                    <h4 class="font-semibold text-gray-800">Pesanan Saya</h4>
                    <p class="text-sm text-gray-600">Lihat riwayat pesanan</p>
                </a>
                <a href="{{ route('customer.book') }}" class="block p-4 bg-white border rounded-lg hover:shadow-md transition duration-200">
                    <h4 class="font-semibold text-gray-800">Pesan Antar</h4>
                    <p class="text-sm text-gray-600">Buat pesanan baru</p>
                </a>
            @elseif(Auth::user()->role === 'driver')
                <a href="{{ route('driver.profile') }}" class="block p-4 bg-white border rounded-lg hover:shadow-md transition duration-200">
                    <h4 class="font-semibold text-gray-800">Profil Driver</h4>
                    <p class="text-sm text-gray-600">Kelola profil driver Anda</p>
                </a>
                <a href="{{ route('driver.orders') }}" class="block p-4 bg-white border rounded-lg hover:shadow-md transition duration-200">
                    <h4 class="font-semibold text-gray-800">Pesanan Saya</h4>
                    <p class="text-sm text-gray-600">Kelola pesanan yang diterima</p>
                </a>
                <a href="{{ route('driver.available.orders') }}" class="block p-4 bg-white border rounded-lg hover:shadow-md transition duration-200">
                    <h4 class="font-semibold text-gray-800">Pesanan Tersedia</h4>
                    <p class="text-sm text-gray-600">Cari pesanan baru</p>
                </a>
            @elseif(Auth::user()->role === 'admin')
                <a href="{{ route('admin.users') }}" class="block p-4 bg-white border rounded-lg hover:shadow-md transition duration-200">
                    <h4 class="font-semibold text-gray-800">Kelola User</h4>
                    <p class="text-sm text-gray-600">Administrasi pengguna</p>
                </a>
                <a href="{{ route('admin.drivers') }}" class="block p-4 bg-white border rounded-lg hover:shadow-md transition duration-200">
                    <h4 class="font-semibold text-gray-800">Kelola Driver</h4>
                    <p class="text-sm text-gray-600">Verifikasi dan kelola driver</p>
                </a>
                <a href="{{ route('admin.orders') }}" class="block p-4 bg-white border rounded-lg hover:shadow-md transition duration-200">
                    <h4 class="font-semibold text-gray-800">Kelola Pesanan</h4>
                    <p class="text-sm text-gray-600">Monitor semua pesanan</p>
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
