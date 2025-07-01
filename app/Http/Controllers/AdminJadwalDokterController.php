<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalDokter;

class AdminJadwalDokterController extends Controller
{
    public function index()
    {
        $jadwalDokters = JadwalDokter::all();
        return view('admin.jadwaldokter', compact('jadwalDokters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_dokter' => 'required|string|max:255',
            'poliklinik' => 'required|string|max:255',
            'senin_masuk' => 'nullable|date_format:H:i',
            'senin_keluar' => 'nullable|date_format:H:i',
            'selasa_masuk' => 'nullable|date_format:H:i',
            'selasa_keluar' => 'nullable|date_format:H:i',
            'rabu_masuk' => 'nullable|date_format:H:i',
            'rabu_keluar' => 'nullable|date_format:H:i',
            'kamis_masuk' => 'nullable|date_format:H:i',
            'kamis_keluar' => 'nullable|date_format:H:i',
            'jumat_masuk' => 'nullable|date_format:H:i',
            'jumat_keluar' => 'nullable|date_format:H:i',
            'sabtu_masuk' => 'nullable|date_format:H:i',
            'sabtu_keluar' => 'nullable|date_format:H:i',
            'minggu_masuk' => 'nullable|date_format:H:i',
            'minggu_keluar' => 'nullable|date_format:H:i',
        ]);

        JadwalDokter::create($validated);

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
            'senin_masuk' => 'nullable|date_format:H:i',
            'senin_keluar' => 'nullable|date_format:H:i',
            'selasa_masuk' => 'nullable|date_format:H:i',
            'selasa_keluar' => 'nullable|date_format:H:i',
            'rabu_masuk' => 'nullable|date_format:H:i',
            'rabu_keluar' => 'nullable|date_format:H:i',
            'kamis_masuk' => 'nullable|date_format:H:i',
            'kamis_keluar' => 'nullable|date_format:H:i',
            'jumat_masuk' => 'nullable|date_format:H:i',
            'jumat_keluar' => 'nullable|date_format:H:i',
            'sabtu_masuk' => 'nullable|date_format:H:i',
            'sabtu_keluar' => 'nullable|date_format:H:i',
            'minggu_masuk' => 'nullable|date_format:H:i',
            'minggu_keluar' => 'nullable|date_format:H:i',
        ]);

        $jadwal = JadwalDokter::findOrFail($id);
        $jadwal->update($validated);

        return redirect()->route('admin.jadwaldokter')->with('success', 'Jadwal dokter berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jadwal = JadwalDokter::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('admin.jadwaldokter')->with('success', 'Jadwal dokter berhasil dihapus.');
    }
}
