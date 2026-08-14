<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AssignmentTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            // Data contoh
            [
                '25/12/2024',
                'Misa Sabtu Sore',
                'Misdinar',
                'jhonny@gmail.com',
                'Penugasan untuk Misa Sabtu Sore',
            ],
            [
                '25/12/2024',
                'Misa Sabtu Sore',
                'Khotbah',
                'budi@gmail.com',
                'Penugasan untuk Misa Sabtu Sore - Khotbah',
            ],
            [
                '26/12/2024',
                'Misa Minggu Pagi I',
                'Lektor',
                'siti@gmail.com',
                '',
            ],
            [
                '26/12/2024',
                'Misa Minggu Pagi I',
                'Pemazmur',
                'maya@gmail.com',
                '',
            ],
            [
                '01/01/2025',
                'Misa Tahun Baru',
                'Misdinar',
                'andi@gmail.com',
                'Penugasan khusus Tahun Baru',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal Penugasan',
            'Jadwal Ibadah',
            'Tugas Pelayanan',
            'Email Petugas',
            'Catatan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Kolom wajib diisi (A sampai D) - warna kuning
        $sheet->getStyle('A1:D1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFC000']
            ],
        ]);

        // Border untuk semua data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:E' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D0D0D0']
                ]
            ]
        ]);

        // Set tinggi baris header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Warna latar untuk data contoh
        for ($row = 2; $row <= $lastRow; $row++) {
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => ($row % 2 == 0) ? 'F8F9FA' : 'FFFFFF']
                ]
            ]);
        }

        // Set komentar di cell
        $sheet->setCellValue('F1', 'Keterangan:');
        $sheet->setCellValue('F2', 'Kolom A-D wajib diisi');
        $sheet->setCellValue('F3', 'Tanggal format: d/m/Y (contoh: 25/12/2024)');
        $sheet->setCellValue('F4', 'Jadwal Ibadah harus sesuai dengan nama di database');
        $sheet->setCellValue('F5', 'Tugas Pelayanan harus sesuai dengan nama di database');
        $sheet->setCellValue('F6', 'Email Petugas harus terdaftar di database');
        
        $sheet->getStyle('F1:F6')->getFont()->setSize(10);
        $sheet->getStyle('F1:F6')->getFont()->setItalic(true);
        $sheet->getStyle('F1:F6')->getFont()->getColor()->setRGB('666666');

        // Freeze header
        $sheet->freezePane('A2');
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Tanggal Penugasan
            'B' => 30, // Jadwal Ibadah
            'C' => 25, // Tugas Pelayanan
            'D' => 30, // Email Petugas
            'E' => 40, // Catatan
            'F' => 35, // Keterangan
        ];
    }
}