<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Login;
use App\Http\Controllers\LogoutController;
use App\Http\Middleware\SingleDeviceLogin;
use App\Services\WebsiteThemeService;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Auth\EditProfile;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Navigation\MenuItem;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use App\Filament\Widgets\PatientMarketingStatsWidget;
use App\Filament\Widgets\BpjsTransferStatsWidget;
use App\Filament\Widgets\MarketingTaskCompletionWidget;
use App\Filament\Widgets\TopMarketingCategoriesWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Skip complex setup during testing
        if (app()->environment('testing') || env('FILAMENT_DISABLE', false)) {
            return $panel
                ->default()
                ->id('admin')
                ->path('admin')
                ->colors(['primary' => Color::Blue]);
        }
        
        // Get website identity data from service
        $identity = app('website.identity');
        
        // Get theme service for dynamic colors
        $themeService = app(WebsiteThemeService::class);
        $colors = $themeService->getAllColors();
        
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            // Set brand name from database
            ->brandName($identity->name ?? 'WebKhanza')
            // Set brand logo from database with fallback
            ->brandLogo(fn () => $identity->logo ? asset('storage/' . $identity->logo) : null)
            ->brandLogoHeight('2rem')
            // Set favicon from database with fallback
            ->favicon(fn () => $identity->favicon ? asset('storage/' . $identity->favicon) : asset('favicon.ico'))
            ->colors([
                'primary' => $colors['primary'],
                'secondary' => $colors['secondary'], 
                'danger' => $colors['accent'],
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\Filament\Clusters')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->profile(EditProfile::class)
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                PatientMarketingStatsWidget::class,
                BpjsTransferStatsWidget::class,
                MarketingTaskCompletionWidget::class,
                TopMarketingCategoriesWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                SingleDeviceLogin::class,
            ])
            ->userMenuItems([
                // Customize profile link
                'profile' => MenuItem::make()
                    ->label('Edit Profile')
                    ->url(fn (): string => EditProfile::getUrl())
                    ->icon('heroicon-o-user-circle'),
                
                // My Activity logs (for users with permission)
                MenuItem::make('my-activity')
                    ->label('My Activity')
                    ->url(function (): string {
                        $baseUrl = '/admin/user-role/activity-logs';
                        $filters = ['tableFilters' => ['causer_id' => ['value' => auth()->id()]]];
                        return $baseUrl . '?' . http_build_query($filters);
                    })
                    ->icon('heroicon-o-clipboard-document-list')
                    ->visible(fn (): bool => auth()->user()?->can('activity_logs_view') ?? false),
                
                // Quick role info
                MenuItem::make('role-info')
                    ->label(function (): string {
                        $user = auth()->user();
                        $roleName = $user?->getRoleNames()->first() ?? 'No Role';
                        return 'Role: ' . $roleName;
                    })
                    ->url('#')
                    ->icon('heroicon-o-shield-check'),
                
                // Website identity quick access (for admins)
                MenuItem::make('website-settings')
                    ->label('Website Settings')
                    ->url('/admin/user-role/website-identities')
                    ->icon('heroicon-o-globe-alt')
                    ->visible(fn (): bool => auth()->user()?->can('view_any_website_identity') ?? false),
                
            ])
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): string => Blade::render('<livewire:real-time-clock />')
            );
    }
}
