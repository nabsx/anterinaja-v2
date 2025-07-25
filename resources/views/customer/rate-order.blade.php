@extends('layouts.app')

@section('title', 'Beri Rating')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center mb-4">
                <a href="{{ route('customer.orders.show', $order->id) }}" 
                   class="text-gray-600 hover:text-gray-800 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Beri Rating</h1>
            </div>
            
            <!-- Order Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Kode Pesanan:</span>
                    <span class="font-medium">{{ $order->order_code }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Tanggal:</span>
                    <span class="font-medium">{{ $order->completed_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Tarif:</span>
                    <span class="font-bold text-green-600">Rp {{ number_format($order->fare_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Driver Info -->
            @if($order->driver)
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3">
                    <i class="fas fa-user-tie mr-2 text-blue-500"></i>Driver
                </h3>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                        {{ strtoupper(substr($order->driver->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $order->driver->user->name }}</p>
                        <p class="text-sm text-gray-600">{{ ucfirst($order->driver->vehicle_type) }}</p>
                        @if($order->driver->vehicle_plate)
                            <p class="text-sm text-gray-600">{{ $order->driver->vehicle_plate }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Rating Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Bagaimana pengalaman Anda?</h2>
            
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('customer.orders.rate.submit', $order->id) }}">
                @csrf
                
                <!-- Star Rating -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Rating</label>
                    <div class="flex items-center space-x-2">
                        <div class="star-rating flex space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" 
                                        class="star text-3xl text-gray-300 hover:text-yellow-400 focus:outline-none transition-colors duration-200" 
                                        data-rating="{{ $i }}">
                                    <i class="fas fa-star"></i>
                                </button>
                            @endfor
                        </div>
                        <span id="rating-text" class="text-sm text-gray-600 ml-4">Pilih rating</span>
                    </div>
                    <input type="hidden" name="rating" id="rating-input" value="{{ old('rating') }}" required>
                    @error('rating')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Review Text -->
                <div class="mb-6">
                    <label for="review" class="block text-sm font-medium text-gray-700 mb-2">
                        Ulasan (Opsional)
                    </label>
                    <textarea name="review" 
                              id="review" 
                              rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Ceritakan pengalaman Anda dengan driver ini...">{{ old('review') }}</textarea>
                    @error('review')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" 
                            id="submit-btn"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-star mr-2"></i>Kirim Rating
                    </button>
                    <a href="{{ route('customer.orders.show', $order->id) }}" 
                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-6 rounded-lg transition duration-200 text-center">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating-input');
    const ratingText = document.getElementById('rating-text');
    const submitBtn = document.getElementById('submit-btn');
    
    const ratingLabels = {
        1: 'Sangat Buruk',
        2: 'Buruk', 
        3: 'Cukup',
        4: 'Baik',
        5: 'Sangat Baik'
    };

    // Set initial state if there's an old value
    const oldRating = ratingInput.value;
    if (oldRating) {
        updateStars(parseInt(oldRating));
    }

    stars.forEach((star, index) => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            updateStars(rating);
            ratingInput.value = rating;
            ratingText.textContent = ratingLabels[rating];
            submitBtn.disabled = false;
        });

        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            highlightStars(rating);
        });
    });

    document.querySelector('.star-rating').addEventListener('mouseleave', function() {
        const currentRating = parseInt(ratingInput.value) || 0;
        updateStars(currentRating);
    });

    function updateStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }

    function highlightStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }

    // Initially disable submit button if no rating
    if (!oldRating) {
        submitBtn.disabled = true;
    }
});
</script>
@endsection
