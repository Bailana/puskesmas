<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Antrian;
use App\Models\Pasien;
use App\Models\HasilPeriksa;
use App\Models\Tagihan;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\TagihanExport;

class KasirDashboardController extends Controller
{
    public function index()
    {
        $antrians = Antrian::with(['pasien', 'poli'])
            ->where('status', 'Pembayaran')
            ->paginate(5);

        return view('kasir.dashboard', compact('antrians'));
    }

    public function pasien(Request $request)
    {
        $query = $request->input('search');
        $jenisKelamin = $request->input('jenis_kelamin');
        $golDarah = $request->input('gol_darah');
        $jaminanKesehatan = $request->input('jaminan_kesehatan');
        $tempatLahir = $request->input('tempat_lahir');
        $kecamatan = $request->input('kecamatan');
        $kelurahan = $request->input('kelurahan');
        $statusPernikahan = $request->input('status_pernikahan');
        $tanggalLahir = $request->input('tanggal_lahir');

        $pasiens = Pasien::query();

        if ($query) {
            $pasiens->where(function ($q) use ($query) {
                $q->where('nama_pasien', 'like', '%' . $query . '%')
                  ->orWhere('no_rekam_medis', 'like', '%' . $query . '%');
            });
        }

        if ($jenisKelamin) {
            $pasiens->where('jenis_kelamin', $jenisKelamin);
        }

        if ($golDarah) {
            $pasiens->where('gol_darah', $golDarah);
        }

        if ($jaminanKesehatan) {
            $pasiens->where('jaminan_kesehatan', $jaminanKesehatan);
        }

        if ($tempatLahir) {
            $pasiens->where('tempat_lahir', 'like', '%' . $tempatLahir . '%');
        }

        if ($kecamatan) {
            $pasiens->where('kecamatan', 'like', '%' . $kecamatan . '%');
        }

        if ($kelurahan) {
            $pasiens->where('kelurahan', 'like', '%' . $kelurahan . '%');
        }

        if ($statusPernikahan) {
            $pasiens->where('status_pernikahan', $statusPernikahan);
        }

        if ($tanggalLahir) {
            $pasiens->whereDate('tanggal_lahir', $tanggalLahir);
        }

        $pasiens = $pasiens->paginate(5);

        if ($request->ajax()) {
            return response()->json($pasiens->items());
        }

        return view('kasir.pasien', compact('pasiens'));
    }

    public function dana(Request $request)
    {
        $query = $request->input('search');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $jaminanKesehatan = $request->input('jaminan_kesehatan');
        $statusPembayaran = $request->input('status_pembayaran');
        $totalBiayaMin = $request->input('total_biaya_min');
        $totalBiayaMax = $request->input('total_biaya_max');

        $tagihans = Tagihan::with('pasien')
            ->when($query, function ($q) use ($query) {
                $q->whereHas('pasien', function ($q2) use ($query) {
                    $q2->where('nama_pasien', 'like', '%' . $query . '%')
                       ->orWhere('no_rekam_medis', 'like', '%' . $query . '%');
                });
            })
            ->when($tanggalAwal, function ($q) use ($tanggalAwal) {
                $q->whereDate('created_at', '>=', $tanggalAwal);
            })
            ->when($tanggalAkhir, function ($q) use ($tanggalAkhir) {
                $q->whereDate('created_at', '<=', $tanggalAkhir);
            })
            ->when($jaminanKesehatan, function ($q) use ($jaminanKesehatan) {
                $q->whereHas('pasien', function ($q2) use ($jaminanKesehatan) {
                    $q2->where('jaminan_kesehatan', $jaminanKesehatan);
                });
            })
            ->when($statusPembayaran, function ($q) use ($statusPembayaran) {
                $q->where('status', $statusPembayaran);
            })
            ->when($totalBiayaMin, function ($q) use ($totalBiayaMin) {
                $q->where('total_biaya', '>=', $totalBiayaMin);
            })
            ->when($totalBiayaMax, function ($q) use ($totalBiayaMax) {
                $q->where('total_biaya', '<=', $totalBiayaMax);
            });

        if ($request->ajax()) {
            $tagihansPaginated = $tagihans->paginate(5);
            return response()->json($tagihansPaginated->items());
        }

        $tagihansPaginated = $tagihans->paginate(5);

        return view('kasir.dana', ['tagihans' => $tagihansPaginated]);
    }

