@extends('layouts.app')

@section('title', 'Daftar - AnterinAja')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <i class="fas fa-user-plus text-blue-600 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Daftar Akun Baru
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    Masuk di sini
                </a>
            </p>
        </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ route('register.post') }}">
            @csrf
            
            <!-- Role Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Daftar sebagai:</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="customer" class="sr-only" checked>
                        <div class="border-2 border-gray-300 rounded-lg p-4 text-center hover:border-blue-500 transition role-card" data-role="customer">
                            <i class="fas fa-user text-2xl text-blue-600 mb-2"></i>
                            <p class="font-medium">Customer</p>
                            <p class="text-xs text-gray-500">Penumpang/Pengirim</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="driver" class="sr-only">
                        <div class="border-2 border-gray-300 rounded-lg p-4 text-center hover:border-blue-500 transition role-card" data-role="driver">
                            <i class="fas fa-motorcycle text-2xl text-green-600 mb-2"></i>
                            <p class="font-medium">Driver</p>
                            <p class="text-xs text-gray-500">Pengendara</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="name" id="name" required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" 
                           value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" 
                           value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Nomor HP</label>
                    <input type="tel" name="phone" id="phone" required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror" 
                           value="{{ old('phone') }}" placeholder="081234567890">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="address" id="address" rows="2" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                              placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">Kota</label>
                    <input type="text" name="city" id="city" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                           value="{{ old('city') }}" placeholder="Jakarta">
                </div>
            </div>

            <!-- Driver Information (Hidden by default) -->
            <div id="driver-fields" class="space-y-4 hidden">
                <h3 class="text-lg font-medium text-gray-900">Informasi Kendaraan</h3>
                
                <div>
                    <label for="vehicle_type" class="block text-sm font-medium text-gray-700">Jenis Kendaraan</label>
                    <select name="vehicle_type" id="vehicle_type" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="motorcycle">Motor</option>
                        <option value="car">Mobil</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="vehicle_brand" class="block text-sm font-medium text-gray-700">Merk</label>
                        <input type="text" name="vehicle_brand" id="vehicle_brand" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('vehicle_brand') }}" placeholder="Honda">
                    </div>
                    <div>
                        <label for="vehicle_model" class="block text-sm font-medium text-gray-700">Model</label>
                        <input type="text" name="vehicle_model" id="vehicle_model" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('vehicle_model') }}" placeholder="Vario">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="vehicle_year" class="block text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" name="vehicle_year" id="vehicle_year" min="1990" max="{{ date('Y') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('vehicle_year') }}" placeholder="2020">
                    </div>
                    <div>
                        <label for="vehicle_plate" class="block text-sm font-medium text-gray-700">Plat Nomor</label>
                        <input type="text" name="vehicle_plate" id="vehicle_plate" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('vehicle_plate') }}" placeholder="B 1234 ABC">
                    </div>
                </div>

                <div>
                    <label for="license_number" class="block text-sm font-medium text-gray-700">Nomor SIM</label>
                    <input type="text" name="license_number" id="license_number" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                           value="{{ old('license_number') }}" placeholder="1234567890123456">
                </div>
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-user-plus mr-2"></i>
                    Daftar
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleCards = document.querySelectorAll('.role-card');
    const driverFields = document.getElementById('driver-fields');
    const roleInputs = document.querySelectorAll('input[name="role"]');

    function updateRoleSelection() {
        roleCards.forEach(card => {
            card.classList.remove('border-blue-500', 'bg-blue-50');
            card.classList.add('border-gray-300');
        });

        const selectedRole = document.querySelector('input[name="role"]:checked').value;
        const selectedCard = document.querySelector(`[data-role="${selectedRole}"]`);
        selectedCard.classList.remove('border-gray-300');
        selectedCard.classList.add('border-blue-500', 'bg-blue-50');

        if (selectedRole === 'driver') {
            driverFields.classList.remove('hidden');
            // Make driver fields required
            driverFields.querySelectorAll('input, select').forEach(field => {
                if (field.name !== 'vehicle_type') {
                    field.setAttribute('required', 'required');
                }
            });
        } else {
            driverFields.classList.add('hidden');
            // Remove required from driver fields
            driverFields.querySelectorAll('input, select').forEach(field => {
                field.removeAttribute('required');
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
</script>
@endpush
@endsection
