<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use App\Models\Hasilanalisa;
use App\Models\Pasien;
use App\Models\JadwalDokter;
use App\Models\User;
use App\Models\Antrian;
use Carbon\Carbon;

class BidanProfileController extends Controller
{
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

        return redirect()->route('bidan.profile')->with('status', 'Profil berhasil diperbarui.');
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

        return view('bidan.jadwal', compact('jadwalDokters', 'users'));
    }

    public function dashboard()
    {
        $today = Carbon::today()->toDateString();

        $totalAntrianKIA = Antrian::whereHas('poli', function ($query) {
            $query->where('nama_poli', 'KIA');
        })
        ->where('status', '!=', 'Selesai')
        ->whereDate('created_at', $today)
        ->count();

        $totalAntrianSelesai = Antrian::whereHas('poli', function ($query) {
            $query->where('nama_poli', 'KIA');
        })
        ->where('status', 'Selesai')
        ->whereDate('created_at', $today)
        ->count();

        return view('bidan.dashboard', compact('totalAntrianKIA', 'totalAntrianSelesai'));
    }

    public function storeHasilAnalisa(Request $request)
    {
        $validatedData = $request->validate([
            'no_rekam_medis' => 'required|string',
            'tekanan_darah' => 'nullable|string',
            'frekuensi_nadi' => 'nullable|string',
            'suhu' => 'nullable|string',
            'frekuensi_nafas' => 'nullable|string',
            'skor_nyeri' => 'nullable|string',
            'skor_jatuh' => 'nullable|string',
            'berat_badan' => 'nullable|string',
            'tinggi_badan' => 'nullable|string',
            'lingkar_kepala' => 'nullable|string',
            'imt' => 'nullable|string',
            'alat_bantu' => 'nullable|string',
            'prosthesa' => 'nullable|string',
            'cacat_tubuh' => 'nullable|string',
            'adl_mandiri' => 'nullable|string',
            'riwayat_jatuh' => 'nullable|string',
            'status_psikologi' => 'nullable|array',
            'hambatan_edukasi' => 'nullable|array',
            'alergi' => 'nullable|string',
            'catatan' => 'nullable|string',
            'poli_tujuan' => 'required|string',
            'nama_user' => 'nullable|string',
        ]);

        $hasilAnalisa = new Hasilanalisa();
        $hasilAnalisa->no_rekam_medis = $validatedData['no_rekam_medis'];
        $hasilAnalisa->tekanan_darah = $validatedData['tekanan_darah'] ?? null;
        $hasilAnalisa->frekuensi_nadi = $validatedData['frekuensi_nadi'] ?? null;
        $hasilAnalisa->suhu = $validatedData['suhu'] ?? null;
        $hasilAnalisa->frekuensi_nafas = $validatedData['frekuensi_nafas'] ?? null;
        $hasilAnalisa->skor_nyeri = $validatedData['skor_nyeri'] ?? null;
        $hasilAnalisa->skor_jatuh = $validatedData['skor_jatuh'] ?? null;
        $hasilAnalisa->berat_badan = $validatedData['berat_badan'] ?? null;
        $hasilAnalisa->tinggi_badan = $validatedData['tinggi_badan'] ?? null;
        $hasilAnalisa->lingkar_kepala = $validatedData['lingkar_kepala'] ?? null;
        $hasilAnalisa->imt = $validatedData['imt'] ?? null;
        $hasilAnalisa->alat_bantu = $validatedData['alat_bantu'] ?? null;
        $hasilAnalisa->prosthesa = $validatedData['prosthesa'] ?? null;
        $hasilAnalisa->cacat_tubuh = $validatedData['cacat_tubuh'] ?? null;
        $hasilAnalisa->adl_mandiri = $validatedData['adl_mandiri'] ?? null;
        $hasilAnalisa->riwayat_jatuh = $validatedData['riwayat_jatuh'] ?? null;
        $hasilAnalisa->status_psikologi = isset($validatedData['status_psikologi']) ? json_encode($validatedData['status_psikologi']) : null;
        $hasilAnalisa->hambatan_edukasi = isset($validatedData['hambatan_edukasi']) ? json_encode($validatedData['hambatan_edukasi']) : null;
        $hasilAnalisa->alergi = $validatedData['alergi'] ?? null;
        $hasilAnalisa->catatan = $validatedData['catatan'] ?? null;
        $hasilAnalisa->poli_tujuan = $validatedData['poli_tujuan'];
        $hasilAnalisa->nama_user = $validatedData['nama_user'] ?? null;

        $hasilAnalisa->save();

        return response()->json([
            'success' => true,
            'message' => 'Data analisa berhasil disimpan.',
        ]);
    }

    public function storeHasilPeriksaAnak(Request $request)
    {
        $validatedData = $request->validate([
            'no_rekam_medis' => 'required|string',
            'berat_badan' => 'required|string',
            'makanan_anak' => 'required|string',
            'gejala' => 'required|string',
            'nasehat' => 'required|string',
            'pegobatan' => 'required|string',
        ]);

        $pasien = \App\Models\Pasien::where('no_rekam_medis', $validatedData['no_rekam_medis'])->first();
        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Pasien tidak ditemukan.'
            ], 404);
        }

        $hasilPeriksaAnak = new \App\Models\HasilperiksaAnak();
        $hasilPeriksaAnak->pasien_id = $pasien->id;
        $hasilPeriksaAnak->berat_badan = $validatedData['berat_badan'];
        $hasilPeriksaAnak->makanan_anak = $validatedData['makanan_anak'];
        $hasilPeriksaAnak->gejala = $validatedData['gejala'];
        $hasilPeriksaAnak->nasehat = $validatedData['nasehat'];
        $hasilPeriksaAnak->pegobatan = $validatedData['pegobatan'];
        // Set penanggung_jawab ke id user yang sedang login
        $hasilPeriksaAnak->penanggung_jawab = $request->user()->id;
        $hasilPeriksaAnak->save();

        // Update status antrian pasien menjadi 'Pembayaran'
        $antrian = \App\Models\Antrian::where('no_rekam_medis', $validatedData['no_rekam_medis'])
            ->where('status', 'Pemeriksaan')
            ->latest()
            ->first();
        if ($antrian) {
            $antrian->status = 'Pembayaran';
            $antrian->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data hasil periksa anak berhasil disimpan.'
        ]);
    }

    public function pasien(Request $request)
    {
        $query = Pasien::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_pasien', 'like', "%$search%")
                    ->orWhere('no_rekam_medis', 'like', "%$search%")
                    ->orWhere('nik', 'like', "%$search%")
                    ->orWhere('alamat_jalan', 'like', "%$search%")
                    ->orWhere('kepala_keluarga', 'like', "%$search%")
                    ->orWhere('no_hp', 'like', "%$search%")
                ;
            });
        }
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->input('jenis_kelamin'));
        }
        if ($request->filled('gol_darah')) {
            $query->where('gol_darah', $request->input('gol_darah'));
        }
        if ($request->filled('jaminan_kesehatan')) {
            $query->where('jaminan_kesehatan', $request->input('jaminan_kesehatan'));
        }
        if ($request->filled('tempat_lahir')) {
            $query->where('tempat_lahir', 'like', "%" . $request->input('tempat_lahir') . "%");
        }
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', 'like', "%" . $request->input('kecamatan') . "%");
        }
        if ($request->filled('kelurahan')) {
            $query->where('kelurahan', 'like', "%" . $request->input('kelurahan') . "%");
        }
        if ($request->filled('status_pernikahan')) {
            $query->where('status_pernikahan', $request->input('status_pernikahan'));
        }
        if ($request->filled('tanggal_lahir')) {
            $query->whereDate('tanggal_lahir', $request->input('tanggal_lahir'));
        }

        // PAGINATION: tampilkan 2 data per halaman
        $pasiens = $query->paginate(2)->withQueryString();

        if ($request->ajax()) {
            $data = [];
            foreach ($pasiens as $pasien) {
                $data[] = [
                    'no_rekam_medis' => $pasien->no_rekam_medis,
                    'nik' => $pasien->nik,
                    'nama_pasien' => $pasien->nama_pasien,
                    'tempat_lahir' => $pasien->tempat_lahir,
                    'tanggal_lahir' => $pasien->tanggal_lahir ? $pasien->tanggal_lahir->format('Y-m-d') : null,
                    'jenis_kelamin' => $pasien->jenis_kelamin,
                    'gol_darah' => $pasien->gol_darah,
                    'agama' => $pasien->agama,
                    'pekerjaan' => $pasien->pekerjaan,
                    'status_pernikahan' => $pasien->status_pernikahan,
                    'alamat_jalan' => $pasien->alamat_jalan,
                    'rt' => $pasien->rt,
                    'rw' => $pasien->rw,
                    'kelurahan' => $pasien->kelurahan,
                    'kecamatan' => $pasien->kecamatan,
                    'kabupaten' => $pasien->kabupaten,
                    'provinsi' => $pasien->provinsi,
                    'jaminan_kesehatan' => $pasien->jaminan_kesehatan,
                    'nomor_kepesertaan' => $pasien->nomor_kepesertaan,
                    'kepala_keluarga' => $pasien->kepala_keluarga,
                    'no_hp' => $pasien->no_hp,
                ];
            }
            // Pastikan $pasiens adalah instance dari LengthAwarePaginator, bukan Collection
            return response()->json([
                'data' => $data,
                'current_page' => $pasiens->currentPage(),
                'last_page' => $pasiens->lastPage(),
                'total' => $pasiens->total(),
                'per_page' => $pasiens->perPage(),
                'from' => $pasiens->firstItem(),
                'to' => $pasiens->lastItem(),
                'prev_page_url' => $pasiens->previousPageUrl(),
                'next_page_url' => $pasiens->nextPageUrl(),
                'path' => $pasiens->path(),
            ]);
        }

        return view('bidan.pasien', compact('pasiens'));
    }

    // Menampilkan riwayat berobat pasien untuk bidan
    public function riwayatBerobat($no_rekam_medis)
    {
        // Contoh: ambil data pasien dan riwayat berobat (ganti sesuai kebutuhan)
        $pasien = \App\Models\Pasien::where('no_rekam_medis', $no_rekam_medis)->firstOrFail();
        $riwayat = $pasien->riwayatBerobat() // pastikan relasi ini ada di model Pasien
            ->orderByDesc('tanggal_kunjungan')
            ->get();
        return view('bidan.riwayat', compact('pasien', 'riwayat'));
    }

    public function antrian()
    {
        $query = \App\Models\Antrian::with(['pasien', 'poli'])
            ->where('status', 'Pemeriksaan')
            ->whereHas('poli', function ($q) {
                $q->where('nama_poli', 'KIA');
            });
        // Filter pencarian
        $search = request('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_rekam_medis', 'like', "%$search%")
                    ->orWhereHas('pasien', function ($q2) use ($search) {
                        $q2->where('nama_pasien', 'like', "%$search%")
                            ->orWhere('jaminan_kesehatan', 'like', "%$search%")
                            ->orWhere('no_rekam_medis', 'like', "%$search%")
                            ->orWhere('nik', 'like', "%$search%")
                            ->orWhere('alamat_jalan', 'like', "%$search%")
                            ->orWhere('kepala_keluarga', 'like', "%$search%")
                            ->orWhere('no_hp', 'like', "%$search%")
                        ;
                    });
            });
        }
        $antrians = $query->orderBy('created_at', 'asc')->paginate(1)->withQueryString();
        // Jika request AJAX, return hanya table dan pagination
        if (request()->ajax()) {
            return view('bidan.antrian', compact('antrians'))->render();
        }
        return view('bidan.antrian', compact('antrians'));
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
}
