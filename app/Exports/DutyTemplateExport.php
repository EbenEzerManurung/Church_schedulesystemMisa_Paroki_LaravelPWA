<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DutyTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            // Data contoh
            [
                'DUTY001',
                'Lektor',
                'Membacakan bacaan pertama, kedua, dan doa umat',
                2,
                4,
                'Aktif'
            ],
            [
                'DUTY002',
                'Pemazmur',
                'Membawakan mazmur tanggapan',
                1,
                2,
                'Aktif'
            ],
            [
                'DUTY003',
                'Misdinar',
                'Membantu imam selama perayaan Misa',
                3,
                6,
                'Aktif'
            ],
            [
                '',
                '',
                'Catatan: Kolom nama WAJIB diisi',
                '',
                '',
                ''
            ],
            [
                '',
                '',
                'min_person: jumlah minimum petugas (default: 1)',
                '',
                '',
                ''
            ],
            [
                '',
                '',
                'max_person: jumlah maksimum petugas (kosongkan jika tidak ada batas)',
                '',
                '',
                ''
            ],
            [
                '',
                '',
                'status: Aktif atau Nonaktif (kosongkan untuk default Aktif)',
                '',
                '',
                ''
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'kode',
            'nama',
            'deskripsi',
            'min_person',
            'max_person',
            'status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Kolom wajib diisi (B = nama) - warna kuning
        $sheet->getStyle('B1:B2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'],
            ],
        ]);

        // Border semua data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:F' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D0D0D0']
                ]
            ]
        ]);

        // Petunjuk di kolom C (baris 4-7) - italic
        for ($row = 4; $row <= 7; $row++) {
            $sheet->getStyle('C' . $row)->applyFromArray([
                'font' => [
                    'italic' => true,
                    'color' => ['rgb' => '666666'],
                    'size' => 10,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8F9FA'],
                ],
            ]);
        }

        // Set tinggi baris header
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // kode
            'B' => 25, // nama
            'C' => 50, // deskripsi
            'D' => 15, // min_person
            'E' => 15, // max_person
            'F' => 12, // status
        ];
    }
}