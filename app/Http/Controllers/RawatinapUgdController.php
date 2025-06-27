<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PasiensUgd;
use App\Models\HasilanalisaRawatinap;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RawatinapUgdController extends Controller
{
    public function index()
    {
        $pasiens_ugd = PasiensUgd::with('pasien')->where('status', 'Rawat Inap')->get();
        return view('admin.rawatinap', compact('pasiens_ugd'));
    }

    public function profile()
    {
        return view('rawatinap.profile');
    }
    public function getUgdPatientDetail($no_rekam_medis)
    {
        \Log::info('getUgdPatientDetail called', ['no_rekam_medis' => $no_rekam_medis]);
        $pasien = \App\Models\Pasien::where('no_rekam_medis', $no_rekam_medis)->first();

        if (!$pasien) {
            \Log::warning('Pasien tidak ditemukan', ['no_rekam_medis' => $no_rekam_medis]);
            return response()->json(['success' => false, 'message' => 'Pasien tidak ditemukan'], 404);
        }

        \Log::info('Pasien ditemukan', ['pasien' => $pasien]);
        return response()->json(['success' => true, 'data' => $pasien]);
    }

    public function getPatientById($id)
    {
        $pasien = \App\Models\Pasien::find($id);

        if (!$pasien) {
            return response()->json(['success' => false, 'message' => 'Pasien tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $pasien]);
    }

    public function edit($id)
    {
        $pasien = PasiensUgd::findOrFail($id);
        return view('admin.rawatinap_edit', compact('pasien'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nik' => 'required|string',
            'nama_pasien' => 'required|string',
            'kepala_keluarga' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string',
            'gol_darah' => 'nullable|string',
            'agama' => 'nullable|string',
            'pekerjaan' => 'nullable|string',
            'status_pernikahan' => 'nullable|string',
            'alamat_jalan' => 'nullable|string',
            'rt' => 'nullable|string',
            'rw' => 'nullable|string',
            'kelurahan' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'kabupaten' => 'nullable|string',
            'provinsi' => 'nullable|string',
            'jaminan_kesehatan' => 'nullable|string',
            'nomor_kepesertaan' => 'nullable|string',
            'ruangan' => 'nullable|string',
        ]);

        $pasien = PasiensUgd::findOrFail($id);
        $pasien->update($validatedData);

        return redirect()->route('rawatinap.ugd')->with('success', 'Pasien UGD berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pasien = PasiensUgd::findOrFail($id);
        $pasien->delete();

        return redirect()->route('admin.pasien.rawatinap')->with('success', 'Data pasien berhasil dihapus.');
    }

    public function surat($id)
    {
        $pasien = PasiensUgd::findOrFail($id);
        // Logika untuk membuat atau menampilkan surat terkait pasien
        // Misalnya, return view('admin.surat', compact('pasien'));
        return view('admin.surat', compact('pasien'));
    }

    private function generateNoRekamMedis()
    {
        // Generate a unique no_rekam_medis, e.g. prefix + timestamp + random number
        do {
            $noRekamMedis = 'UGD' . date('YmdHis') . rand(100, 999);
        } while (PasiensUgd::where('no_rekam_medis', $noRekamMedis)->exists());

        return $noRekamMedis;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'pasien_id' => 'required|exists:pasiens,id',
            'nama_pasien' => 'required|string',
            'tanggal_masuk' => 'required|date',
            // 'status' dan 'ruangan' bisa nullable jika tidak diinput dari form
        ], [
            'pasien_id.required' => 'Silakan cari dan pilih pasien terlebih dahulu.',
            'pasien_id.exists' => 'Data pasien tidak valid.',
        ]);

        // Ambil data pasien dari tabel pasien
        $pasien = \App\Models\Pasien::find($validatedData['pasien_id']);
        if (!$pasien) {
            return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.'], 404);
        }

        // Siapkan data untuk pasien UGD (hanya kolom yang ada di tabel)
        $dataUgd = [
            'pasien_id' => $pasien->id,
            'nama_pasien' => $pasien->nama_pasien,
            'tanggal_masuk' => $validatedData['tanggal_masuk'],
            'status' => 'Perlu Analisa',
            'ruangan' => '-',
        ];

        $pasienUgd = \App\Models\PasiensUgd::create($dataUgd);

        if ($request->ajax()) {
            $umur = null;
            if ($pasien->tanggal_lahir) {
                $umur = \Carbon\Carbon::parse($pasien->tanggal_lahir)->age . ' tahun';
            }
            $row = [
                'id' => $pasienUgd->id,
                'pasien_id' => $pasienUgd->pasien_id,
                'nama_pasien' => $pasienUgd->nama_pasien,
                'tanggal_masuk' => $pasienUgd->tanggal_masuk,
                'status' => $pasienUgd->status,
                'ruangan' => $pasienUgd->ruangan,
                'umur' => $umur,
                'pasien_json' => json_encode($pasienUgd),
            ];
            return response()->json(['success' => true, 'data' => $row]);
        }

        return redirect()->route('rawatinap.ugd')->with('success', 'Pasien UGD berhasil ditambahkan.');
    }

    public function storeAnalisa(Request $request)
    {
        $validatedData = $request->validate([
            'pasien_id' => 'required|integer|exists:pasiens,id',
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
            'ruangan' => 'nullable|string',
        ]);

        // Convert array fields to JSON strings if present
        if (isset($validatedData['status_psikologi'])) {
            $validatedData['status_psikologi'] = json_encode($validatedData['status_psikologi']);
        }
        if (isset($validatedData['hambatan_edukasi'])) {
            $validatedData['hambatan_edukasi'] = json_encode($validatedData['hambatan_edukasi']);
        }

        try {
            $hasilAnalisa = HasilanalisaRawatinap::create($validatedData);

            // Tambahan: Jika ruangan diisi, update status pasien UGD ke 'Rawat Inap'
            if (!empty($validatedData['ruangan'])) {
                $pasienUgd = PasiensUgd::where('pasien_id', $validatedData['pasien_id'])->first();
                if ($pasienUgd) {
                    $pasienUgd->status = 'Rawat Inap';
                    $pasienUgd->ruangan = $validatedData['ruangan'];
                    $pasienUgd->save();
                }
            }

            return response()->json(['success' => true, 'message' => 'Data analisa berhasil disimpan.', 'data' => $hasilAnalisa]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data analisa.', 'error' => $e->getMessage()], 500);
        }
    }

    // Ambil data analisa terakhir dari hasilanalisa_rawatinap untuk pasien_id tertentu
    public function getRiwayatAnalisaRawatinap($pasien_id)
    {
        $analisa = \App\Models\HasilanalisaRawatinap::where('pasien_id', $pasien_id)->orderByDesc('created_at')->first();
        if (!$analisa) {
            return response()->json(['success' => false, 'message' => 'Data analisa tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $analisa]);
    }

    public function storeHasilPeriksa(Request $request)
    {
        \Log::info('storeHasilPeriksa called', ['request' => $request->all()]);

        $validatedData = $request->validate([
            'pasien_id' => 'required|exists:pasiens,id',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'soap' => 'nullable|string',
            'intruksi_tenaga_kerja' => 'nullable|string',
            'penanggung_jawab' => 'nullable|string',
        ]);

        try {
            \DB::table('hasilperiksa_ugd')->insert([
                'pasien_id' => $validatedData['pasien_id'],
                'tanggal' => $validatedData['tanggal'],
                'waktu' => $validatedData['waktu'],
                'soap' => $validatedData['soap'],
                'intruksi_tenagakerja' => $validatedData['intruksi_tenagakerja'],
                'penanggung_jawab' => $validatedData['penanggung_jawab'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Log::info('Data hasil periksa berhasil disimpan', ['data' => $validatedData]);

            return redirect()->route('rawatinap.rawatinap')->with('success', 'Data hasil periksa berhasil disimpan.');
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan data hasil periksa', ['error' => $e->getMessage()]);
            return redirect()->route('rawatinap.rawatinap')->withErrors(['error' => 'Gagal menyimpan data hasil periksa: ' . $e->getMessage()]);
        }
    }

    public function getHasilPeriksaByPasienId($pasien_id)
    {
        $hasilPeriksa = \DB::table('hasilperiksa_ugd')
            ->where('pasien_id', $pasien_id)
            ->orderByDesc('tanggal')
            ->orderByDesc('waktu')
            ->get();

        if ($hasilPeriksa->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Data hasil periksa tidak ditemukan.'], 404);
        }

        return response()->json(['success' => true, 'data' => $hasilPeriksa]);
    }

    public function getRiwayatBerobatByPasienId($no_rekam_medis)
    {
        $pasien = \App\Models\Pasien::where('no_rekam_medis', $no_rekam_medis)->first();

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Pasien tidak ditemukan',
            ], 404);
        }

        $hasilAnalisa = \DB::table('hasilanalisa_rawatinap')
            ->where('pasien_id', $pasien->id)
            ->orderByDesc('created_at')
            ->get();

        $hasilPeriksa = \DB::table('hasilperiksa_ugd')
            ->where('pasien_id', $pasien->id)
            ->orderByDesc('tanggal')
            ->orderByDesc('waktu')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'hasil_analisa' => $hasilAnalisa,
                'hasil_periksa' => $hasilPeriksa,
            ],
        ]);
    }
}
