<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Pasien;
use App\Models\Antrian;
use PDF;


class PasienController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $pasiens = Pasien::query();

        // Apply filters
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

        $pasiens = $pasiens->paginate($perPage);

        $antrians = Antrian::paginate($perPage);

        // \Log::info('index result count', ['count' => $pasiens->count()]);

        return view('admin.rawatjalan', compact('pasiens', 'antrians'));
    }

    public function rawatinapPasien(Request $request)
    {
        // \Log::info('rawatinapPasien called', ['search' => $request->input('search'), 'per_page' => $request->input('per_page'), 'ajax' => $request->ajax(), 'get_new_no_rm' => $request->has('get_new_no_rm')]);

        $query = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $pasiens = Pasien::query();

        // Apply filters
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

        $pasiens = $pasiens->paginate($perPage);

        // \Log::info('rawatinapPasien result count', ['count' => $pasiens->count()]);

        // Generate newNoRekamMedis logic (selalu ambil increment dari database, format RM-xxxxxx)
        $lastPasien = Pasien::orderByRaw("CAST(SUBSTRING(no_rekam_medis, 4) AS UNSIGNED) DESC")->first();
        if ($lastPasien && preg_match('/RM-(\\d{6})/', $lastPasien->no_rekam_medis, $matches)) {
            $lastNumber = (int)$matches[1];
            $newNoRekamMedis = 'RM-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            // Cari nomor rekam medis terbesar yang sudah ada
            $maxNumber = Pasien::selectRaw('MAX(CAST(SUBSTRING(no_rekam_medis, 4) AS UNSIGNED)) as max_no')->value('max_no');
            if ($maxNumber) {
                $newNoRekamMedis = 'RM-' . str_pad($maxNumber + 1, 6, '0', STR_PAD_LEFT);
            } else {
                $newNoRekamMedis = 'RM-000001';
            }
        }

        if ($request->ajax() && $request->has('get_new_no_rm')) {
            $maxNumber = Pasien::selectRaw('MAX(CAST(SUBSTRING(no_rekam_medis, 4) AS UNSIGNED)) as max_no')->value('max_no');
            if ($maxNumber) {
                $newNoRekamMedis = 'RM-' . str_pad($maxNumber + 1, 6, '0', STR_PAD_LEFT);
            } else {
                $newNoRekamMedis = 'RM-000001';
            }
            // \Log::info('rawatinapPasien AJAX get_new_no_rm response', ['newNoRekamMedis' => $newNoRekamMedis]);
            return response()->json(['newNoRekamMedis' => $newNoRekamMedis]);
        }

        return view('rawatinap.pasien', compact('pasiens', 'newNoRekamMedis'));
    }

    /**
     * Get patient detail by no_rekam_medis
     */
    public function getPatientDetail($no_rekam_medis)
    {
        // \Log::info('getPatientDetail called', ['no_rekam_medis' => $no_rekam_medis]);
        $pasien = Pasien::where('no_rekam_medis', $no_rekam_medis)->first();
        if (!$pasien) {
            // \Log::warning('Pasien tidak ditemukan', ['no_rekam_medis' => $no_rekam_medis]);
            return response()->json(['error' => 'Pasien tidak ditemukan'], 404);
        }
        // \Log::info('Pasien ditemukan', ['pasien' => $pasien]);
        return response()->json($pasien);
    }

    /**
     * Get patient detail by pasien_id
     */
    public function getPatientDetailById($pasien_id)
    {
        // \Log::info('getPatientDetailById called', ['pasien_id' => $pasien_id]);
        $pasien = Pasien::find($pasien_id);
        if (!$pasien) {
            // \Log::warning('Pasien tidak ditemukan', ['pasien_id' => $pasien_id]);
            return response()->json(['error' => 'Pasien tidak ditemukan'], 404);
        }
        // \Log::info('Pasien ditemukan', ['pasien' => $pasien]);
        return response()->json($pasien);
    }

    public function edit($id)
    {
        $pasien = Pasien::findOrFail($id);
        return view('admin.rawatjalan_edit', compact('pasien'));
    }

    public function update(Request $request, $id)
    {
        \Log::info('Update method called', ['id' => $id, 'user_id' => auth()->id()]);

        $validatedData = $request->validate([
            'no_rekam_medis' => 'nullable|string|unique:pasiens,no_rekam_medis,' . $id,
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

        $pasien = Pasien::findOrFail($id);
        $pasien->update($validatedData);

        // Log update activity
        $userId = auth()->id();
        \Log::info('Logging update activity', ['user_id' => $userId, 'pasien_id' => $pasien->id]);
        try {
            \App\Models\ActivityLog::create([
                'user_id' => $userId,
                'action' => 'ubah',
                'description' => 'Updated pasien data: ' . $pasien->nama_pasien . ' (ID: ' . $pasien->id . ')',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log update activity: ' . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Data pasien berhasil diperbarui']);
        }

        return redirect()->route('admin.rawatjalan')->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pasien = Pasien::findOrFail($id);
        $pasien->delete();

        // Log delete activity
        $userId = auth()->id();
        \Log::info('Logging delete activity', ['user_id' => $userId, 'pasien_id' => $pasien->id]);
        try {
            \App\Models\ActivityLog::create([
                'user_id' => $userId,
                'action' => 'hapus',
                'description' => 'Deleted pasien data: ' . $pasien->nama_pasien . ' (ID: ' . $pasien->id . ')',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log delete activity: ' . $e->getMessage());
        }

        return redirect()->route('admin.rawatjalan')->with('success', 'Data pasien berhasil dihapus.');
    }

    public function surat($id)
    {
        $pasien = Pasien::findOrFail($id);
        // Logika untuk membuat atau menampilkan surat terkait pasien
        // Misalnya, return view('admin.surat', compact('pasien'));
        return view('admin.surat', compact('pasien'));
    }

    public function generateSuratPdf(Request $request, $id)
    {
        $request->validate([
            'keperluan' => 'required|string|max:255',
        ]);

        $pasien = Pasien::findOrFail($id);
        $keperluan = $request->input('keperluan');

        // Generate PDF using Dompdf or similar package
        $pdf = \PDF::loadView('admin.surat_pdf', compact('pasien', 'keperluan'));

        $filename = 'surat_' . $pasien->id . '_' . date('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }

    public function store(Request $request)
    {
        // \Log::info('store method called', ['request' => $request->all()]);

        $validatedData = $request->validate([
            'no_rekam_medis' => 'required|string|unique:pasiens,no_rekam_medis',
            'nik' => 'required|string|size:16',
            'nama_pasien' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string',
            'gol_darah' => 'required|string',
            'agama' => 'required|string',
            'pekerjaan' => 'required|string',
            'status_pernikahan' => 'required|string',
            'alamat_jalan' => 'required|string',
            'rt' => 'required|string',
            'rw' => 'required|string',
            'kelurahan' => 'required|string',
            'kecamatan' => 'required|string',
            'kabupaten' => 'required|string',
            'provinsi' => 'required|string',
            'jaminan_kesehatan' => 'required|string',
            'nomor_kepesertaan' => 'nullable|string|max:16',
            'kepala_keluarga' => 'required|string',
            'no_hp' => 'required|string|max:14',
        ]);

        $pasien = Pasien::create($validatedData);

        // Log create activity
        $userId = auth()->id();
        \Log::info('Logging create activity', ['user_id' => $userId, 'pasien_id' => $pasien->id]);
        try {
            \App\Models\ActivityLog::create([
                'user_id' => $userId,
                'action' => 'tambah',
                'description' => 'Created pasien data: ' . $pasien->nama_pasien . ' (ID: ' . $pasien->id . ')',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log create activity: ' . $e->getMessage());
        }

        // \Log::info('pasien created', ['pasien' => $pasien]);

        // Ambil nomor rekam medis terakhir setelah insert
        $lastPasien = Pasien::orderBy('no_rekam_medis', 'desc')->first();
        if ($lastPasien && preg_match('/RM-(\d{6})/', $lastPasien->no_rekam_medis, $matches)) {
            $lastNumber = (int)$matches[1];
            $newNoRekamMedis = 'RM-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newNoRekamMedis = 'RM-000001';
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pasien berhasil disimpan.',
                'newNoRekamMedis' => $newNoRekamMedis
            ], 201);
        }
        return redirect()->route('rawatinap.pasien')->with('success', 'Data pasien berhasil disimpan.');
    }

    /**
     * AJAX: Cari pasien berdasarkan nomor kepesertaan
     */
    public function cariNomorKepesertaan(Request $request)
    {
        $nomor = $request->input('nomor_kepesertaan');
        $pasien = Pasien::where('nomor_kepesertaan', $nomor)->first();
        if (!$pasien) {
            return response()->json(['error' => 'Pasien tidak ditemukan'], 404);
        }
        return response()->json([
            'id' => $pasien->id,
            'nama_pasien' => $pasien->nama_pasien,
            'no_rekam_medis' => $pasien->no_rekam_medis,
            'nik' => $pasien->nik,
            'tanggal_lahir' => $pasien->tanggal_lahir,
            'jenis_kelamin' => $pasien->jenis_kelamin,
            'alamat_jalan' => $pasien->alamat_jalan,
        ]);
    }
}
