<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $users;

    public function __construct($users = null)
    {
        $this->users = $users;
    }

    public function collection()
    {
        if ($this->users) {
            return $this->users;
        }
        return User::with(['keuskupan', 'gereja'])->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Email',
            'Level Akses',
            'Keuskupan',
            'Gereja',
            'Telepon',
            'Alamat',
            'Status',
            'Tanggal Dibuat'
        ];
    }

    public function map($user): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        
        return [
            $rowNumber,
            $user->name,
            $user->email,
            $this->getLevelAksesText($user->level_akses),
            $user->keuskupan->name ?? '-',
            $user->gereja->nama ?? '-',
            $user->phone ?? '-',
            $user->address ?? '-',
            $user->is_active ? 'Aktif' : 'Nonaktif',
            $user->created_at ? $user->created_at->setTimezone('Asia/Jakarta')->translatedFormat('l, d F Y H:i:s') : '-',
        ];
    }

    private function getLevelAksesText($level)
    {
        $levels = [
            'super_admin' => 'Super Admin',
            'admin_keuskupan' => 'Admin Keuskupan',
            'admin_gereja' => 'Admin Gereja',
            'user' => 'User Biasa'
        ];
        return $levels[$level] ?? $level;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A1:J1' => [
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
        return 'Data User';
    }
}