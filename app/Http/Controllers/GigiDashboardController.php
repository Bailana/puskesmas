<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antrian;
use App\Models\Pasien;
use Illuminate\Support\Facades\Hash;
use App\Models\JadwalDokter;
use App\Models\User;

class GigiDashboardController extends Controller
{
    public function index()
    {
        $antrians = Antrian::where('status', 'Pemeriksaan')
            ->whereHas('poli', function ($query) {
                $query->where('nama_poli', 'Gigi');
            })
            ->paginate(5);
        return view('gigi.dashboard', compact('antrians'));
    }

    public function profile()
    {
        return view('gigi.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'profile_photo' => 'nullable|image|max:2048',
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update name and email
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        // Update profile photo if uploaded
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        // Update password if current_password and new_password are provided
        if (!empty($validatedData['current_password']) && !empty($validatedData['new_password'])) {
            if (!Hash::check($validatedData['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai'])->withInput();
            }
            $user->password = bcrypt($validatedData['new_password']);
        }

        $user->save();

        return redirect()->route('gigi.profile')->with('status', 'Profil berhasil diperbarui.');
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

        return view('gigi.pasien', compact('pasiens'));
    }

    public function antrian()
    {
        $antrians = \App\Models\Antrian::where('status', 'Pemeriksaan')
            ->whereHas('poli', function ($query) {
                $query->where('nama_poli', 'Gigi');
            })
            ->paginate(10);

        return view('gigi.antrian', compact('antrians'));
    }

    public function jadwal()
    {
        $jadwalDoktersRaw = JadwalDokter::all();
        $users = User::whereIn('role', ['dokter', 'doktergigi', 'bidan'])->get();

        // Group jadwalDokters by nama_dokter and poliklinik
        $jadwalGrouped = [];

        foreach ($jadwalDoktersRaw as $jadwal) {
            $key = $jadwal->nama_dokter . '|' . $jadwal->poliklinik;
            if (!isset($jadwalGrouped[$key])) {
                $jadwalGrouped[$key] = [
                    'nama_dokter' => $jadwal->nama_dokter,
                    'poliklinik' => $jadwal->poliklinik,
                    'senin' => '',
                    'selasa' => '',
                    'rabu' => '',
                    'kamis' => '',
                    'jumat' => '',
                    'sabtu' => '',
                    'minggu' => '',
                    'ids' => [], // store ids for delete/edit if needed
                ];
            }

            $hariArray = is_array($jadwal->hari) ? $jadwal->hari : [$jadwal->hari];
            $jamMasukArray = is_array($jadwal->jam_masuk) ? $jadwal->jam_masuk : [$jadwal->jam_masuk];
            $jamKeluarArray = is_array($jadwal->jam_keluar) ? $jadwal->jam_keluar : [$jadwal->jam_keluar];

            foreach ($hariArray as $index => $hari) {
                $hariLower = strtolower($hari);
                if (array_key_exists($hariLower, $jadwalGrouped[$key])) {
                    $jamMasuk = $jamMasukArray[$index] ?? '';
                    $jamKeluar = $jamKeluarArray[$index] ?? '';
                    $timeRange = $jamMasuk && $jamKeluar ? $jamMasuk . ' - ' . $jamKeluar : '';
                    $jadwalGrouped[$key][$hariLower] = $timeRange;
                }
            }

            $jadwalGrouped[$key]['ids'][] = $jadwal->id;
        }

        // Convert to collection
        $jadwalDokters = collect(array_values($jadwalGrouped));

        return view('gigi.jadwal', compact('jadwalDokters', 'users'));
    }

    // public function storeHasilPeriksa(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'pasien_id' => 'required|exists:pasiens,id',
    //         'tanggal_periksa' => 'required|date',
    //         'anamnesis' => 'required|string',
    //         'pemeriksaan_fisik' => 'nullable|string',
    //         'rencana_dan_terapi' => 'nullable|string',
    //         'diagnosis' => 'nullable|string',
    //         'edukasi' => 'nullable|string',
    //         'kode_icd' => 'nullable|string',
    //         'kesan_status_gizi' => 'nullable|in:Gizi Kurang/Buruk,Gizi Cukup,Gizi Lebih',
    //     ]);

    //     // Simpan data hasil periksa ke database
    //     $hasilPeriksa = new \App\Models\HasilPeriksa();
    //     $hasilPeriksa->pasien_id = $validatedData['pasien_id'];
    //     $hasilPeriksa->penanggung_jawab = auth()->id();
    //     $hasilPeriksa->tanggal_periksa = $validatedData['tanggal_periksa'];
    //     $hasilPeriksa->anamnesis = $validatedData['anamnesis'];
    //     $hasilPeriksa->pemeriksaan_fisik = $validatedData['pemeriksaan_fisik'] ?? null;
    //     $hasilPeriksa->rencana_dan_terapi = $validatedData['rencana_dan_terapi'] ?? null;
    //     $hasilPeriksa->diagnosis = $validatedData['diagnosis'] ?? null;
    //     $hasilPeriksa->edukasi = $validatedData['edukasi'] ?? null;
    //     $hasilPeriksa->kode_icd = $validatedData['kode_icd'] ?? null;
    //     $hasilPeriksa->kesan_status_gizi = $validatedData['kesan_status_gizi'] ?? null;
    //     $hasilPeriksa->save();

    //     // Update status pasien in antrian to 'Pembayaran'
    //     $antrian = \App\Models\Antrian::where('pasien_id', $validatedData['pasien_id'])
    //         ->where('poli_id', 2) // Poli Gigi
    //         ->first();
    //     if ($antrian) {
    //         $antrian->status = 'Pembayaran';
    //         $antrian->save();
    //     }

    //     return response()->json(['message' => 'Data hasil periksa berhasil disimpan']);
    // }

    public function storeHasilPeriksaGigi(Request $request)
    {
        $validatedData = $request->validate([
            'pasien_id' => 'required|exists:pasiens,id',
            'tanggal_periksa' => 'required|date',
            'odontogram' => 'nullable|string',
            'pemeriksaan_subjektif' => 'nullable|string',
            'pemeriksaan_objektif' => 'nullable|string',
            'diagnosa' => 'nullable|string',
            'terapi_anjuran' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $hasilPeriksaGigi = \App\Models\HasilPeriksagigi::updateOrCreate(
            ['pasien_id' => $validatedData['pasien_id'], 'tanggal_periksa' => $validatedData['tanggal_periksa']],
            [
                'penanggung_jawab' => auth()->id(),
                'odontogram' => $validatedData['odontogram'] ?? null,
                'pemeriksaan_subjektif' => $validatedData['pemeriksaan_subjektif'] ?? null,
                'pemeriksaan_objektif' => $validatedData['pemeriksaan_objektif'] ?? null,
                'diagnosa' => $validatedData['diagnosa'] ?? null,
                'terapi_anjuran' => $validatedData['terapi_anjuran'] ?? null,
                'catatan' => $validatedData['catatan'] ?? null,
            ]
        );

        // Update status pasien in antrian to 'Pembayaran'
        $antrian = \App\Models\Antrian::where('pasien_id', $validatedData['pasien_id'])
            ->where('poli_id', 2) // Poli Gigi
            ->first();
        if ($antrian) {
            $antrian->status = 'Pembayaran';
            $antrian->save();
        }

        return response()->json(['message' => '']);
    }

    public function getRiwayatBerobat($noRekamMedis)
    {
        $pasien = Pasien::where('no_rekam_medis', $noRekamMedis)->first();

        if (!$pasien) {
            return response()->json(['message' => 'Pasien tidak ditemukan'], 404);
        }

        $dates = \App\Models\HasilPeriksagigi::where('pasien_id', $pasien->id)
            ->orderBy('tanggal_periksa', 'desc')
            ->pluck('tanggal_periksa');

        return response()->json($dates);
    }

    public function getHasilPeriksaDetail($noRekamMedis, $tanggal)
    {
        $pasien = Pasien::where('no_rekam_medis', $noRekamMedis)->first();

        if (!$pasien) {
            return response()->json(['message' => 'Pasien tidak ditemukan'], 404);
        }

        $hasilPeriksa = \App\Models\HasilPeriksagigi::with('penanggungJawabUser')
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
