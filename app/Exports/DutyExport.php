<?php

namespace App\Exports;

use App\Models\Duty;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DutyExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnWidths
{
    protected $duties;

    public function __construct($duties = null)
    {
        $this->duties = $duties;
    }

    public function collection()
    {
        if ($this->duties) {
            return $this->duties;
        }
        return Duty::all();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Nama Tugas',
            'Slug',
            'Deskripsi',
            'Minimum Petugas',
            'Maksimum Petugas',
            'Jumlah Petugas',
            'Status Ketersediaan',
            'Status',
            'Tanggal Dibuat'
        ];
    }

    public function map($duty): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        
        $ketersediaan = $duty->ketersediaan_status;
        $petugasCount = $duty->petugas_count;
        
        return [
            $rowNumber,
            $duty->code,
            $duty->name,
            $duty->slug,
            $duty->description ?? '-',
            $duty->min_person ?? 1,
            $duty->max_person ?? '-',
            $petugasCount,
            $ketersediaan['label'],
            $duty->is_active ? 'Aktif' : 'Nonaktif',
            $duty->created_at ? $duty->created_at->setTimezone('Asia/Jakarta')->translatedFormat('l, d F Y H:i:s') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        return [
            // Header
            1 => [
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
                ]
            ],
            // Border semua data
            'A1:K' . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D0D0D0']
                    ]
                ]
            ],
            // Alternating row colors
            'A2:K' . $lastRow => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8F9FA']
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 25,
            'D' => 25,
            'E' => 40,
            'F' => 18,
            'G' => 18,
            'H' => 15,
            'I' => 20,
            'J' => 12,
            'K' => 20,
        ];
    }
}