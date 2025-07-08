<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Pasien;
use Carbon\Carbon;

class AntrianController extends Controller
{
    /**
     * Store a newly created antrian in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the request data
        $validated = $request->validate([
            'no_rekam_medis' => 'required|string',
            'tanggal_berobat' => 'required|date',
            'status' => 'required|string',
            'tujuan_poli' => 'nullable|string',
        ]);

        // Find pasien by no_rekam_medis
        $pasien = \App\Models\Pasien::where('no_rekam_medis', $validated['no_rekam_medis'])->first();
        if (!$pasien) {
            return response()->json([
                'message' => 'Pasien tidak ditemukan',
            ], 404);
        }

        // Find poli by nama_poli
        $poli = null;
        if (!empty($validated['tujuan_poli'])) {
            $poli = \App\Models\Poli::where('nama_poli', $validated['tujuan_poli'])->first();
        }

        // Create new antrian
        $antrian = new \App\Models\Antrian();
        $antrian->no_rekam_medis = $validated['no_rekam_medis'];
        $antrian->tanggal_berobat = $validated['tanggal_berobat'];
        $antrian->status = $validated['status'];
        $antrian->pasien_id = $pasien->id;
        $antrian->poli_id = $poli ? $poli->id : null;
        $antrian->save();

        return response()->json([
            'message' => 'Antrian berhasil dibuat',
            'data' => $antrian,
        ], 201);
    }

    /**
     * Search pasien by nomor kepesertaan.
     *
     * @param string $nomorKepesertaan
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPasien(string $nomorKepesertaan): JsonResponse
    {
        $pasien = Pasien::where('nomor_kepesertaan', $nomorKepesertaan)->first();

        if ($pasien) {
            $umur = Carbon::parse($pasien->tanggal_lahir)->age;

            return response()->json([
                'success' => true,
                'pasien' => [
                    'nama_pasien' => $pasien->nama_pasien,
                    'no_rekam_medis' => $pasien->no_rekam_medis,
                    'umur' => $umur,
                ],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pasien dengan nomor kepesertaan tersebut tidak ditemukan.',
            ]);
        }
    }

    /**
     * Remove the specified antrian from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id): JsonResponse
    {
        $antrian = \App\Models\Antrian::find($id);
        if (!$antrian) {
            return response()->json([
                'message' => 'Antrian tidak ditemukan',
            ], 404);
        }

        $antrian->delete();

        return response()->json([
            'message' => 'Antrian berhasil dihapus',
        ]);
    }
}
