@props(['blogs' => collect(), 'websiteIdentity'])

<!-- Blog Section -->
<section id="blog" class="section-padding bg-gray-50" data-aos="fade-up">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center mb-5">
                <span class="badge badge-primary-custom mb-3">
                    <i class="fas fa-newspaper me-2"></i>Blog & Artikel
                </span>
                <h2 class="display-5 fw-bold mb-3">Informasi & Artikel Terbaru</h2>
                <p class="lead text-muted">
                    Dapatkan informasi terkini seputar kesehatan, teknologi medis, dan berita dari 
                    {{ $websiteIdentity->name ?? 'WebKhanza' }}
                </p>
                <div class="section-divider"></div>
            </div>
        </div>

        @if($blogs->count() > 0)
            <div class="row g-4">
                @foreach($blogs->take(3) as $blog)
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <article class="blog-card card h-100 shadow-sm border-0 hover-lift">
                            @if($blog->featured_image)
                                <div class="blog-image-wrapper position-relative overflow-hidden">
                                    <img 
                                        src="{{ asset('storage/' . $blog->featured_image) }}" 
                                        alt="{{ $blog->title }}" 
                                        class="card-img-top blog-image"
                                        loading="lazy"
                                        style="height: 250px; object-fit: cover;"
                                    >
                                    <div class="blog-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-end p-3">
                                        <div class="blog-overlay-content">
                                            @if($blog->category)
                                                <span class="badge blog-category mb-2" 
                                                      style="background-color: {{ $blog->category->color ?? '#3b82f6' }};">
                                                    {{ $blog->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="blog-image-placeholder d-flex align-items-center justify-content-center bg-gradient-primary text-white" 
                                     style="height: 250px;">
                                    <div class="text-center">
                                        <i class="fas fa-newspaper display-1 opacity-50 mb-3"></i>
                                        @if($blog->category)
                                            <div>
                                                <span class="badge bg-white bg-opacity-20 text-white">
                                                    {{ $blog->category->name }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">
                                <div class="blog-meta d-flex align-items-center mb-3 text-muted small">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <span>{{ $blog->formatted_published_at ?? $blog->created_at->format('d M Y') }}</span>
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-clock me-2"></i>
                                    <span>{{ $blog->reading_time_text ?? '5 min read' }}</span>
                                    @if($blog->views_count > 0)
                                        <span class="ms-auto">
                                            <i class="fas fa-eye me-1"></i>{{ $blog->views_count }}
                                        </span>
                                    @endif
                                </div>

                                <h5 class="card-title fw-bold mb-3">
                                    <a href="#" class="text-decoration-none text-dark stretched-link">
                                        {{ $blog->title }}
                                    </a>
                                </h5>

                                <p class="card-text text-muted mb-3 flex-grow-1">
                                    {{ $blog->excerpt_text ?? Str::limit(strip_tags($blog->content), 120) }}
                                </p>

                                @if($blog->tags->count() > 0)
                                    <div class="blog-tags mt-auto">
                                        @foreach($blog->tags->take(3) as $tag)
                                            <span class="badge bg-light text-muted me-1 mb-1"
                                                  style="font-size: 0.7rem;">
                                                #{{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="blog-author mt-3 pt-3 border-top d-flex align-items-center">
                                    <div class="author-avatar me-3">
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $blog->author->name ?? 'Admin' }}</div>
                                        <div class="text-muted" style="font-size: 0.8rem;">{{ $blog->author->email ?? 'Author' }}</div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            <!-- View All Button -->
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <a href="#" class="btn btn-outline-primary btn-lg hover-lift">
                        <i class="fas fa-th-large me-2"></i>
                        Lihat Semua Artikel
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="row">
                <div class="col-12 text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-newspaper display-1 text-muted mb-4 opacity-50"></i>
                        <h4 class="text-muted mb-3">Belum Ada Artikel</h4>
                        <p class="text-muted">
                            Artikel dan informasi terbaru akan segera hadir. 
                            Stay tuned untuk update terkini!
                        </p>
                        <div class="mt-4">
                            <div class="d-inline-flex align-items-center text-muted">
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                Coming Soon...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

@push('styles')
<style>
    .blog-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 16px !important;
        overflow: hidden;
    }
    
    .blog-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }
    
    .blog-image {
        transition: transform 0.5s ease;
    }
    
    .blog-card:hover .blog-image {
        transform: scale(1.05);
    }
    
    .blog-overlay {
        background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.7) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .blog-card:hover .blog-overlay {
        opacity: 1;
    }
    
    .blog-category {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 20px !important;
        padding: 0.5rem 1rem;
    }
    
    .blog-image-placeholder {
        background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
    }
    
    .blog-meta {
        font-size: 0.85rem;
    }
    
    .author-avatar {
        flex-shrink: 0;
    }
    
    .empty-state {
        padding: 4rem 2rem;
        max-width: 500px;
        margin: 0 auto;
    }
    
    .badge.bg-light {
        border: 1px solid #e9ecef;
    }
    
    /* Reading progress indicator for future use */
    .blog-progress {
        height: 3px;
        background: linear-gradient(90deg, var(--color-primary), var(--color-secondary));
        border-radius: 2px;
        transform-origin: left;
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        .blog-card .card-body {
            padding: 1.5rem;
        }
        
        .blog-meta {
            font-size: 0.8rem;
        }
        
        .empty-state {
            padding: 2rem 1rem;
        }
    }
    
    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .blog-card {
            background-color: #1f2937;
            border-color: #374151 !important;
        }
        
        .blog-card .card-title a {
            color: #f9fafb !important;
        }
        
        .blog-card .card-text {
            color: #d1d5db !important;
        }
        
        .badge.bg-light {
            background-color: #374151 !important;
            color: #d1d5db !important;
            border-color: #4b5563 !important;
        }
    }
</style>
@endpush