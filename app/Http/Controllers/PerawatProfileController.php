<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PerawatProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        return view('perawat.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'current_password' => ['required_with:new_password', 'nullable', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Check current password if new password is provided
        if ($request->filled('new_password')) {
            if (!Hash::check($request->input('current_password'), $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai.'])->withInput();
            }
            $user->password = Hash::make($request->input('new_password'));
        }

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        if ($request->wantsJson()) {
            // Add cache-busting query string to profile photo URL
            $profilePhotoUrl = null;
            if ($user->profile_photo_path) {
                $profilePhotoUrl = asset('storage/' . $user->profile_photo_path) . '?t=' . time();
            }

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui.',
                'profile_photo_url' => $profilePhotoUrl,
            ]);
        }

        return redirect()->route('perawat.profile')->with('status', 'Profil berhasil diperbarui.');
    }
}
