@extends('layouts.app')

@section('title', 'Blog & Artikel - ' . $websiteIdentity->name)
@section('description', 'Baca artikel dan informasi terbaru seputar kesehatan dari ' . $websiteIdentity->name)
@section('keywords', 'blog, artikel, kesehatan, informasi medis, ' . strtolower($websiteIdentity->name))

@section('content')
    <!-- Navigation -->
    <x-landing.navbar :website-identity="$websiteIdentity" />
    
    <!-- Header Section -->
    <section class="relative py-20 bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0">
            <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl animate-float"></div>
            <div class="absolute top-20 right-20 w-16 h-16 bg-white/5 rounded-full blur-xl animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-10 left-1/3 w-24 h-24 bg-white/5 rounded-full blur-xl animate-float" style="animation-delay: 4s;"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center text-white">
                <div class="inline-flex items-center px-4 py-2 bg-white/20 rounded-full text-sm font-medium mb-6 backdrop-blur-sm border border-white/30">
                    <i class="fas fa-newspaper mr-2"></i>Blog & Artikel
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                    Informasi & Artikel Terbaru
                </h1>
                <p class="text-xl text-white/90 leading-relaxed mb-8 max-w-2xl mx-auto">
                    Dapatkan informasi terkini seputar kesehatan, teknologi medis, dan berita dari {{ $websiteIdentity->name }}
                </p>
                
                <!-- Search Form -->
                <div class="max-w-2xl mx-auto">
                    <form method="GET" action="{{ route('blog.index') }}" class="relative">
                        <div class="flex">
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}"
                                placeholder="Cari artikel, topik, atau kategori..." 
                                class="flex-1 px-6 py-4 text-gray-900 bg-white rounded-l-2xl border-0 focus:ring-4 focus:ring-white/50 focus:outline-none text-lg"
                            >
                            <button 
                                type="submit" 
                                class="px-8 py-4 bg-white text-blue-600 rounded-r-2xl hover:bg-gray-50 focus:ring-4 focus:ring-white/50 focus:outline-none transition-all duration-300 font-semibold"
                            >
                                <i class="fas fa-search text-lg"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap -mx-4">
                <!-- Main Content -->
                <div class="w-full lg:w-2/3 px-4 mb-12 lg:mb-0">
                    <!-- Filter & Sort -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 md:p-6 mb-8">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <!-- Category Filter -->
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="text-gray-700 font-medium">Kategori:</span>
                                <a href="{{ route('blog.index', array_merge(request()->except('category'), request()->only(['search', 'sort']))) }}" 
                                   class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 {{ !request('category') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    Semua
                                </a>
                                @foreach($categories as $category)
                                    <a href="{{ route('blog.index', array_merge(request()->except('category'), ['category' => $category->slug] + request()->only(['search', 'sort']))) }}" 
                                       class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 {{ request('category') === $category->slug ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                        {{ $category->name }} ({{ $category->blogs_count }})
                                    </a>
                                @endforeach
                            </div>
                            
                            <!-- Sort & Per Page Options -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6">
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-700 font-medium">Urutkan:</span>
                                    <select name="sort" onchange="window.location.href='{{ route('blog.index') }}?' + new URLSearchParams(Object.assign({}, Object.fromEntries(new URLSearchParams(window.location.search)), {sort: this.value})).toString()" 
                                            class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                                        <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Terpopuler</option>
                                        <option value="featured" {{ request('sort') === 'featured' ? 'selected' : '' }}>Unggulan</option>
                                        <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>Judul A-Z</option>
                                    </select>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-700 font-medium">Tampilkan:</span>
                                    <select name="per_page" onchange="window.location.href='{{ route('blog.index') }}?' + new URLSearchParams(Object.assign({}, Object.fromEntries(new URLSearchParams(window.location.search)), {per_page: this.value, page: 1})).toString()" 
                                            class="px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="6" {{ request('per_page') == '6' ? 'selected' : '' }}>6 artikel</option>
                                        <option value="12" {{ request('per_page', 12) == '12' ? 'selected' : '' }}>12 artikel</option>
                                        <option value="18" {{ request('per_page') == '18' ? 'selected' : '' }}>18 artikel</option>
                                        <option value="24" {{ request('per_page') == '24' ? 'selected' : '' }}>24 artikel</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        @if(request('search') || request('category'))
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    @if(request('search'))
                                        <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                                            <i class="fas fa-search mr-2"></i>
                                            "{{ request('search') }}"
                                            <a href="{{ route('blog.index', request()->except('search')) }}" class="ml-2 text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </span>
                                    @endif
                                    @if(request('category'))
                                        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full">
                                            <i class="fas fa-folder mr-2"></i>
                                            {{ $categories->where('slug', request('category'))->first()->name ?? request('category') }}
                                            <a href="{{ route('blog.index', request()->except('category')) }}" class="ml-2 text-green-600 hover:text-green-800">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </span>
                                    @endif
                                    <span class="text-gray-500">•</span>
                                    <span>{{ $blogs->total() }} artikel ditemukan</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Blog Grid -->
                    @if($blogs->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 mb-12">
                            @foreach($blogs as $blog)
                                <article class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden group">
                                    @if($blog->featured_image)
                                        <div class="relative overflow-hidden">
                                            <img 
                                                src="{{ asset('storage/' . $blog->featured_image) }}" 
                                                alt="{{ $blog->title }}" 
                                                class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110"
                                                loading="lazy"
                                            >
                                            @if($blog->is_featured)
                                                <div class="absolute top-4 left-4">
                                                    <span class="inline-block px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full">
                                                        <i class="fas fa-star mr-1"></i>Unggulan
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                <div class="absolute bottom-4 left-4">
                                                    @if($blog->category)
                                                        <span class="inline-block px-3 py-1 text-xs font-semibold text-white rounded-full" 
                                                              style="background-color: {{ $blog->category->color ?? '#3b82f6' }};">
                                                            {{ $blog->category->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center h-48 bg-gradient-to-br from-blue-600 to-blue-800 text-white relative">
                                            <div class="text-center">
                                                <i class="fas fa-newspaper text-4xl opacity-50 mb-2"></i>
                                                @if($blog->category)
                                                    <div>
                                                        <span class="inline-block px-3 py-1 text-sm font-medium bg-white/20 text-white rounded-full">
                                                            {{ $blog->category->name }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($blog->is_featured)
                                                <div class="absolute top-4 left-4">
                                                    <span class="inline-block px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full">
                                                        <i class="fas fa-star mr-1"></i>Unggulan
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="p-6">
                                        <div class="flex items-center text-gray-500 text-sm mb-3 space-x-4">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar-alt mr-2"></i>
                                                <span>{{ $blog->published_at ? $blog->published_at->format('d M Y') : $blog->created_at->format('d M Y') }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-clock mr-2"></i>
                                                <span>{{ $blog->reading_time ?? 5 }} min read</span>
                                            </div>
                                            @if($blog->views_count > 0)
                                                <div class="flex items-center">
                                                    <i class="fas fa-eye mr-1"></i>{{ number_format($blog->views_count) }}
                                                </div>
                                            @endif
                                        </div>

                                        <h2 class="text-xl font-bold text-gray-900 mb-3 leading-tight line-clamp-2">
                                            <a href="{{ route('blog.detail', $blog->slug) }}" class="hover:text-blue-600 transition-colors duration-200">
                                                {{ $blog->title }}
                                            </a>
                                        </h2>

                                        <p class="text-gray-600 mb-4 leading-relaxed line-clamp-3">
                                            {{ $blog->excerpt ?? Str::limit(strip_tags($blog->content), 150) }}
                                        </p>

                                        @if($blog->tags->count() > 0)
                                            <div class="flex flex-wrap gap-2 mb-4">
                                                @foreach($blog->tags->take(3) as $tag)
                                                    <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">
                                                        #{{ $tag->name }}
                                                    </span>
                                                @endforeach
                                                @if($blog->tags->count() > 3)
                                                    <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-500 rounded-full">
                                                        +{{ $blog->tags->count() - 3 }} lainnya
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                            <div class="flex items-center">
                                                <div class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full mr-3">
                                                    <i class="fas fa-user text-xs"></i>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-sm text-gray-900">{{ $blog->author->name ?? 'Admin' }}</div>
                                                    <div class="text-gray-500 text-xs">Author</div>
                                                </div>
                                            </div>
                                            <a href="{{ route('blog.detail', $blog->slug) }}" 
                                               class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200 group">
                                                Baca Selengkapnya
                                                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-200"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($blogs->hasPages())
                            <div class="bg-white rounded-2xl shadow-lg p-6">
                                <x-custom-pagination :paginator="$blogs->appends(request()->query())" />
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                            <i class="fas fa-search text-6xl text-gray-300 mb-6"></i>
                            <h3 class="text-2xl font-bold text-gray-600 mb-4">Tidak Ada Artikel Ditemukan</h3>
                            <p class="text-gray-500 leading-relaxed mb-6">
                                @if(request('search'))
                                    Maaf, tidak ada artikel yang cocok dengan pencarian "<strong>{{ request('search') }}</strong>".
                                @elseif(request('category'))
                                    Tidak ada artikel dalam kategori "<strong>{{ $categories->where('slug', request('category'))->first()->name ?? request('category') }}</strong>".
                                @else
                                    Belum ada artikel yang dipublikasikan.
                                @endif
                            </p>
                            <a href="{{ route('blog.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors duration-200">
                                <i class="fas fa-refresh mr-2"></i>
                                Lihat Semua Artikel
                            </a>
                        </div>
                    @endif
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

                    <!-- Popular Tags -->
                    @if($popularTags->count() > 0)
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-tags mr-3 text-blue-600"></i>
                                Tag Populer
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($popularTags as $tag)
                                    <a href="{{ route('blog.index', ['search' => '#'.$tag->name]) }}" 
                                       class="inline-block px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded-full hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200">
                                        #{{ $tag->name }} ({{ $tag->blogs_count }})
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <x-landing.footer :website-identity="$websiteIdentity" />
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush