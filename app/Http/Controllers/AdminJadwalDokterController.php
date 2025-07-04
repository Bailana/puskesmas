<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalDokter;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AdminJadwalDokterController extends Controller
{
    public function index()
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

        return view('admin.jadwaldokter', compact('jadwalDokters', 'users'));
    }

    public function editGroup($nama_dokter, $poliklinik)
    {
        $jadwals = JadwalDokter::where('nama_dokter', $nama_dokter)
            ->where('poliklinik', $poliklinik)
            ->get();

        return response()->json($jadwals);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_dokter' => 'required|string|max:255',
            'poliklinik' => 'required|string|max:255',
            'hari' => 'required|array',
            'hari.*' => 'required|string',
            'jam_masuk' => 'required|array',
            'jam_masuk.*' => 'required|date_format:H:i',
            'jam_keluar' => 'required|array',
            'jam_keluar.*' => 'required|date_format:H:i',
        ]);

        $namaDokter = $validated['nama_dokter'];
        $poliklinik = $validated['poliklinik'];
        $hariArray = $validated['hari'];
        $jamMasukArray = $validated['jam_masuk'];
        $jamKeluarArray = $validated['jam_keluar'];

        // Filter data yang lengkap (tidak kosong)
        $filteredHari = [];
        $filteredJamMasuk = [];
        $filteredJamKeluar = [];
        foreach ($hariArray as $index => $hari) {
            if (!empty($hari) && !empty($jamMasukArray[$index]) && !empty($jamKeluarArray[$index])) {
                $filteredHari[] = $hari;
                $filteredJamMasuk[] = $jamMasukArray[$index];
                $filteredJamKeluar[] = $jamKeluarArray[$index];
            }
        }

        if (empty($filteredHari)) {
            return redirect()->back()->withErrors(['hari' => 'Harap isi minimal satu jadwal hari dengan lengkap.'])->withInput();
        }

        // Simpan satu record dengan array data
        JadwalDokter::create([
            'nama_dokter' => $namaDokter,
            'poliklinik' => $poliklinik,
            'hari' => $filteredHari,
            'jam_masuk' => $filteredJamMasuk,
            'jam_keluar' => $filteredJamKeluar,
        ]);

        return redirect()->route('admin.jadwaldokter')->with('success', 'Jadwal dokter berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jadwal = JadwalDokter::findOrFail($id);
        return response()->json($jadwal);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_dokter' => 'required|string|max:255',
            'poliklinik' => 'required|string|max:255',
            'hari' => 'required|array',
            'hari.*' => 'required|string',
            'jam_masuk' => 'required|array',
            'jam_masuk.*' => 'required|date_format:H:i',
            'jam_keluar' => 'required|array',
            'jam_keluar.*' => 'required|date_format:H:i',
        ]);

        $jadwalId = $request->input('jadwalId');

        $originalJadwal = JadwalDokter::find($jadwalId);

        if (!$originalJadwal) {
            return redirect()->back()->withErrors(['jadwalId' => 'Data jadwal tidak ditemukan.'])->withInput();
        }

        $namaDokter = $validated['nama_dokter'];
        $poliklinik = $validated['poliklinik'];
        $hariArray = $validated['hari'];
        $jamMasukArray = $validated['jam_masuk'];
        $jamKeluarArray = $validated['jam_keluar'];

        // Filter data yang lengkap (tidak kosong)
        $filteredData = [];
        foreach ($hariArray as $index => $hari) {
            if (!empty($hari) && !empty($jamMasukArray[$index]) && !empty($jamKeluarArray[$index])) {
                $filteredData[] = [
                    'hari' => $hari,
                    'jam_masuk' => $jamMasukArray[$index],
                    'jam_keluar' => $jamKeluarArray[$index],
                ];
            }
        }

        if (empty($filteredData)) {
            return redirect()->back()->withErrors(['hari' => 'Harap isi minimal satu jadwal hari dengan lengkap.'])->withInput();
        }

        // Hapus semua jadwal lama berdasarkan nama_dokter dan poliklinik asli
        JadwalDokter::where('nama_dokter', $originalJadwal->nama_dokter)
            ->where('poliklinik', $originalJadwal->poliklinik)
            ->delete();

        // Simpan ulang jadwal baru
        foreach ($filteredData as $data) {
            JadwalDokter::create([
                'nama_dokter' => $namaDokter,
                'poliklinik' => $poliklinik,
                'hari' => $data['hari'],
                'jam_masuk' => $data['jam_masuk'],
                'jam_keluar' => $data['jam_keluar'],
            ]);
        }

        return redirect()->route('admin.jadwaldokter')->with('success', 'Jadwal dokter berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jadwal = JadwalDokter::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('admin.jadwaldokter')->with('success', 'Jadwal dokter berhasil dihapus.');
    }
}
