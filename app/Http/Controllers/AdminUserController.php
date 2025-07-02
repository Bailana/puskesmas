<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $users = User::where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%")
                ->get();
        } else {
            $users = User::all();
        }

        return view('admin.datauser', compact('users'));
    }

    public function getUserById($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }

        // Tambahkan URL foto profil jika ada
        $user->profile_photo_url = $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : null;

        return response()->json($user);
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = new User();
        $user->role = $request->input('role');
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('admin.datauser')->with('success', 'User berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        $user->role = $request->input('role');
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        // Jika user yang diupdate adalah user yang sedang login, logout agar sesi diperbarui
        if (Auth::check() && Auth::user()->id === $user->id) {
            Auth::logout();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Role diubah, silakan login kembali untuk memperbarui sesi.']);
            }
            return redirect()->route('login')->with('success', 'Role diubah, silakan login kembali untuk memperbarui sesi.');
        }

        if ($request->ajax()) {
            // Return profile photo URL for updating preview
            $user->profile_photo_url = $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : null;
            return response()->json(['success' => true, 'message' => 'User berhasil diperbarui', 'profile_photo_url' => $user->profile_photo_url]);
        }

        return redirect()->route('admin.datauser')->with('success', 'User berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.datauser')->with('success', 'User berhasil dihapus');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
        ]);
    }
}
