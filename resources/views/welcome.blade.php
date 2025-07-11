<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>AnterinAja - Ojek & Kurir Online Terpercaya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s ease-out',
                        'fade-in-down': 'fadeInDown 0.8s ease-out',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        fadeInDown: {
                            '0%': { opacity: '0', transform: 'translateY(-30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' }
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-gray-100">
        <div class="container mx-auto flex justify-between items-center px-6 py-4">
            <div class="flex items-center space-x-2 animate-fade-in-down">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-motorcycle text-white text-lg"></i>
                </div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    AnterinAja
                </h1>
            </div>
            <div class="flex items-center space-x-4 animate-fade-in-down">
                <a href="{{ route('login') }}" 
                   class="text-gray-600 hover:text-blue-600 font-medium transition-colors duration-200 flex items-center space-x-1">
                    <i class="fas fa-sign-in-alt text-sm"></i>
                    <span>Masuk</span>
                </a>
                <a href="{{ route('register') }}" 
                   class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-2.5 rounded-full hover:shadow-lg transform hover:scale-105 transition-all duration-200 font-medium">
                    Daftar Gratis
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-20 lg:py-32">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-10 left-10 w-20 h-20 bg-blue-600 rounded-full animate-float"></div>
            <div class="absolute top-32 right-20 w-16 h-16 bg-indigo-600 rounded-full animate-float" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-purple-600 rounded-full animate-float" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="container mx-auto px-6 text-center relative z-10">
            <div class="animate-fade-in-up">
                <h2 class="text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Ojek Online
                    </span>
                    <br>
                    <span class="text-gray-800">Termurah & Terpercaya</span>
                </h2>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto leading-relaxed">
                    Pesan ojek dan kurir online dengan harga terjangkau. 
                    <span class="font-semibold text-blue-600">Cek tarif tanpa perlu login!</span>
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="#cek-ongkir" 
                       class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-full font-semibold text-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-calculator"></i>
                        <span>Cek Harga Sekarang</span>
                    </a>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-star text-yellow-400"></i>
                            <span>4.8/5 Rating</span>
                        </div>
                        <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-users text-blue-500"></i>
                            <span>10K+ Pengguna</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12 animate-fade-in-up">
                <h3 class="text-3xl font-bold text-gray-800 mb-4">Mengapa Pilih AnterinAja?</h3>
                <p class="text-gray-600 max-w-2xl mx-auto">Kami berkomitmen memberikan layanan terbaik dengan harga yang kompetitif</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-6 rounded-2xl hover:shadow-lg transition-shadow duration-300 animate-fade-in-up">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-money-bill-wave text-white text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-semibold mb-2 text-gray-800">Harga Terjangkau</h4>
                    <p class="text-gray-600">Tarif transparan tanpa biaya tersembunyi. Hemat hingga 30% dari kompetitor.</p>
                </div>
                <div class="text-center p-6 rounded-2xl hover:shadow-lg transition-shadow duration-300 animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-white text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-semibold mb-2 text-gray-800">Cepat & Tepat Waktu</h4>
                    <p class="text-gray-600">Driver berpengalaman dengan waktu tunggu rata-rata hanya 5 menit.</p>
                </div>
                <div class="text-center p-6 rounded-2xl hover:shadow-lg transition-shadow duration-300 animate-fade-in-up" style="animation-delay: 0.4s;">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-semibold mb-2 text-gray-800">Aman & Terpercaya</h4>
                    <p class="text-gray-600">Driver terverifikasi dengan asuransi perjalanan untuk keamanan Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Fare Calculator Section -->
    <section id="cek-ongkir" class="py-20 bg-gradient-to-br from-gray-50 to-blue-50">
        <div class="max-w-2xl mx-auto px-6">
            <div class="text-center mb-10 animate-fade-in-up">
                <h3 class="text-4xl font-bold text-gray-800 mb-4">Cek Tarif Perjalanan</h3>
                <p class="text-gray-600 text-lg">Hitung estimasi biaya perjalanan Anda tanpa perlu mendaftar</p>
            </div>
            
            @if(session('fare_result'))
                <div class="bg-white border border-green-200 rounded-2xl p-6 mb-8 shadow-lg animate-fade-in-up">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <h4 class="text-xl font-semibold text-gray-800">Hasil Perhitungan Tarif</h4>
                    </div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Jarak Tempuh:</span>
                                <span class="font-semibold text-gray-800">{{ session('fare_result.distance') }} km</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Estimasi Waktu:</span>
                                <span class="font-semibold text-gray-800">{{ session('fare_result.duration') }} menit</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-motorcycle text-green-600"></i>
                                    <span class="text-gray-700 font-medium">Motor:</span>
                                </div>
                                <span class="font-bold text-green-600 text-lg">Rp {{ number_format(session('fare_result.motorcycle_fare'), 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-car text-blue-600"></i>
                                    <span class="text-gray-700 font-medium">Mobil:</span>
                                </div>
                                <span class="font-bold text-blue-600 text-lg">Rp {{ number_format(session('fare_result.car_fare'), 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-xl p-8 animate-fade-in-up">
                <form method="POST" action="{{ route('calculate.fare') }}" class="space-y-6">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                </div>
                                <span>Lokasi Penjemputan</span>
                            </label>
                            <input type="text" name="pickup_address" value="{{ old('pickup_address') }}" 
                                   class="w-full border-2 border-gray-200 px-4 py-4 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-800 placeholder-gray-400" 
                                   placeholder="Masukkan alamat penjemputan..." required>
                            @error('pickup_address')
                                <p class="text-red-500 text-sm mt-2 flex items-center space-x-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                                <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center">
                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                </div>
                                <span>Lokasi Tujuan</span>
                            </label>
                            <input type="text" name="destination_address" value="{{ old('destination_address') }}" 
                                   class="w-full border-2 border-gray-200 px-4 py-4 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-800 placeholder-gray-400" 
                                   placeholder="Masukkan alamat tujuan..." required>
                            @error('destination_address')
                                <p class="text-red-500 text-sm mt-2 flex items-center space-x-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Kendaraan</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="service_type" value="motorcycle" class="sr-only peer" checked>
                                    <div class="border-2 border-gray-200 rounded-xl p-4 text-center hover:border-green-300 peer-checked:border-green-500 peer-checked:bg-green-50 transition-all duration-200">
                                        <i class="fas fa-motorcycle text-2xl text-green-600 mb-2"></i>
                                        <div class="font-semibold text-gray-800">Motor</div>
                                        <div class="text-sm text-gray-500">Lebih Cepat & Murah</div>
                                        <div class="text-xs text-green-600 font-medium mt-1">Mulai Rp 8.000</div>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="service_type" value="car" class="sr-only peer">
                                    <div class="border-2 border-gray-200 rounded-xl p-4 text-center hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all duration-200">
                                        <i class="fas fa-car text-2xl text-blue-600 mb-2"></i>
                                        <div class="font-semibold text-gray-800">Mobil</div>
                                        <div class="text-sm text-gray-500">Lebih Nyaman & Aman</div>
                                        <div class="text-xs text-blue-600 font-medium mt-1">Mulai Rp 11.000</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4 rounded-xl hover:shadow-lg transform hover:scale-[1.02] transition-all duration-200 font-semibold text-lg flex items-center justify-center space-x-2">
                        <i class="fas fa-calculator"></i>
                        <span>Hitung Estimasi Tarif</span>
                    </button>
                </form>
                
                <div class="text-center mt-8 pt-6 border-t border-gray-100">
                    <p class="text-gray-600 mb-4 font-medium">Siap untuk memesan perjalanan?</p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-200 font-medium">
                            <i class="fas fa-user-plus mr-2"></i>
                            Daftar Sekarang
                        </a>
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-200 font-medium">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sudah Punya Akun?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Driver CTA Section -->
    <section class="py-20 bg-gradient-to-r from-green-600 to-emerald-600 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="container mx-auto px-6 text-center relative z-10">
            <div class="animate-fade-in-up">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-handshake text-3xl"></i>
                </div>
                <h3 class="text-4xl font-bold mb-4">Bergabung Sebagai Driver</h3>
                <p class="text-xl mb-8 max-w-2xl mx-auto opacity-90">
                    Dapatkan penghasilan tambahan dengan bergabung sebagai driver AnterinAja. 
                    Fleksibel, mudah, dan menguntungkan!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
                    <div class="flex items-center space-x-2 text-green-100">
                        <i class="fas fa-check-circle"></i>
                        <span>Pendaftaran Gratis</span>
                    </div>
                    <div class="flex items-center space-x-2 text-green-100">
                        <i class="fas fa-check-circle"></i>
                        <span>Komisi Kompetitif</span>
                    </div>
                    <div class="flex items-center space-x-2 text-green-100">
                        <i class="fas fa-check-circle"></i>
                        <span>Jadwal Fleksibel</span>
                    </div>
                </div>
                <a href="{{ url('/register?role=driver') }}" 
                   class="inline-flex items-center px-8 py-4 bg-white text-green-600 rounded-full font-bold text-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    <i class="fas fa-motorcycle mr-2"></i>
                    Daftar Jadi Driver
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-motorcycle text-white text-sm"></i>
                        </div>
                        <h4 class="text-xl font-bold text-white">AnterinAja</h4>
                    </div>
                    <p class="text-gray-400 mb-4">Solusi transportasi online terpercaya dengan harga terjangkau untuk semua kalangan.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors duration-200">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-400 transition-colors duration-200">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-pink-600 transition-colors duration-200">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h5 class="font-semibold text-white mb-4">Layanan</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Ojek Online</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Kurir & Delivery</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Antar Jemput</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Cek Ongkir</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold text-white mb-4">Perusahaan</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Karir</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Kontak</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold text-white mb-4">Bantuan</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">FAQ</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center">
                <p class="text-gray-400">
                    &copy; {{ date('Y') }} AnterinAja. Semua hak dilindungi. 
                    <span class="text-blue-400">Dibuat dengan ❤️ di Indonesia</span>
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);

        // Observe all sections
        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });
    </script>

</body>
</html>
