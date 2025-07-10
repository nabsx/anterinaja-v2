<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Selamat Datang | AnterinAja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-800">

    <!-- Header -->
    <header class="bg-white shadow-md py-4">
        <div class="container mx-auto flex justify-between items-center px-4">
            <h1 class="text-xl font-bold text-blue-600">AnterinAja</h1>
            <div>
                <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:underline mr-4">Login</a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Daftar</a>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-20 text-center">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold mb-4">Ojek & Kurir Online Termurah</h2>
            <p class="text-lg mb-6">Pesan sekarang tanpa ribet. Cek harga & rute tanpa login!</p>
            <a href="#cek-ongkir" class="bg-white text-blue-600 font-semibold px-6 py-3 rounded shadow hover:bg-gray-100">Cek Harga Sekarang</a>
        </div>
    </section>

    <!-- Form Cek Harga -->
    <section id="cek-ongkir" class="py-16 bg-gray-50">
        <div class="max-w-xl mx-auto px-4">
            <h3 class="text-2xl font-semibold mb-6 text-center">Cek Ongkir (Guest)</h3>
            
            @if(session('fare_result'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <h4 class="font-semibold">Hasil Perhitungan:</h4>
                    <p>Jarak: {{ session('fare_result.distance') }} km</p>
                    <p>Estimasi Waktu: {{ session('fare_result.duration') }} menit</p>
                    <p>Tarif Motor: Rp {{ number_format(session('fare_result.motorcycle_fare'), 0, ',', '.') }}</p>
                    <p>Tarif Mobil: Rp {{ number_format(session('fare_result.car_fare'), 0, ',', '.') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('calculate.fare') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Alamat Jemput</label>
                    <input type="text" name="pickup_address" value="{{ old('pickup_address') }}" 
                           class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Contoh: Jl. Diponegoro No.1" required>
                    @error('pickup_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Alamat Tujuan</label>
                    <input type="text" name="destination_address" value="{{ old('destination_address') }}" 
                           class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Contoh: Jl. Merdeka No.2" required>
                    @error('destination_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1">Jenis Kendaraan</label>
                    <select name="service_type" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="motorcycle">Motor (Lebih Murah)</option>
                        <option value="car">Mobil (Lebih Nyaman)</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-calculator mr-2"></i>Hitung Ongkir
                </button>
            </form>
            
            <div class="text-center mt-6">
                <p class="text-gray-600 mb-4">Ingin pesan langsung?</p>
                <div class="space-x-2">
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Gabung Driver -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 text-center">
            <h3 class="text-2xl font-bold mb-4">Gabung Jadi Driver?</h3>
            <p class="mb-6 text-gray-600">Dapatkan penghasilan tambahan. Daftar sekarang dan mulai narik!</p>
            <a href="{{ url('/register?role=driver') }}" class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700">Daftar Driver</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-100 py-4 text-center text-sm text-gray-600">
        &copy; {{ date('Y') }} AnterinAja. All rights reserved.
    </footer>

</body>
</html>
