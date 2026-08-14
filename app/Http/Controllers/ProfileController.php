<?php
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display user profile
     */
    public function index()
    {
        $user = Auth::user();
        $user->load(['keuskupan', 'gereja']);
        
        return view('profile.index', compact('user'));
    }
    
    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        
        return redirect()->route('profile.index')
            ->with('success', 'Profil berhasil diupdate.');
    }
    
    /**
     * Update profile photo
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);
        
        $user = Auth::user();
        
        // Hapus foto lama jika ada
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }
        
        // Upload foto baru
        $file = $request->file('photo');
        $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('profile-photos', $filename, 'public');
        
        $user->update(['photo' => $path]);
        
        return redirect()->route('profile.index')
            ->with('success', 'Foto profil berhasil diupdate.');
    }
    
    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        $user = Auth::user();
        
        // Cek current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }
        
        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        return redirect()->route('profile.index')
            ->with('success', 'Password berhasil diubah.');
    }
    
    /**
     * Remove profile photo
     */
    public function removePhoto()
    {
        $user = Auth::user();
        
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
            $user->update(['photo' => null]);
        }
        
        return redirect()->route('profile.index')
            ->with('success', 'Foto profil berhasil dihapus.');
    }
}