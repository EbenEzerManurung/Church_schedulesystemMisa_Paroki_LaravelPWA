<?php
// app/Models/Gereja.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gereja extends Model
{
    use HasFactory;

    protected $table = 'gerejas';
    
    protected $fillable = [
        'nama', 'kode', 'lokasi', 'alamat_lengkap', 'telepon', 'email', 
        'pastor', 'jumlah_umat', 'deskripsi', 'keuskupan_id', 'is_active'
    ];

    protected $attributes = [
        'is_active' => true,
        'jumlah_umat' => 0,
    ];

    // Relasi dengan Keuskupan
    public function keuskupan()
    {
        return $this->belongsTo(Keuskupan::class, 'keuskupan_id');
    }

    // Relasi dengan User
    public function users()
    {
        return $this->hasMany(User::class, 'gereja_id');
    }

    // Generate kode gereja otomatis
    public static function generateCode($nama, $keuskupanId)
    {
        $keuskupan = Keuskupan::find($keuskupanId);
        $prefix = $keuskupan ? substr($keuskupan->code, 0, 2) : 'GR';
        
        // Ambil huruf pertama dari setiap kata
        $words = explode(' ', $nama);
        $suffix = '';
        foreach ($words as $word) {
            $suffix .= substr($word, 0, 1);
        }
        $suffix = strtoupper($suffix);
        
        $code = $prefix . '-' . $suffix;
        
        // Cek apakah kode sudah ada
        $originalCode = $code;
        $counter = 1;
        while (self::where('kode', $code)->exists()) {
            $code = $originalCode . $counter;
            $counter++;
        }
        
        return $code;
    }

    // Scope untuk filter aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}