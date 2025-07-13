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


    <section class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">
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
                            <div class="relative">
                                <textarea id="pickup_address" rows="2" class="w-full border border-gray-300 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Masukkan alamat penjemputan..."></textarea>
                                <input type="hidden" id="pickup_latitude">
                                <input type="hidden" id="pickup_longitude">
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
                            <div class="relative">
                                <textarea id="destination_address" rows="2" class="w-full border border-gray-300 rounded-lg p-3 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Masukkan alamat tujuan..."></textarea>
                                <input type="hidden" id="destination_latitude">
                                <input type="hidden" id="destination_longitude">
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
                            Klik tombol "Pilih di Peta" untuk menentukan lokasi
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
                    <div id="fareEstimation" class="hidden bg-white border border-blue-200 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-money-bill-wave text-blue-500"></i>
                            <h3 class="text-lg font-semibold">Estimasi Tarif</h3>
                        </div>
                        <div id="fareDetails"></div>
                    </div>
                    
                    <!-- Calculate Button -->
                    <button onclick="calculateFare()" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-3 px-6 rounded-lg font-semibold text-lg transition-all transform hover:scale-[1.02]">
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

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Polyline Encoded -->
    <script src="https://cdn.jsdelivr.net/npm/polyline-encoded@0.0.9/Polyline.encoded.js"></script>
    
    <script>
    // Map variables
    let map;
    let pickupMarker = null;
    let destinationMarker = null;
    let routeControl = null;
    let currentLocationMode = null;
    let tempMarker = null;

    // Initialize map
    function initMap() {
        try {
            // Default center (Jakarta)
            map = L.map('map').setView([-6.2088, 106.8456], 13);
            
            // Add OpenStreetMap tiles with error handling
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
            }).on('tileerror', function() {
                // Fallback tile server
                L.tileLayer('https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
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
                '<div class="p-3 bg-red-50 text-red-700">Gagal memuat peta. Silakan refresh halaman.</div>';
        }
    }

    // Handle map click
    function handleMapClick(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        // Remove temp marker if exists
        if (tempMarker) {
            map.removeLayer(tempMarker);
        }
        
        // Add temporary marker
        tempMarker = L.marker([lat, lng], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(map);
        
        // Store coordinates
        window.selectedLat = lat;
        window.selectedLng = lng;
        
        // Get address using reverse geocoding
        reverseGeocode(lat, lng, currentLocationMode);
    }

    // Reverse geocoding using Nominatim
    function reverseGeocode(lat, lng, locationType) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                const address = data.display_name || `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
                
                if (locationType === 'pickup') {
                    document.getElementById('pickup_address').value = address;
                } else if (locationType === 'destination') {
                    document.getElementById('destination_address').value = address;
                }
            })
            .catch(error => {
                console.log('Reverse geocoding error:', error);
                const address = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
                if (locationType === 'pickup') {
                    document.getElementById('pickup_address').value = address;
                } else if (locationType === 'destination') {
                    document.getElementById('destination_address').value = address;
                }
            });
    }

    // Set location mode
    function setLocationMode(type) {
        currentLocationMode = type;
        document.getElementById('mapControls').classList.remove('hidden');
        document.getElementById('mapMode').textContent = `Mode: Pilih lokasi ${type === 'pickup' ? 'penjemputan' : 'tujuan'}`;
        document.getElementById('mapInstructions').innerHTML = `
            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
            Klik pada peta untuk memilih lokasi ${type === 'pickup' ? 'penjemputan' : 'tujuan'}
        `;
    }

    // Confirm location
    function confirmLocation() {
        if (!window.selectedLat || !window.selectedLng) {
            alert('Silakan pilih lokasi pada peta terlebih dahulu');
            return;
        }
        
        const lat = window.selectedLat;
        const lng = window.selectedLng;
        const address = currentLocationMode === 'pickup' ? 
            document.getElementById('pickup_address').value : 
            document.getElementById('destination_address').value;
        
        // Remove temp marker
        if (tempMarker) {
            map.removeLayer(tempMarker);
        }
        
        if (currentLocationMode === 'pickup') {
            // Set coordinates
            document.getElementById('pickup_latitude').value = lat;
            document.getElementById('pickup_longitude').value = lng;
            
            // Remove existing pickup marker
            if (pickupMarker) {
                map.removeLayer(pickupMarker);
            }
            
            // Add pickup marker
            pickupMarker = L.marker([lat, lng], {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            }).addTo(map).bindPopup('Lokasi Penjemputan').openPopup();
            
        } else if (currentLocationMode === 'destination') {
            // Set coordinates
            document.getElementById('destination_latitude').value = lat;
            document.getElementById('destination_longitude').value = lng;
            
            // Remove existing destination marker
            if (destinationMarker) {
                map.removeLayer(destinationMarker);
            }
            
            // Add destination marker
            destinationMarker = L.marker([lat, lng], {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            }).addTo(map).bindPopup('Lokasi Tujuan').openPopup();
        }
        
        // Clear location mode
        cancelLocationMode();
        
        // Draw route if both locations are set
        drawRoute();
    }

    // Cancel location mode
    function cancelLocationMode() {
        currentLocationMode = null;
        document.getElementById('mapControls').classList.add('hidden');
        document.getElementById('mapInstructions').innerHTML = `
            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
            Klik tombol "Pilih di Peta" untuk menentukan lokasi
        `;
        
        // Remove temp marker
        if (tempMarker) {
            map.removeLayer(tempMarker);
            tempMarker = null;
        }
        
        // Clear selected coordinates
        window.selectedLat = null;
        window.selectedLng = null;
    }

    // Draw route between pickup and destination
    function drawRoute() {
        const pickupLat = document.getElementById('pickup_latitude').value;
        const pickupLng = document.getElementById('pickup_longitude').value;
        const destLat = document.getElementById('destination_latitude').value;
        const destLng = document.getElementById('destination_longitude').value;
        
        if (pickupLat && pickupLng && destLat && destLng) {
            // Get route from OSRM
            getOSRMRoute(pickupLat, pickupLng, destLat, destLng);
        }
    }

    // Get route from OSRM API
    function getOSRMRoute(pickupLat, pickupLng, destLat, destLng) {
        const url = `https://router.project-osrm.org/route/v1/driving/${pickupLng},${pickupLat};${destLng},${destLat}?overview=full&geometries=polyline`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.routes && data.routes.length > 0) {
                    drawOSRMRoute(data.routes[0].geometry);
                } else {
                    // Fallback to simple line
                    drawSimpleRoute(pickupLat, pickupLng, destLat, destLng);
                }
            })
            .catch(error => {
                console.log('OSRM route error:', error);
                // Fallback to simple line
                drawSimpleRoute(pickupLat, pickupLng, destLat, destLng);
            });
    }

    // Draw OSRM route using polyline
    function drawOSRMRoute(polyline) {
        // Remove existing route
        if (routeControl) {
            map.removeLayer(routeControl);
        }
        
        // Decode polyline
        const coordinates = L.Polyline.fromEncoded(polyline).getLatLngs();
        
        // Draw route
        routeControl = L.polyline(coordinates, {
            color: '#3B82F6',
            weight: 4,
            opacity: 0.8
        }).addTo(map);
        
        // Fit map to route bounds
        if (coordinates.length > 0) {
            map.fitBounds(routeControl.getBounds(), { padding: [20, 20] });
        }
    }

    // Get current location
    function getCurrentLocation(type) {
        const button = document.getElementById('currentLocationText');
        button.innerHTML = `
            <i class="fas fa-spinner fa-spin mr-1"></i>
            Mendeteksi lokasi...
        `;
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                document.getElementById(type + '_latitude').value = lat;
                document.getElementById(type + '_longitude').value = lng;
                
                // Update map
                map.setView([lat, lng], 15);
                
                // Add marker
                if (type === 'pickup') {
                    if (pickupMarker) {
                        map.removeLayer(pickupMarker);
                    }
                    pickupMarker = L.marker([lat, lng], {
                        icon: L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        })
                    }).addTo(map).bindPopup('Lokasi Anda').openPopup();
                }
                
                // Reverse geocoding
                reverseGeocode(lat, lng, type);
                
                // Draw route if both locations are set
                drawRoute();
                
                // Reset button text
                button.innerHTML = `
                    <i class="fas fa-check mr-1"></i>
                    Lokasi ditemukan
                `;
                
                setTimeout(() => {
                    button.textContent = 'Lokasi Saya';
                }, 2000);
                
            }, function(error) {
                alert('Gagal mendapatkan lokasi: ' + error.message);
                button.textContent = 'Lokasi Saya';
            });
        } else {
            alert('Browser tidak mendukung geolokasi');
            button.textContent = 'Lokasi Saya';
        }
    }

    // Calculate fare using OSRM
    function calculateFare() {
        const pickupLat = document.getElementById('pickup_latitude').value;
        const pickupLng = document.getElementById('pickup_longitude').value;
        const destLat = document.getElementById('destination_latitude').value;
        const destLng = document.getElementById('destination_longitude').value;
        const serviceType = document.querySelector('input[name="service_type"]:checked')?.value;
        
        if (!pickupLat || !pickupLng || !destLat || !destLng || !serviceType) {
            alert('Silakan lengkapi lokasi penjemputan, tujuan, dan pilih jenis kendaraan');
            return;
        }
        
        // Show loading state
        document.getElementById('fareDetails').innerHTML = `
            <div class="flex items-center justify-center py-4">
                <i class="fas fa-spinner fa-spin text-blue-500 text-xl mr-2"></i>
                <span class="text-blue-500">Menghitung tarif...</span>
            </div>
        `;
        document.getElementById('fareEstimation').classList.remove('hidden');
        
        // Get route from OSRM
        const url = `https://router.project-osrm.org/route/v1/driving/${pickupLng},${pickupLat};${destLng},${destLat}?overview=false`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.code !== 'Ok' || !data.routes || data.routes.length === 0) {
                    throw new Error('Gagal mendapatkan rute');
                }
                
                const route = data.routes[0];
                const distance = (route.distance / 1000).toFixed(1); // in km
                const duration = Math.ceil(route.duration / 60); // in minutes
                
                // Calculate fare based on service type
                let fare;
                if (serviceType === 'motorcycle') {
                    // Motor: 9000 for first 2 km, then 2500 per km, plus 200 per minute
                    fare = 8800 + Math.max(0, distance - 4) * 2000;
                } else {
                    // Car: 11000 for first 2 km, then 3500 per km, plus 200 per minute
                    fare = 11000 + Math.max(0, distance - 4) * 3500;
                }
                
                // Format fare
                fare = Math.round(fare);
                
                // Display result
                document.getElementById('fareDetails').innerHTML = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">Jarak</p>
                                <p class="font-semibold">${distance} km</p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">Estimasi Waktu</p>
                                <p class="font-semibold">${duration} menit</p>
                            </div>
                        </div>
                        <div class="text-center p-3 ${serviceType === 'motorcycle' ? 'bg-green-50' : 'bg-blue-50'} rounded-lg">
                            <p class="text-sm ${serviceType === 'motorcycle' ? 'text-green-600' : 'text-blue-600'}">Tarif ${serviceType === 'motorcycle' ? 'Motor' : 'Mobil'}</p>
                            <p class="font-bold text-lg ${serviceType === 'motorcycle' ? 'text-green-700' : 'text-blue-700'}">Rp ${fare.toLocaleString('id-ID')}</p>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p>* Tarif sudah termasuk komisi driver</p>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('fareDetails').innerHTML = `
                    <div class="p-3 bg-red-50 text-red-700 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Gagal menghitung tarif: ${error.message || 'Silakan coba lagi'}
                    </div>
                `;
            });
    }

    // Initialize map when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        
        // Fix map size after load
        setTimeout(function() {
            if (map) map.invalidateSize();
        }, 100);
    });
    </script>

</body>
</html>
