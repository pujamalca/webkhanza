@props(['blogs' => collect(), 'websiteIdentity'])

<!-- Blog Section -->
<section id="blog" class="py-20 bg-gray-50 animate-on-scroll">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center mb-16">
            <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-600 rounded-full text-sm font-medium mb-6">
                <i class="fas fa-newspaper mr-2"></i>Blog & Artikel
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">Informasi & Artikel Terbaru</h2>
            <p class="text-xl text-gray-600 leading-relaxed">
                Dapatkan informasi terkini seputar kesehatan, teknologi medis, dan berita dari 
                {{ $websiteIdentity->name ?? 'WebKhanza' }}
            </p>
            <div class="w-24 h-1 bg-gradient-to-r from-blue-600 to-blue-800 rounded-full mx-auto mt-8"></div>
        </div>

        @if($blogs->count() > 0)
            <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-8">
                @foreach($blogs->take(3) as $blog)
                    <div class="animate-on-scroll" style="animation-delay: {{ $loop->iteration * 100 }}ms;">
                        <article class="blog-card bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden h-full flex flex-col">
                            @if($blog->featured_image)
                                <div class="relative overflow-hidden group">
                                    <img 
                                        src="{{ asset('storage/' . $blog->featured_image) }}" 
                                        alt="{{ $blog->title }}" 
                                        class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110"
                                        loading="lazy"
                                    >
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
                                <div class="flex items-center justify-center h-64 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
                                    <div class="text-center">
                                        <i class="fas fa-newspaper text-6xl opacity-50 mb-4"></i>
                                        @if($blog->category)
                                            <div>
                                                <span class="inline-block px-3 py-1 text-sm font-medium bg-white/20 text-white rounded-full">
                                                    {{ $blog->category->name }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="flex-1 flex flex-col p-6">
                                <div class="flex items-center text-gray-500 text-sm mb-4 space-x-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        <span>{{ $blog->formatted_published_at ?? $blog->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-clock mr-2"></i>
                                        <span>{{ $blog->reading_time_text ?? '5 min read' }}</span>
                                    </div>
                                    @if($blog->views_count > 0)
                                        <div class="flex items-center ml-auto">
                                            <i class="fas fa-eye mr-1"></i>{{ $blog->views_count }}
                                        </div>
                                    @endif
                                </div>

                                <h3 class="text-xl font-bold text-gray-900 mb-3 leading-tight">
                                    <a href="#" class="hover:text-blue-600 transition-colors duration-200">
                                        {{ $blog->title }}
                                    </a>
                                </h3>

                                <p class="text-gray-600 mb-4 flex-grow leading-relaxed">
                                    {{ $blog->excerpt_text ?? Str::limit(strip_tags($blog->content), 120) }}
                                </p>

                                @if($blog->tags->count() > 0)
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        @foreach($blog->tags->take(3) as $tag)
                                            <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full border">
                                                #{{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="flex items-center pt-4 border-t border-gray-200 mt-auto">
                                    <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full mr-3">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm text-gray-900">{{ $blog->author->name ?? 'Admin' }}</div>
                                        <div class="text-gray-500 text-xs">{{ $blog->author->email ?? 'Author' }}</div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            <!-- View All Button -->
            <div class="text-center mt-12">
                <a href="#" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-2xl border-2 border-blue-600 shadow-lg hover:bg-blue-600 hover:text-white hover:shadow-xl transition-all duration-300 transform hover:scale-105 group">
                    <i class="fas fa-th-large mr-3 group-hover:rotate-3 transition-transform duration-300"></i>
                    Lihat Semua Artikel
                    <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform duration-300"></i>
                </a>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-20">
                <div class="max-w-md mx-auto">
                    <i class="fas fa-newspaper text-8xl text-gray-300 mb-8"></i>
                    <h3 class="text-2xl font-bold text-gray-600 mb-4">Belum Ada Artikel</h3>
                    <p class="text-gray-500 leading-relaxed mb-8">
                        Artikel dan informasi terbaru akan segera hadir. 
                        Stay tuned untuk update terkini!
                    </p>
                    <div class="inline-flex items-center text-gray-400">
                        <div class="animate-spin rounded-full h-5 w-5 border-2 border-gray-300 border-t-blue-600 mr-3"></div>
                        Coming Soon...
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

