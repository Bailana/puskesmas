<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Antrian;
use App\Models\Pasien;
use App\Models\HasilPeriksa;

class DokterDashboardController extends Controller
{
    public function index()
    {
        $antrians = Antrian::where('poli_id', 1)
            ->where('status', 'Pemeriksaan')
            ->paginate(5);

        $obats = \App\Models\Obat::select('id', 'nama_obat', 'bentuk_obat', 'stok')->get();

        return view('dokter.dashboard', compact('antrians', 'obats'));
    }

    public function pasien(Request $request)
    {
        $query = Pasien::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pasien', 'like', '%' . $search . '%')
                  ->orWhere('no_rekam_medis', 'like', '%' . $search . '%')
                  ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }
        if ($request->filled('gol_darah')) {
            $query->where('gol_darah', $request->gol_darah);
        }
        if ($request->filled('jaminan_kesehatan')) {
            $query->where('jaminan_kesehatan', $request->jaminan_kesehatan);
        }
        if ($request->filled('tempat_lahir')) {
            $query->where('tempat_lahir', 'like', '%' . $request->tempat_lahir . '%');
        }
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', 'like', '%' . $request->kecamatan . '%');
        }
        if ($request->filled('kelurahan')) {
            $query->where('kelurahan', 'like', '%' . $request->kelurahan . '%');
        }
        if ($request->filled('status_pernikahan')) {
            $query->where('status_pernikahan', $request->status_pernikahan);
        }
        if ($request->filled('tanggal_lahir')) {
            $query->whereDate('tanggal_lahir', $request->tanggal_lahir);
        }

        $pasiens = $query->paginate(5)->withQueryString();

        if ($request->ajax()) {
            return response()->json($pasiens);
        }

        return view('dokter.pasien', compact('pasiens'));
    }

    public function antrian(Request $request)
    {
        $query = Antrian::where('poli_id', 1)
            ->where('status', 'Pemeriksaan')
            ->with('pasien', 'poli');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pasien', function ($q) use ($search) {
                $q->where('nama_pasien', 'like', '%' . $search . '%')
                  ->orWhere('no_rekam_medis', 'like', '%' . $search . '%')
                  ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }

        $antrians = $query->paginate(5);

        if ($request->ajax()) {
            return response()->json($antrians);
        }

        // Get list of available medicines from 'obat' table with bentuk_obat and stok
        $obats = \App\Models\Obat::select('id', 'nama_obat', 'bentuk_obat', 'stok')->get();

        return view('dokter.antrian', compact('antrians', 'obats'));
    }

    // New method for medicine search API
    public function searchObat(Request $request)
    {
        $query = \App\Models\Obat::query();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where('nama_obat', 'like', '%' . $search . '%');
        }

        $obats = $query->select('id', 'nama_obat', 'bentuk_obat', 'stok')->limit(10)->get();

        $results = $obats->map(function ($obat) {
            return [
                'id' => $obat->id,
                'text' => $obat->nama_obat,
                'bentuk_obat' => $obat->bentuk_obat,
                'stok' => $obat->stok,
            ];
        });

        return response()->json(['results' => $results]);
    }

    // Method to show the profile edit form
    public function profile()
    {
        $user = Auth::user();
        return view('dokter.profile', compact('user'));
    }

    // Method to update the profile
    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::id());

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'current_password' => ['required_with:new_password', 'nullable', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo_path = $path;
        }

        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                if ($request->expectsJson()) {
                    return response()->json(['errors' => ['current_password' => ['Password lama tidak sesuai']]], 422);
                }
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai'])->withInput();
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        if ($request->expectsJson()) {
            $message = 'Profil berhasil diperbarui.';
            $passwordChanged = false;
            if ($request->filled('current_password') && $request->filled('new_password')) {
                $passwordChanged = true;
            }
            if ($passwordChanged) {
                $message = 'Password berhasil diubah.';
            }
            return response()->json([
                'message' => $message,
                'name' => $user->name,
                'profile_photo_path' => $user->profile_photo_path,
            ]);
        }

        return redirect()->route('dokter.profile')->with('status', 'Profil berhasil diperbarui.');
    }

    public function storeHasilPeriksa(Request $request)
    {
        $validatedData = $request->validate([
            'pasien_id' => 'required|exists:pasiens,id',
            'tanggal_periksa' => 'required|date',
            'anamnesis' => 'required|string',
            'pemeriksaan_fisik' => 'nullable|string',
            'rencana_dan_terapi' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'edukasi' => 'nullable|string',
            'kode_icd' => 'nullable|string',
            'kesan_status_gizi' => 'nullable|in:Gizi Kurang/Buruk,Gizi Cukup,Gizi Lebih',
            'obats' => 'nullable|array',
            'obats.*.id' => 'required|exists:obat,id',
            'obats.*.jumlah' => 'required|integer|min:1',
            'obats.*.catatan_obat' => 'nullable|string',
        ]);

        $hasilPeriksa = new \App\Models\HasilPeriksa();
        $hasilPeriksa->pasien_id = $validatedData['pasien_id'];
        $hasilPeriksa->penanggung_jawab = auth()->id();
        $hasilPeriksa->tanggal_periksa = $validatedData['tanggal_periksa'];
        $hasilPeriksa->anamnesis = $validatedData['anamnesis'];
        $hasilPeriksa->pemeriksaan_fisik = $validatedData['pemeriksaan_fisik'] ?? null;
        $hasilPeriksa->rencana_dan_terapi = $validatedData['rencana_dan_terapi'] ?? null;
        $hasilPeriksa->diagnosis = $validatedData['diagnosis'] ?? null;
        $hasilPeriksa->edukasi = $validatedData['edukasi'] ?? null;
        $hasilPeriksa->kode_icd = $validatedData['kode_icd'] ?? null;
        $hasilPeriksa->kesan_status_gizi = $validatedData['kesan_status_gizi'] ?? null;
        $hasilPeriksa->save();

        // Save obat resep with quantities and catatan_obat
        if (!empty($validatedData['obats'])) {
            foreach ($validatedData['obats'] as $obat) {
                $catatanObat = $obat['catatan_obat'] ?? null;
                $hasilPeriksa->obats()->attach($obat['id'], [
                    'jumlah' => $obat['jumlah'],
                    'catatan_obat' => $catatanObat,
                ]);
            }
        }

        // Update status pasien in antrian to 'Pembayaran'
        $antrian = \App\Models\Antrian::where('pasien_id', $validatedData['pasien_id'])
            ->where('poli_id', 1) // Assuming poli_id 1 is Poli Umum
            ->first();
        if ($antrian) {
            $antrian->status = 'Pembayaran';
            $antrian->save();
        }

        return response()->json(['message' => 'Hasil periksa berhasil disimpan']);
    }

    public function getRiwayatBerobat($noRekamMedis)
    {
        $pasien = \App\Models\Pasien::where('no_rekam_medis', $noRekamMedis)->first();

        if (!$pasien) {
            return response()->json(['message' => 'Pasien tidak ditemukan'], 404);
        }

        $dates = \App\Models\HasilPeriksa::where('pasien_id', $pasien->id)
            ->orderBy('tanggal_periksa', 'desc')
            ->pluck('tanggal_periksa');

        return response()->json($dates);
    }

    public function getHasilPeriksaDetail($noRekamMedis, $tanggal)
    {
        $pasien = \App\Models\Pasien::where('no_rekam_medis', $noRekamMedis)->first();

        if (!$pasien) {
            return response()->json(['message' => 'Pasien tidak ditemukan'], 404);
        }

        $hasilPeriksa = \App\Models\HasilPeriksa::with('penanggungJawabUser')
            ->where('pasien_id', $pasien->id)
            ->where('tanggal_periksa', $tanggal)
            ->first();

        if (!$hasilPeriksa) {
            return response()->json(['message' => 'Data hasil periksa tidak ditemukan'], 404);
        }

        $result = $hasilPeriksa->toArray();
        $result['penanggung_jawab_nama'] = $hasilPeriksa->penanggungJawabUser ? $hasilPeriksa->penanggungJawabUser->name : null;

        return response()->json($result);
    }
}
