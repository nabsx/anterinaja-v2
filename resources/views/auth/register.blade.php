@extends('layouts.app')

@section('title', 'Daftar - AnterInAja')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-user-plus text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Bergabung dengan AnterInAja</h2>
            <p class="text-gray-600">Daftar sebagai customer atau driver untuk memulai</p>
        </div>

        <!-- Registration Form -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <form method="POST" action="{{ route('register') }}" id="registerForm" class="p-8">
                @csrf

                <!-- Role Selection -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pilih Peran Anda</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Customer Role -->
                        <div class="role-card border-2 border-gray-200 rounded-xl p-6 cursor-pointer transition-all duration-200 hover:border-blue-500 hover:shadow-md" data-role="customer">
                            <div class="flex items-center mb-3">
                                <input type="radio" id="customer" name="role" value="customer" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" {{ old('role') == 'customer' ? 'checked' : '' }}>
                                <label for="customer" class="ml-3 flex items-center cursor-pointer">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Customer</h4>
                                        <p class="text-sm text-gray-600">Kirim barang dengan mudah</p>
                                    </div>
                                </label>
                            </div>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Pesan antar jemput</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Lacak pengiriman real-time</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Berbagai pilihan kendaraan</li>
                            </ul>
                        </div>

                        <!-- Driver Role -->
                        <div class="role-card border-2 border-gray-200 rounded-xl p-6 cursor-pointer transition-all duration-200 hover:border-green-500 hover:shadow-md" data-role="driver">
                            <div class="flex items-center mb-3">
                                <input type="radio" id="driver" name="role" value="driver" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300" {{ old('role') == 'driver' ? 'checked' : '' }}>
                                <label for="driver" class="ml-3 flex items-center cursor-pointer">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-motorcycle text-green-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Driver</h4>
                                        <p class="text-sm text-gray-600">Dapatkan penghasilan tambahan</p>
                                    </div>
                                </label>
                            </div>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Penghasilan fleksibel</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Jadwal kerja bebas</li>
                                <li><i class="fas fa-check text-green-500 mr-2"></i>Bonus dan insentif</li>
                            </ul>
                        </div>
                    </div>
                    @error('role')
                        <div class="mt-2 flex items-center text-red-600 text-sm">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Personal Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pribadi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user text-gray-400 mr-2"></i>Nama Lengkap *
                            </label>
                            <input 
                                id="name" 
                                name="name" 
                                type="text" 
                                required
                                value="{{ old('name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('name') border-red-500 @enderror"
                                placeholder="Masukkan nama lengkap"
                            >
                            @error('name')
                                <div class="mt-2 flex items-center text-red-600 text-sm">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-phone text-gray-400 mr-2"></i>Nomor Telepon *
                            </label>
                            <input 
                                id="phone" 
                                name="phone" 
                                type="tel" 
                                required
                                value="{{ old('phone') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('phone') border-red-500 @enderror"
                                placeholder="08xxxxxxxxxx"
                            >
                            @error('phone')
                                <div class="mt-2 flex items-center text-red-600 text-sm">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope text-gray-400 mr-2"></i>Email *
                            </label>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                required
                                value="{{ old('email') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('email') border-red-500 @enderror"
                                placeholder="nama@email.com"
                            >
                            @error('email')
                                <div class="mt-2 flex items-center text-red-600 text-sm">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>Kota
                            </label>
                            <input 
                                id="city" 
                                name="city" 
                                type="text"
                                value="{{ old('city') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('city') border-red-500 @enderror"
                                placeholder="Masukkan kota"
                            >
                            @error('city')
                                <div class="mt-2 flex items-center text-red-600 text-sm">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Section -->
                <div class="mb-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-home text-blue-600 mr-2"></i>
                            Alamat Lengkap
                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Opsional</span>
                        </h3>
                        <button type="button" onclick="getCurrentLocation()" class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center">
                            <i class="fas fa-location-arrow mr-1"></i>
                            Gunakan Lokasi Saat Ini
                        </button>
                    </div>
                    <textarea 
                        id="address" 
                        name="address" 
                        rows="3"
                        value="{{ old('address') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('address') border-red-500 @enderror"
                        placeholder="Masukkan alamat lengkap (jalan, nomor rumah, RT/RW, kelurahan, kecamatan)"
                    >{{ old('address') }}</textarea>
                    <p class="mt-2 text-sm text-gray-600">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                        Alamat membantu kami memberikan layanan yang lebih baik dan akurat
                    </p>
                    @error('address')
                        <div class="mt-2 flex items-center text-red-600 text-sm">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Keamanan Akun</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock text-gray-400 mr-2"></i>Password *
                            </label>
                            <div class="relative">
                                <input 
                                    id="password" 
                                    name="password" 
                                    type="password" 
                                    required
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('password') border-red-500 @enderror"
                                    placeholder="Minimal 6 karakter"
                                >
                                <button 
                                    type="button" 
                                    onclick="togglePasswordField('password', 'passwordIcon')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                >
                                    <i id="passwordIcon" class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="mt-2 flex items-center text-red-600 text-sm">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock text-gray-400 mr-2"></i>Konfirmasi Password *
                            </label>
                            <div class="relative">
                                <input 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    type="password" 
                                    required
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    placeholder="Ulangi password"
                                >
                                <button 
                                    type="button" 
                                    onclick="togglePasswordField('password_confirmation', 'confirmPasswordIcon')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                >
                                    <i id="confirmPasswordIcon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Driver Information (Hidden by default) -->
                <div id="driverFields" class="mb-8 bg-green-50 border border-green-200 rounded-xl p-6 hidden">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-motorcycle text-green-600 mr-2"></i>
                        Informasi Kendaraan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Vehicle Type -->
                        <div>
                            <label for="vehicle_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-car text-gray-400 mr-2"></i>Jenis Kendaraan *
                            </label>
                            <select 
                                id="vehicle_type" 
                                name="vehicle_type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('vehicle_type') border-red-500 @enderror"
                            >
                                <option value="">Pilih jenis kendaraan</option>
                                <option value="motorcycle" {{ old('vehicle_type') == 'motorcycle' ? 'selected' : '' }}>Motor</option>
                                <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>Mobil</option>
                                <option value="van" {{ old('vehicle_type') == 'van' ? 'selected' : '' }}>Van</option>
                                <option value="truck" {{ old('vehicle_type') == 'truck' ? 'selected' : '' }}>Truk</option>
                            </select>
                            @error('vehicle_type')
                                <div class="mt-2 flex items-center text-red-600 text-sm">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Vehicle Brand -->
                        <div>
                            <label for="vehicle_brand" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-tag text-gray-400 mr-2"></i>Merk Kendaraan *
                            </label>
                            <input 
                                id="vehicle_brand" 
                                name="vehicle_brand" 
                                type="text"
                                value="{{ old('vehicle_brand') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('vehicle_brand') border-red-500 @enderror"
                                placeholder="Honda, Yamaha, Toyota, dll"
                            >
                            @error('vehicle_brand')
                                <div class="mt-2 flex items-center text-red-600 text-sm">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Vehicle Model -->
                        <div>
                            <label for="vehicle_model" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-cogs text-gray-400 mr-2"></i>Model Kendaraan *
                            </label>
                            <input 
                                id="vehicle_model" 
                                name="vehicle_model" 
                                type="text"
                                value="{{ old('vehicle_model') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('vehicle_model') border-red-500 @enderror"
                                placeholder="Vario, Beat, Avanza, dll"
                            >
                            @error('vehicle_model')
                                <div class="mt-2 flex items-center text-red-600 text-sm">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Vehicle Year -->
                        <div>
                            <label for="vehicle_year" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar text-gray-400 mr-2"></i>Tahun Kendaraan *
                            </label>
                            <input 
                                id="vehicle_year" 
                                name="vehicle_year" 
                                type="number" 
                                min="1990" 
                                max="{{ date('Y') }}"
                                value="{{ old('vehicle_year') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('vehicle_year') border-red-500 @enderror"
                                placeholder="{{ date('Y') }}"
                            >
                            @error('vehicle_year')
                                <div class="mt-2 flex items-center text-red-600 text-sm">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Vehicle Plate -->
                        <div>
                            <label for="vehicle_plate" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-id-card text-gray-400 mr-2"></i>Plat Nomor *
                            </label>
                            <input 
                                id="vehicle_plate" 
                                name="vehicle_plate" 
                                type="text"
                                value="{{ old('vehicle_plate') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('vehicle_plate') border-red-500 @enderror"
                                placeholder="B 1234 ABC"
                                style="text-transform: uppercase;"
                            >
                            @error('vehicle_plate')
                                <div class="mt-2 flex items-center text-red-600 text-sm">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- License Number -->
                        <div>
                            <label for="license_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-id-badge text-gray-400 mr-2"></i>Nomor SIM *
                            </label>
                            <input 
                                id="license_number" 
                                name="license_number" 
                                type="text"
                                value="{{ old('license_number') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('license_number') border-red-500 @enderror"
                                placeholder="1234567890123456"
                            >
                            @error('license_number')
                                <div class="mt-2 flex items-center text-red-600 text-sm">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 transform hover:scale-105"
                >
                    <span id="submitText">Daftar Sekarang</span>
                    <i id="loadingIcon" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                </button>

                <!-- Login Link -->
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                            Masuk di sini
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Benefits -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-rocket text-blue-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Mulai Cepat</h3>
                <p class="text-gray-600 text-sm">Daftar dalam hitungan menit dan langsung mulai menggunakan layanan</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-green-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Aman & Terpercaya</h3>
                <p class="text-gray-600 text-sm">Data Anda dilindungi dengan enkripsi tingkat bank</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-purple-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Support 24/7</h3>
                <p class="text-gray-600 text-sm">Tim support siap membantu Anda kapan saja</p>
            </div>
        </div>
    </div>
