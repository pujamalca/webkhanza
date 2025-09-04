<?php

namespace App\Http\Controllers;

use App\Models\WebsiteIdentity;
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

        return view('landing.index', compact('websiteIdentity', 'colors'));
    }
}
