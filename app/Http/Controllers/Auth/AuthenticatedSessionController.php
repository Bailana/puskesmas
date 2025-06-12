<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        if ($user->role === 'dokter') {
            return redirect()->route('dokter.dashboard')->with('status', 'Login berhasil');
        } elseif ($user->role === 'doktergigi') {
            return redirect()->to('/gigi')->with('status', 'Login berhasil');
        } elseif ($user->role === 'perawat') {
            return redirect()->to('/perawat')->with('status', 'Login berhasil');
        } elseif ($user->role === 'resepsionis') {
            return redirect()->route('resepsionis.dashboard')->with('status', 'Login berhasil');
        } elseif ($user->role === 'apoteker') {
            return redirect()->route('apoteker.dashboard')->with('status', 'Anda berhasil login');
        } elseif ($user->role === 'kasir') {
            return redirect()->route('kasir.dashboard')->with('status', 'Login berhasil');
        } else {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->withErrors([
                'loginError' => 'Akun tidak terdeteksi.',
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Anda berhasil logout');
    }
}
