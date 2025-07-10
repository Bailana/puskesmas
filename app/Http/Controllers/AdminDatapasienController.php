<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasien;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminDatapasienController extends Controller
{
    public function index(Request $request)
    {
        $query = Pasien::query();

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nama_pasien) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(no_rekam_medis) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(nik) LIKE ?', ['%' . $search . '%']);
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

        $pasiens = $query->paginate(10)->withQueryString();

        return view('admin.datapasien', compact('pasiens'));
    }

    public function updatePasien(Request $request, $no_rekam_medis)
    {
        $validatedData = $request->validate([
            'nik' => 'required|unique:pasiens,nik,' . $no_rekam_medis . ',no_rekam_medis',
            'nama_pasien' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string|max:50',
            'gol_darah' => 'nullable|string|max:10',
            'agama' => 'nullable|string|max:50',
            'pekerjaan' => 'nullable|string|max:100',
            'status_pernikahan' => 'required|string|max:50',
            'alamat_jalan' => 'required|string|max:255',
            'rt' => 'required|string|max:10',
            'rw' => 'required|string|max:10',
            'kelurahan' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kabupaten' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'jaminan_kesehatan' => 'required|string|max:100',
            'nomor_kepesertaan' => 'nullable|string|max:100',
            'kepala_keluarga' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $pasien = Pasien::where('no_rekam_medis', $no_rekam_medis)->firstOrFail();

        $pasien->nik = $validatedData['nik'];
        $pasien->nama_pasien = $validatedData['nama_pasien'];
        $pasien->tempat_lahir = $validatedData['tempat_lahir'];
        $pasien->tanggal_lahir = $validatedData['tanggal_lahir'];
        $pasien->jenis_kelamin = $validatedData['jenis_kelamin'];
        $pasien->gol_darah = $validatedData['gol_darah'] ?? null;
        $pasien->agama = $validatedData['agama'] ?? null;
        $pasien->pekerjaan = $validatedData['pekerjaan'] ?? null;
        $pasien->status_pernikahan = $validatedData['status_pernikahan'];
        $pasien->alamat_jalan = $validatedData['alamat_jalan'];
        $pasien->rt = $validatedData['rt'];
        $pasien->rw = $validatedData['rw'];
        $pasien->kelurahan = $validatedData['kelurahan'];
        $pasien->kecamatan = $validatedData['kecamatan'];
        $pasien->kabupaten = $validatedData['kabupaten'];
        $pasien->provinsi = $validatedData['provinsi'];
        $pasien->jaminan_kesehatan = $validatedData['jaminan_kesehatan'];
        $pasien->nomor_kepesertaan = $validatedData['nomor_kepesertaan'] ?? null;
        $pasien->kepala_keluarga = $validatedData['kepala_keluarga'] ?? null;
        $pasien->no_hp = $validatedData['no_hp'] ?? null;

        $pasien->save();

        return response()->json(['message' => 'Pasien berhasil diperbarui']);
    }

    public function exportExcel(Request $request)
    {
        $query = Pasien::query();

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nama_pasien) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(no_rekam_medis) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(nik) LIKE ?', ['%' . $search . '%']);
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

        $pasiens = $query->get()->map(function ($pasien) {
            return $pasien->toArray();
        })->toArray();

        $export = new \App\Exports\PasienExport($pasiens);
        return $export->export();
    }

    public function exportPdf(Request $request)
    {
        $query = Pasien::query();

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nama_pasien) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(no_rekam_medis) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(nik) LIKE ?', ['%' . $search . '%']);
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

        $pasiens = $query->get();

        $pdf = Pdf::loadView('admin.export_pdf_html', ['pasiens' => $pasiens]);
        return $pdf->download('data_pasien_' . date('Ymd_His') . '.pdf');
    }
}
