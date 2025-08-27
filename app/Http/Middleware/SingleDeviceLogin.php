<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Auth\Events\Logout;
use Symfony\Component\HttpFoundation\Response;

class SingleDeviceLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Skip single device check for users with multi_device_login permission
            if ($this->isAdminUser($user)) {
                \Log::debug("SingleDeviceLogin: Skipping single device check for user ID: {$user->id} (has multi_device_login permission)");
                return $next($request);
            }
            
            $sessionDeviceToken = Session::get('device_token');
            
            // Jika tidak ada device token di session, logout
            if (!$sessionDeviceToken) {
                $this->performCleanLogout($user);
                return redirect()->route('filament.admin.auth.login')
                    ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }
            
            // Jika device token tidak cocok dengan database, logout
            if (!$user->isDeviceAllowed($sessionDeviceToken)) {
                $this->performCleanLogout($user);
                return redirect()->route('filament.admin.auth.login')
                    ->with('error', 'Akun Anda telah login dari perangkat lain. Silakan login kembali.');
            }
            
            // Jika user tidak dalam status logged in, logout
            if (!$user->isCurrentlyLoggedIn()) {
                $this->performCleanLogout($user);
                return redirect()->route('filament.admin.auth.login')
                    ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }
        }
        
        return $next($request);
    }
    
    /**
     * Check if user has permission to login from multiple devices
     */
    private function isAdminUser($user): bool
    {
        // Simple permission-based check
        return $user->can('multi_device_login');
    }
    
    /**
     * Perform clean logout including database session cleanup
     */
    private function performCleanLogout($user): void
    {
        if ($user) {
            \Log::info("SingleDeviceLogin: Performing clean logout for user ID: {$user->id}");
            
            // Fire logout event BEFORE doing anything else
            // This ensures our SetUserLoggedOutOnLogout listener updates the database
            event(new Logout('web', $user));
            
            // Clean up all database sessions for this user
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
                
            \Log::info("SingleDeviceLogin: Cleaned up sessions and fired logout event for user ID: {$user->id}");
        }
        
        // Logout from current session
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();
    }
}
