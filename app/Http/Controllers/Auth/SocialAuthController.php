<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user with this email exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Log the user in
                Auth::login($user, true);

                // Redirect based on user role without intended()
                if ($user->role === 'dokter') {
                    return redirect()->route('dokter.dashboard')->with('status', 'Login berhasil.');
                } elseif ($user->role === 'apoteker') {
                    return redirect()->route('apoteker.dashboard')->with('status', 'Login berhasil.');
                } elseif ($user->role === 'kasir') {
                    return redirect()->route('kasir.dashboard')->with('status', 'Login berhasil.');
                } elseif ($user->role === 'perawat') {
                    return redirect()->route('perawat.dashboard')->with('status', 'Login berhasil.');
                } elseif ($user->role === 'resepsionis') {
                    return redirect()->route('resepsionis.dashboard')->with('status', 'Login berhasil.');
                } else {
                    // Default redirect if role not matched
                    return redirect()->route('login')->withErrors(['loginError' => 'Role tidak dikenali.']);
                }
            } else {
                // User not found, redirect back with error
                return redirect()->route('login')->withErrors(['loginError' => 'Akun anda tidak terdaftar.']);
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['loginError' => 'Failed to login with Google.']);
        }
    }
}
