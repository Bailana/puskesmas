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

        return view('perawat.dashboard', compact('totalAntrianPerluAnalisa', 'totalAntrianSelesai'));
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
}
