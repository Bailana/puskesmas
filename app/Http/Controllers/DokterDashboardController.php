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
use App\Models\Hasilanalisa;

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

    public function hasilAnalisaAjax($no_rekam_medis)
    {
        try {
            $pasien = \App\Models\Pasien::where('no_rekam_medis', $no_rekam_medis)->first();
            if (!$pasien) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pasien tidak ditemukan.'
                ]);
            }
            $hasil = \App\Models\Hasilanalisa::with(['poli', 'penanggungJawab'])->where('pasien_id', $pasien->id)->latest()->first();
            if ($hasil) {
                $data = $hasil->toArray();
                $data['nama_poli'] = $hasil->poli ? $hasil->poli->nama_poli : '-';
                $data['nama_penanggung_jawab'] = $hasil->penanggungJawab ? $hasil->penanggungJawab->name : '-';
                $data['status_psikologi'] = $hasil->status_psikologi ? (is_array(json_decode($hasil->status_psikologi, true)) ? implode(', ', json_decode($hasil->status_psikologi, true)) : (is_string($hasil->status_psikologi) ? $hasil->status_psikologi : '-')) : '-';
                $data['hambatan_edukasi'] = $hasil->hambatan_edukasi ? (is_array(json_decode($hasil->hambatan_edukasi, true)) ? implode(', ', json_decode($hasil->hambatan_edukasi, true)) : (is_string($hasil->hambatan_edukasi) ? $hasil->hambatan_edukasi : '-')) : '-';
                // Jangan ubah value asli, biarkan kosong/null dikirim ke JS agar bisa di-handle di JS
                return response()->json([
                    'success' => true,
                    'hasil' => $data
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data hasil analisa tidak ditemukan.'
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi error: ' . $e->getMessage(),
            ], 500);
        }
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

        $antrians = $query->paginate(1);

        if ($request->ajax()) {
            return response()->json($antrians);
        }

        // Get list of available medicines from 'obat' table with bentuk_obat and stok
        $obats = \App\Models\Obat::select('id', 'nama_obat', 'bentuk_obat', 'stok')->get();

        return view('dokter.antrian', compact('antrians', 'obats'));
    }

    // public function storeHasilAnalisa(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'no_rekam_medis' => 'required|string',
    //         'tekanan_darah' => 'nullable|string',
    //         'frekuensi_nadi' => 'nullable|string',
    //         'suhu' => 'nullable|string',
    //         'frekuensi_nafas' => 'nullable|string',
    //         'skor_nyeri' => 'nullable|string',
    //         'skor_jatuh' => 'nullable|string',
    //         'berat_badan' => 'nullable|string',
    //         'tinggi_badan' => 'nullable|string',
    //         'lingkar_kepala' => 'nullable|string',
    //         'imt' => 'nullable|string',
    //         'alat_bantu' => 'nullable|string',
    //         'prosthesa' => 'nullable|string',
    //         'cacat_tubuh' => 'nullable|string',
    //         'adl_mandiri' => 'nullable|string',
    //         'riwayat_jatuh' => 'nullable|string',
    //         'status_psikologi' => 'nullable|array',
    //         'hambatan_edukasi' => 'nullable|array',
    //         'alergi' => 'nullable|string',
    //         'catatan' => 'nullable|string',
    //         'poli_tujuan' => 'required|string',
    //         'nama_user' => 'nullable|string',
    //     ]);

    //     $hasilAnalisa = new Hasilanalisa();
    //     $hasilAnalisa->no_rekam_medis = $validatedData['no_rekam_medis'];
    //     $hasilAnalisa->tekanan_darah = $validatedData['tekanan_darah'] ?? null;
    //     $hasilAnalisa->frekuensi_nadi = $validatedData['frekuensi_nadi'] ?? null;
    //     $hasilAnalisa->suhu = $validatedData['suhu'] ?? null;
    //     $hasilAnalisa->frekuensi_nafas = $validatedData['frekuensi_nafas'] ?? null;
    //     $hasilAnalisa->skor_nyeri = $validatedData['skor_nyeri'] ?? null;
    //     $hasilAnalisa->skor_jatuh = $validatedData['skor_jatuh'] ?? null;
    //     $hasilAnalisa->berat_badan = $validatedData['berat_badan'] ?? null;
    //     $hasilAnalisa->tinggi_badan = $validatedData['tinggi_badan'] ?? null;
    //     $hasilAnalisa->lingkar_kepala = $validatedData['lingkar_kepala'] ?? null;
    //     $hasilAnalisa->imt = $validatedData['imt'] ?? null;
    //     $hasilAnalisa->alat_bantu = $validatedData['alat_bantu'] ?? null;
    //     $hasilAnalisa->prosthesa = $validatedData['prosthesa'] ?? null;
    //     $hasilAnalisa->cacat_tubuh = $validatedData['cacat_tubuh'] ?? null;
    //     $hasilAnalisa->adl_mandiri = $validatedData['adl_mandiri'] ?? null;
    //     $hasilAnalisa->riwayat_jatuh = $validatedData['riwayat_jatuh'] ?? null;
    //     $hasilAnalisa->status_psikologi = isset($validatedData['status_psikologi']) ? json_encode($validatedData['status_psikologi']) : null;
    //     $hasilAnalisa->hambatan_edukasi = isset($validatedData['hambatan_edukasi']) ? json_encode($validatedData['hambatan_edukasi']) : null;
    //     $hasilAnalisa->alergi = $validatedData['alergi'] ?? null;
    //     $hasilAnalisa->catatan = $validatedData['catatan'] ?? null;
    //     $hasilAnalisa->poli_tujuan = $validatedData['poli_tujuan'];
    //     $hasilAnalisa->nama_user = $validatedData['nama_user'] ?? null;

    //     $hasilAnalisa->save();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Data analisa berhasil disimpan.',
    //     ]);
    // }
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

    public function getVisitDates($no_rekam_medis)
    {
        try {
            $pasien = \App\Models\Pasien::where('no_rekam_medis', $no_rekam_medis)->first();
            if (!$pasien) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pasien tidak ditemukan.'
                ]);
            }

            // Ambil semua tanggal dari hasil_periksa, hasil_analisa, hasil_periksa_anak, hasil_periksa_gigi
            $hasilPeriksaDates = \App\Models\HasilPeriksa::where('pasien_id', $pasien->id)
                ->pluck('tanggal_periksa')
                ->toArray();
            $hasilAnalisaDates = \App\Models\Hasilanalisa::where('pasien_id', $pasien->id)
                ->pluck('tanggal_analisa')
                ->toArray();
            $hasilPeriksaAnakDates = \App\Models\HasilperiksaAnak::where('pasien_id', $pasien->id)
                ->pluck('created_at')
                ->toArray();
            $hasilPeriksaGigiDates = \App\Models\HasilPeriksagigi::where('pasien_id', $pasien->id)
                ->pluck('tanggal_periksa')
                ->toArray();

            // Gabungkan semua tanggal, hilangkan duplikat, urutkan dari terlama ke terbaru
            $allDates = array_merge($hasilPeriksaDates, $hasilAnalisaDates, $hasilPeriksaAnakDates, $hasilPeriksaGigiDates);
            $allDates = array_filter($allDates); // hilangkan null/empty
            $uniqueDates = array_unique(array_map(function($d) {
                return date('Y-m-d', strtotime($d));
            }, $allDates));
            sort($uniqueDates); // urutkan dari terlama ke terbaru

            return response()->json([
                'success' => true,
                'data' => array_values($uniqueDates),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getVisitData($no_rekam_medis, $tanggal)
    {
        try {
            $pasien = \App\Models\Pasien::where('no_rekam_medis', $no_rekam_medis)->first();
            if (!$pasien) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pasien dengan nomor rekam medis ' . $no_rekam_medis . ' tidak ditemukan.'
                ], 404);
            }

            // Parse tanggal to date format
            $date = date('Y-m-d', strtotime($tanggal));

            // Query Hasilanalisa for the patient and date
            $hasilAnalisa = \App\Models\Hasilanalisa::where('pasien_id', $pasien->id)
                ->whereDate('tanggal_analisa', $date)
                ->latest()
                ->first();

            // Query HasilPeriksa for the patient and date
            $hasilPeriksa = \App\Models\HasilPeriksa::where('pasien_id', $pasien->id)
                ->whereDate('tanggal_periksa', $date)
                ->latest()
                ->first();

            // Query HasilperiksaAnak for the patient and date
            $hasilPeriksaAnak = \App\Models\HasilperiksaAnak::where('pasien_id', $pasien->id)
                ->whereDate('created_at', $date)
                ->latest()
                ->first();

            // Query HasilPeriksagigi for the patient and date
            $hasilPeriksaGigi = \App\Models\HasilPeriksagigi::where('pasien_id', $pasien->id)
                ->whereDate('tanggal_periksa', $date)
                ->latest()
                ->first();

            if (!$hasilAnalisa && !$hasilPeriksa && !$hasilPeriksaAnak && !$hasilPeriksaGigi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data hasil analisa atau hasil periksa untuk tanggal ' . $date . ' dan nomor rekam medis ' . $no_rekam_medis . '.',
                ], 404);
            }

            // Prepare response data
            $penanggungJawabNama = null;
            if ($hasilAnalisa && method_exists($hasilAnalisa, 'penanggungJawab')) {
                try {
                    $penanggungJawab = $hasilAnalisa->penanggungJawab;
                    if ($penanggungJawab) {
                        $penanggungJawabNama = $penanggungJawab->name;
                    }
                } catch (\Throwable $e) {
                    $penanggungJawabNama = null;
                }
            }

            // Ambil tanggal periksa yang valid dari hasil query (ISO 8601)
            $tanggalPeriksa = null;
            if ($hasilPeriksa && $hasilPeriksa->tanggal_periksa) {
                $tanggalPeriksa = \Carbon\Carbon::parse($hasilPeriksa->tanggal_periksa)->toISOString();
            } elseif ($hasilAnalisa && $hasilAnalisa->tanggal_analisa) {
                $tanggalPeriksa = \Carbon\Carbon::parse($hasilAnalisa->tanggal_analisa)->toISOString();
            } elseif ($hasilPeriksaAnak && $hasilPeriksaAnak->created_at) {
                $tanggalPeriksa = \Carbon\Carbon::parse($hasilPeriksaAnak->created_at)->toISOString();
            } elseif ($hasilPeriksaGigi && $hasilPeriksaGigi->tanggal_periksa) {
                $tanggalPeriksa = \Carbon\Carbon::parse($hasilPeriksaGigi->tanggal_periksa)->toISOString();
            } else {
                $tanggalPeriksa = "-";
            }

            $namaPoliTujuan = null;
            if ($hasilAnalisa && method_exists($hasilAnalisa, 'poli') && $hasilAnalisa->poli) {
                $namaPoliTujuan = $hasilAnalisa->poli->nama_poli;
            } elseif ($hasilAnalisa && $hasilAnalisa->poli_tujuan) {
                $namaPoliTujuan = $hasilAnalisa->poli_tujuan;
            }

            $data = [
                'tanggal_periksa' => $tanggalPeriksa,
                // Hasil Periksa fields
                'anamnesis' => $hasilPeriksa ? $hasilPeriksa->anamnesis : null,
                'pemeriksaan_fisik' => $hasilPeriksa ? $hasilPeriksa->pemeriksaan_fisik : null,
                'rencana_dan_terapi' => $hasilPeriksa ? $hasilPeriksa->rencana_dan_terapi : null,
                'diagnosis' => $hasilPeriksa ? $hasilPeriksa->diagnosis : null,
                'edukasi' => $hasilPeriksa ? $hasilPeriksa->edukasi : null,
                'kode_icd' => $hasilPeriksa ? $hasilPeriksa->kode_icd : null,
                'status_gizi' => $hasilPeriksa ? $hasilPeriksa->kesan_status_gizi : null,
                'penanggung_jawab_periksa' => ($hasilPeriksa && $hasilPeriksa->penanggung_jawab) ? (\App\Models\User::find($hasilPeriksa->penanggung_jawab)->name ?? '-') : null,
                // Hasil Periksa Gigi fields (mapping sesuai struktur tabel)
                'odontogram' => $hasilPeriksaGigi ? $hasilPeriksaGigi->odontogram : null,
                'pemeriksaan_subjektif' => $hasilPeriksaGigi ? $hasilPeriksaGigi->pemeriksaan_subjektif : null,
                'pemeriksaan_objektif' => $hasilPeriksaGigi ? $hasilPeriksaGigi->pemeriksaan_objektif : null,
                'diagnosa_gigi' => $hasilPeriksaGigi ? $hasilPeriksaGigi->diagnosa : null,
                'terapi_anjuran_gigi' => $hasilPeriksaGigi ? $hasilPeriksaGigi->terapi_anjuran : null,
                'catatan_gigi' => $hasilPeriksaGigi ? $hasilPeriksaGigi->catatan : null,
                'penanggung_jawab_gigi' => ($hasilPeriksaGigi && $hasilPeriksaGigi->penanggung_jawab) ? (\App\Models\User::find($hasilPeriksaGigi->penanggung_jawab)->name ?? '-') : null,
                // Hasil Analisa fields
                'tekanan_darah' => $hasilAnalisa ? $hasilAnalisa->tekanan_darah : null,
                'frekuensi_nadi' => $hasilAnalisa ? $hasilAnalisa->frekuensi_nadi : null,
                'suhu' => $hasilAnalisa ? $hasilAnalisa->suhu : null,
                'frekuensi_nafas' => $hasilAnalisa ? $hasilAnalisa->frekuensi_nafas : null,
                'skor_nyeri' => $hasilAnalisa ? $hasilAnalisa->skor_nyeri : null,
                'skor_jatuh' => $hasilAnalisa ? $hasilAnalisa->skor_jatuh : null,
                'berat_badan' => $hasilAnalisa ? $hasilAnalisa->berat_badan : null,
                'tinggi_badan' => $hasilAnalisa ? $hasilAnalisa->tinggi_badan : null,
                'lingkar_kepala' => $hasilAnalisa ? $hasilAnalisa->lingkar_kepala : null,
                'imt' => $hasilAnalisa ? $hasilAnalisa->imt : null,
                'alat_bantu' => $hasilAnalisa ? $hasilAnalisa->alat_bantu : null,
                'prosthesa' => $hasilAnalisa ? $hasilAnalisa->prosthesa : null,
                'cacat_tubuh' => $hasilAnalisa ? $hasilAnalisa->cacat_tubuh : null,
                'adl_mandiri' => $hasilAnalisa ? $hasilAnalisa->adl_mandiri : null,
                'riwayat_jatuh' => $hasilAnalisa ? $hasilAnalisa->riwayat_jatuh : null,
                'status_psikologi' => $hasilAnalisa ? (
                    $hasilAnalisa->status_psikologi
                        ? (is_array(json_decode($hasilAnalisa->status_psikologi, true))
                            ? implode(', ', json_decode($hasilAnalisa->status_psikologi, true))
                            : (is_string($hasilAnalisa->status_psikologi) ? $hasilAnalisa->status_psikologi : '-')
                        )
                        : null
                ) : null,
                'penanggung_jawab_analisa' => ($hasilAnalisa && $hasilAnalisa->penanggung_jawab) ? (\App\Models\User::find($hasilAnalisa->penanggung_jawab)->name ?? '-') : null,
                'hambatan_edukasi' => $hasilAnalisa ? (
                    $hasilAnalisa->hambatan_edukasi
                        ? (is_array(json_decode($hasilAnalisa->hambatan_edukasi, true))
                            ? implode(', ', json_decode($hasilAnalisa->hambatan_edukasi, true))
                            : (is_string($hasilAnalisa->hambatan_edukasi) ? $hasilAnalisa->hambatan_edukasi : '-')
                        )
                        : null
                ) : null,
                'alergi' => $hasilAnalisa ? $hasilAnalisa->alergi : null,
                'catatan' => $hasilAnalisa ? $hasilAnalisa->catatan : null,
                'poli_tujuan' => $namaPoliTujuan,
                'penanggung_jawab_nama' => $penanggungJawabNama,
                // Hasil Periksa Anak fields
                'berat_badan_anak' => $hasilPeriksaAnak ? $hasilPeriksaAnak->berat_badan : null,
                'makanan_anak' => $hasilPeriksaAnak ? $hasilPeriksaAnak->makanan_anak : null,
                'gejala_anak' => $hasilPeriksaAnak ? $hasilPeriksaAnak->gejala : null,
                'nasehat_anak' => $hasilPeriksaAnak ? $hasilPeriksaAnak->nasehat : null,
                'pegobatan_anak' => $hasilPeriksaAnak ? $hasilPeriksaAnak->pegobatan : null,
                'penanggung_jawab_anak' => ($hasilPeriksaAnak && $hasilPeriksaAnak->penanggung_jawab) ? (\App\Models\User::find($hasilPeriksaAnak->penanggung_jawab)->name ?? '-') : null,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi error pada server: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
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
