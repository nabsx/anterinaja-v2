@extends('layouts.app')

@section('title', 'Daftar - AnterinAja')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-gradient-to-r from-indigo-500 to-blue-600 shadow-lg mb-4">
                <i class="fas fa-user-plus text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                Bergabung dengan AnterinAja
            </h2>
            <p class="text-gray-600">
                Daftar sekarang dan nikmati kemudahan transportasi online
            </p>
        </div>

        <!-- Registration Form Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <span class="text-red-700 font-medium">Mohon perbaiki kesalahan berikut:</span>
                    </div>
                    <ul class="text-sm text-red-600 list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-6">
                @csrf
                
                <!-- Role Selection -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">
                        <i class="fas fa-users text-gray-400 mr-2"></i>
                        Pilih Jenis Akun
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="cursor-pointer group">
                            <input type="radio" name="role" value="customer" class="sr-only" checked>
                            <div class="border-2 border-gray-200 rounded-xl p-6 text-center hover:border-blue-400 hover:bg-blue-50 transition duration-200 role-card group-hover:shadow-md" data-role="customer">
                                <div class="w-12 h-12 mx-auto mb-3 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-xl"></i>
                                </div>
                                <h3 class="font-semibold text-gray-900 mb-1">Customer</h3>
                                <p class="text-sm text-gray-600">Penumpang atau pengirim barang</p>
                                <div class="mt-3 flex justify-center space-x-2 text-xs text-gray-500">
                                    <span class="bg-gray-100 px-2 py-1 rounded">Pesan Ride</span>
                                    <span class="bg-gray-100 px-2 py-1 rounded">Kirim Barang</span>
                                </div>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="role" value="driver" class="sr-only">
                            <div class="border-2 border-gray-200 rounded-xl p-6 text-center hover:border-green-400 hover:bg-green-50 transition duration-200 role-card group-hover:shadow-md" data-role="driver">
                                <div class="w-12 h-12 mx-auto mb-3 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-motorcycle text-green-600 text-xl"></i>
                                </div>
                                <h3 class="font-semibold text-gray-900 mb-1">Driver</h3>
                                <p class="text-sm text-gray-600">Pengendara atau mitra driver</p>
                                <div class="mt-3 flex justify-center space-x-2 text-xs text-gray-500">
                                    <span class="bg-gray-100 px-2 py-1 rounded">Terima Order</span>
                                    <span class="bg-gray-100 px-2 py-1 rounded">Dapatkan Penghasilan</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Personal Information Section -->
                <div class="space-y-6">
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Informasi Pribadi</h3>
                        <p class="text-sm text-gray-600">Lengkapi data diri Anda dengan benar</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user text-gray-400 mr-2"></i>
                                Nama Lengkap *
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('name') border-red-500 @enderror" 
                                placeholder="Masukkan nama lengkap Anda"
                                value="{{ old('name') }}"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                Email *
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('email') border-red-500 @enderror" 
                                placeholder="nama@email.com"
                                value="{{ old('email') }}"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-phone text-gray-400 mr-2"></i>
                                Nomor HP *
                            </label>
                            <input 
                                type="tel" 
                                name="phone" 
                                id="phone" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('phone') border-red-500 @enderror" 
                                placeholder="081234567890"
                                value="{{ old('phone') }}"
                            >
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock text-gray-400 mr-2"></i>
                                Password *
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password" 
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('password') border-red-500 @enderror" 
                                    placeholder="Minimal 6 karakter"
                                >
                                <button 
                                    type="button" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    onclick="togglePasswordVisibility('password', 'password-icon')"
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

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock text-gray-400 mr-2"></i>
                                Konfirmasi Password *
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    name="password_confirmation" 
                                    id="password_confirmation" 
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                    placeholder="Ulangi password"
                                >
                                <button 
                                    type="button" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    onclick="togglePasswordVisibility('password_confirmation', 'password-confirm-icon')"
                                >
                                    <i id="password-confirm-icon" class="fas fa-eye text-gray-400 hover:text-gray-600 cursor-pointer"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                            <h4 class="font-semibold text-gray-900">Informasi Alamat</h4>
                            <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Opsional</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat Lengkap
                                </label>
                                <textarea 
                                    name="address" 
                                    id="address" 
                                    rows="3" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 resize-none" 
                                    placeholder="Contoh: Jl. Sudirman No. 123, RT 01/RW 02, Kelurahan ABC"
                                >{{ old('address') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Alamat akan membantu driver menemukan lokasi Anda dengan lebih mudah
                                </p>
                            </div>

                            <!-- City -->
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kota
                                </label>
                                <input 
                                    type="text" 
                                    name="city" 
                                    id="city" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                    placeholder="Jakarta"
                                    value="{{ old('city') }}"
                                >
                            </div>

                            <!-- Get Current Location Button -->
                            <div class="flex items-end">
                                <button 
                                    type="button" 
                                    onclick="getCurrentLocation()" 
                                    class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition duration-200 flex items-center justify-center"
                                >
                                    <i class="fas fa-location-arrow mr-2"></i>
                                    Gunakan Lokasi Saat Ini
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Driver Information (Hidden by default) -->
                <div id="driver-fields" class="space-y-6 hidden">
                    <div class="border-l-4 border-green-500 pl-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Informasi Kendaraan</h3>
                        <p class="text-sm text-gray-600">Data kendaraan yang akan digunakan untuk melayani customer</p>
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Vehicle Type -->
                            <div class="md:col-span-2">
                                <label for="vehicle_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-car text-gray-400 mr-2"></i>
                                    Jenis Kendaraan *
                                </label>
                                <select 
                                    name="vehicle_type" 
                                    id="vehicle_type" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                                >
                                    <option value="motorcycle">🏍️ Motor</option>
                                    <option value="car">🚗 Mobil</option>
                                </select>
                            </div>

                            <!-- Vehicle Brand & Model -->
                            <div>
                                <label for="vehicle_brand" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Merk Kendaraan *
                                </label>
                                <input 
                                    type="text" 
                                    name="vehicle_brand" 
                                    id="vehicle_brand" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200" 
                                    placeholder="Honda, Yamaha, Toyota, dll"
                                    value="{{ old('vehicle_brand') }}"
                                >
                            </div>
                            <div>
                                <label for="vehicle_model" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Model Kendaraan *
                                </label>
                                <input 
                                    type="text" 
                                    name="vehicle_model" 
                                    id="vehicle_model" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200" 
                                    placeholder="Vario, Beat, Avanza, dll"
                                    value="{{ old('vehicle_model') }}"
                                >
                            </div>

                            <!-- Vehicle Year & Plate -->
                            <div>
                                <label for="vehicle_year" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tahun Kendaraan *
                                </label>
                                <input 
                                    type="number" 
                                    name="vehicle_year" 
                                    id="vehicle_year" 
                                    min="1990" 
                                    max="{{ date('Y') }}" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200" 
                                    placeholder="2020"
                                    value="{{ old('vehicle_year') }}"
                                >
                            </div>
                            <div>
                                <label for="vehicle_plate" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Plat Nomor *
                                </label>
                                <input 
                                    type="text" 
                                    name="vehicle_plate" 
                                    id="vehicle_plate" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200" 
                                    placeholder="B 1234 ABC"
                                    value="{{ old('vehicle_plate') }}"
                                    style="text-transform: uppercase;"
                                >
                            </div>

                            <!-- License Number -->
                            <div class="md:col-span-2">
                                <label for="license_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-id-card text-gray-400 mr-2"></i>
                                    Nomor SIM *
                                </label>
                                <input 
                                    type="text" 
                                    name="license_number" 
                                    id="license_number" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200" 
                                    placeholder="1234567890123456"
                                    value="{{ old('license_number') }}"
                                >
                                <p class="mt-1 text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Pastikan SIM masih berlaku dan sesuai dengan jenis kendaraan
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6">
                    <button 
                        type="submit" 
                        class="w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-lg shadow-sm text-base font-semibold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200 transform hover:scale-[1.02]"
                        id="register-btn"
                    >
                        <span id="register-text">
                            <i class="fas fa-user-plus mr-2"></i>
                            Daftar Sekarang
                        </span>
                        <span id="register-loading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Memproses pendaftaran...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Login Link -->
            <div class="mt-8 text-center">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500 font-medium">Sudah punya akun?</span>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition duration-200">
                        Masuk ke akun Anda
                    </a>
                </div>
            </div>
        </div>

        <!-- Benefits Section -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100 text-center">
                <div class="w-12 h-12 mx-auto mb-3 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                </div>
                <h4 class="font-semibold text-gray-900 mb-1">Keamanan Terjamin</h4>
                <p class="text-sm text-gray-600">Data pribadi Anda aman dengan enkripsi tingkat tinggi</p>
            </div>
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100 text-center">
                <div class="w-12 h-12 mx-auto mb-3 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-green-600 text-xl"></i>
                </div>
                <h4 class="font-semibold text-gray-900 mb-1">Layanan 24/7</h4>
                <p class="text-sm text-gray-600">Siap melayani Anda kapan saja, di mana saja</p>
            </div>
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100 text-center">
                <div class="w-12 h-12 mx-auto mb-3 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
                <h4 class="font-semibold text-gray-900 mb-1">Rating Terbaik</h4>
                <p class="text-sm text-gray-600">Dipercaya oleh ribuan pengguna di seluruh Indonesia</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Role selection functionality
document.addEventListener('DOMContentLoaded', function() {
    const roleCards = document.querySelectorAll('.role-card');
    const driverFields = document.getElementById('driver-fields');
    const roleInputs = document.querySelectorAll('input[name="role"]');

    function updateRoleSelection() {
        roleCards.forEach(card => {
            card.classList.remove('border-blue-400', 'bg-blue-50', 'border-green-400', 'bg-green-50');
            card.classList.add('border-gray-200');
        });

        const selectedRole = document.querySelector('input[name="role"]:checked').value;
        const selectedCard = document.querySelector(`[data-role="${selectedRole}"]`);
        
        if (selectedRole === 'customer') {
            selectedCard.classList.remove('border-gray-200');
            selectedCard.classList.add('border-blue-400', 'bg-blue-50');
            driverFields.classList.add('hidden');
            // Remove required from driver fields
            driverFields.querySelectorAll('input, select').forEach(field => {
                field.removeAttribute('required');
            });
        } else {
            selectedCard.classList.remove('border-gray-200');
            selectedCard.classList.add('border-green-400', 'bg-green-50');
            driverFields.classList.remove('hidden');
            // Make driver fields required
            driverFields.querySelectorAll('input, select').forEach(field => {
                if (!field.name.includes('vehicle_type')) {
                    field.setAttribute('required', 'required');
                }
            });
        }
    }

    roleCards.forEach(card => {
        card.addEventListener('click', function() {
            const role = this.dataset.role;
            document.querySelector(`input[value="${role}"]`).checked = true;
            updateRoleSelection();
        });
    });

    roleInputs.forEach(input => {
        input.addEventListener('change', updateRoleSelection);
    });

    // Initialize
    updateRoleSelection();
});

// Password visibility toggle
function togglePasswordVisibility(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const passwordIcon = document.getElementById(iconId);
    
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

// Get current location
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            // Here you would typically use a reverse geocoding service
            // For now, we'll just show a success message
            alert('Lokasi berhasil dideteksi! Silakan isi alamat secara manual.');
        }, function(error) {
            alert('Tidak dapat mengakses lokasi. Silakan isi alamat secara manual.');
        });
    } else {
        alert('Browser Anda tidak mendukung geolokasi. Silakan isi alamat secara manual.');
    }
}

// Form submission loading state
document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('register-btn');
    const text = document.getElementById('register-text');
    const loading = document.getElementById('register-loading');
    
    btn.disabled = true;
    text.classList.add('hidden');
    loading.classList.remove('hidden');
});

// Auto-format vehicle plate
document.getElementById('vehicle_plate').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

// Phone number formatting
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.startsWith('0')) {
        value = '62' + value.substring(1);
    }
    if (!value.startsWith('62')) {
        value = '62' + value;
    }
    e.target.value = value;
});
</script>
@endpush
@endsection
