<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                Auth::logout();
                Session::flush();
                return redirect()->route('filament.admin.auth.login')
                    ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }
            
            // Jika device token tidak cocok dengan database, logout
            if (!$user->isDeviceAllowed($sessionDeviceToken)) {
                Auth::logout();
                Session::flush();
                return redirect()->route('filament.admin.auth.login')
                    ->with('error', 'Akun Anda telah login dari perangkat lain. Silakan login kembali.');
            }
        }
        
        return $next($request);
    }
}
