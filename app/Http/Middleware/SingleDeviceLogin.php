<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
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
     * Perform clean logout including database session cleanup
     */
    private function performCleanLogout($user): void
    {
        if ($user) {
            // Set user as logged out in database
            $user->setLoggedOut();
            
            // Clean up all database sessions for this user
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
                
            \Log::info("SingleDeviceLogin: Cleaned up sessions for user ID: {$user->id}");
        }
        
        // Logout from current session
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();
    }
}
