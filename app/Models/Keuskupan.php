<?php
// app/Models/Keuskupan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Keuskupan extends Model
{
    use HasFactory;

    protected $table = 'keuskupans';
    
    protected $fillable = [
        'code', 'name', 'email', 'phone', 'address', 'description', 'is_active'
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi dengan Gereja
    public function gerejas()
    {
        return $this->hasMany(Gereja::class, 'keuskupan_id');
    }

    // Alias untuk churches
    public function churches()
    {
        return $this->hasMany(Gereja::class, 'keuskupan_id');
    }

    // Relasi dengan User
    public function users()
    {
        return $this->hasMany(User::class, 'keuskupan_id');
    }

    // Generate kode otomatis
    public static function generateCode($name)
    {
        // Ambil kata pertama dan terakhir dari nama keuskupan
        $words = explode(' ', $name);
        $code = '';
        
        if (count($words) == 1) {
            $code = substr($words[0], 0, 3);
        } elseif (count($words) == 2) {
            $code = substr($words[0], 0, 1) . substr($words[1], 0, 2);
        } else {
            $code = substr($words[0], 0, 1) . substr($words[1], 0, 1) . substr($words[2], 0, 1);
        }
        
        $code = strtoupper($code);
        
        // Cek apakah kode sudah ada, jika ya tambahkan angka
        $originalCode = $code;
        $counter = 1;
        while (self::where('code', $code)->exists()) {
            $code = $originalCode . $counter;
            $counter++;
        }
        
        return $code;
    }

    // Scope untuk keuskupan aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}