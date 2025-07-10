<?php

namespace App\Exports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class ObatExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Obat::all();
    }

    public function headings(): array
    {
        return [
            'No',
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

    public function map($obat): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $obat->nama_obat,
            $obat->jenis_obat,
            $obat->dosis,
            $obat->bentuk_obat,
            $obat->stok,
            $obat->harga_satuan,
            Carbon::parse($obat->tanggal_kadaluarsa)->format('d-m-Y'),
            $obat->nama_pabrikan,
            $obat->keterangan,
        ];
    }
}
