<?php

namespace App\Http\Controllers;

use App\Models\WebsiteIdentity;
use App\Models\Blog;
use App\Services\WebsiteThemeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function __construct(
        private WebsiteThemeService $themeService
    ) {}

    /**
     * Tampilkan halaman landing page
     */
    public function index(): View
    {
        $websiteIdentity = WebsiteIdentity::getInstance();
        $colors = $this->themeService->getAllColors();
        
        // Get latest published blogs for landing page
        $blogs = Blog::published()
            ->with(['category', 'author', 'tags'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        // Determine which template to use based on website identity setting
        $template = $websiteIdentity->landing_template ?? 'default';
        
        // Route to appropriate template view
        switch ($template) {
            case 'doctor':
                return view('templates.doctor.index', compact('websiteIdentity', 'colors', 'blogs'));
            case 'clinic':
                return view('templates.clinic.index', compact('websiteIdentity', 'colors', 'blogs'));
            case 'hospital':
                return view('templates.hospital.index', compact('websiteIdentity', 'colors', 'blogs'));
            case 'pharmacy':
                return view('templates.pharmacy.index', compact('websiteIdentity', 'colors', 'blogs'));
            default:
                return view('landing.index', compact('websiteIdentity', 'colors', 'blogs'));
        }
    }

    /**
     * Tampilkan halaman daftar blog
     */
    public function blog(Request $request): View
    {
        $websiteIdentity = WebsiteIdentity::getInstance();
        $colors = $this->themeService->getAllColors();

        $query = Blog::published()->with(['category', 'author', 'tags']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('excerpt', 'LIKE', "%{$search}%")
                  ->orWhere('content', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('tags', function ($tagQuery) use ($search) {
                      $tagQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Sort options
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('published_at', 'desc');
                break;
            default: // latest
                $query->orderBy('published_at', 'desc');
        }

        // Configure pagination - bisa disesuaikan sesuai kebutuhan
        $perPage = $request->get('per_page', 12); // Default 12 artikel per halaman
        $perPage = in_array($perPage, [6, 12, 18, 24]) ? $perPage : 12; // Batasi pilihan
        
        $blogs = $query->paginate($perPage);
        
        // Get categories for filter
        $categories = \App\Models\BlogCategory::withCount('blogs')
            ->having('blogs_count', '>', 0)
            ->orderBy('name')
            ->get();

        // Get recent blogs for sidebar
        $recentBlogs = Blog::published()
            ->with(['category'])
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        // Get popular tags
        $popularTags = \App\Models\BlogTag::withCount('blogs')
            ->having('blogs_count', '>', 0)
            ->orderBy('blogs_count', 'desc')
            ->limit(20)
            ->get();

        return view('blog.index', compact(
            'websiteIdentity', 
            'colors', 
            'blogs', 
            'categories', 
            'recentBlogs', 
            'popularTags'
        ));
    }

    /**
     * Tampilkan detail blog
     */
    public function blogDetail(string $slug): View
    {
        $websiteIdentity = WebsiteIdentity::getInstance();
        $colors = $this->themeService->getAllColors();

        $blog = Blog::published()
            ->with(['category', 'author', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views count
        $blog->increment('views_count');

        // Get related blogs
        $relatedBlogs = Blog::published()
            ->with(['category', 'author'])
            ->where('id', '!=', $blog->id)
            ->where(function ($query) use ($blog) {
                $query->where('blog_category_id', $blog->blog_category_id)
                      ->orWhereHas('tags', function ($tagQuery) use ($blog) {
                          $tagQuery->whereIn('blog_tags.id', $blog->tags->pluck('id'));
                      });
            })
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        // Get recent blogs for sidebar
        $recentBlogs = Blog::published()
            ->with(['category'])
            ->where('id', '!=', $blog->id)
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        return view('blog.detail', compact(
            'websiteIdentity', 
            'colors', 
            'blog', 
            'relatedBlogs', 
            'recentBlogs'
        ));
    }
}