    public function exportPdf(Request $request)
    {
        $query = $request->input('search');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $jaminanKesehatan = $request->input('jaminan_kesehatan');
        $statusPembayaran = $request->input('status_pembayaran');
        $totalBiayaMin = $request->input('total_biaya_min');
        $totalBiayaMax = $request->input('total_biaya_max');

        $tagihans = Tagihan::with('pasien')
            ->when($query, function ($q) use ($query) {
                $q->whereHas('pasien', function ($q2) use ($query) {
                    $q2->where('nama_pasien', 'like', '%' . $query . '%')
                       ->orWhere('no_rekam_medis', 'like', '%' . $query . '%');
                });
            })
            ->when($tanggalAwal, function ($q) use ($tanggalAwal) {
                $q->whereDate('created_at', '>=', $tanggalAwal);
            })
            ->when($tanggalAkhir, function ($q) use ($tanggalAkhir) {
                $q->whereDate('created_at', '<=', $tanggalAkhir);
            })
            ->when($jaminanKesehatan, function ($q) use ($jaminanKesehatan) {
                $q->whereHas('pasien', function ($q2) use ($jaminanKesehatan) {
                    $q2->where('jaminan_kesehatan', $jaminanKesehatan);
                });
            })
            ->when($statusPembayaran, function ($q) use ($statusPembayaran) {
                $q->where('status', $statusPembayaran);
            })
            ->when($totalBiayaMin, function ($q) use ($totalBiayaMin) {
                $q->where('total_biaya', '>=', $totalBiayaMin);
            })
            ->when($totalBiayaMax, function ($q) use ($totalBiayaMax) {
                $q->where('total_biaya', '<=', $totalBiayaMax);
            })
            ->get();

        $pdf = \PDF::loadView('kasir.export_pdf', ['tagihans' => $tagihans]);

        return $pdf->download('tagihan_pasien.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = $request->input('search');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $jaminanKesehatan = $request->input('jaminan_kesehatan');
        $statusPembayaran = $request->input('status_pembayaran');
        $totalBiayaMin = $request->input('total_biaya_min');
        $totalBiayaMax = $request->input('total_biaya_max');

        $tagihans = Tagihan::with('pasien')
            ->when($query, function ($q) use ($query) {
                $q->whereHas('pasien', function ($q2) use ($query) {
                    $q2->where('nama_pasien', 'like', '%' . $query . '%')
                       ->orWhere('no_rekam_medis', 'like', '%' . $query . '%');
                });
            })
            ->when($tanggalAwal, function ($q) use ($tanggalAwal) {
                $q->whereDate('created_at', '>=', $tanggalAwal);
            })
            ->when($tanggalAkhir, function ($q) use ($tanggalAkhir) {
                $q->whereDate('created_at', '<=', $tanggalAkhir);
            })
            ->when($jaminanKesehatan, function ($q) use ($jaminanKesehatan) {
                $q->whereHas('pasien', function ($q2) use ($jaminanKesehatan) {
                    $q2->where('jaminan_kesehatan', $jaminanKesehatan);
                });
            })
            ->when($statusPembayaran, function ($q) use ($statusPembayaran) {
                $q->where('status', $statusPembayaran);
            })
            ->when($totalBiayaMin, function ($q) use ($totalBiayaMin) {
                $q->where('total_biaya', '>=', $totalBiayaMin);
            })
            ->when($totalBiayaMax, function ($q) use ($totalBiayaMax) {
                $q->where('total_biaya', '<=', $totalBiayaMax);
            })
            ->get();

        $export = new TagihanExport($tagihans);
        return $export->export();
    }

    public function antrian(Request $request)
    {
        $query = Antrian::with(['pasien', 'poli'])
            ->where('status', 'Pembayaran');

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

        if ($request->ajax()) {
            return response()->json($antrians);
        }

        return view('kasir.antrian', compact('antrians'));
    }

    public function getHasilPeriksa($pasienId)
    {
        $hasilPeriksa = HasilPeriksa::with('penanggungJawabUser')
            ->where('pasien_id', $pasienId)
            ->orderBy('tanggal_periksa', 'desc')
            ->first();

        if ($hasilPeriksa) {
            $data = $hasilPeriksa->toArray();
            $data['penanggung_jawab'] = $hasilPeriksa->penanggungJawabUser ? $hasilPeriksa->penanggungJawabUser->name : null;
            return response()->json($data);
        } else {
            return response()->json(['message' => 'Data hasil periksa tidak ditemukan'], 404);
        }
    }

    public function getTagihan($pasienId)
    {
        $pasien = Pasien::find($pasienId);
        if (!$pasien) {
            return response()->json(['message' => 'Pasien tidak ditemukan'], 404);
        }

        $tagihan = Tagihan::where('pasien_id', $pasienId)->latest()->first();

        $hasilPeriksa = HasilPeriksa::where('pasien_id', $pasienId)
            ->orderBy('tanggal_periksa', 'desc')
            ->first();

        $resepObat = [];

        // Query langsung ke tabel hasilperiksa_obat join obat dan hasilperiksa untuk pasien ini
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

        // Fallback poli_tujuan from latest Antrian if tagihan poli_tujuan is null
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
            'resep_obat_count' => count($resepObat), // debug count
            'total_biaya' => $tagihan ? $tagihan->total_biaya : 0,
            'status_pembayaran' => $tagihan ? $tagihan->status : 'Belum Lunas',
            'created_at' => $tagihan ? $tagihan->created_at : null,
            'jaminan_kesehatan' => $pasien->jaminan_kesehatan,
        ]);
    }

    public function profile()
    {
        return view('kasir.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'profile_photo' => 'nullable|image|max:2048',
            'current_password' => 'required_with:new_password|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!\Hash::check($request->current_password, $user->password)) {
                if ($request->expectsJson()) {
                    return response()->json(['errors' => ['current_password' => ['Password lama tidak sesuai']]], 422);
                }
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai'])->withInput();
            }
            $user->password = bcrypt($request->new_password);
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

        return redirect()->route('kasir.profile')->with('status', 'Profil berhasil diperbarui.');
    }

    public function bayarTagihan(Request $request)
    {
        // Log::info('bayarTagihan called', ['request' => $request->all()]);

        $request->validate([
            'pasien_id' => 'required|exists:pasiens,id',
        ]);

        $pasienId = $request->input('pasien_id');

        $tagihan = Tagihan::where('pasien_id', $pasienId)->latest()->first();

        if (!$tagihan) {
            // \Log::warning('Tagihan not found for pasien_id, creating new tagihan', ['pasien_id' => $pasienId]);
            $tagihan = new Tagihan();
            $tagihan->pasien_id = $pasienId;

            // Fetch poli_tujuan from latest Antrian
            $antrian = \App\Models\Antrian::where('pasien_id', $pasienId)
                ->orderBy('tanggal_berobat', 'desc')
                ->first();
            $tagihan->poli_tujuan = $antrian && $antrian->poli ? $antrian->poli->nama_poli : 'Unknown';

            // Set resep_obat and total_biaya from request
            $tagihan->resep_obat = $request->input('resep_obat', null);
            $totalBiayaRaw = $request->input('total_biaya', 0);
            // Remove currency formatting if any, keep only numbers and dot
            $totalBiayaClean = preg_replace('/[^\d.]/', '', $totalBiayaRaw);
            $tagihan->total_biaya = is_numeric($totalBiayaClean) ? floatval($totalBiayaClean) : 0;

            $tagihan->status = 'Lunas'; // Since paying now
        } else {
            $tagihan->status = 'Lunas';
        }

        $saved = $tagihan->save();

        if (!$saved) {
            // \Log::error('Failed to save tagihan status for pasien_id', ['pasien_id' => $pasienId]);
            return response()->json(['message' => 'Gagal menyimpan status pembayaran'], 500);
        }

        // Update antrian status to 'Farmasi'
        $antrianToUpdate = \App\Models\Antrian::where('pasien_id', $pasienId)
            ->orderBy('tanggal_berobat', 'desc')
            ->first();

        if ($antrianToUpdate) {
            $antrianToUpdate->status = 'Farmasi';
            $antrianToUpdate->save();
        }

        // \Log::info('Pembayaran berhasil for pasien_id', ['pasien_id' => $pasienId]);

        return response()->json(['message' => 'Pembayaran berhasil']);
    }

    // New method to update antrian status (optional, if needed separately)
    public function updateAntrianStatus(Request $request)
    {
        $request->validate([
            'pasien_id' => 'required|exists:pasiens,id',
            'status' => 'required|string',
        ]);

        $pasienId = $request->input('pasien_id');
        $status = $request->input('status');

        $antrian = \App\Models\Antrian::where('pasien_id', $pasienId)
            ->orderBy('tanggal_berobat', 'desc')
            ->first();

        if (!$antrian) {
            return response()->json(['message' => 'Antrian tidak ditemukan'], 404);
        }

        $antrian->status = $status;
        $antrian->save();

        return response()->json(['message' => 'Status antrian berhasil diperbarui']);
    }
}
