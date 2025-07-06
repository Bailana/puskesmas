<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Obat;
use App\Models\HasilPeriksa;
use App\Exports\ObatExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\JadwalDokter;

class ApotekerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Assuming antrian has a field to relate to user, e.g., user_id or apoteker_id
        // Adjust the filter condition based on your database schema
        $antrians = \App\Models\Antrian::with(['pasien', 'poli'])
            ->where('status', 'Farmasi')
            ->paginate(5);

        // Count obat with stok not zero
        $totalObatTersedia = \App\Models\Obat::where('stok', '!=', 0)->count();

        // Count obat that are expired (tanggal_kadaluarsa before today)
        $today = date('Y-m-d');
        $totalObatKadaluarsa = \App\Models\Obat::whereDate('tanggal_kadaluarsa', '<', $today)->count();

        // Count obat with stok less than 10
        $totalObatStokMenipis = \App\Models\Obat::where('stok', '<', 10)->count();

        // Count obat with stok 0
        $totalObatStokHabis = \App\Models\Obat::where('stok', 0)->count();

        return view('apoteker.dashboard', compact('antrians', 'totalObatTersedia', 'totalObatKadaluarsa', 'totalObatStokMenipis', 'totalObatStokHabis'));
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

        return view('apoteker.jadwal', compact('jadwalDokters', 'users'));
    }

    public function obat(Request $request)
    {
        $search = $request->input('search');

        $query = \App\Models\Obat::query();

        if ($search) {
            $query->where('nama_obat', 'like', '%' . $search . '%')
                  ->orWhere('jenis_obat', 'like', '%' . $search . '%')
                  ->orWhere('bentuk_obat', 'like', '%' . $search . '%')
                  ->orWhere('nama_pabrikan', 'like', '%' . $search . '%');
        }

        $obats = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->json($obats);
        }

        return view('apoteker.obat', compact('obats'));
    }

    public function exportPdf(Request $request)
    {
        $filters = $request->all();

        $query = \App\Models\Obat::query();

        if (!empty($filters['nama_obat'])) {
            $query->where('nama_obat', 'like', '%' . $filters['nama_obat'] . '%');
        }
        if (!empty($filters['jenis_obat'])) {
            $query->where('jenis_obat', 'like', '%' . $filters['jenis_obat'] . '%');
        }
        if (!empty($filters['dosis'])) {
            $query->where('dosis', 'like', '%' . $filters['dosis'] . '%');
        }
        if (!empty($filters['bentuk_obat'])) {
            $query->where('bentuk_obat', 'like', '%' . $filters['bentuk_obat'] . '%');
        }
        if (!empty($filters['stok'])) {
            $query->where('stok', $filters['stok']);
        }
        if (!empty($filters['harga_satuan'])) {
            $query->where('harga_satuan', $filters['harga_satuan']);
        }
        if (!empty($filters['tanggal_kadaluarsa'])) {
            $query->where('tanggal_kadaluarsa', $filters['tanggal_kadaluarsa']);
        }
        if (!empty($filters['nama_pabrikan'])) {
            $query->where('nama_pabrikan', 'like', '%' . $filters['nama_pabrikan'] . '%');
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_obat', 'like', '%' . $search . '%')
                  ->orWhere('jenis_obat', 'like', '%' . $search . '%')
                  ->orWhere('bentuk_obat', 'like', '%' . $search . '%')
                  ->orWhere('stok', 'like', '%' . $search . '%')
                  ->orWhere('harga_satuan', 'like', '%' . $search . '%')
                  ->orWhere('tanggal_kadaluarsa', 'like', '%' . $search . '%');
            });
        }

        $obats = $query->get();

        $export = new ObatExport($obats);
        return $export->export();
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->all();
        $fileName = 'data_obat_' . date('Ymd_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ObatExport($filters), $fileName);
    }

    public function updateAntrianStatus(Request $request, $pasienId)
    {
        $antrian = \App\Models\Antrian::where('pasien_id', $pasienId)
            ->where('status', 'Farmasi')
            ->first();

        if (!$antrian) {
            return response()->json(['message' => 'Antrian tidak ditemukan atau sudah selesai'], 404);
        }

        // Fetch prescription items for the pasien
        $resepObatItems = \DB::table('hasilperiksa_obat')
            ->join('hasilperiksa', 'hasilperiksa_obat.hasilperiksa_id', '=', 'hasilperiksa.id')
            ->where('hasilperiksa.pasien_id', $pasienId)
            ->select('hasilperiksa_obat.obat_id', 'hasilperiksa_obat.jumlah')
            ->get();

        // Update stock for each obat
        foreach ($resepObatItems as $item) {
            $obat = \App\Models\Obat::find($item->obat_id);
            if ($obat) {
                $newStock = $obat->stok - $item->jumlah;
                if ($newStock < 0) {
                    return response()->json(['message' => "Stok obat {$obat->nama_obat} tidak cukup"], 400);
                }
                $obat->stok = $newStock;
                $obat->save();
            }
        }

        $antrian->status = 'Selesai';

        try {
            $antrian->save();
            return response()->json(['message' => 'Status antrian berhasil diperbarui dan stok obat dikurangi']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui status antrian'], 500);
        }
    }

    public function profile()
    {
        return view('apoteker.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::id());

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
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
            if (!\Hash::check($request->current_password, $user->password)) {
                if ($request->expectsJson()) {
                    return response()->json(['errors' => ['current_password' => ['Password lama tidak sesuai']]], 422);
                }
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai'])->withInput();
            }
            $user->password = Hash::make($request->new_password);
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

        return redirect()->route('profileapoteker')->with('status', 'Profil berhasil diperbarui.');
    }

    public function pasien(Request $request)
    {
        $search = $request->input('search');

        $jenis_kelamin = $request->input('jenis_kelamin');
        $gol_darah = $request->input('gol_darah');
        $jaminan_kesehatan = $request->input('jaminan_kesehatan');
        $tempat_lahir = $request->input('tempat_lahir');
        $kecamatan = $request->input('kecamatan');
        $kelurahan = $request->input('kelurahan');
        $status_pernikahan = $request->input('status_pernikahan');
        $tanggal_lahir = $request->input('tanggal_lahir');

        $query = \App\Models\Pasien::query();

        if (!empty($jenis_kelamin)) {
            $query->where('jenis_kelamin', $jenis_kelamin);
        }
        if (!empty($gol_darah)) {
            $query->where('gol_darah', $gol_darah);
        }
        if (!empty($jaminan_kesehatan)) {
            $query->where('jaminan_kesehatan', $jaminan_kesehatan);
        }
        if (!empty($tempat_lahir)) {
            $query->where('tempat_lahir', 'like', '%' . $tempat_lahir . '%');
        }
        if (!empty($kecamatan)) {
            $query->where('kecamatan', 'like', '%' . $kecamatan . '%');
        }
        if (!empty($kelurahan)) {
            $query->where('kelurahan', 'like', '%' . $kelurahan . '%');
        }
        if (!empty($status_pernikahan)) {
            $query->where('status_pernikahan', $status_pernikahan);
        }
        if (!empty($tanggal_lahir)) {
            $query->where('tanggal_lahir', $tanggal_lahir);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pasien', 'like', '%' . $search . '%')
                  ->orWhere('no_rekam_medis', 'like', '%' . $search . '%')
                  ->orWhere('nomor_kepesertaan', 'like', '%' . $search . '%');
            });
        }

        $pasiens = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->json($pasiens);
        }

        return view('apoteker.pasien', compact('pasiens'));
    }

    public function storeObat(Request $request)
    {
        $validated = $request->validate([
            'nama_obat' => 'required|string|max:255',
            'jenis_obat' => 'required|string|max:255',
            'dosis' => 'required|string|max:255',
            'bentuk_obat' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal_kadaluarsa' => 'required|date',
            'nama_pabrikan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        Obat::create($validated);

        return redirect()->route('apoteker.obat')->with('success', 'Data obat berhasil ditambahkan.');
    }

    public function getObatDetail($id)
    {
        $obat = Obat::find($id);

        if (!$obat) {
            return response()->json(['error' => 'Obat tidak ditemukan'], 404);
        }

        return response()->json($obat);
    }

    public function updateObat(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_obat' => 'required|string|max:255',
            'jenis_obat' => 'required|string|max:255',
            'dosis' => 'required|string|max:255',
            'bentuk_obat' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal_kadaluarsa' => 'required|date',
            'nama_pabrikan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $obat = Obat::find($id);

        if (!$obat) {
            return response()->json(['error' => 'Obat tidak ditemukan'], 404);
        }

        $obat->nama_obat = $validated['nama_obat'];
        $obat->jenis_obat = $validated['jenis_obat'];
        $obat->dosis = $validated['dosis'];
        $obat->bentuk_obat = $validated['bentuk_obat'];
        $obat->stok = $validated['stok'];
        $obat->harga_satuan = $validated['harga_satuan'];
        $obat->tanggal_kadaluarsa = $validated['tanggal_kadaluarsa'];
        $obat->nama_pabrikan = $validated['nama_pabrikan'];
        $obat->keterangan = $validated['keterangan'];

        try {
            $obat->save();
            return response()->json(['message' => 'Data obat berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui data obat'], 500);
        }
    }

    public function antrian(Request $request)
    {
        $search = $request->input('search');

        $query = \App\Models\Antrian::with(['pasien', 'poli'])
            ->where('status', 'Farmasi');

        if ($search) {
            $query->whereHas('pasien', function ($q) use ($search) {
                $q->where('nama_pasien', 'like', '%' . $search . '%')
                  ->orWhere('no_rekam_medis', 'like', '%' . $search . '%');
            });
        }

        $antrians = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->json($antrians);
        }

        return view('apoteker.antrian', compact('antrians'));
    }

    public function destroyObat($id)
    {
        $obat = Obat::find($id);

        if (!$obat) {
            return response()->json(['error' => 'Obat tidak ditemukan'], 404);
        }

        try {
            $obat->delete();
            return response()->json(['message' => 'Data obat berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus data obat'], 500);
        }
    }

    public function getHasilPeriksa($pasienId)
    {
        $hasilPeriksa = \App\Models\HasilPeriksa::with('penanggungJawabUser')
            ->where('pasien_id', $pasienId)
            ->orderBy('tanggal_periksa', 'desc')
            ->first();

        if (!$hasilPeriksa) {
            return response()->json(['error' => 'Hasil periksa tidak ditemukan'], 404);
        }

        $data = $hasilPeriksa->toArray();
        $data['penanggung_jawab_name'] = $hasilPeriksa->penanggungJawabUser ? $hasilPeriksa->penanggungJawabUser->name : null;

        return response()->json($data);
    }

    public function getTagihanApoteker($pasienId)
    {
        $pasien = \App\Models\Pasien::find($pasienId);
        if (!$pasien) {
            return response()->json(['message' => 'Pasien tidak ditemukan'], 404);
        }

        $tagihan = \App\Models\Tagihan::where('pasien_id', $pasienId)->latest()->first();

        $resepObat = \DB::table('hasilperiksa_obat')
            ->join('hasilperiksa', 'hasilperiksa_obat.hasilperiksa_id', '=', 'hasilperiksa.id')
            ->join('obat', 'hasilperiksa_obat.obat_id', '=', 'obat.id')
            ->where('hasilperiksa.pasien_id', $pasienId)
            ->select('obat.nama_obat', 'obat.bentuk_obat', 'hasilperiksa_obat.jumlah', 'obat.harga_satuan')
            ->get()
            ->map(function ($item) {
                return [
                    'nama_obat' => $item->nama_obat,
                    'bentuk_obat' => $item->bentuk_obat,
                    'jumlah' => $item->jumlah,
                    'harga_satuan' => $item->harga_satuan,
                ];
            })
            ->toArray();

        $poliTujuan = null;
        if ($tagihan && $tagihan->poli_tujuan) {
            $poliTujuan = $tagihan->poli_tujuan;
        } else {
            $antrian = \App\Models\Antrian::where('pasien_id', $pasienId)
                ->orderBy('tanggal_berobat', 'desc')
                ->first();
            if ($antrian && $antrian->poli) {
                $poliTujuan = $antrian->poli->nama_poli;
            }
        }

        return response()->json([
            'pasien_id' => $pasien->id,
            'nama_pasien' => $pasien->nama_pasien,
            'no_rekam_medis' => $pasien->no_rekam_medis,
            'poli_tujuan' => $poliTujuan,
            'resep_obat' => $resepObat,
            'resep_obat_count' => count($resepObat),
            'total_biaya' => $tagihan ? $tagihan->total_biaya : 0,
            'status_pembayaran' => $tagihan ? $tagihan->status : 'Belum Lunas',
            'created_at' => $tagihan ? $tagihan->created_at : null,
            'jaminan_kesehatan' => $pasien->jaminan_kesehatan,
        ]);
    }
}
