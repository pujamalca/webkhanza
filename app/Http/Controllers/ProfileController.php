<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'avatar_url' => ['nullable', 'image', 'max:2048'],
        ];
        
        if ($request->filled('password')) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password'] = ['required', Password::default(), 'confirmed'];
        }
        
        $validated = $request->validate($rules);
        
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];
        
        // Handle avatar upload
        if ($request->hasFile('avatar_url')) {
            // Delete old avatar if exists
            if ($user->avatar_url) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            
            $avatarPath = $request->file('avatar_url')->store('avatars', 'public');
            $data['avatar_url'] = $avatarPath;
        }
        
        // Handle password update
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }
        
        $user->update($data);
        
        return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
    }
}