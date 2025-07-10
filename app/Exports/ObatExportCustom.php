<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class ObatExportCustom
{
    protected $obats;

    public function __construct($obats)
    {
        $this->obats = $obats;
    }

    public function export(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headings
        $headings = [
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

        $sheet->fromArray($headings, null, 'A1');

        // Fill data
        $row = 2;
        $index = 1;
        foreach ($this->obats as $obat) {
            $sheet->setCellValue('A' . $row, $index++);
            $sheet->setCellValue('B' . $row, $obat->nama_obat);
            $sheet->setCellValue('C' . $row, $obat->jenis_obat);
            $sheet->setCellValue('D' . $row, $obat->dosis);
            $sheet->setCellValue('E' . $row, $obat->bentuk_obat);
            $sheet->setCellValue('F' . $row, $obat->stok);
            $sheet->setCellValue('G' . $row, $obat->harga_satuan);
            $sheet->setCellValue('H' . $row, Carbon::parse($obat->tanggal_kadaluarsa)->format('d-m-Y'));
            $sheet->setCellValue('I' . $row, $obat->nama_pabrikan);
            $sheet->setCellValue('J' . $row, $obat->keterangan);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'laporan-data-obat-' . date('Ymd_His') . '.xlsx';

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
