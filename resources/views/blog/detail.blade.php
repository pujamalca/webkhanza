@extends('layouts.app')

@section('title', $blog->meta_title ?: $blog->title . ' - ' . $websiteIdentity->name)
@section('description', $blog->meta_description ?: $blog->excerpt ?: Str::limit(strip_tags($blog->content), 160))
@section('keywords', $blog->meta_keywords ? (is_array($blog->meta_keywords) ? implode(', ', $blog->meta_keywords) : $blog->meta_keywords) : ($blog->tags->pluck('name')->implode(', ')))

@section('content')
    <!-- Navigation -->
    <x-landing.navbar :website-identity="$websiteIdentity" />
    
    <!-- Article Header -->
    <article class="relative">
        @if($blog->featured_image)
            <div class="relative h-96 lg:h-[500px] overflow-hidden">
                <img src="{{ asset('storage/' . $blog->featured_image) }}" 
                     alt="{{ $blog->title }}" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                
                <!-- Article Info Overlay -->
                <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                    <div class="container mx-auto">
                        <div class="max-w-4xl">
                            @if($blog->category)
                                <div class="mb-4">
                                    <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full" 
                                          style="background-color: {{ $blog->category->color ?? '#3b82f6' }};">
                                        {{ $blog->category->name }}
                                    </span>
                                </div>
                            @endif
                            
                            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-6">
                                {{ $blog->title }}
                            </h1>
                            
                            <div class="flex flex-wrap items-center gap-6 text-white/90">
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full mr-3">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold">{{ $blog->author->name ?? 'Admin' }}</div>
                                        <div class="text-sm text-white/70">Author</div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <span>{{ $blog->published_at ? $blog->published_at->format('d F Y') : $blog->created_at->format('d F Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-2"></i>
                                    <span>{{ $blog->reading_time ?? 5 }} min read</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-eye mr-2"></i>
                                    <span>{{ number_format($blog->views_count) }} views</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Header without image -->
            <div class="py-20 bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 text-white">
                <div class="container mx-auto px-4">
                    <div class="max-w-4xl">
                        @if($blog->category)
                            <div class="mb-6">
                                <span class="inline-block px-4 py-2 text-sm font-semibold bg-white/20 backdrop-blur-sm rounded-full border border-white/30">
                                    {{ $blog->category->name }}
                                </span>
                            </div>
                        @endif
                        
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-8">
                            {{ $blog->title }}
                        </h1>
                        
                        <div class="flex flex-wrap items-center gap-6 text-white/90">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full mr-3">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <div>
                                    <div class="font-semibold">{{ $blog->author->name ?? 'Admin' }}</div>
                                    <div class="text-sm text-white/70">Author</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>{{ $blog->published_at ? $blog->published_at->format('d F Y') : $blog->created_at->format('d F Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock mr-2"></i>
                                <span>{{ $blog->reading_time ?? 5 }} min read</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-eye mr-2"></i>
                                <span>{{ number_format($blog->views_count) }} views</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Article Content -->
        <div class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="flex flex-wrap -mx-4">
                    <!-- Main Content -->
                    <div class="w-full lg:w-2/3 px-4">
                        <!-- Article Body -->
                        <div class="prose prose-lg max-w-none mb-12">
                            @if($blog->excerpt)
                                <div class="text-xl text-gray-600 leading-relaxed mb-8 p-6 bg-gray-50 rounded-2xl border-l-4 border-blue-600">
                                    {{ $blog->excerpt }}
                                </div>
                            @endif
                            
                            <div class="article-content">
                                {!! $blog->content !!}
                            </div>
                        </div>

                        <!-- Tags -->
                        @if($blog->tags->count() > 0)
                            <div class="mb-12">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tags:</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($blog->tags as $tag)
                                        <a href="{{ route('blog.index', ['search' => '#'.$tag->name]) }}" 
                                           class="inline-block px-4 py-2 bg-blue-100 text-blue-800 text-sm rounded-full hover:bg-blue-200 transition-colors duration-200">
                                            #{{ $tag->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Share Buttons -->
                        <div class="mb-12 p-6 bg-gray-50 rounded-2xl btn-override">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bagikan Artikel:</h3>
                            <div class="flex flex-wrap gap-3">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <i class="fab fa-facebook-f mr-2"></i>Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($blog->title) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors duration-200">
                                    <i class="fab fa-twitter mr-2"></i>Twitter
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-blue-800 text-white rounded-lg hover:bg-blue-900 transition-colors duration-200">
                                    <i class="fab fa-linkedin-in mr-2"></i>LinkedIn
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($blog->title . ' - ' . request()->fullUrl()) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <i class="fab fa-whatsapp mr-2"></i>WhatsApp
                                </a>
                                <button onclick="copyToClipboard('{{ request()->fullUrl() }}')" 
                                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                                    <i class="fas fa-copy mr-2"></i>Copy Link
                                </button>
                            </div>
                        </div>

                        <!-- Author Box -->
                        <div class="mb-12 p-6 bg-gradient-to-r from-blue-50 to-blue-100 rounded-2xl border border-blue-200">
                            <div class="flex items-start gap-4">
                                <div class="flex items-center justify-center w-16 h-16 bg-blue-600 text-white rounded-full flex-shrink-0">
                                    <i class="fas fa-user text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $blog->author->name ?? 'Admin' }}</h3>
                                    <p class="text-gray-600 mb-3">
                                        Penulis artikel di {{ $websiteIdentity->name }}. Berbagi informasi dan wawasan seputar kesehatan dan teknologi medis.
                                    </p>
                                    <div class="text-sm text-gray-500">
                                        <i class="fas fa-envelope mr-2"></i>{{ $blog->author->email ?? 'admin@' . strtolower($websiteIdentity->name) . '.com' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="w-full lg:w-1/3 px-4">
                        <!-- Recent Posts -->
                        @if($recentBlogs->count() > 0)
                            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-clock mr-3 text-blue-600"></i>
                                    Artikel Terbaru
                                </h3>
                                <div class="space-y-4">
                                    @foreach($recentBlogs as $recentBlog)
                                        <div class="flex gap-4 group">
                                            @if($recentBlog->featured_image)
                                                <img src="{{ asset('storage/' . $recentBlog->featured_image) }}" 
                                                     alt="{{ $recentBlog->title }}" 
                                                     class="w-16 h-16 object-cover rounded-lg flex-shrink-0 group-hover:scale-105 transition-transform duration-200">
                                            @else
                                                <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg flex-shrink-0 flex items-center justify-center">
                                                    <i class="fas fa-newspaper text-white text-sm"></i>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-semibold text-gray-900 text-sm leading-tight mb-1 line-clamp-2 group-hover:text-blue-600 transition-colors duration-200">
                                                    <a href="{{ route('blog.detail', $recentBlog->slug) }}">{{ $recentBlog->title }}</a>
                                                </h4>
                                                <div class="flex items-center text-xs text-gray-500 space-x-2">
                                                    <span>{{ $recentBlog->published_at ? $recentBlog->published_at->format('d M Y') : $recentBlog->created_at->format('d M Y') }}</span>
                                                    @if($recentBlog->category)
                                                        <span>•</span>
                                                        <span class="text-blue-600">{{ $recentBlog->category->name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Back to Blog -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-2xl p-6 text-center btn-override">
                            <i class="fas fa-arrow-left text-3xl mb-4"></i>
                            <h3 class="text-xl font-bold mb-3">Lihat Artikel Lainnya</h3>
                            <p class="text-blue-100 mb-6">
                                Temukan lebih banyak artikel menarik di blog kami
                            </p>
                            <a href="{{ route('blog.index') }}" 
                               class="inline-flex items-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                                <i class="fas fa-th-large mr-2"></i>
                                Semua Artikel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Articles -->
        @if($relatedBlogs->count() > 0)
            <div class="py-16 bg-gray-50">
                <div class="container mx-auto px-4">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Artikel Terkait</h2>
                        <p class="text-gray-600">Artikel lainnya yang mungkin menarik untuk Anda</p>
                    </div>
                    
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($relatedBlogs->take(6) as $relatedBlog)
                            <article class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden group">
                                @if($relatedBlog->featured_image)
                                    <div class="relative overflow-hidden">
                                        <img 
                                            src="{{ asset('storage/' . $relatedBlog->featured_image) }}" 
                                            alt="{{ $relatedBlog->title }}" 
                                            class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110"
                                            loading="lazy"
                                        >
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <div class="absolute bottom-4 left-4">
                                                @if($relatedBlog->category)
                                                    <span class="inline-block px-3 py-1 text-xs font-semibold text-white rounded-full" 
                                                          style="background-color: {{ $relatedBlog->category->color ?? '#3b82f6' }};">
                                                        {{ $relatedBlog->category->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center h-48 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
                                        <div class="text-center">
                                            <i class="fas fa-newspaper text-4xl opacity-50 mb-2"></i>
                                            @if($relatedBlog->category)
                                                <div>
                                                    <span class="inline-block px-3 py-1 text-sm font-medium bg-white/20 text-white rounded-full">
                                                        {{ $relatedBlog->category->name }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="p-6">
                                    <h3 class="text-lg font-bold text-gray-900 mb-3 leading-tight line-clamp-2">
                                        <a href="{{ route('blog.detail', $relatedBlog->slug) }}" class="hover:text-blue-600 transition-colors duration-200">
                                            {{ $relatedBlog->title }}
                                        </a>
                                    </h3>

                                    <p class="text-gray-600 mb-4 leading-relaxed line-clamp-2">
                                        {{ $relatedBlog->excerpt ?? Str::limit(strip_tags($relatedBlog->content), 100) }}
                                    </p>

                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <span>{{ $relatedBlog->published_at ? $relatedBlog->published_at->format('d M Y') : $relatedBlog->created_at->format('d M Y') }}</span>
                                        <span>{{ $relatedBlog->reading_time ?? 5 }} min read</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </article>
    
    <!-- Footer -->
    <x-landing.footer :website-identity="$websiteIdentity" />
@endsection

@push('styles')
<style>
    .prose {
        color: #374151;
        max-width: none;
    }
    
    .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
        color: #1f2937;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    
    .prose h1 { font-size: 2.25rem; line-height: 2.5rem; }
    .prose h2 { font-size: 1.875rem; line-height: 2.25rem; }
    .prose h3 { font-size: 1.5rem; line-height: 2rem; }
    .prose h4 { font-size: 1.25rem; line-height: 1.75rem; }
    
    .prose p {
        margin-top: 1.25rem;
        margin-bottom: 1.25rem;
        line-height: 1.75;
    }
    
    .prose a {
        color: #3b82f6;
        text-decoration: underline;
        font-weight: 500;
    }
    
    .prose a:hover {
        color: #1d4ed8;
    }
    
    .prose strong {
        color: #1f2937;
        font-weight: 600;
    }
    
    .prose blockquote {
        font-style: italic;
        font-weight: 500;
        color: #374151;
        border-left: 4px solid #3b82f6;
        padding-left: 1.5rem;
        margin: 2rem 0;
    }
    
    .prose ul, .prose ol {
        margin-top: 1.25rem;
        margin-bottom: 1.25rem;
        padding-left: 1.625rem;
    }
    
    .prose li {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .prose img {
        border-radius: 0.75rem;
        margin: 2rem auto;
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }
    
    .prose pre {
        background-color: #1f2937;
        color: #e5e7eb;
        padding: 1.5rem;
        border-radius: 0.75rem;
        overflow-x: auto;
        margin: 2rem 0;
    }
    
    .prose code {
        background-color: #f3f4f6;
        color: #1f2937;
        padding: 0.125rem 0.375rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }
    
    .prose pre code {
        background-color: transparent;
        padding: 0;
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Override global link colors for buttons */
    .btn-override a,
    .btn-override button {
        color: inherit !important;
    }
    
    .btn-override a:hover,
    .btn-override button:hover {
        color: inherit !important;
    }
    
    /* Specific button color overrides */
    .btn-override .bg-blue-600,
    .btn-override .bg-blue-700,
    .btn-override .bg-blue-800 {
        color: white !important;
    }
    
    .btn-override .bg-sky-500,
    .btn-override .bg-sky-600 {
        color: white !important;
    }
    
    .btn-override .bg-green-600,
    .btn-override .bg-green-700 {
        color: white !important;
    }
    
    .btn-override .bg-gray-600,
    .btn-override .bg-gray-700 {
        color: white !important;
    }
    
    .btn-override .bg-white {
        color: #2563eb !important;
    }
    
    .btn-override .text-white {
        color: white !important;
    }
    
    .btn-override .text-blue-600 {
        color: #2563eb !important;
    }
    
    .btn-override .hover\:bg-blue-700:hover,
    .btn-override .hover\:bg-sky-600:hover,
    .btn-override .hover\:bg-green-700:hover,
    .btn-override .hover\:bg-gray-700:hover {
        color: white !important;
    }
    
    .btn-override .hover\:bg-gray-50:hover {
        color: #2563eb !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show success message
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
            button.classList.remove('bg-gray-600', 'hover:bg-gray-700');
            button.classList.add('bg-green-600', 'hover:bg-green-700');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600', 'hover:bg-green-700');
                button.classList.add('bg-gray-600', 'hover:bg-gray-700');
            }, 2000);
        });
    }
</script>
@endpush