</div>

<script>
// Role selection functionality
document.querySelectorAll('.role-card').forEach(card => {
    card.addEventListener('click', function() {
        const role = this.dataset.role;
        const radio = this.querySelector('input[type="radio"]');
        
        // Clear all selections
        document.querySelectorAll('.role-card').forEach(c => {
            c.classList.remove('border-blue-500', 'border-green-500', 'bg-blue-50', 'bg-green-50');
            c.classList.add('border-gray-200');
        });
        
        // Select current card
        radio.checked = true;
        if (role === 'customer') {
            this.classList.remove('border-gray-200');
            this.classList.add('border-blue-500', 'bg-blue-50');
        } else {
            this.classList.remove('border-gray-200');
            this.classList.add('border-green-500', 'bg-green-50');
        }
        
        // Show/hide driver fields
        const driverFields = document.getElementById('driverFields');
        if (role === 'driver') {
            driverFields.classList.remove('hidden');
            // Make driver fields required
            driverFields.querySelectorAll('input, select').forEach(field => {
                if (field.name !== 'vehicle_year') {
                    field.required = true;
                }
            });
        } else {
            driverFields.classList.add('hidden');
            // Remove required from driver fields
            driverFields.querySelectorAll('input, select').forEach(field => {
                field.required = false;
            });
        }
    });
});

