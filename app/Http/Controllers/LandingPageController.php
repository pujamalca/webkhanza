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

        return view('landing.index', compact('websiteIdentity', 'colors', 'blogs'));
    }
}
