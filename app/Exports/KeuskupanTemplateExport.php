<?php
// app/Exports/KalenderLiturgiTemplateExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class KalenderLiturgiTemplateExport implements WithHeadings, ShouldAutoSize, WithStyles, WithProperties, WithColumnFormatting, WithTitle
{
    protected $withSample = false;

    public function __construct($withSample = false)
    {
        $this->withSample = $withSample;
    }

    public function headings(): array
    {
        return [
            'Tanggal (YYYY-MM-DD)*',
            'Keterangan Hari*',
            'Warna Liturgi*',
            'Bacaan 1',
            'Mazmur Tanggapan',
            'Bait Pengantar Injil',
            'Bacaan Injil',
            'Catatan',
            'Status Aktif (1/0)'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Set tinggi baris header
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Jika ada sample data
        if ($this->withSample) {
            // Style untuk baris contoh data
            $sheet->getStyle('A2:I4')->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F3F4F6'],
                ],
                'font' => [
                    'color' => ['rgb' => '6B7280'],
                    'italic' => true,
                ],
            ]);

            // Border untuk seluruh data
            $sheet->getStyle('A1:I4')->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);

            // Catatan
            $sheet->setCellValue('A7', 'CATATAN PENTING:');
            $sheet->setCellValue('A8', '1. Kolom dengan tanda * (bintang) WAJIB diisi');
            $sheet->setCellValue('A9', '2. Format tanggal: YYYY-MM-DD (contoh: 2026-01-15)');
            $sheet->setCellValue('A10', '3. Warna liturgi yang valid: putih, merah, ungu, hijau, kuning, hitam, pink, biru');
            $sheet->setCellValue('A11', '4. Status Aktif: 1 = Aktif, 0 = Tidak Aktif (default: 1)');
            $sheet->setCellValue('A12', '5. HAPUS baris contoh sebelum melakukan import');
            $sheet->setCellValue('A13', '6. Pastikan tanggal tidak duplikat dengan data yang sudah ada');
            
            $sheet->getStyle('A7:I13')->applyFromArray([
                'font' => [
                    'color' => ['rgb' => 'DC2626'],
                    'size' => 10,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FEF2F2'],
                ],
            ]);
            
            // Merge cell untuk catatan
            $sheet->mergeCells('A7:I7');
            $sheet->mergeCells('A8:I8');
            $sheet->mergeCells('A9:I9');
            $sheet->mergeCells('A10:I10');
            $sheet->mergeCells('A11:I11');
            $sheet->mergeCells('A12:I12');
            $sheet->mergeCells('A13:I13');
        }

        return $sheet;
    }

    public function properties(): array
    {
        return [
            'creator' => 'Sistem Kalender Liturgi',
            'lastModifiedBy' => 'Sistem Kalender Liturgi',
            'title' => 'Template Import Kalender Liturgi',
            'description' => 'Template untuk import data kalender liturgi',
            'subject' => 'Kalender Liturgi',
            'keywords' => 'kalender,liturgi,template,import',
            'category' => 'Template',
            'manager' => 'Admin',
            'company' => 'Gereja',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }

    public function title(): string
    {
        return 'Template Kalender Liturgi';
    }
}