<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hasilanalisa;
use App\Models\Antrian;
use App\Models\Poli;
use Illuminate\Support\Facades\Log;

class PerawatHasilAnalisaController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'pasien_id' => 'required|integer',
                'tekanan_darah' => 'nullable|string',
                'frekuensi_nadi' => 'nullable|string',
                'suhu' => 'nullable|string',
                'frekuensi_nafas' => 'nullable|string',
                'skor_nyeri' => 'nullable|integer',
                'skor_jatuh' => 'nullable|integer',
                'berat_badan' => 'nullable|numeric',
                'tinggi_badan' => 'nullable|numeric',
                'lingkar_kepala' => 'nullable|numeric',
                'imt' => 'nullable|numeric',
                'alat_bantu' => 'nullable|string',
                'prosthesa' => 'nullable|string',
                'cacat_tubuh' => 'nullable|string',
                'adl_mandiri' => 'nullable|string',
                'riwayat_jatuh' => 'nullable|string',
                'status_psikologi' => 'nullable|array',
                'hambatan_edukasi' => 'nullable|array',
                'alergi' => 'nullable|string',
                'catatan' => 'nullable|string',
                'poli_tujuan' => 'required|integer|exists:poli,id',
                'penanggung_jawab' => 'required|integer|exists:users,id', // pastikan penanggung_jawab adalah ID user yang valid
            ]);

            // Validasi poli_tujuan benar-benar ada di tabel poli
            $poli = Poli::find($validatedData['poli_tujuan']);
            if (!$poli) {
                Log::warning('Poli tujuan tidak ditemukan', ['poli_tujuan' => $validatedData['poli_tujuan'], 'request' => $request->all()]);
                return response()->json(['success' => false, 'message' => 'Poli tujuan tidak ditemukan.'], 422);
            }

            // Validasi penanggung_jawab wajib ada dan valid
            if (!isset($validatedData['penanggung_jawab']) || empty($validatedData['penanggung_jawab'])) {
                Log::warning('Penanggung jawab wajib diisi', ['request' => $request->all()]);
                return response()->json(['success' => false, 'message' => 'Penanggung jawab wajib diisi.'], 422);
            }

            // Ubah array ke JSON string jika perlu
            if (isset($validatedData['status_psikologi'])) {
                $validatedData['status_psikologi'] = json_encode($validatedData['status_psikologi']);
            }
            if (isset($validatedData['hambatan_edukasi'])) {
                $validatedData['hambatan_edukasi'] = json_encode($validatedData['hambatan_edukasi']);
            }

            // Tambahkan tanggal_analisa otomatis
            $validatedData['tanggal_analisa'] = now();

            $hasil = Hasilanalisa::create($validatedData);

            // Update status dan poli_id pada tabel antrian
            $antrian = Antrian::where('pasien_id', $validatedData['pasien_id'])->where('status', 'Perlu Analisa')->first();
            if ($antrian) {
                $antrian->status = 'Pemeriksaan';
                $antrian->poli_id = $validatedData['poli_tujuan'];
                $antrian->save();
            }

            if ($hasil) {
                return response()->json(['success' => true, 'message' => 'Data analisa berhasil disimpan.']);
            } else {
                Log::error('Gagal insert ke tabel hasilanalisa', $validatedData);
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan data analisa.'], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error saving hasil analisa: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getRiwayatHasilAnalisa($no_rekam_medis)
    {
        Log::info('getRiwayatHasilAnalisa called', ['no_rekam_medis' => $no_rekam_medis]);
        $pasien = \App\Models\Pasien::where('no_rekam_medis', $no_rekam_medis)->first();
        if (!$pasien) {
            Log::warning('Pasien tidak ditemukan', ['no_rekam_medis' => $no_rekam_medis]);
            return response()->json(['error' => 'Pasien tidak ditemukan'], 404);
        }
        Log::info('Pasien ditemukan', ['pasien_id' => $pasien->id]);
        $hasilAnalisa = Hasilanalisa::where('pasien_id', $pasien->id)->orderBy('tanggal_analisa', 'desc')->get();
        Log::info('Hasil analisa count', ['count' => $hasilAnalisa->count()]);
        return response()->json($hasilAnalisa);
    }
}