@extends('layouts.app')

@section('title', 'Login - AnterinAja')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 shadow-lg mb-4">
                <i class="fas fa-motorcycle text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                Selamat Datang Kembali
            </h2>
            <p class="text-gray-600">
                Masuk ke akun AnterinAja Anda
            </p>
        </div>

        <!-- Login Form Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <span class="text-red-700 font-medium">Terjadi kesalahan:</span>
                    </div>
                    <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                @csrf
                
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                        Alamat Email
                    </label>
                    <div class="relative">
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            autocomplete="email" 
                            required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 placeholder-gray-400 @error('email') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                            placeholder="nama@email.com"
                            value="{{ old('email') }}"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-at text-gray-400"></i>
                        </div>
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-gray-400 mr-2"></i>
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            autocomplete="current-password" 
                            required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 placeholder-gray-400 @error('password') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                            placeholder="Masukkan password Anda"
                        >
                        <button 
                            type="button" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            onclick="togglePassword()"
                        >
                            <i id="password-icon" class="fas fa-eye text-gray-400 hover:text-gray-600 cursor-pointer"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition duration-200"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700 font-medium">
                            Ingat saya
                        </label>
                    </div>
                    <div class="text-sm">
                        <a href="#" class="font-medium text-blue-600 hover:text-blue-500 transition duration-200">
                            Lupa password?
                        </a>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit" 
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 transform hover:scale-[1.02]"
                        id="login-btn"
                    >
                        <span id="login-text">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Masuk
                        </span>
                        <span id="login-loading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500 font-medium">Atau</span>
                    </div>
                </div>
            </div>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-500 transition duration-200">
                        Daftar sekarang
                    </a>
                </p>
            </div>
        </div>

        <!-- Features Section -->
        <div class="mt-8 grid grid-cols-3 gap-4 text-center">
            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                <i class="fas fa-shield-alt text-blue-500 text-xl mb-2"></i>
                <p class="text-xs text-gray-600 font-medium">Aman & Terpercaya</p>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                <i class="fas fa-clock text-green-500 text-xl mb-2"></i>
                <p class="text-xs text-gray-600 font-medium">24/7 Service</p>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                <i class="fas fa-star text-yellow-500 text-xl mb-2"></i>
                <p class="text-xs text-gray-600 font-medium">Rating Terbaik</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
    }
}

// Loading state for form submission
document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('login-btn');
    const text = document.getElementById('login-text');
    const loading = document.getElementById('login-loading');
    
    btn.disabled = true;
    text.classList.add('hidden');
    loading.classList.remove('hidden');
});

// Auto-focus on first input
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('email').focus();
});
</script>
@endpush
@endsection
