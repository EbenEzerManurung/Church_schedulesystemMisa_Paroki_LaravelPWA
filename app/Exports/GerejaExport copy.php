<?php

namespace App\Exports;

use App\Models\Gereja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GerejaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $gerejas;

    public function __construct($gerejas = null)
    {
        $this->gerejas = $gerejas;
    }

    public function collection()
    {
        if ($this->gerejas) {
            return $this->gerejas;
        }
        return Gereja::with('keuskupan')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Nama Gereja',
            'Keuskupan',
            'Kode Keuskupan',
            'Lokasi/Kota',
            'Alamat Lengkap',
            'Telepon',
            'Email',
            'Pastor',
            'Jumlah Umat',
            'Deskripsi',
            'Status',
            'Tanggal Dibuat'
        ];
    }

    public function map($gereja): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        
        return [
            $rowNumber,
            $gereja->kode,
            $gereja->nama,
            $gereja->keuskupan->name ?? '-',
            $gereja->keuskupan->code ?? '-',
            $gereja->lokasi ?? '-',
            $gereja->alamat_lengkap ?? '-',
            $gereja->telepon ?? '-',
            $gereja->email ?? '-',
            $gereja->pastor ?? '-',
            $gereja->jumlah_umat ?? 0,
            $gereja->deskripsi ?? '-',
            $gereja->is_active ? 'Aktif' : 'Nonaktif',
            $gereja->created_at ? $gereja->created_at->setTimezone('Asia/Jakarta')->translatedFormat('l, d F Y H:i:s') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A1:N1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF']]
            ],
        ];
    }

    public function title(): string
    {
        return 'Data Gereja';
    }
}