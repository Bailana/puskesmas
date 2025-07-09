<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class PasienExport
{
    protected $pasiens;

    public function __construct($pasiens)
    {
        $this->pasiens = $pasiens;
    }

    public function export(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headings
        $headings = [
            'No. Rekam Medis',
            'NIK',
            'Nama Pasien',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Golongan Darah',
            'Agama',
            'Pekerjaan',
            'Status Pernikahan',
            'Alamat Jalan',
            'RT',
            'RW',
            'Kelurahan',
            'Kecamatan',
            'Kabupaten',
            'Provinsi',
            'Jaminan Kesehatan',
            'Nomor Kepesertaan',
        ];

        $sheet->fromArray($headings, null, 'A1');

        // Fill data
        $row = 2;
        foreach ($this->pasiens as $pasien) {
            $sheet->setCellValueExplicit('A' . $row, $pasien['no_rekam_medis'] ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('B' . $row, $pasien['nik'] ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, $pasien['nama_pasien'] ?? '');
            $sheet->setCellValue('D' . $row, $pasien['tempat_lahir'] ?? '');
            $sheet->setCellValue('E' . $row, $pasien['tanggal_lahir'] ?? '');
            $sheet->setCellValue('F' . $row, $pasien['jenis_kelamin'] ?? '');
            $sheet->setCellValue('G' . $row, $pasien['gol_darah'] ?? '');
            $sheet->setCellValue('H' . $row, $pasien['agama'] ?? '');
            $sheet->setCellValue('I' . $row, $pasien['pekerjaan'] ?? '');
            $sheet->setCellValue('J' . $row, $pasien['status_pernikahan'] ?? '');
            $sheet->setCellValue('K' . $row, $pasien['alamat_jalan'] ?? '');
            $sheet->setCellValue('L' . $row, $pasien['rt'] ?? '');
            $sheet->setCellValue('M' . $row, $pasien['rw'] ?? '');
            $sheet->setCellValue('N' . $row, $pasien['kelurahan'] ?? '');
            $sheet->setCellValue('O' . $row, $pasien['kecamatan'] ?? '');
            $sheet->setCellValue('P' . $row, $pasien['kabupaten'] ?? '');
            $sheet->setCellValue('Q' . $row, $pasien['provinsi'] ?? '');
            $sheet->setCellValue('R' . $row, $pasien['jaminan_kesehatan'] ?? '');
            $sheet->setCellValueExplicit('S' . $row, $pasien['nomor_kepesertaan'] ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'pasien_export_' . date('Ymd_His') . '.xlsx';

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    public function exportPdf(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $pdf = Pdf::loadView('admin.export_pdf_html', ['pasiens' => $this->pasiens]);
        $filename = 'pasien_export_' . date('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }
}
