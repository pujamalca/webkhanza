<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeoutHandler
{
    /**
     * Handle session timeout and clean database sessions
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if session has timed out
        if ($this->hasSessionTimedOut($request)) {
            $this->handleTimeout($request);
            
            // If this is an API request, return 401
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Session timeout'], 401);
            }
            
            // For web requests, redirect to login
            return redirect()->route('filament.admin.auth.login')
                ->with('status', 'Session telah berakhir. Silakan login kembali.');
        }
        
        // Update last activity if user is authenticated
        if (Auth::check()) {
            $this->updateSessionActivity($request);
        }
        
        return $next($request);
    }
    
    /**
     * Check if session has timed out
     */
    private function hasSessionTimedOut(Request $request): bool
    {
        $sessionId = $request->session()->getId();
        $sessionLifetime = config('session.lifetime') * 60; // Convert minutes to seconds
        
        if (!$sessionId) {
            return false;
        }
        
        $sessionRecord = DB::table('sessions')
            ->where('id', $sessionId)
            ->first();
            
        if (!$sessionRecord) {
            return false;
        }
        
        // Check if session has expired based on last_activity
        $lastActivity = $sessionRecord->last_activity;
        $currentTime = time();
        
        return ($currentTime - $lastActivity) > $sessionLifetime;
    }
    
    /**
     * Handle session timeout
     */
    private function handleTimeout(Request $request): void
    {
        $sessionId = $request->session()->getId();
        
        if ($sessionId) {
            // Get user info before cleanup
            $sessionRecord = DB::table('sessions')
                ->where('id', $sessionId)
                ->first();
            
            $userId = $sessionRecord ? $sessionRecord->user_id : null;
            
            // Clean up all sessions for this user
            if ($userId) {
                DB::table('sessions')
                    ->where('user_id', $userId)
                    ->delete();
                    
                \Log::info("Session timeout: Cleaned up all sessions for user ID: {$userId}");
            } else {
                // Clean up just this session if no user_id
                DB::table('sessions')
                    ->where('id', $sessionId)
                    ->delete();
                    
                \Log::info("Session timeout: Cleaned up session: {$sessionId}");
            }
            
            // Logout the user and invalidate session
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();
        }
    }
    
    /**
     * Update session activity timestamp
     */
    private function updateSessionActivity(Request $request): void
    {
        $sessionId = $request->session()->getId();
        
        if ($sessionId) {
            DB::table('sessions')
                ->where('id', $sessionId)
                ->update([
                    'last_activity' => time(),
                    'user_id' => Auth::id(),
                ]);
        }
    }
}