<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AnterinAja - Ojek Online Termurah')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-blue-600">
                            <i class="fas fa-motorcycle mr-2"></i>AnterinAja
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @auth
                        @php
                            // Coba ambil user login sekarang
                            $user = auth()->user();
                            $isDriver = isset($user->driver); // Cek apakah user punya relasi driver

                            if ($isDriver) {
                                $driver = $user->driver;
                                $profilePhoto = $driver->documents->where('document_type', 'photo')->first();
                                $profilePhotoUrl = $profilePhoto && $profilePhoto->document_path 
                                    ? Storage::url($profilePhoto->document_path)
                                    : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF&size=128';
                            } else {
                                $profilePhotoUrl = 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF&size=128';
                            }
                        @endphp
                            <div class="relative group">
                                <button class="flex items-center space-x-2 text-gray-700 hover:text-blue-600">
                                    <img src="{{ $profilePhotoUrl }}" alt="Profile picture" class="w-8 h-8 rounded-full">
                                    <span>{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-sm"></i>
                                </button>
                                
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-1">
                                        @if(auth()->user()->role === 'driver')
                                            <a href="{{ route('driver.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                            </a>
                                            <a href="{{ route('driver.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-user mr-2"></i>Profil
                                            </a>
                                        @else
                                            <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                            </a>
                                            <a href="{{ route('customer.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-user mr-2"></i>Profil
                                            </a>
                                        @endif
                                        <hr class="my-1">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">Login</a>
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Daftar</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <script>
        // Add CSRF token to all AJAX requests
        window.addEventListener('DOMContentLoaded', function() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
