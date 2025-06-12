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

class ResepsionisDashboardController extends Controller
{
    // ... other methods unchanged ...

    public function exportExcel(Request $request)
    {
        $query = Pasien::query();

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

        $export = new PasienExport($pasiens);
        return $export->export();
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
}
