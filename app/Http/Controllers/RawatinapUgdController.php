<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PasiensUgd;
use App\Models\HasilanalisaRawatinap;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\PasienExport;
use App\Models\Antrian;
use App\Models\Pasien;
use App\Models\User;
use App\Models\JadwalDokter;

class RawatinapUgdController extends Controller
{
    public function index()
    {
        // Keep existing index method unchanged
        // Join with pasien table to get jaminan_kesehatan
        $pasiens_ugd = PasiensUgd::select('pasiens_ugd.*', 'pasiens.jaminan_kesehatan')
            ->join('pasiens', 'pasiens_ugd.pasien_id', '=', 'pasiens.id')
            ->whereIn('pasiens_ugd.status', ['Rawat Inap'])
            ->paginate(10);

        $users = \App\Models\User::all();

        return view('rawatinap.ugd', compact('pasiens_ugd', 'users'));
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

        return view('rawatinap.jadwal', compact('jadwalDokters', 'users'));
    }

    public function adminPasienRawatinap()
    {
        $pasiens_ugd = PasiensUgd::where('status', 'Rawat Inap')->paginate(10);
        $users = \App\Models\User::all();
        return view('admin.rawatinap', compact('pasiens_ugd', 'users'));
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
        return view('rawatinap.export_pdf_html', ['pasiens' => $pasiens]);
    }

    public function profile()
    {
        return view('rawatinap.profile');
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
                'success' => true,
                'message' => $message,
                'name' => $user->name,
                'profile_photo_path' => $user->profile_photo_path,
            ]);
        }

        return redirect()->route('resepsionis.profile')->with('status', 'Profil berhasil diperbarui.');
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

    public function update(Request $request, $no_rekam_medis)
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

        // Set default value for 'ruangan' if null or empty
        if (empty($validatedData['ruangan'])) {
            $validatedData['ruangan'] = '-';
        }

        // Remove penanggung_jawab from validatedData if present to avoid override
        if (isset($validatedData['penanggung_jawab'])) {
            unset($validatedData['penanggung_jawab']);
        }

        try {
            // Set penanggung_jawab to current authenticated user's id before create
            $hasilAnalisa = HasilanalisaRawatinap::create(array_merge($validatedData, [
                'penanggung_jawab' => auth()->id(),
            ]));

            // Tambahan: Update status pasien UGD berdasarkan isi ruangan
            $pasienUgd = PasiensUgd::where('pasien_id', $validatedData['pasien_id'])->first();
            if ($pasienUgd) {
                if (!empty($validatedData['ruangan']) && $validatedData['ruangan'] !== '-') {
                    $pasienUgd->status = 'Rawat Inap';
                    $pasienUgd->ruangan = $validatedData['ruangan'];
                } else {
                    $pasienUgd->status = 'UGD';
                    $pasienUgd->ruangan = '-';
                }
                $pasienUgd->save();
            }

            return response()->json(['success' => true, 'message' => 'Data analisa berhasil disimpan.', 'data' => $hasilAnalisa]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data analisa.', 'error' => $e->getMessage()], 500);
        }
    }

    // Ambil data analisa terakhir dari hasilanalisa_rawatinap untuk pasien_id tertentu
    public function getRiwayatAnalisaRawatinap($pasien_id)
    {
        try {
            $analisa = \App\Models\HasilanalisaRawatinap::with('penanggungJawabUser')->where('pasien_id', $pasien_id)->orderByDesc('created_at')->first();
            if (!$analisa) {
                return response()->json(['success' => false, 'message' => 'Data analisa tidak ditemukan'], 404);
            }
            $analisa->penanggung_jawab_user = $analisa->penanggungJawabUser;
            return response()->json(['success' => true, 'data' => $analisa]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi error pada server: ' . $e->getMessage()], 500);
        }
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
            'pasien_pulang' => 'nullable|boolean',
            'tanggal_pulang' => 'nullable|date',
            'waktu_pulang' => 'nullable',
        ]);

        try {
            \DB::table('hasilperiksa_ugd')->insert([
                'pasien_id' => $validatedData['pasien_id'],
                'tanggal' => $validatedData['tanggal'],
                'waktu' => $validatedData['waktu'],
                'soap' => $validatedData['soap'],
                'intruksi_tenagakerja' => $validatedData['intruksi_tenaga_kerja'],
                'penanggung_jawab' => $validatedData['penanggung_jawab'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!empty($validatedData['pasien_pulang']) && !empty($validatedData['tanggal_pulang']) && !empty($validatedData['waktu_pulang'])) {
                $tanggalPulangDatetime = $validatedData['tanggal_pulang'] . ' ' . $validatedData['waktu_pulang'] . ':00';
                \DB::table('pasiens_ugd')
                    ->where('pasien_id', $validatedData['pasien_id'])
                    ->update([
                        'tanggal_pulang' => $tanggalPulangDatetime,
                        'status' => 'Selesai',
                    ]);
            }

            \Log::info('Data hasil periksa berhasil disimpan', ['data' => $validatedData]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Data hasil periksa berhasil disimpan.']);
            }

            return redirect()->route('rawatinap.rawatinap')->with('success', 'Data hasil periksa berhasil disimpan.');
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan data hasil periksa', ['error' => $e->getMessage()]);

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan data hasil periksa: ' . $e->getMessage()], 500);
            }

            return redirect()->route('rawatinap.rawatinap')->withErrors(['error' => 'Gagal menyimpan data hasil periksa: ' . $e->getMessage()]);
        }
    }

    public function getHasilPeriksaByPasienId($pasien_id)
    {
        $hasilPeriksa = \DB::table('hasilperiksa_ugd')
            ->leftJoin('users', 'hasilperiksa_ugd.penanggung_jawab', '=', 'users.id')
            ->where('hasilperiksa_ugd.pasien_id', $pasien_id)
            ->orderByDesc('hasilperiksa_ugd.tanggal')
            ->orderByDesc('hasilperiksa_ugd.waktu')
            ->select(
                'hasilperiksa_ugd.*',
                'users.name as penanggung_jawab_nama'
            )
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
