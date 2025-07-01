<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Obat;

class AdminObatController extends Controller
{
    public function index()
    {
        $obats = Obat::all();
        return view('admin.obat', compact('obats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_obat' => 'required|string|max:255',
            'jenis_obat' => 'required|string|max:255',
            'dosis' => 'required|string|max:255',
            'bentuk_obat' => 'required|string|max:255',
            'stok' => 'required|integer',
            'harga_satuan' => 'required|numeric',
            'tanggal_kadaluarsa' => 'required|date',
            'nama_pabrikan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        Obat::create($validated);

        return redirect()->route('admin.obat')->with('success', 'Data obat berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $obat = Obat::findOrFail($id);
        return view('admin.obat_edit', compact('obat'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_obat' => 'required|string|max:255',
            'jenis_obat' => 'required|string|max:255',
            'dosis' => 'required|string|max:255',
            'bentuk_obat' => 'required|string|max:255',
            'stok' => 'required|integer',
            'harga_satuan' => 'required|numeric',
            'tanggal_kadaluarsa' => 'required|date',
            'nama_pabrikan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $obat = Obat::findOrFail($id);
        $obat->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $obat->id_obat,
                'nama_obat' => $obat->nama_obat,
                'jenis_obat' => $obat->jenis_obat,
                'dosis' => $obat->dosis,
                'bentuk_obat' => $obat->bentuk_obat,
                'stok' => $obat->stok,
                'harga_satuan' => $obat->harga_satuan,
                'tanggal_kadaluarsa' => $obat->tanggal_kadaluarsa,
                'nama_pabrikan' => $obat->nama_pabrikan,
                'keterangan' => $obat->keterangan,
            ]);
        }

        return redirect()->route('admin.obat')->with('success', 'Data obat berhasil diupdate.');
    }

    // Tambahkan ini untuk handle error validasi AJAX
    protected function invalidJson($request, \Illuminate\Validation\ValidationException $exception)
    {
        return response()->json([
            'message' => $exception->getMessage(),
            'errors' => $exception->errors(),
        ], 422);
    }

    public function destroy($id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();

        return redirect()->route('admin.obat')->with('success', 'Data obat berhasil dihapus.');
    }
}
