<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antrian;
use Illuminate\Support\Facades\Log;
use App\Models\JadwalDokter;
use App\Models\User;

class PerawatDashboardController extends Controller
{
    /**
     * Display the perawat dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $today = \Carbon\Carbon::today()->toDateString();

        $totalAntrianPerluAnalisa = \App\Models\Antrian::where('status', 'Perlu Analisa')
            ->whereDate('created_at', $today)
            ->count();

        $totalAntrianSelesai = \App\Models\Antrian::where('status', 'Selesai')
            ->whereDate('created_at', $today)
            ->count();

        // Get patient count per month for current year
        $currentYear = \Carbon\Carbon::now()->year;
        $pasienPerBulan = \App\Models\Antrian::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Fill missing months with 0 count
        $pasienPerBulanFull = [];
        for ($m = 1; $m <= 12; $m++) {
            $pasienPerBulanFull[$m] = $pasienPerBulan[$m] ?? 0;
        }

        return view('perawat.dashboard', compact('totalAntrianPerluAnalisa', 'totalAntrianSelesai', 'pasienPerBulanFull'));
    }

    /**
     * Display the antrian page for perawat.
     *
     * @return \Illuminate\View\View
     */
    public function antrian(Request $request)
    {
        $query = $request->input('search');
        $antrians = \App\Models\Antrian::with('pasien')->where('status', 'Perlu Analisa');
        if ($query) {
            $antrians->whereHas('pasien', function ($q) use ($query) {
                $q->where('nama_pasien', 'like', "%$query%")
                    ->orWhere('no_rekam_medis', 'like', "%$query%")
                    ->orWhere('nik', 'like', "%$query%")
                ;
            });
        }
        $antrians = $antrians->orderBy('id', 'asc')->paginate(5);
        if ($request->ajax()) {
            // Kirim data dengan relasi pasien secara eksplisit
            $antrians->getCollection()->transform(function ($antrian) {
                $antrianArr = $antrian->toArray();
                $antrianArr['pasien'] = $antrian->pasien ? $antrian->pasien->toArray() : null;
                return $antrianArr;
            });
            return response()->json($antrians);
        }
        return view('perawat.antrian', compact('antrians'));
    }

    /**
     * Tampilkan data pasien untuk perawat (dengan filter, search, dan pagination)
     */
    public function pasien(Request $request)
    {
        $query = $request->input('search');
        $pasiens = \App\Models\Pasien::query();

        // Filter dari form modal
        if ($request->filled('jenis_kelamin')) {
            $pasiens->where('jenis_kelamin', $request->input('jenis_kelamin'));
        }
        if ($request->filled('gol_darah')) {
            $pasiens->where('gol_darah', $request->input('gol_darah'));
        }
        if ($request->filled('jaminan_kesehatan')) {
            $pasiens->where('jaminan_kesehatan', $request->input('jaminan_kesehatan'));
        }
        if ($request->filled('tempat_lahir')) {
            $pasiens->where('tempat_lahir', 'like', '%' . $request->input('tempat_lahir') . '%');
        }
        if ($request->filled('kecamatan')) {
            $pasiens->where('kecamatan', 'like', '%' . $request->input('kecamatan') . '%');
        }
        if ($request->filled('kelurahan')) {
            $pasiens->where('kelurahan', 'like', '%' . $request->input('kelurahan') . '%');
        }
        if ($request->filled('status_pernikahan')) {
            $pasiens->where('status_pernikahan', $request->input('status_pernikahan'));
        }
        if ($request->filled('tanggal_lahir')) {
            $pasiens->whereDate('tanggal_lahir', $request->input('tanggal_lahir'));
        }

        if ($query) {
            $pasiens = $pasiens->where(function ($q) use ($query) {
                $q->where('nama_pasien', 'like', '%' . $query . '%')
                    ->orWhere('no_rekam_medis', 'like', '%' . $query . '%');
            });
        }

        $pasiens = $pasiens->paginate(5);

        if ($request->ajax()) {
            return response()->json($pasiens);
        }

        return view('perawat.pasien', compact('pasiens'));
    }

    /**
     * AJAX: Get patient detail by no_rekam_medis (untuk modal detail pasien perawat)
     */
    public function getPatientDetail($no_rekam_medis)
    {
        Log::info('getPatientDetail called', ['no_rekam_medis' => $no_rekam_medis]);
        $pasien = \App\Models\Pasien::where('no_rekam_medis', $no_rekam_medis)->first();
        if (!$pasien) {
            Log::warning('Pasien tidak ditemukan', ['no_rekam_medis' => $no_rekam_medis]);
            return response()->json(['error' => 'Pasien tidak ditemukan'], 404);
        }
        Log::info('Pasien ditemukan', ['pasien' => $pasien]);
        return response()->json($pasien);
    }

