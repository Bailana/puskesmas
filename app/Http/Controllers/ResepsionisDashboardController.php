<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Antrian;
use App\Models\Pasien;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\PasswordChangeConfirmation;

use Dompdf\Dompdf;
use Dompdf\Options;

use App\Exports\PasienExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\JadwalDokter;

class ResepsionisDashboardController extends Controller
{
    public function index()
    {
        $totalAntrianCount = \App\Models\Antrian::where('status', '!=', 'selesai')->count();
        $antrianSelesaiCount = \App\Models\Antrian::where('status', 'selesai')->count();
        $antrians = \App\Models\Antrian::with(['pasien', 'poli'])
            ->where('status', '!=', 'selesai')
            ->paginate(5);

        return view('resepsionis.dashboard', compact('totalAntrianCount', 'antrianSelesaiCount', 'antrians'));
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

        return view('resepsionis.jadwal', compact('jadwalDokters', 'users'));
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

        $export = new PasienExport($pasiens);
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

        // Return HTML view for client-side PDF generation
        return view('resepsionis.export_pdf_html', ['pasiens' => $pasiens]);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PasienImport, $request->file('excel_file'));
            return redirect()->back()->with('success', 'Data pasien berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }

    public function profile()
    {
        $user = Auth::user();
        return view('resepsionis.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'current_password' => ['required_with:new_password', 'nullable', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo_path = $path;
        }

        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
                if ($request->expectsJson()) {
                    return response()->json(['errors' => ['current_password' => ['Password lama tidak sesuai']]], 422);
                }
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai'])->withInput();
            }
            $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        }

        $user->save();

        if ($request->expectsJson()) {
            $message = 'Profil berhasil diperbarui.';
            $passwordChanged = false;
            if ($request->filled('current_password') && $request->filled('new_password')) {
                $passwordChanged = true;
            }
            if ($passwordChanged) {
                $message = 'Password berhasil diubah.';
            }
            return response()->json([
                'message' => $message,
                'name' => $user->name,
                'profile_photo_path' => $user->profile_photo_path,
            ]);
        }

        return redirect()->route('resepsionis.profile')->with('status', 'Profil berhasil diperbarui.');
    }

    public function antrian(Request $request)
    {
        $query = Antrian::with(['pasien', 'poli'])
            ->where('status', '!=', 'selesai');

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('pasien', function ($q2) use ($search) {
                    $q2->whereRaw('LOWER(nama_pasien) LIKE ?', ['%' . $search . '%'])
                       ->orWhereRaw('LOWER(no_rekam_medis) LIKE ?', ['%' . $search . '%'])
                       ->orWhereRaw('LOWER(nik) LIKE ?', ['%' . $search . '%']);
                })
                ->orWhereRaw('LOWER(no_rekam_medis) LIKE ?', ['%' . $search . '%'])
                ->orWhereRaw('LOWER(status) LIKE ?', ['%' . $search . '%']);
            });
        }

        $antrians = $query->paginate(5);
        $polis = \App\Models\Poli::all();
        if ($request->ajax()) {
            return response()->json($antrians);
        }
        return view('resepsionis.antrian', compact('antrians', 'polis'));
    }

    public function pasien(Request $request)
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

        $pasiens = $query->paginate(5)->withQueryString();

        // Get the last no_rekam_medis and increment by 1 with format RM.XXXXX
        $lastNoRekamMedis = Pasien::max('no_rekam_medis');
        if ($lastNoRekamMedis === null) {
            $newNoRekamMedis = 'RM-00001';
        } else {
            // Extract numeric part after 'RM-'
            $numberPart = intval(substr($lastNoRekamMedis, 3));
            $newNumber = $numberPart + 1;
            // Format with leading zeros to 5 digits
            $newNoRekamMedis = 'RM-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        }

        if ($request->ajax()) {
            return response()->json($pasiens);
        }

        return view('resepsionis.pasien', compact('pasiens', 'newNoRekamMedis'));
    }

    public function tambahPasien(Request $request)
    {
        $validatedData = $request->validate([
            'no_rekam_medis' => 'required|unique:pasiens,no_rekam_medis',
            'nik' => 'required|unique:pasiens,nik',
            'nama_pasien' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string|max:50',
            'gol_darah' => 'required|string|max:10',
            'agama' => 'required|string|max:50',
            'pekerjaan' => 'required|string|max:100',
            'status_pernikahan' => 'required|string|max:50',
            'alamat_jalan' => 'required|string|max:255',
            'rt' => 'required|string|max:10',
            'rw' => 'required|string|max:10',
            'kelurahan' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kabupaten' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'jaminan_kesehatan' => 'required|string|max:100',
            'nomor_kepesertaan' => 'required|string|max:100',
            'kepala_keluarga' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
        ]);

        $pasien = new Pasien();
        $pasien->no_rekam_medis = $validatedData['no_rekam_medis'];
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

        return response()->json(['message' => 'Pasien berhasil ditambahkan']);
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
