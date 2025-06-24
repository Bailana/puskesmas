<?php

namespace App\Exports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class ObatExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Obat::query();

        if (!empty($this->filters['nama_obat'])) {
            $query->where('nama_obat', 'like', '%' . $this->filters['nama_obat'] . '%');
        }
        if (!empty($this->filters['jenis_obat'])) {
            $query->where('jenis_obat', 'like', '%' . $this->filters['jenis_obat'] . '%');
        }
        if (!empty($this->filters['dosis'])) {
            $query->where('dosis', 'like', '%' . $this->filters['dosis'] . '%');
        }
        if (!empty($this->filters['bentuk_obat'])) {
            $query->where('bentuk_obat', 'like', '%' . $this->filters['bentuk_obat'] . '%');
        }
        if (!empty($this->filters['stok'])) {
            $query->where('stok', $this->filters['stok']);
        }
        if (!empty($this->filters['harga_satuan'])) {
            $query->where('harga_satuan', $this->filters['harga_satuan']);
        }
        if (!empty($this->filters['tanggal_kadaluarsa'])) {
            $query->where('tanggal_kadaluarsa', $this->filters['tanggal_kadaluarsa']);
        }
        if (!empty($this->filters['nama_pabrikan'])) {
            $query->where('nama_pabrikan', 'like', '%' . $this->filters['nama_pabrikan'] . '%');
        }
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_obat', 'like', '%' . $search . '%')
                  ->orWhere('jenis_obat', 'like', '%' . $search . '%')
                  ->orWhere('bentuk_obat', 'like', '%' . $search . '%')
                  ->orWhere('stok', 'like', '%' . $search . '%')
                  ->orWhere('harga_satuan', 'like', '%' . $search . '%')
                  ->orWhere('tanggal_kadaluarsa', 'like', '%' . $search . '%');
            });
        }

        return $query->get([
            'nama_obat',
            'jenis_obat',
            'dosis',
            'bentuk_obat',
            'stok',
            'harga_satuan',
            'tanggal_kadaluarsa',
            'nama_pabrikan',
            'keterangan',
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Obat',
            'Jenis Obat',
            'Dosis',
            'Bentuk Obat',
            'Stok',
            'Harga Satuan',
            'Tanggal Kadaluarsa',
            'Nama Pabrikan',
            'Keterangan',
        ];
    }
}
