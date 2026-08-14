<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AssignmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $assignments;

    public function __construct($assignments = null)
    {
        $this->assignments = $assignments;
    }

    public function collection()
    {
        if ($this->assignments) {
            return $this->assignments;
        }
        return collect();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Penugasan',
            'Hari',
            'Jadwal Ibadah',
            'Jam',
            'Tugas Pelayanan',
            'Kode Tugas',
            'Petugas',
            'Email Petugas',
            'Status',
            'Catatan',
            'Alasan Penolakan',
            'Tanggal Dibuat',
            'Tanggal Direspon'
        ];
    }

    public function map($assignment): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        
        $statusLabels = [
            'pending' => 'Menunggu Konfirmasi',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai'
            // 'cancelled' => 'Dibatalkan'
        ];
        
        $schedule = $assignment->schedule;
        $duty = $assignment->duty;
        $user = $assignment->user;
        
        return [
            $rowNumber,
            // Tanggal Penugasan (event_date)
            $assignment->event_date ? $assignment->event_date->translatedFormat('d F Y') : '-',
            // Hari
            $assignment->event_date ? $assignment->event_date->translatedFormat('l') : '-',
            // Jadwal Ibadah
            $schedule->display ?? $schedule->name ?? '-',
            // Jam
            $schedule->time ? date('H:i', strtotime($schedule->time)) : '-',
            // Tugas Pelayanan
            $duty->name ?? '-',
            // Kode Tugas
            $duty->code ?? '-',
            // Petugas
            $user->name ?? '-',
            // Email Petugas
            $user->email ?? '-',
            // Status
            $statusLabels[$assignment->status] ?? $assignment->status,
            // Catatan
            $assignment->notes ?? '-',
            // Alasan Penolakan
            $assignment->rejection_reason ?? '-',
          // Format dengan hari dan bulan Indonesia
$assignment->created_at ? $assignment->created_at->setTimezone('Asia/Jakarta')->translatedFormat('l, d F Y H:i:s') : '-',

$assignment->responded_at ? $assignment->responded_at->setTimezone('Asia/Jakarta')->translatedFormat('l, d F Y H:i:s') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount = $this->collection()->count() + 1;
        
        return [
            // Header style
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ],
            // Border untuk semua data
            'A1:N' . $rowCount => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'D0D0D0']
                    ]
                ]
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'M' => NumberFormat::FORMAT_DATE_DATETIME,
            'N' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }
}