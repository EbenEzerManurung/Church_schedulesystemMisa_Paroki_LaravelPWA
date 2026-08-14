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

class KalenderLiturgiTemplateExport implements 
    WithHeadings, 
    ShouldAutoSize, 
    WithStyles, 
    WithProperties, 
    WithColumnFormatting,
    WithTitle
{
    /**
     * Contoh data untuk template
     */
    private function getExampleData(): array
    {
        return [
            [
                'keterangan_hari' => 'Minggu Biasa II',
                'warna_liturgi' => 'Hijau',
                'bacaan1' => 'Yes 49:1-3,5-6',
                'mazmur_tanggapan' => 'Mzm 40:2,4,7-10',
                'bait_pengantarinjil' => 'Alleluia, Alleluia, ...',
                'bacaan_injil' => 'Yoh 1:29-34',
            ],
            [
                'keterangan_hari' => 'Hari Raya Paskah',
                'warna_liturgi' => 'Putih',
                'bacaan1' => 'Kis 10:34,37-43',
                'mazmur_tanggapan' => 'Mzm 118:1-2,16-17,22-23',
                'bait_pengantarinjil' => 'Alleluia, Alleluia, ...',
                'bacaan_injil' => 'Mat 28:1-10',
            ],
            [
                'keterangan_hari' => 'Minggu Prapaskah III',
                'warna_liturgi' => 'Ungu',
                'bacaan1' => 'Kel 17:3-7',
                'mazmur_tanggapan' => 'Mzm 95:1-2,6-9',
                'bait_pengantarinjil' => 'Alleluia, Alleluia, ...',
                'bacaan_injil' => 'Yoh 4:5-42',
            ],
        ];
    }

    /**
     * Header / Judul Kolom
     */
    public function headings(): array
    {
        return [
            'KETERANGAN_HARI*',
            'WARNA_LITURGI*',
            'BACAAN_1*',
            'MAZMUR_TANGGAPAN*',
            'BAIT_PENGANTAR_INJIL*',
            'BACAAN_INJIL*',
        ];
    }

    /**
     * Data contoh yang akan ditampilkan di template
     */
    public function map($row): array
    {
        // Method ini tidak digunakan karena kita menggunakan data statis
        return [];
    }

    /**
     * Menampilkan data contoh di bawah header
     */
    public function collection()
    {
        // Data contoh akan ditambahkan langsung di worksheet melalui method styles
        return collect($this->getExampleData());
    }

    /**
     * Style untuk worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Ambil data contoh
        $examples = $this->getExampleData();
        
        // -- SET HEADER (Baris 1) --
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'], // Indigo
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '1E3A8A'],
                ],
            ],
        ]);

        // Set tinggi baris header
        $sheet->getRowDimension(1)->setRowHeight(30);

        // -- SET DATA CONTOH (Baris 2-4) --
        $rowIndex = 2;
        foreach ($examples as $example) {
            $sheet->setCellValue('A' . $rowIndex, $example['keterangan_hari']);
            $sheet->setCellValue('B' . $rowIndex, $example['warna_liturgi']);
            $sheet->setCellValue('C' . $rowIndex, $example['bacaan1']);
            $sheet->setCellValue('D' . $rowIndex, $example['mazmur_tanggapan']);
            $sheet->setCellValue('E' . $rowIndex, $example['bait_pengantarinjil']);
            $sheet->setCellValue('F' . $rowIndex, $example['bacaan_injil']);
            
            // Style untuk baris data contoh
            $sheet->getStyle('A' . $rowIndex . ':F' . $rowIndex)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F3F4F6'], // Gray-100
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);
            
            // Color coding untuk warna liturgi
            $this->applyColorCoding($sheet, 'B' . $rowIndex, $example['warna_liturgi']);
            
            $rowIndex++;
        }

        // -- KETERANGAN TAMBAHAN (Mulai Baris 6) --
        $notesRow = 6;
        
        // Judul keterangan
        $sheet->mergeCells('A' . $notesRow . ':F' . $notesRow);
        $sheet->setCellValue('A' . $notesRow, '📋 PETUNJUK PENGISIAN TEMPLATE');
        $sheet->getStyle('A' . $notesRow . ':F' . $notesRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 13,
                'color' => ['rgb' => '1E3A8A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DBEAFE'],
            ],
        ]);
        $sheet->getRowDimension($notesRow)->setRowHeight(25);

        // Keterangan kolom wajib
        $notesRow++;
        $sheet->mergeCells('A' . $notesRow . ':F' . $notesRow);
        $sheet->setCellValue('A' . $notesRow, '⚠️ SEMUA KOLOM DENGAN TANDA * ADALAH WAJIB DIISI');
        $sheet->getStyle('A' . $notesRow . ':F' . $notesRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'DC2626'], // Red-600
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FEF2F2'], // Red-50
            ],
        ]);

        // Panduan warna liturgi
        $notesRow++;
        $sheet->mergeCells('A' . $notesRow . ':F' . $notesRow);
        $sheet->setCellValue('A' . $notesRow, '🎨 WARNA LITURGI YANG UMUM DIGUNAKAN:');
        $sheet->getStyle('A' . $notesRow . ':F' . $notesRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => '374151'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Daftar warna liturgi
        $colors = [
            ['Hijau', 'Masa Biasa', 'E9FBE8'],
            ['Putih', 'Paskah, Natal, Hari Raya Tuhan', 'FFFFFF'],
            ['Merah', 'Minggu Palma, Jumat Agung, Hari Raya Martir', 'FEE2E2'],
            ['Ungu', 'Masa Prapaskah, Masa Adven', 'F3E8FF'],
            ['Merah Jambu (Rose)', 'Minggu Gaudete (Adven), Minggu Laetare (Prapaskah)', 'FCE8F5'],
            ['Hitam', 'Misa Requiem/Penghormatan', 'E5E7EB'],
        ];

        foreach ($colors as $color) {
            $notesRow++;
            $sheet->mergeCells('A' . $notesRow . ':F' . $notesRow);
            $sheet->setCellValue('A' . $notesRow, '• ' . $color[0] . ' → ' . $color[1]);
            
            // Warna background sesuai warna liturgi
            $sheet->getStyle('A' . $notesRow . ':F' . $notesRow)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => ltrim($color[2], '#')],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOTTED,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ]);
        }

        // Tambahan info
        $notesRow++;
        $sheet->mergeCells('A' . $notesRow . ':F' . $notesRow);
        $sheet->setCellValue('A' . $notesRow, '📌 FORMAT PENULISAN BACAAN:');
        $sheet->getStyle('A' . $notesRow . ':F' . $notesRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => '374151'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $notesRow++;
        $sheet->mergeCells('A' . $notesRow . ':F' . $notesRow);
        $sheet->setCellValue('A' . $notesRow, '   Contoh: Yes 49:1-3,5-6 (bisa menggunakan penulisan singkatan kitab)');
        $sheet->getStyle('A' . $notesRow . ':F' . $notesRow)->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['rgb' => '4B5563'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $notesRow++;
        $sheet->mergeCells('A' . $notesRow . ':F' . $notesRow);
        $sheet->setCellValue('A' . $notesRow, '   Singkatan kitab yang umum: Kej, Kel, Im, Bil, Ul, Yos, Hak, Rut, 1Sam, 2Sam, ...');
        $sheet->getStyle('A' . $notesRow . ':F' . $notesRow)->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['rgb' => '6B7280'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // -- FREEZE PANE --
        $sheet->freezePane('A2');

        // -- AUTO FILTER --
        $sheet->setAutoFilter('A1:F1');
        
        return [];
    }

    /**
     * Apply color coding untuk sel warna liturgi
     */
    private function applyColorCoding(Worksheet $sheet, string $cell, string $colorName)
    {
        $colorMap = [
            'Hijau' => 'E9FBE8',
            'Putih' => 'FFFFFF',
            'Merah' => 'FEE2E2',
            'Ungu' => 'F3E8FF',
            'Merah Jambu' => 'FCE8F5',
            'Rose' => 'FCE8F5',
            'Hitam' => 'E5E7EB',
        ];

        $bgColor = $colorMap[$colorName] ?? 'F3F4F6';
        
        $sheet->getStyle($cell)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $bgColor],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => ($colorName === 'Putih' || $colorName === 'Merah Jambu' || $colorName === 'Rose') ? '1F2937' : '1F2937'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    /**
     * Properti file
     */
    public function properties(): array
    {
        return [
            'creator' => 'Sistem Kalender Liturgi',
            'lastModifiedBy' => 'Sistem Kalender Liturgi',
            'title' => 'Template Import Kalender Liturgi',
            'description' => 'Template untuk import data kalender liturgi. Semua kolom wajib diisi.',
            'subject' => 'Kalender Liturgi',
            'keywords' => 'kalender,liturgi,template,import,liturgical calendar',
            'category' => 'Template',
            'manager' => 'Admin',
            'company' => 'Gereja Paroki',
        ];
    }

    /**
     * Format kolom
     */
    public function columnFormats(): array
    {
        return [
            // Tidak ada format khusus karena semua kolom adalah teks
        ];
    }

    /**
     * Judul sheet
     */
    public function title(): string
    {
        return 'Template Liturgi';
    }
}