<?php

namespace App\Exports;

use App\Models\Keuskupan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class KeuskupanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $keuskupans;

    public function __construct($keuskupans = null)
    {
        $this->keuskupans = $keuskupans;
    }

    public function collection()
    {
        if ($this->keuskupans) {
            return $this->keuskupans;
        }
        return Keuskupan::withCount('gerejas')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Nama Keuskupan',
            'Email',
            'Telepon',
            'Deskripsi',
            'Jumlah Gereja',
            'Status',
            'Tanggal Dibuat',
            'Tanggal Diupdate'
        ];
    }

    public function map($keuskupan): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        
        return [
            $rowNumber,
            $keuskupan->code,
            $keuskupan->name,
            $keuskupan->email ?? '-',
            $keuskupan->phone ?? '-',
            $keuskupan->description ?? '-',
            $keuskupan->gerejas_count ?? 0,
            $keuskupan->is_active ? 'Aktif' : 'Nonaktif',
            $keuskupan->created_at ? $keuskupan->created_at->setTimezone('Asia/Jakarta')->translatedFormat('l, d F Y H:i:s') : '-',
            $keuskupan->updated_at ? $keuskupan->updated_at->format('d/m/Y H:i:s') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A1:J1' => ['fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ]],
        ];
    }

    public function title(): string
    {
        return 'Data Keuskupan';
    }
}