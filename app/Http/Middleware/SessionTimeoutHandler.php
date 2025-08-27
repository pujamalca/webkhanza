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
        $sessionId = $request->session()->getId();
        $userId = Auth::id();
        $path = $request->path();
        
        \Log::info('=== SESSION TIMEOUT MIDDLEWARE START ===', [
            'session_id' => $sessionId,
            'user_id' => $userId,
            'path' => $path,
            'is_authenticated' => Auth::check(),
            'timestamp' => now()->toDateTimeString()
        ]);
        
        // Check if session has timed out
        if ($this->hasSessionTimedOut($request)) {
            \Log::warning('=== SESSION TIMEOUT DETECTED ===', [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'path' => $path
            ]);
            
            $this->handleTimeout($request);
            
            \Log::info('=== SESSION TIMEOUT HANDLED ===');
            
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
            \Log::debug('SessionTimeoutHandler: Updated session activity', [
                'session_id' => $sessionId,
                'user_id' => $userId
            ]);
        }
        
        \Log::info('=== SESSION TIMEOUT MIDDLEWARE END ===');
        
        return $next($request);
    }
    
    /**
     * Check if session has timed out
     */
    private function hasSessionTimedOut(Request $request): bool
    {
        $sessionId = $request->session()->getId();
        $sessionLifetime = config('session.lifetime') * 60; // Convert minutes to seconds
        
        \Log::info('SessionTimeoutHandler: Checking timeout', [
            'session_id' => $sessionId,
            'session_lifetime' => $sessionLifetime
        ]);
        
        if (!$sessionId) {
            \Log::info('SessionTimeoutHandler: No session ID found');
            return false;
        }
        
        $sessionRecord = DB::table('sessions')
            ->where('id', $sessionId)
            ->first();
            
        if (!$sessionRecord) {
            \Log::info('SessionTimeoutHandler: No session record found in database');
            return false;
        }
        
        // Check if session has expired based on last_activity
        $lastActivity = $sessionRecord->last_activity;
        $currentTime = time();
        $age = $currentTime - $lastActivity;
        $isExpired = $age > $sessionLifetime;
        
        \Log::info('SessionTimeoutHandler: Session age check', [
            'last_activity' => date('Y-m-d H:i:s', $lastActivity),
            'current_time' => date('Y-m-d H:i:s', $currentTime),
            'age_seconds' => $age,
            'lifetime_seconds' => $sessionLifetime,
            'is_expired' => $isExpired
        ]);
        
        return $isExpired;
    }
    
    /**
     * Handle session timeout
     */
    private function handleTimeout(Request $request): void
    {
        $sessionId = $request->session()->getId();
        
        \Log::info('=== HANDLE TIMEOUT START ===', [
            'session_id' => $sessionId,
            'current_user_id' => Auth::id()
        ]);
        
        if ($sessionId) {
            // Get user info before cleanup
            $sessionRecord = DB::table('sessions')
                ->where('id', $sessionId)
                ->first();
            
            \Log::info('Session record found:', [
                'session_record' => $sessionRecord ? [
                    'user_id' => $sessionRecord->user_id,
                    'last_activity' => date('Y-m-d H:i:s', $sessionRecord->last_activity),
                    'ip_address' => $sessionRecord->ip_address
                ] : null
            ]);
            
            $userId = $sessionRecord ? $sessionRecord->user_id : null;
            
            // Update user status before cleaning up sessions
            if ($userId) {
                \Log::info("=== UPDATING USER STATUS ===", ['user_id' => $userId]);
                
                // Check current user status
                $currentUser = DB::table('users')->where('id', $userId)->first();
                \Log::info('User before update:', [
                    'user_id' => $userId,
                    'is_logged_in' => $currentUser->is_logged_in ?? 'null',
                    'logged_in_at' => $currentUser->logged_in_at ?? 'null',
                    'device_token' => $currentUser->device_token ?? 'null'
                ]);
                
                // Update user login status and clear device info
                $updated = DB::table('users')
                    ->where('id', $userId)
                    ->update([
                        'is_logged_in' => false,
                        'logged_in_at' => null,
                        'device_token' => null,
                        'device_info' => null,
                    ]);
                
                \Log::info("=== USER STATUS UPDATE RESULT ===", [
                    'user_id' => $userId,
                    'rows_updated' => $updated,
                    'update_successful' => $updated > 0
                ]);
                
                // Verify update
                $updatedUser = DB::table('users')->where('id', $userId)->first();
                \Log::info('User after update:', [
                    'user_id' => $userId,
                    'is_logged_in' => $updatedUser->is_logged_in ?? 'null',
                    'logged_in_at' => $updatedUser->logged_in_at ?? 'null',
                    'device_token' => $updatedUser->device_token ?? 'null'
                ]);
                
                // Clean up all sessions for this user
                $sessionsDeleted = DB::table('sessions')
                    ->where('user_id', $userId)
                    ->delete();
                    
                \Log::info("=== SESSIONS CLEANUP ===", [
                    'user_id' => $userId,
                    'sessions_deleted' => $sessionsDeleted
                ]);
            } else {
                // Clean up just this session if no user_id
                $deleted = DB::table('sessions')
                    ->where('id', $sessionId)
                    ->delete();
                    
                \Log::info("=== SESSION CLEANUP (NO USER) ===", [
                    'session_id' => $sessionId,
                    'sessions_deleted' => $deleted
                ]);
            }
            
            // Logout the user and invalidate session
            \Log::info('=== LOGGING OUT USER ===');
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();
            \Log::info('=== USER LOGGED OUT ===');
        }
        
        \Log::info('=== HANDLE TIMEOUT END ===');
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