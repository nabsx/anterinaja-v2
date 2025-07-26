@extends('layouts.app')

@section('title', 'Rating & Ulasan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Rating & Ulasan</h1>
                    <p class="text-gray-600">Lihat feedback dari customer Anda</p>
                </div>
                <div class="text-right">
                    <div class="flex items-center space-x-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-600">{{ number_format($averageRating, 1) }}</div>
                            <div class="flex items-center justify-center mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($averageRating))
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @elseif($i == ceil($averageRating) && $averageRating - floor($averageRating) >= 0.5)
                                        <svg class="w-5 h-5 text-yellow-400" viewBox="0 0 20 20">
                                            <defs>
                                                <linearGradient id="half-fill">
                                                    <stop offset="50%" stop-color="#FCD34D"/>
                                                    <stop offset="50%" stop-color="#E5E7EB"/>
                                                </linearGradient>
                                            </defs>
                                            <path fill="url(#half-fill)" d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <div class="text-sm text-gray-600">{{ $totalRatings }} ulasan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating Statistics -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik Rating</h3>
            <div class="space-y-3">
                @for($star = 5; $star >= 1; $star--)
                    @php
                        $count = $ratingStats[$star] ?? 0;
                        $percentage = $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0;
                    @endphp
                    <div class="flex items-center">
                        <div class="flex items-center w-20">
                            <span class="text-sm font-medium text-gray-700">{{ $star }}</span>
                            <svg class="w-4 h-4 text-yellow-400 fill-current ml-1" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        </div>
                        <div class="flex-1 mx-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <div class="w-12 text-right">
                            <span class="text-sm text-gray-600">{{ $count }}</span>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Filter:</label>
                <select id="ratingFilter" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Rating</option>
                    <option value="5">5 Bintang</option>
                    <option value="4">4 Bintang</option>
                    <option value="3">3 Bintang</option>
                    <option value="2">2 Bintang</option>
                    <option value="1">1 Bintang</option>
                </select>
                <select id="timeFilter" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Waktu</option>
                    <option value="today">Hari Ini</option>
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                </select>
            </div>
        </div>

        <!-- Ratings List -->
        <div class="space-y-4">
            @forelse($ratings as $rating)
                <div class="bg-white rounded-lg shadow-sm border p-6 rating-item" 
                     data-rating="{{ $rating->rating }}" 
                     data-date="{{ $rating->created_at->format('Y-m-d') }}">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <!-- Anonymous Customer Avatar -->
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Customer</h4>
                                <p class="text-sm text-gray-600">{{ $rating->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $rating->rating)
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @endif
                            @endfor
                            <span class="ml-2 text-sm font-medium text-gray-700">{{ $rating->rating }}/5</span>
                        </div>
                    </div>

                    @if($rating->review)
                        <div class="mb-4">
                            <p class="text-gray-700 leading-relaxed">{{ $rating->review }}</p>
                        </div>
                    @endif

                    <!-- Rating Helpful Actions -->
                    <div class="border-t pt-4 mt-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button class="flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L9 7v13m-3-4h-2m0-4h2m0-4h2"></path>
                                    </svg>
                                    Membantu
                                </button>
                                <button class="flex items-center text-sm text-gray-600 hover:text-red-600 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    Laporkan
                                </button>
                            </div>
                            @if($rating->rating >= 4)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Rating Positif
                                </span>
                            @elseif($rating->rating >= 3)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Rating Netral
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Rating Negatif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Rating</h3>
                    <p class="text-gray-600">Anda belum menerima rating dari customer. Terus berikan pelayanan terbaik!</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($ratings->hasPages())
            <div class="mt-6">
                {{ $ratings->links() }}
            </div>
        @endif

        <!-- Back Button -->
        <div class="mt-6">
            <a href="{{ route('driver.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingFilter = document.getElementById('ratingFilter');
    const timeFilter = document.getElementById('timeFilter');
    const ratingItems = document.querySelectorAll('.rating-item');

    function filterRatings() {
        const selectedRating = ratingFilter.value;
        const selectedTime = timeFilter.value;
        const today = new Date();
        
        ratingItems.forEach(item => {
            let showItem = true;
            
            // Filter by rating
            if (selectedRating && item.dataset.rating !== selectedRating) {
                showItem = false;
            }
            
            // Filter by time
            if (selectedTime && showItem) {
                const itemDate = new Date(item.dataset.date);
                
                switch(selectedTime) {
                    case 'today':
                        if (itemDate.toDateString() !== today.toDateString()) {
                            showItem = false;
                        }
                        break;
                    case 'week':
                        const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                        if (itemDate < weekAgo) {
                            showItem = false;
                        }
                        break;
                    case 'month':
                        const monthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
                        if (itemDate < monthAgo) {
                            showItem = false;
                        }
                        break;
                }
            }
            
            item.style.display = showItem ? 'block' : 'none';
        });
    }

    ratingFilter.addEventListener('change', filterRatings);
    timeFilter.addEventListener('change', filterRatings);
});
</script>
@endsection
