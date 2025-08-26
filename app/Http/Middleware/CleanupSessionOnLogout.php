<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CleanupSessionOnLogout
{
    /**
     * Handle an incoming request and clean up database sessions on logout
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Check if user was logged out (either manually or automatically)
        if ($this->wasUserLoggedOut($request)) {
            $this->cleanupUserSessions($request);
        }
        
        return $response;
    }
    
    /**
     * Check if user was logged out during this request
     */
    private function wasUserLoggedOut(Request $request): bool
    {
        // Check if session was invalidated or user is no longer authenticated
        // but session data suggests they were authenticated before
        $sessionId = $request->session()->getId();
        
        // If no current auth user but session exists in database with user_id
        if (!Auth::check() && $sessionId) {
            $sessionRecord = DB::table('sessions')
                ->where('id', $sessionId)
                ->whereNotNull('user_id')
                ->first();
                
            return $sessionRecord !== null;
        }
        
        return false;
    }
    
    /**
     * Clean up user sessions from database
     */
    private function cleanupUserSessions(Request $request): void
    {
        $sessionId = $request->session()->getId();
        
        if ($sessionId) {
            // Get the user_id from current session before cleaning up
            $sessionRecord = DB::table('sessions')
                ->where('id', $sessionId)
                ->first();
            
            if ($sessionRecord && $sessionRecord->user_id) {
                // Remove all sessions for this user
                DB::table('sessions')
                    ->where('user_id', $sessionRecord->user_id)
                    ->delete();
                    
                // Also invalidate current session to ensure clean logout
                Session::invalidate();
                Session::regenerateToken();
                
                // Log the cleanup for debugging
                \Log::info("Cleaned up sessions for user ID: {$sessionRecord->user_id}");
            }
        }
    }
}