<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogTag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
    ];

    protected $casts = [
        'meta_keywords' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    public function blogs(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class, 'blog_blog_tag');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount(['blogs' => function ($q) {
                $q->published();
            }])
            ->orderBy('blogs_count', 'desc')
            ->limit($limit);
    }

    public function scopeWithBlogCount($query)
    {
        return $query->withCount(['blogs' => function ($q) {
            $q->published();
        }]);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getMetaTitleAttribute($value)
    {
        return $value ?: $this->name;
    }

    public function getMetaDescriptionAttribute($value)
    {
        return $value ?: $this->description;
    }

    public function getUrl()
    {
        return route('blog.tag', $this->slug);
    }

    public static function getColorOptions()
    {
        return [
            'blue' => '#3B82F6',
            'red' => '#EF4444',
            'green' => '#10B981',
            'yellow' => '#F59E0B',
            'purple' => '#8B5CF6',
            'pink' => '#EC4899',
            'indigo' => '#6366F1',
            'gray' => '#6B7280',
        ];
    }
}