// Initialize role selection on page load
document.addEventListener('DOMContentLoaded', function() {
    const selectedRole = document.querySelector('input[name="role"]:checked');
    if (selectedRole) {
        const card = selectedRole.closest('.role-card');
        card.click();
    }
});

// Password toggle functionality
function togglePasswordField(fieldId, iconId) {
    const passwordField = document.getElementById(fieldId);
    const passwordIcon = document.getElementById(iconId);
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
    }
}

// Phone number formatting
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.startsWith('0')) {
        value = value;
    } else if (value.startsWith('62')) {
        value = '0' + value.substring(2);
    }
    e.target.value = value;
});

// Vehicle plate uppercase
document.getElementById('vehicle_plate').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

// Get current location
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            // Here you would typically use a reverse geocoding service
            // For now, just show a message
            alert('Fitur lokasi akan segera tersedia. Silakan masukkan alamat secara manual.');
        }, function(error) {
            alert('Tidak dapat mengakses lokasi. Silakan masukkan alamat secara manual.');
        });
    } else {
        alert('Browser Anda tidak mendukung geolocation. Silakan masukkan alamat secara manual.');
    }
}

// Form submission
document.getElementById('registerForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingIcon = document.getElementById('loadingIcon');
    
    submitBtn.disabled = true;
    submitText.textContent = 'Memproses...';
    loadingIcon.classList.remove('hidden');
});
</script>
@endsection
