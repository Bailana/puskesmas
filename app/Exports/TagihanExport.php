<?php

namespace App\Exports;

use App\Models\Tagihan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TagihanExport
{
    protected $tagihans;

    public function __construct($tagihans)
    {
        $this->tagihans = $tagihans;
    }

    public function export(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headings
        $headings = [
            'No',
            'Hari/Tanggal',
            'No. RM',
            'Nama Pasien',
            'JamKes',
            'Total Biaya',
            'Status Pembayaran',
        ];

        $sheet->fromArray($headings, null, 'A1');

        // Fill data
        $row = 2;
        foreach ($this->tagihans as $index => $tagihan) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $tagihan->created_at ? $tagihan->created_at->format('d-m-Y') : '');
            $sheet->setCellValue('C' . $row, $tagihan->pasien->no_rekam_medis ?? '');
            $sheet->setCellValue('D' . $row, $tagihan->pasien->nama_pasien ?? '');
            $sheet->setCellValue('E' . $row, $tagihan->pasien->jaminan_kesehatan ?? '');
            $sheet->setCellValue('F' . $row, $tagihan->total_biaya);
            $sheet->setCellValue('G' . $row, $tagihan->status);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'tagihan_export_' . date('Ymd_His') . '.xlsx';

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
