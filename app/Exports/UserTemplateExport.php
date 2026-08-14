<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    public function array(): array
    {
        return [
            [
                'John Doe',
                'john@example.com',
                'user',
                'Gereja Santo Petrus',
                '08123456789',
                'Jl. Contoh No. 123',
                'Aktif',
                'password'
            ],
            [
                'Jane Smith',
                'jane@example.com',
                'admin_gereja',
                'Gereja Santa Maria',
                '08198765432',
                'Jl. Contoh No. 456',
                'Aktif',
                ''
            ],
            [
                'Budi Santoso',
                'budi@example.com',
                'admin_keuskupan',
                '',
                '08155512345',
                'Jl. Keuskupan No. 1',
                'Aktif',
                '',
                'Keuskupan Agung Jakarta'
            ],
            ['', '', '', '', '', '', 'Catatan:', '', ''],
            ['', '', '', '', '', '', '1. Kolom nama, email, level_akses WAJIB diisi', '', ''],
            ['', '', '', '', '', '', '2. Level akses: user, admin_gereja, admin_keuskupan, super_admin', '', ''],
            ['', '', '', '', '', '', '3. Untuk admin_gereja dan user, wajib mengisi kolom gereja', '', ''],
            ['', '', '', '', '', '', '4. Untuk admin_keuskupan, wajib mengisi kolom keuskupan', '', ''],
            ['', '', '', '', '', '', '5. Status: Aktif atau Nonaktif (kosongkan untuk default Aktif)', '', ''],
            ['', '', '', '', '', '', '6. Password: kosongkan untuk menggunakan password default (password)', '', ''],
        ];
    }

    public function headings(): array
    {
        return [
            'nama',
            'email',
            'level_akses',
            'gereja',
            'telepon',
            'alamat',
            'status',
            'password',
            'keuskupan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:I1')->applyFromArray([
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

        // Style untuk kolom wajib diisi
        $sheet->getStyle('A2:B2')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'],
            ],
        ]);

        // Style untuk catatan
        $sheet->getStyle('G5:I11')->applyFromArray([
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
        return 'Template Import User';
    }
}