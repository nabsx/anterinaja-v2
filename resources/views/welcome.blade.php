<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>AnterinAja - Ojek & Kurir Online Terpercaya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { 
            height: 300px;
            z-index: 0;
        }
        .leaflet-container {
            background: #f8fafc !important;
        }
        .search-results {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            position: absolute;
            width: 100%;
        }
        .search-result-item {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .search-result-item:hover {
            background-color: #f8fafc;
        }
        .search-result-item:last-child {
            border-bottom: none;
        }
        .search-input-container {
            position: relative;
        }
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
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
                    Aplikasi transportasi online yang dibuat oleh komunitas driver untuk kesejahteraan bersama.
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
    <section id="cek-ongkir" class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 md:p-8">
                    <h2 class="text-3xl font-bold text-center text-gray-800 mb-2">Cek Tarif Perjalanan</h2>
                    <p class="text-gray-600 text-center mb-6">Hitung estimasi biaya perjalanan Anda dengan akurat</p>
                    
                    <div class="space-y-6">
                        <!-- Location Inputs -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Pickup Location -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                    Lokasi Penjemputan
                                </label>
                                <div class="search-input-container">
                                    <textarea id="pickup_address" rows="2" class="w-full border border-gray-300 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none" placeholder="Cari alamat Penjemputan..."></textarea>
                                    <div id="pickup_search_results" class="search-results hidden"></div>
                                    <input type="hidden" id="pickup_latitude">
                                    <input type="hidden" id="pickup_longitude">
                                    <div id="pickup_loading" class="absolute right-3 top-3 hidden">
                                        <div class="loading-spinner"></div>
                                    </div>
                                </div>
                                <div class="flex gap-2 mt-2">
                                    <button onclick="getCurrentLocation('pickup')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm py-2 px-3 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-location-arrow mr-2"></i>
                                        <span id="currentLocationText">Lokasi Saya</span>
                                    </button>
                                    <button onclick="setLocationMode('pickup')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm py-2 px-3 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        Pilih di Peta
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Destination Location -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                    Lokasi Tujuan
                                </label>
                                <div class="search-input-container">
                                    <textarea id="destination_address" rows="2" class="w-full border border-gray-300 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none" placeholder="Cari alamat Tujuan..."></textarea>
                                    <div id="destination_search_results" class="search-results hidden"></div>
                                    <input type="hidden" id="destination_latitude">
                                    <input type="hidden" id="destination_longitude">
                                    <div id="destination_loading" class="absolute right-3 top-3 hidden">
                                        <div class="loading-spinner"></div>
                                    </div>
                                </div>
                                <button onclick="setLocationMode('destination')" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm py-2 px-3 rounded-lg mt-2 flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    Pilih di Peta
                                </button>
                            </div>
                        </div>
                        
                        <!-- Map Container -->
                        <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                            <div id="mapInstructions" class="text-sm text-gray-600 p-3 bg-blue-50 border-b border-blue-100">
                                <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                Ketik alamat di kotak pencarian atau klik "Pilih di Peta"
                            </div>
                            <div id="map"></div>
                            <div id="mapControls" class="hidden p-3 bg-yellow-50 border-t border-yellow-200">
                                <p class="text-sm text-yellow-800 mb-2 flex items-center">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                    <span id="mapMode">Mode:</span> Klik pada peta untuk memilih lokasi
                                </p>
                                <div class="flex gap-2">
                                    <button onclick="confirmLocation()" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg">
                                        <i class="fas fa-check mr-1"></i> Konfirmasi
                                    </button>
                                    <button onclick="cancelLocationMode()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg">
                                        <i class="fas fa-times mr-1"></i> Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Vehicle Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Jenis Kendaraan</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" name="service_type" value="motorcycle" class="sr-only peer" checked>
                                    <div class="border-2 border-gray-200 rounded-xl p-4 text-center hover:border-green-300 peer-checked:border-green-500 peer-checked:bg-green-50 transition-all">
                                        <i class="fas fa-motorcycle text-2xl text-green-600 mb-2"></i>
                                        <div class="font-semibold">Motor</div>
                                        <div class="text-sm text-gray-500">Cepat & Hemat</div>
                                        <div class="text-xs text-green-600 font-medium mt-1">Mulai Rp 9.000</div>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="service_type" value="car" class="sr-only peer">
                                    <div class="border-2 border-gray-200 rounded-xl p-4 text-center hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                        <i class="fas fa-car text-2xl text-blue-600 mb-2"></i>
                                        <div class="font-semibold">Mobil</div>
                                        <div class="text-sm text-gray-500">Nyaman & Aman</div>
                                        <div class="text-xs text-blue-600 font-medium mt-1">Mulai Rp 11.000</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Fare Estimation -->
                        <div id="fareEstimation" class="hidden bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                                <h3 class="text-lg font-semibold text-gray-800">Estimasi Tarif</h3>
                            </div>
                            <div id="fareDetails"></div>
                        </div>
                        
                        <!-- Calculate Button -->
                        <button onclick="calculateFare()" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-3 px-6 rounded-lg font-semibold text-lg transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-calculator mr-2"></i>
                            Hitung Tarif
                        </button>
                    </div>
                </div>
                
                <!-- Registration CTA -->
                <div class="bg-gray-50 border-t border-gray-200 p-6 text-center">
                    <p class="text-gray-600 mb-3">Siap memesan perjalanan?</p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:shadow-lg transition-all">
                            <i class="fas fa-user-plus mr-2"></i>
                            Daftar Sekarang
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Masuk
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
                <a href="{{ route('register') }}" 
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
                    &copy; 2024 AnterinAja. Semua hak dilindungi. 
                    <span class="text-blue-400">Dibuat dengan @kodeframe dengan sepenuh hati ❤️</span>
                </p>
            </div>
        </div>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
    // Map variables
    let map;
    let pickupMarker = null;
    let destinationMarker = null;
    let routeControl = null;
    let currentLocationMode = null;
    let tempMarker = null;
    let searchTimeouts = {};
    let routePolyline = null;

    // Initialize map when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        setupEventListeners();
    });

    // Initialize map
    function initMap() {
        try {
            // Default center (Semarang)
            map = L.map('map').setView([-6.966667, 110.416664], 12);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(map);
            
            // Add click event to map
            map.on('click', function(e) {
                if (currentLocationMode) {
                    handleMapClick(e);
                }
            });
            
        } catch (e) {
            console.error('Map initialization error:', e);
            document.getElementById('map').innerHTML = 
                '<div class="p-4 bg-red-50 text-red-700 text-center">Gagal memuat peta. Silakan refresh halaman.</div>';
        }
    }

    // Setup event listeners
    function setupEventListeners() {
        // Address search event listeners
        document.getElementById('pickup_address').addEventListener('input', function(e) {
            handleAddressInput(e.target.value, 'pickup');
        });
        
        document.getElementById('destination_address').addEventListener('input', function(e) {
            handleAddressInput(e.target.value, 'destination');
        });

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-input-container')) {
                hideSearchResults('pickup');
                hideSearchResults('destination');
            }
        });
    }

    // Handle address input with debouncing
    function handleAddressInput(query, type) {
        // Clear existing timeout
        if (searchTimeouts[type]) {
            clearTimeout(searchTimeouts[type]);
        }

        // Hide results if query is too short
        if (query.length < 3) {
            hideSearchResults(type);
            return;
        }

        // Show loading spinner
        showLoading(type, true);

        // Set timeout for search
        searchTimeouts[type] = setTimeout(() => {
            searchAddress(query, type);
        }, 500);
    }

    // Search address using Nominatim API
    function searchAddress(query, type) {
        const resultsContainer = document.getElementById(type + '_search_results');
        
        // Add priority for Semarang area
        const semarangQuery = query.includes('Semarang') ? query : query + ' Semarang';
        
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(semarangQuery)}&limit=5&countrycodes=ID&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                showLoading(type, false);
                
                if (data.length === 0) {
                    // Try without Semarang if no results
                    return fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=ID&addressdetails=1`)
                        .then(response => response.json());
                }
                return data;
            })
            .then(data => {
                displaySearchResults(data, type);
            })
            .catch(error => {
                console.error('Search error:', error);
                showLoading(type, false);
                resultsContainer.innerHTML = '<div class="search-result-item text-red-600">Gagal mencari alamat. Coba lagi.</div>';
                resultsContainer.classList.remove('hidden');
            });
    }

    // Display search results
    function displaySearchResults(results, type) {
        const resultsContainer = document.getElementById(type + '_search_results');
        
        if (results.length === 0) {
            resultsContainer.innerHTML = '<div class="search-result-item text-gray-500">Tidak ada hasil ditemukan</div>';
            resultsContainer.classList.remove('hidden');
            return;
        }

        let html = '';
        results.forEach(result => {
            const displayName = result.display_name;
            const shortName = displayName.split(',').slice(0, 3).join(', ');
            
            html += `
                <div class="search-result-item" onclick="selectSearchResult('${result.lat}', '${result.lon}', '${displayName.replace(/'/g, "\\'")}', '${type}')">
                    <div class="font-medium text-gray-800">${shortName}</div>
                    <div class="text-sm text-gray-500">${displayName}</div>
                </div>
            `;
        });

        resultsContainer.innerHTML = html;
        resultsContainer.classList.remove('hidden');
    }

    // Select search result
    function selectSearchResult(lat, lon, displayName, type) {
        // Set coordinates
        document.getElementById(type + '_latitude').value = lat;
        document.getElementById(type + '_longitude').value = lon;
        
        // Set address text
        document.getElementById(type + '_address').value = displayName;
        
        // Hide search results
        hideSearchResults(type);
        
        // Add marker to map
        addMarker(lat, lon, type);
        
        // Update route if both locations are set
        updateRoute();
    }

    // Add marker to map
    function addMarker(lat, lon, type) {
        const latLng = L.latLng(lat, lon);
        
        // Remove existing marker
        if (type === 'pickup' && pickupMarker) {
            map.removeLayer(pickupMarker);
        } else if (type === 'destination' && destinationMarker) {
            map.removeLayer(destinationMarker);
        }
        
        // Create new marker
        const icon = L.divIcon({
            html: `<div class="w-6 h-6 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-xs font-bold" style="background-color: ${type === 'pickup' ? '#10b981' : '#ef4444'}">
                ${type === 'pickup' ? 'A' : 'B'}
            </div>`,
            className: 'custom-marker',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });
        
        const marker = L.marker(latLng, { icon: icon }).addTo(map);
        
        // Store marker reference
        if (type === 'pickup') {
            pickupMarker = marker;
        } else {
            destinationMarker = marker;
        }
        
        // Pan map to show marker
        map.setView(latLng, Math.max(map.getZoom(), 15));
    }

    // Update route between pickup and destination
    function updateRoute() {
    const pickupLat = document.getElementById('pickup_latitude').value;
    const pickupLon = document.getElementById('pickup_longitude').value;
    const destLat = document.getElementById('destination_latitude').value;
    const destLon = document.getElementById('destination_longitude').value;
    const serviceType = document.querySelector('input[name="service_type"]:checked')?.value;

    if (pickupLat && pickupLon && destLat && destLon) {
        const url = `https://router.project-osrm.org/route/v1/driving/${pickupLon},${pickupLat};${destLon},${destLat}?overview=full&geometries=geojson`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (!data.routes || data.routes.length === 0) {
                    alert('Gagal menemukan rute jalan.');
                    return;
                }

                const route = data.routes[0];
                const routeCoords = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
                const distanceKm = route.distance / 1000;

                // Hapus garis lama
                if (routePolyline) map.removeLayer(routePolyline);
                routePolyline = L.polyline(routeCoords, { color: '#3b82f6', weight: 4 }).addTo(map);

                // Tampilkan semua marker
                const group = new L.featureGroup([pickupMarker, destinationMarker]);
                map.fitBounds(group.getBounds().pad(0.1));

                // Hitung ongkir
                const baseFare = serviceType === 'motorcycle' ? 8000 : 11000;
                const perKmRate = serviceType === 'motorcycle' ? 2000 : 3500;
                const extraDistance = distanceKm > 4 ? (distanceKm - 4) : 0;
                const extraFare = Math.round(extraDistance * perKmRate);
                const subtotal = baseFare + extraFare;
                const commission = Math.ceil(subtotal * 0.10);
                const totalFare = subtotal + commission;
                const estimatedTime = Math.round(distanceKm * 3);

                displayFareEstimation(totalFare, distanceKm, estimatedTime, serviceType);
            })
            .catch(err => {
                console.error(err);
                alert('Gagal mengambil data rute jalan.');
            });
    }
}

    // Hide search results
    function hideSearchResults(type) {
        document.getElementById(type + '_search_results').classList.add('hidden');
    }

    // Show/hide loading spinner
    function showLoading(type, show) {
        const loadingElement = document.getElementById(type + '_loading');
        if (show) {
            loadingElement.classList.remove('hidden');
        } else {
            loadingElement.classList.add('hidden');
        }
    }

    // Get current location
    function getCurrentLocation(type) {
        const button = document.getElementById('currentLocationText');
        
        if (navigator.geolocation) {
            button.textContent = 'Mencari...';
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    
                    // Reverse geocoding to get address
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1`)
                        .then(response => response.json())
                        .then(data => {
                            const address = data.display_name || `${lat}, ${lon}`;
                            
                            // Set values
                            document.getElementById(type + '_latitude').value = lat;
                            document.getElementById(type + '_longitude').value = lon;
                            document.getElementById(type + '_address').value = address;
                            
                            // Add marker
                            addMarker(lat, lon, type);
                            
                            // Update route
                            updateRoute();
                            
                            button.textContent = 'Lokasi Saya';
                        })
                        .catch(error => {
                            console.error('Reverse geocoding error:', error);
                            alert('Gagal mendapatkan alamat. Coba lagi.');
                            button.textContent = 'Lokasi Saya';
                        });
                },
                function(error) {
                    console.error('Geolocation error:', error);
                    alert('Gagal mendapatkan lokasi. Pastikan GPS diaktifkan.');
                    button.textContent = 'Lokasi Saya';
                }
            );
        } else {
            alert('Geolocation tidak didukung browser ini.');
        }
    }

    // Set location mode for map selection
    function setLocationMode(type) {
        currentLocationMode = type;
        
        // Show map controls
        document.getElementById('mapControls').classList.remove('hidden');
        document.getElementById('mapMode').textContent = `Mode: Pilih lokasi ${type === 'pickup' ? 'penjemputan' : 'tujuan'}`;
        
        // Update instructions
        document.getElementById('mapInstructions').innerHTML = 
            `<i class="fas fa-hand-pointer text-blue-500 mr-1"></i> 
            Klik pada peta untuk memilih lokasi ${type === 'pickup' ? 'penjemputan' : 'tujuan'}`;
        
        // Change cursor
        document.getElementById('map').style.cursor = 'crosshair';
    }

    // Handle map click
    function handleMapClick(e) {
        const lat = e.latlng.lat;
        const lon = e.latlng.lng;
        
        // Remove temp marker if exists
        if (tempMarker) {
            map.removeLayer(tempMarker);
        }
        
        // Add temporary marker
        const icon = L.divIcon({
            html: `<div class="w-6 h-6 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-xs font-bold animate-pulse" style="background-color: ${currentLocationMode === 'pickup' ? '#10b981' : '#ef4444'}">
                ${currentLocationMode === 'pickup' ? 'A' : 'B'}
            </div>`,
            className: 'custom-marker',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });
        
        tempMarker = L.marker([lat, lon], { icon: icon }).addTo(map);
        
        // Store temporary coordinates
        window.tempLat = lat;
        window.tempLon = lon;
    }

    // Confirm location selection
    function confirmLocation() {
        if (!tempMarker) {
            alert('Pilih lokasi di peta terlebih dahulu.');
            return;
        }
        
        const lat = window.tempLat;
        const lon = window.tempLon;
        
        // Reverse geocoding
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                const address = data.display_name || `${lat}, ${lon}`;
                
                // Set values
                document.getElementById(currentLocationMode + '_latitude').value = lat;
                document.getElementById(currentLocationMode + '_longitude').value = lon;
                document.getElementById(currentLocationMode + '_address').value = address;
                
                // Remove temp marker and add permanent marker
                if (tempMarker) {
                    map.removeLayer(tempMarker);
                    tempMarker = null;
                }
                
                addMarker(lat, lon, currentLocationMode);
                
                // Update route
                updateRoute();
                
                // Cancel location mode
                cancelLocationMode();
            })
            .catch(error => {
                console.error('Reverse geocoding error:', error);
                alert('Gagal mendapatkan alamat. Coba lagi.');
            });
    }

    // Cancel location mode
    function cancelLocationMode() {
        currentLocationMode = null;
        
        // Hide map controls
        document.getElementById('mapControls').classList.add('hidden');
        
        // Reset instructions
        document.getElementById('mapInstructions').innerHTML = 
            '<i class="fas fa-info-circle text-blue-500 mr-1"></i> Ketik alamat di kotak pencarian atau klik "Pilih di Peta"';
        
        // Reset cursor
        document.getElementById('map').style.cursor = '';
        
        // Remove temp marker
        if (tempMarker) {
            map.removeLayer(tempMarker);
            tempMarker = null;
        }
    }

    // Calculate fare
    function calculateFare() {
    const pickupLat = document.getElementById('pickup_latitude').value;
    const pickupLon = document.getElementById('pickup_longitude').value;
    const destLat = document.getElementById('destination_latitude').value;
    const destLon = document.getElementById('destination_longitude').value;
    const serviceType = document.querySelector('input[name="service_type"]:checked').value;

    if (!pickupLat || !pickupLon || !destLat || !destLon) {
        alert('Silakan pilih lokasi penjemputan dan tujuan terlebih dahulu.');
        return;
    }

    const distance = calculateDistance(
        parseFloat(pickupLat), parseFloat(pickupLon),
        parseFloat(destLat), parseFloat(destLon)
    );

    let baseFare, perKmRate;

    if (serviceType === 'motorcycle') {
        baseFare = 8000;
        perKmRate = 2000;
    } else {
        baseFare = 11000;
        perKmRate = 3500;
    }

    const extraDistance = distance > 4 ? (distance - 4) : 0;
    const extraFare = Math.round(extraDistance * perKmRate);
    const subtotal = baseFare + extraFare;
    const commission = Math.ceil(subtotal * 0.10);
    const fare = subtotal + commission;

    const estimatedTime = Math.round(distance * 3);
    displayFareEstimation(fare, distance, estimatedTime, serviceType);
}

    // Calculate distance using Haversine formula
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Earth's radius in km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // Display fare estimation
    function displayFareEstimation(_, distance, estimatedTime, serviceType) {
    const fareContainer = document.getElementById('fareEstimation');
    const fareDetails = document.getElementById('fareDetails');

    const baseFare = serviceType === 'motorcycle' ? 8000 : 11000;
    const perKmRate = serviceType === 'motorcycle' ? 2000 : 3500;
    const extraDistance = distance > 4 ? (distance - 4) : 0;

    const rawExtraFare = extraDistance * perKmRate;
    const subtotal = baseFare + rawExtraFare;
    const commission = Math.ceil(subtotal * 0.10);
    const totalFare = Math.round(subtotal + commission);
    const extraFare = Math.round(rawExtraFare); // hanya untuk ditampilkan

    const vehicleIcon = serviceType === 'motorcycle' ? 'fa-motorcycle' : 'fa-car';
    const vehicleText = serviceType === 'motorcycle' ? 'Motor' : 'Mobil';
    const vehicleColor = serviceType === 'motorcycle' ? 'text-green-600' : 'text-blue-600';

    fareDetails.innerHTML = `
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                <div class="flex items-center space-x-3">
                    <i class="fas ${vehicleIcon} text-2xl ${vehicleColor}"></i>
                    <div>
                        <div class="font-semibold text-gray-800">${vehicleText}</div>
                        <div class="text-sm text-gray-500">Estimasi ${estimatedTime} menit</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-800">Rp ${totalFare.toLocaleString('id-ID')}</div>
                    <div class="text-sm text-gray-500">${distance.toFixed(1)} km</div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-2">Rincian Tarif</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Tarif Dasar (≤ 4 km)</span>
                        <span>Rp ${baseFare.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Tambahan (${extraDistance.toFixed(1)} km)</span>
                        <span>Rp ${extraFare.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Komisi 10%</span>
                        <span>Rp ${commission.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between font-semibold">
                        <span>Total</span>
                        <span>Rp ${totalFare.toLocaleString('id-ID')}</span>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center text-blue-800 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span class="font-medium">Informasi Tambahan</span>
                </div>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Tarif sudah termasuk biaya aplikasi</li>
                    <li>• Tidak ada biaya tambahan untuk bagasi ringan</li>
                    <li>• Pembayaran bisa tunai atau digital</li>
                    <li>• Estimasi waktu dapat berubah sesuai kondisi lalu lintas</li>
                </ul>
            </div>
        </div>
    `;

    fareContainer.classList.remove('hidden');
    fareContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}


    // Smooth scroll for anchor links
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
    </script>
</body>
</html>