<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasKeuskupanAccess;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasKeuskupanAccess;

    // Role constants
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN_KEUSKUPAN = 'admin_keuskupan';
    const ROLE_ADMIN_GEREJA = 'admin_gereja';
    const ROLE_PIC_GROUP = 'pic_group';
    const ROLE_USER = 'user';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'photo',
        'is_active',
        'level_akses',
        'keuskupan_id',
        'gereja_id',
        'duty_id',
        'schedule_id', // TAMBAHKAN
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================
    
    public function keuskupan()
    {
        return $this->belongsTo(Keuskupan::class, 'keuskupan_id');
    }

    public function gereja()
    {
        return $this->belongsTo(Gereja::class, 'gereja_id');
    }

    public function duty()
    {
        return $this->belongsTo(Duty::class, 'duty_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function dutyAssignments()
    {
        return $this->hasMany(DutyAssignment::class, 'user_id');
    }

    public function replacementAssignments()
    {
        return $this->hasMany(DutyAssignment::class, 'replacement_user_id');
    }

    // ============================================
    // ROLE CHECK METHODS
    // ============================================

    public function isSuperAdmin()
    {
        return $this->level_akses === self::ROLE_SUPER_ADMIN || $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function isAdminKeuskupan()
    {
        return $this->level_akses === self::ROLE_ADMIN_KEUSKUPAN || $this->hasRole(self::ROLE_ADMIN_KEUSKUPAN);
    }

    public function isAdminGereja()
    {
        return $this->level_akses === self::ROLE_ADMIN_GEREJA || $this->hasRole(self::ROLE_ADMIN_GEREJA);
    }

    public function isPicGroup()
    {
        return $this->level_akses === self::ROLE_PIC_GROUP || $this->hasRole(self::ROLE_PIC_GROUP);
    }

    public function isUser()
    {
        return $this->level_akses === self::ROLE_USER || $this->hasRole(self::ROLE_USER);
    }

    /**
     * Cek apakah user adalah admin (gabungan semua role admin)
     */
    public function isAdmin()
    {
        return $this->isSuperAdmin() || $this->isAdminKeuskupan() || $this->isAdminGereja();
    }

    // ============================================
    // ACCESS CONTROL METHODS FOR PIC GROUP
    // ============================================

    /**
     * Cek apakah user memiliki akses ke schedule tertentu
     */
    public function canAccessSchedule($scheduleId)
    {
        // Admin bisa akses semua
        if ($this->isAdmin()) {
            return true;
        }
        
        // PIC Group hanya bisa akses schedule_id yang sama
        if ($this->isPicGroup()) {
            return $this->schedule_id == $scheduleId;
        }
        
        // User biasa tidak bisa akses schedule
        return false;
    }

    /**
     * Cek apakah user memiliki akses ke duty tertentu
     */
    public function canAccessDuty($dutyId)
    {
        // Admin bisa akses semua
        if ($this->isAdmin()) {
            return true;
        }
        
        // PIC Group hanya bisa akses duty_id yang sama
        if ($this->isPicGroup()) {
            return $this->duty_id == $dutyId;
        }
        
        return false;
    }

    /**
     * Cek apakah user memiliki akses ke user lain
     */
    public function canAccessUser($targetUserId)
    {
        // Admin bisa akses semua
        if ($this->isAdmin()) {
            return true;
        }
        
        // PIC Group hanya bisa akses user dengan duty_id yang sama
        if ($this->isPicGroup()) {
            $targetUser = User::find($targetUserId);
            if ($targetUser) {
                return $this->duty_id == $targetUser->duty_id;
            }
            return false;
        }
        
        // User biasa hanya bisa akses dirinya sendiri
        return $this->id == $targetUserId;
    }

    /**
     * Cek apakah user memiliki akses ke assignment tertentu
     */
    public function canAccessAssignment($assignmentId)
    {
        $assignment = DutyAssignment::find($assignmentId);
        if (!$assignment) {
            return false;
        }
        
        // Admin bisa akses semua
        if ($this->isAdmin()) {
            return true;
        }
        
        // PIC Group hanya bisa akses assignment dengan duty_id yang sama
        if ($this->isPicGroup()) {
            return $this->duty_id == $assignment->duty_id;
        }
        
        // User biasa hanya bisa akses assignment miliknya
        return $this->id == $assignment->user_id;
    }

    /**
     * Mendapatkan scope query untuk PIC Group
     */
    public function getPicGroupScope($query)
    {
        if ($this->isPicGroup()) {
            if ($this->duty_id) {
                return $query->where('duty_id', $this->duty_id);
            }
            return $query->whereRaw('1 = 0');
        }
        return $query;
    }

    // ============================================
    // AVATAR METHODS
    // ============================================

    public function getAvatarUrlAttribute()
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return Storage::url($this->photo);
        }
        
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&color=7F9CF5&background=EBF4FF&size=128";
    }

    public function getAvatarSmallAttribute()
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return Storage::url($this->photo);
        }
        
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&color=7F9CF5&background=EBF4FF&size=64";
    }

    public function getAvatarAttribute()
    {
        return $this->getAvatarUrlAttribute();
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getRoleDisplayAttribute()
    {
        $roles = [
            'super_admin' => 'Super Admin',
            'admin_keuskupan' => 'Admin Keuskupan',
            'admin_gereja' => 'Admin Gereja',
            'pic_group' => 'PIC Group',
            'user' => 'User Biasa',
        ];
        
        return $roles[$this->level_akses] ?? 'Unknown';
    }

    public function getKeuskupanNameAttribute()
    {
        return $this->keuskupan ? $this->keuskupan->name : null;
    }

    public function getGerejaNameAttribute()
    {
        return $this->gereja ? $this->gereja->nama : null;
    }

    public function getDutyNameAttribute()
    {
        return $this->duty ? $this->duty->name : null;
    }

    public function getScheduleNameAttribute()
    {
        if (!$this->schedule) {
            return null;
        }
        return $this->schedule->day_name . ' ' . $this->schedule->time_string;
    }

    public function getScheduleDisplayAttribute()
    {
        if (!$this->schedule) {
            return 'Tidak ada jadwal';
        }
        return $this->schedule->full_display;
    }

    // ============================================
    // SCOPE METHODS
    // ============================================
    
    public function scopeByKeuskupan($query, $keuskupanId)
    {
        return $query->where('keuskupan_id', $keuskupanId);
    }

    public function scopeByGereja($query, $gerejaId)
    {
        return $query->where('gereja_id', $gerejaId);
    }

    public function scopeByDuty($query, $dutyId)
    {
        return $query->where('duty_id', $dutyId);
    }

    public function scopeBySchedule($query, $scheduleId)
    {
        return $query->where('schedule_id', $scheduleId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAdmins($query)
    {
        return $query->whereIn('level_akses', [
            self::ROLE_SUPER_ADMIN, 
            self::ROLE_ADMIN_KEUSKUPAN, 
            self::ROLE_ADMIN_GEREJA
        ]);
    }

    public function scopePicGroups($query)
    {
        return $query->where('level_akses', self::ROLE_PIC_GROUP);
    }

    public function scopeUsers($query)
    {
        return $query->where('level_akses', self::ROLE_USER);
    }

    // ============================================
    // SCHEDULE METHODS FOR PIC GROUP
    // ============================================

    /**
     * Cek apakah user memiliki akses ke schedule berdasarkan duty_id
     */
    public function hasScheduleAccess($scheduleId)
    {
        if ($this->isAdmin()) {
            return true;
        }
        
        if ($this->isPicGroup()) {
            return $this->schedule_id == $scheduleId;
        }
        
        return false;
    }

    /**
     * Mendapatkan schedule yang diassign ke PIC Group
     */
    public function getAssignedSchedule()
    {
        if ($this->isPicGroup() && $this->schedule_id) {
            return $this->schedule;
        }
        return null;
    }
}