    /**
     * AJAX: Get patient detail by pasien_id (untuk modal detail pasien perawat)
     */
    public function getPatientDetailById($pasien_id)
    {
        Log::info('getPatientDetailById called', ['pasien_id' => $pasien_id]);
        $pasien = \App\Models\Pasien::find($pasien_id);
        if (!$pasien) {
            Log::warning('Pasien tidak ditemukan', ['pasien_id' => $pasien_id]);
            return response()->json(['error' => 'Pasien tidak ditemukan'], 404);
        }
        Log::info('Pasien ditemukan', ['pasien' => $pasien]);
        return response()->json($pasien);
    }

    /**
     * Tampilkan jadwal dokter untuk perawat
     */
    public function jadwalDokter()
    {
        $jadwalDokters = \App\Models\JadwalDokter::all();
        return view('perawat.jadwaldokter', compact('jadwalDokters'));
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

        return view('perawat.jadwal', compact('jadwalDokters', 'users'));
    }

    public function getVisitDates($no_rekam_medis, Request $request)
    {
        try {
            \Log::info('getVisitDates called', ['no_rekam_medis' => $no_rekam_medis]);
            $pasien = \App\Models\Pasien::where('no_rekam_medis', $no_rekam_medis)->first();
            if (!$pasien) {
                \Log::warning('Pasien tidak ditemukan', ['no_rekam_medis' => $no_rekam_medis]);
                return response()->json([
                    'success' => false,
                    'message' => 'Pasien tidak ditemukan.'
                ]);
            }

            $type = $request->query('type', 'rawatjalan');

            if ($type === 'rawatinap') {
                // Ambil tanggal dari hasilperiksa_ugd dan hasilanalisa_rawatinap
                $hasilPeriksaUgdDates = \DB::table('hasilperiksa_ugd')
                    ->where('pasien_id', $pasien->id)
                    ->pluck('tanggal')
                    ->toArray();

                $hasilAnalisaRawatinapDates = \DB::table('hasilanalisa_rawatinap')
                    ->where('pasien_id', $pasien->id)
                    ->pluck('created_at')
                    ->toArray();

                // Hilangkan duplikat dan urutkan tanggal rawatinap
                $rawatinapDatesWithSource = [];

                foreach ($hasilPeriksaUgdDates as $date) {
                    $rawatinapDatesWithSource[] = [
                        'date' => date('Y-m-d', strtotime($date)),
                        'source' => 'hasilperiksa_ugd',
                    ];
                }

                foreach ($hasilAnalisaRawatinapDates as $date) {
                    $rawatinapDatesWithSource[] = [
                        'date' => date('Y-m-d', strtotime($date)),
                        'source' => 'hasilanalisa_rawatinap',
                    ];
                }

                $uniqueRawatinapDates = [];
                $seenRawatinap = [];
                foreach ($rawatinapDatesWithSource as $item) {
                    $key = $item['date'] . '_' . $item['source'];
                    if (!isset($seenRawatinap[$key])) {
                        $uniqueRawatinapDates[] = $item;
                        $seenRawatinap[$key] = true;
                    }
                }

                usort($uniqueRawatinapDates, function ($a, $b) {
                    return strcmp($a['date'], $b['date']);
                });

                // Ambil tanggal dari hasilperiksa, hasilanalisa, hasilperiksagigi, hasilperiksa_anak
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

                // Hilangkan duplikat dan urutkan tanggal rawatjalan
                $rawatjalanDatesWithSource = [];

                foreach ($hasilPeriksaDates as $date) {
                    $rawatjalanDatesWithSource[] = [
                        'date' => date('Y-m-d', strtotime($date)),
                        'source' => 'hasilperiksa',
                    ];
                }

                foreach ($hasilAnalisaDates as $date) {
                    $rawatjalanDatesWithSource[] = [
                        'date' => date('Y-m-d', strtotime($date)),
                        'source' => 'hasilanalisa',
                    ];
                }

                foreach ($hasilPeriksaAnakDates as $date) {
                    $rawatjalanDatesWithSource[] = [
                        'date' => date('Y-m-d', strtotime($date)),
                        'source' => 'hasilperiksa_anak',
                    ];
                }

                foreach ($hasilPeriksaGigiDates as $date) {
                    $rawatjalanDatesWithSource[] = [
                        'date' => date('Y-m-d', strtotime($date)),
                        'source' => 'hasilperiksagigi',
                    ];
                }

                $uniqueRawatjalanDates = [];
                $seenRawatjalan = [];
                foreach ($rawatjalanDatesWithSource as $item) {
                    $key = $item['date'] . '_' . $item['source'];
                    if (!isset($seenRawatjalan[$key])) {
                        $uniqueRawatjalanDates[] = $item;
                        $seenRawatjalan[$key] = true;
                    }
                }

                usort($uniqueRawatjalanDates, function ($a, $b) {
                    return strcmp($a['date'], $b['date']);
                });

                \Log::info('getVisitDates returning separated data', [
                    'rawatinap' => $uniqueRawatinapDates,
                    'rawatjalan' => $uniqueRawatjalanDates,
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'rawatinap' => $uniqueRawatinapDates,
                        'rawatjalan' => $uniqueRawatjalanDates,
                    ],
                ]);
            } else {
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
                $uniqueDates = array_unique(array_map(function ($d) {
                    return date('Y-m-d', strtotime($d));
                }, $allDates));
                sort($uniqueDates); // urutkan dari terlama ke terbaru

                \Log::info('getVisitDates returning', ['data' => $uniqueDates]);

                return response()->json([
                    'success' => true,
                    'data' => array_values($uniqueDates),
                ]);
            }
        } catch (\Throwable $e) {
            \Log::error('Error in getVisitDates', ['exception' => $e]);
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

            // Query HasilPeriksaUgd for the patient and date
            $hasilPeriksaUgd = \DB::table('hasilperiksa_ugd')
                ->where('pasien_id', $pasien->id)
                ->whereDate('tanggal', $date)
                ->latest('tanggal')
                ->first();

            // Query HasilanalisaRawatinap for the patient and date
            $hasilAnalisaRawatinap = \App\Models\HasilanalisaRawatinap::where('pasien_id', $pasien->id)
                ->whereDate('created_at', $date)
                ->latest()
                ->first();

            if (!$hasilAnalisa && !$hasilPeriksa && !$hasilPeriksaAnak && !$hasilPeriksaGigi && !$hasilPeriksaUgd && !$hasilAnalisaRawatinap) {
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
            } elseif ($hasilPeriksaUgd && $hasilPeriksaUgd->tanggal) {
                $tanggalPeriksa = \Carbon\Carbon::parse($hasilPeriksaUgd->tanggal)->toISOString();
            } elseif ($hasilAnalisaRawatinap && $hasilAnalisaRawatinap->created_at) {
                $tanggalPeriksa = \Carbon\Carbon::parse($hasilAnalisaRawatinap->created_at)->toISOString();
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
                // Hasil Periksa UGD fields
                'tanggal_periksa_ugd' => $hasilPeriksaUgd ? $hasilPeriksaUgd->tanggal : null,
                'waktu_ugd' => $hasilPeriksaUgd ? $hasilPeriksaUgd->waktu : null,
                'soap_ugd' => $hasilPeriksaUgd ? $hasilPeriksaUgd->soap : null,
                'intruksi_tenaga_kerja_ugd' => $hasilPeriksaUgd ? $hasilPeriksaUgd->intruksi_tenagakerja : null,
                'penanggung_jawab_ugd' => $hasilPeriksaUgd ? (\App\Models\User::find($hasilPeriksaUgd->penanggung_jawab)->name ?? null) : null,
                // Hasil Analisa Rawatinap fields
                'tekanan_darah_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->tekanan_darah : null,
                'frekuensi_nadi_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->frekuensi_nadi : null,
                'suhu_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->suhu : null,
                'frekuensi_nafas_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->frekuensi_nafas : null,
                'skor_nyeri_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->skor_nyeri : null,
                'skor_jatuh_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->skor_jatuh : null,
                'berat_badan_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->berat_badan : null,
                'tinggi_badan_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->tinggi_badan : null,
                'lingkar_kepala_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->lingkar_kepala : null,
                'imt_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->imt : null,
                'alat_bantu_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->alat_bantu : null,
                'prosthesa_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->prosthesa : null,
                'cacat_tubuh_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->cacat_tubuh : null,
                'adl_mandiri_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->adl_mandiri : null,
                'riwayat_jatuh_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->riwayat_jatuh : null,
                'status_psikologi_rawatinap' => $hasilAnalisaRawatinap ? (
                    $hasilAnalisaRawatinap->status_psikologi
                        ? (is_array(json_decode($hasilAnalisaRawatinap->status_psikologi, true))
                            ? implode(', ', json_decode($hasilAnalisaRawatinap->status_psikologi, true))
                            : (is_string($hasilAnalisaRawatinap->status_psikologi) ? $hasilAnalisaRawatinap->status_psikologi : '-')
                        )
                        : null
                ) : null,
                'hambatan_edukasi_rawatinap' => $hasilAnalisaRawatinap ? (
                    $hasilAnalisaRawatinap->hambatan_edukasi
                        ? (is_array(json_decode($hasilAnalisaRawatinap->hambatan_edukasi, true))
                            ? implode(', ', json_decode($hasilAnalisaRawatinap->hambatan_edukasi, true))
                            : (is_string($hasilAnalisaRawatinap->hambatan_edukasi) ? $hasilAnalisaRawatinap->hambatan_edukasi : '-')
                        )
                        : null
                ) : null,
                'alergi_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->alergi : null,
                'catatan_rawatinap' => $hasilAnalisaRawatinap ? $hasilAnalisaRawatinap->catatan : null,
                'penanggung_jawab_rawatinap' => ($hasilAnalisaRawatinap && $hasilAnalisaRawatinap->penanggung_jawab) ? (\App\Models\User::find($hasilAnalisaRawatinap->penanggung_jawab)->name ?? '-') : null,
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
