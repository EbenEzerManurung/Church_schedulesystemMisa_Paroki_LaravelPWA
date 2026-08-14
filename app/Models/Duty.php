<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Duty extends Model
{
    protected $fillable = [
        'code',
        'name',
        'slug',
        'description',
        'min_person',
        'max_person',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_person' => 'integer',
        'max_person' => 'integer',
    ];

    // Boot method untuk auto-generate code dan slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($duty) {
            $duty->slug = Str::slug($duty->name);
            
            if (empty($duty->code)) {
                $duty->code = static::generateUniqueCode();
            }
        });

        static::updating(function ($duty) {
            if ($duty->isDirty('name')) {
                $duty->slug = Str::slug($duty->name);
            }
        });
    }

    // Generate kode unik
    public static function generateUniqueCode()
    {
        $prefix = 'DUTY';
        $length = 3;
        
        do {
            $number = str_pad(rand(1, 999), $length, '0', STR_PAD_LEFT);
            $code = $prefix . $number;
        } while (self::where('code', $code)->exists());
        
        return $code;
    }

    // Accessor untuk status badge
    public function getStatusBadgeAttribute()
    {
        return $this->is_active 
            ? 'bg-green-100 text-green-800' 
            : 'bg-red-100 text-red-800';
    }

    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }

    /**
     * Hitung jumlah petugas yang terdaftar untuk tugas ini
     */
    public function getPetugasCountAttribute()
    {
        return User::where('duty_id', $this->id)->where('is_active', true)->count();
    }

    /**
     * Get status ketersediaan petugas
     * - membutuhkan_petugas: jika jumlah < min_person
     * - cukup: jika min_person <= jumlah < max_person
     * - full: jika jumlah >= max_person
     */
    public function getKetersediaanStatusAttribute()
    {
        $count = $this->petugas_count;
        
        if ($this->max_person) {
            if ($count >= $this->max_person) {
                return [
                    'status' => 'full',
                    'label' => 'Full',
                    'badge' => 'bg-red-100 text-red-800',
                    'icon' => 'fa-users-slash',
                    'message' => "Jumlah petugas sudah mencapai maksimal ({$count}/{$this->max_person})"
                ];
            } elseif ($count >= $this->min_person) {
                return [
                    'status' => 'cukup',
                    'label' => 'Cukup',
                    'badge' => 'bg-green-100 text-green-800',
                    'icon' => 'fa-check-circle',
                    'message' => "Jumlah petugas sudah cukup ({$count}/{$this->max_person})"
                ];
            } else {
                return [
                    'status' => 'membutuhkan_petugas',
                    'label' => 'Membutuhkan Petugas',
                    'badge' => 'bg-yellow-100 text-yellow-800',
                    'icon' => 'fa-exclamation-triangle',
                    'message' => "Masih membutuhkan " . ($this->min_person - $count) . " petugas lagi (min: {$this->min_person})"
                ];
            }
        } else {
            // Jika max_person tidak diisi (null)
            if ($count >= $this->min_person) {
                return [
                    'status' => 'cukup',
                    'label' => 'Cukup',
                    'badge' => 'bg-green-100 text-green-800',
                    'icon' => 'fa-check-circle',
                    'message' => "Jumlah petugas sudah cukup ({$count} petugas)"
                ];
            } else {
                return [
                    'status' => 'membutuhkan_petugas',
                    'label' => 'Membutuhkan Petugas',
                    'badge' => 'bg-yellow-100 text-yellow-800',
                    'icon' => 'fa-exclamation-triangle',
                    'message' => "Masih membutuhkan " . ($this->min_person - $count) . " petugas lagi (min: {$this->min_person})"
                ];
            }
        }
    }

    // Relasi ke duty_assignments
    public function assignments()
    {
        return $this->hasMany(DutyAssignment::class);
    }

    // Relasi ke users
    public function users()
    {
        return $this->hasMany(User::class);
    }
}