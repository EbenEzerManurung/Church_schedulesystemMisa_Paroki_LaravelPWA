<?php
// app/Models/KalenderLiturgiHari.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KalenderLiturgiHari extends Model
{
    use HasFactory;

    protected $table = 'kalender_liturgi_hari';
    
    protected $fillable = [
        'tanggal',
        'keterangan_hari',
        'warna_liturgi',
        'bacaan1',
        'mazmur_tanggapan',
        'bait_pengantarinjil',
        'bacaan_injil',
        'catatan',
        'is_active'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_active' => 'boolean',
    ];

    public function getWarnaLiturgiBadgeAttribute()
    {
        $warna = [
            'putih' => 'bg-white text-gray-800 border border-gray-300',
            'merah' => 'bg-red-600 text-white',
            'ungu' => 'bg-purple-600 text-white',
            'hijau' => 'bg-green-600 text-white',
            'kuning' => 'bg-yellow-500 text-white',
            'hitam' => 'bg-gray-800 text-white',
            'pink' => 'bg-pink-500 text-white',
            'biru' => 'bg-blue-600 text-white',
        ];

        $color = strtolower($this->warna_liturgi ?? '');
        return $warna[$color] ?? 'bg-gray-500 text-white';
    }
}