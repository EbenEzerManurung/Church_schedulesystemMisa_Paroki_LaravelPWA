<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GerejaTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    public function array(): array
    {
        return [
            [
                'Gereja Santo Petrus',
                'KSK001',
                'Jakarta Pusat',
                'Jl. Katedral No.1, Jakarta Pusat',
                '021-1234567',
                'info@santopetrus.or.id',
                'Pater Yohanes',
                '1500',
                'Gereja Katedral Jakarta',
                'Aktif'
            ],
            [
                'Gereja Santa Maria',
                'KSK001',
                'Jakarta Selatan',
                'Jl. Melawai No.10, Kebayoran Baru',
                '021-7654321',
                'santamaria@gmail.com',
                'Pater Petrus',
                '1200',
                'Gereja di pusat kota',
                'Aktif'
            ],
            ['', '', '', '', '', '', '', '', 'Catatan:', ''],
            ['', '', '', '', '', '', '', '', '1. Kolom nama dan kode_keuskupan WAJIB diisi', ''],
            ['', '', '', '', '', '', '', '', '2. Kode keuskupan bisa menggunakan kode (KSK001) atau nama keuskupan', ''],
            ['', '', '', '', '', '', '', '', '3. Status: Aktif atau Nonaktif', ''],
            ['', '', '', '', '', '', '', '', '4. Hapus baris contoh sebelum import', ''],
        ];
    }

    public function headings(): array
    {
        return [
            'nama',
            'kode_keuskupan',
            'lokasi',
            'alamat_lengkap',
            'telepon',
            'email',
            'pastor',
            'jumlah_umat',
            'deskripsi',
            'status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
        ]);

        // Style untuk kolom wajib diisi (nama dan kode_keuskupan)
        $sheet->getStyle('A2:B2')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'],
            ],
        ]);

        // Style untuk catatan
        $sheet->getStyle('I4:J8')->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['rgb' => 'FF0000'],
            ],
        ]);

        return [];
    }

    public function title(): string
    {
        return 'Template Import Gereja';
    }
}