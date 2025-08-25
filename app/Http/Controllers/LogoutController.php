<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Clear device token dari database
            $user->logoutFromAllDevices();
        }
        
        // Logout user
        Auth::logout();
        
        // Clear session
        Session::flush();
        
        // Regenerate session untuk keamanan
        $request->session()->regenerate();
        
        return redirect()->route('filament.admin.auth.login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}