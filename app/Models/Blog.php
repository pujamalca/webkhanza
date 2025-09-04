<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'gallery_images',
        'blog_category_id',
        'user_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'social_meta',
        'status',
        'published_at',
        'scheduled_at',
        'reading_time',
        'views_count',
        'likes_count',
        'shares_count',
        'is_featured',
        'allow_comments',
        'is_sticky',
        'sort_order',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'meta_keywords' => 'array',
        'social_meta' => 'array',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'is_sticky' => 'boolean',
    ];

    protected $dates = [
        'published_at',
        'scheduled_at',
        'deleted_at',
    ];

    // Boot method untuk auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
            
            // Auto calculate reading time
            if (empty($blog->reading_time)) {
                $blog->reading_time = self::calculateReadingTime($blog->content);
            }
            
            // Auto set published_at if status is published
            if ($blog->status === 'published' && !$blog->published_at) {
                $blog->published_at = now();
            }
        });

        static::updating(function ($blog) {
            // Update reading time if content changed
            if ($blog->isDirty('content')) {
                $blog->reading_time = self::calculateReadingTime($blog->content);
            }
            
            // Set published_at when status changes to published
            if ($blog->isDirty('status') && $blog->status === 'published' && !$blog->published_at) {
                $blog->published_at = now();
            }
        });
    }

    /**
     * Calculate estimated reading time in minutes
     */
    public static function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $readingSpeed = 200; // Average words per minute
        return max(1, ceil($wordCount / $readingSpeed));
    }

    /**
     * Relationships
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_blog_tag');
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $categorySlug)
    {
        return $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    public function scopeByTag($query, $tagSlug)
    {
        return $query->whereHas('tags', function ($q) use ($tagSlug) {
            $q->where('slug', $tagSlug);
        });
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    /**
     * Accessors & Mutators
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getFeaturedImageUrlAttribute()
    {
        if ($this->featured_image) {
            return asset('storage/' . $this->featured_image);
        }
        return asset('images/blog-placeholder.jpg');
    }

    public function getFormattedPublishedAtAttribute()
    {
        return $this->published_at ? $this->published_at->format('M d, Y') : null;
    }

    public function getReadingTimeTextAttribute()
    {
        return $this->reading_time . ' min read';
    }

    public function getIsPublishedAttribute()
    {
        return $this->status === 'published' && 
               $this->published_at && 
               $this->published_at <= now();
    }

    public function getExcerptTextAttribute()
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }
        
        // Generate excerpt from content
        return Str::limit(strip_tags($this->content), 160);
    }

    public function getMetaTitleAttribute($value)
    {
        return $value ?: $this->title;
    }

    public function getMetaDescriptionAttribute($value)
    {
        return $value ?: $this->excerpt_text;
    }

    /**
     * Methods
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementLikes()
    {
        $this->increment('likes_count');
    }

    public function incrementShares()
    {
        $this->increment('shares_count');
    }

    public function getUrl()
    {
        return route('blog.show', $this->slug);
    }

    public function getCanonicalUrl()
    {
        return $this->canonical_url ?: $this->getUrl();
    }

    public function getSocialMeta()
    {
        $default = [
            'og_title' => $this->meta_title,
            'og_description' => $this->meta_description,
            'og_image' => $this->featured_image_url,
            'og_url' => $this->getUrl(),
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $this->meta_title,
            'twitter_description' => $this->meta_description,
            'twitter_image' => $this->featured_image_url,
        ];

        return array_merge($default, $this->social_meta ?: []);
    }

    /**
     * Generate structured data for SEO
     */
    public function getStructuredData()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $this->title,
            'description' => $this->meta_description,
            'image' => $this->featured_image_url,
            'url' => $this->getUrl(),
            'datePublished' => $this->published_at?->toISOString(),
            'dateModified' => $this->updated_at->toISOString(),
            'author' => [
                '@type' => 'Person',
                'name' => $this->author->name,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $this->getUrl(),
            ],
        ];
    }
}