<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Schedule extends Model
{
    protected $table = 'schedules';
    
    protected $fillable = [
        'day',
        'date',
        'time',
        'master_date', // TAMBAHKAN INI
        'name',
        'service_id',
        'gereja_id',
        'status',
        'description',
        'schedule_type'
    ];
    
    protected $casts = [
        'date' => 'date',
        'master_date' => 'date', // TAMBAHKAN INI
        'time' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // CONSTANTS
    // ============================================
    
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    const TYPE_MORNING = 'morning';
    const TYPE_AFTERNOON = 'afternoon';
    const TYPE_EVENING = 'evening';
    const TYPE_WEEKDAY = 'weekday';
    const TYPE_SPECIAL = 'special';

    // ============================================
    // RELATIONSHIPS
    // ============================================
    
    /**
     * Relasi ke Service
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    
    /**
     * Relasi ke Gereja
     */
    public function gereja(): BelongsTo
    {
        return $this->belongsTo(Gereja::class, 'gereja_id');
    }
    
    /**
     * Relasi ke Duty Assignments
     */
    public function dutyAssignments(): HasMany
    {
        return $this->hasMany(DutyAssignment::class);
    }

    /**
     * Relasi ke Duty melalui DutyAssignment (many-to-many)
     */
    public function duties()
    {
        return $this->belongsToMany(Duty::class, 'duty_assignments')
                    ->withPivot('user_id', 'status', 'notes', 'event_date')
                    ->withTimestamps();
    }

    /**
     * Relasi ke User melalui DutyAssignment (many-to-many)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'duty_assignments')
                    ->withPivot('duty_id', 'status', 'notes', 'event_date')
                    ->withTimestamps();
    }

    // ============================================
    // ACCESSORS
    // ============================================
    
    /**
     * Daftar hari
     */
    public static function days(): array
    {
        return [
            'sabtu' => 'Sabtu',
            'minggu' => 'Minggu',
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat' => 'Jumat',
        ];
    }

    /**
     * Daftar status
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => ['label' => 'Aktif', 'badge' => 'bg-green-100 text-green-800'],
            self::STATUS_INACTIVE => ['label' => 'Nonaktif', 'badge' => 'bg-gray-100 text-gray-800'],
            self::STATUS_CANCELLED => ['label' => 'Dibatalkan', 'badge' => 'bg-red-100 text-red-800'],
            self::STATUS_COMPLETED => ['label' => 'Selesai', 'badge' => 'bg-blue-100 text-blue-800'],
        ];
    }

    /**
     * Daftar tipe jadwal
     */
    public static function scheduleTypes(): array
    {
        return [
            self::TYPE_MORNING => 'Minggu Pagi',
            self::TYPE_AFTERNOON => 'Minggu Siang',
            self::TYPE_EVENING => 'Minggu Sore',
            self::TYPE_WEEKDAY => 'Hari Biasa',
            self::TYPE_SPECIAL => 'Ibadah Khusus',
        ];
    }

    /**
     * Format waktu
     */
    public function getTimeFormatAttribute(): string
    {
        return $this->time ? Carbon::parse($this->time)->format('H:i') : '--:--';
    }

    /**
     * Format waktu dengan WIB
     */
    public function getTimeStringAttribute(): string
    {
        return $this->time_format . ' WIB';
    }

    /**
     * Format waktu 12 jam
     */
    public function getTime12Attribute(): string
    {
        return $this->time ? Carbon::parse($this->time)->format('h:i A') : '--:--';
    }

    /**
     * Format display lengkap
     */
    public function getDisplayAttribute(): string
    {
        $dayName = self::days()[$this->day] ?? ucfirst($this->day);
        $timeStr = $this->time_format;
        return $dayName . ' (' . $timeStr . ')';
    }

    /**
     * Display dengan tanggal
     */
    public function getFullDisplayAttribute(): string
    {
        $parts = [];
        
        if ($this->date) {
            $parts[] = $this->formatted_date;
        }
        
        if ($this->day) {
            $parts[] = self::days()[$this->day] ?? ucfirst($this->day);
        }
        
        if ($this->time) {
            $parts[] = $this->time_string;
        }
        
        return implode(' - ', $parts);
    }

    /**
     * Display untuk dropdown
     */
    public function getDropdownDisplayAttribute(): string
    {
        $parts = [];
        
        if ($this->date) {
            $parts[] = $this->date->format('d/m/Y');
        }
        
        if ($this->time) {
            $parts[] = $this->time_format;
        }
        
        if ($this->name) {
            $parts[] = $this->name;
        }
        
        return implode(' - ', $parts);
    }

    /**
     * Tipe jadwal display
     */
    public function getScheduleTypeDisplayAttribute(): string
    {
        return self::scheduleTypes()[$this->schedule_type] ?? ucfirst($this->schedule_type ?? $this->display);
    }

    /**
     * Nama hari
     */
    public function getDayNameAttribute(): string
    {
        return self::days()[$this->day] ?? ucfirst($this->day);
    }

    /**
     * Format tanggal
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date ? $this->date->translatedFormat('d F Y') : 'Tanggal belum ditentukan';
    }

    /**
     * Format tanggal pendek
     */
    public function getShortDateAttribute(): string
    {
        return $this->date ? $this->date->translatedFormat('d/m/Y') : '-';
    }

    /**
     * Format tanggal untuk input
     */
    public function getDateForInputAttribute(): ?string
    {
        return $this->date ? $this->date->format('Y-m-d') : null;
    }

    /**
     * Nama hari + tanggal
     */
    public function getDateWithDayAttribute(): string
    {
        if (!$this->date) return '-';
        return $this->date->translatedFormat('l, d F Y');
    }

    /**
     * Status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        return self::statuses()[$this->status]['badge'] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Status label
     */
    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status]['label'] ?? ucfirst($this->status);
    }

    /**
     * Cek apakah schedule aktif
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Cek apakah schedule sudah selesai
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Cek apakah schedule dibatalkan
     */
    public function getIsCancelledAttribute(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Hari ini? (berdasarkan date)
     */
    public function getIsTodayAttribute(): bool
    {
        return $this->date && $this->date->isToday();
    }

    /**
     * Lewat? (berdasarkan date)
     */
    public function getIsPastAttribute(): bool
    {
        return $this->date && $this->date->isPast();
    }

    /**
     * Mendatang? (berdasarkan date)
     */
    public function getIsFutureAttribute(): bool
    {
        return $this->date && $this->date->isFuture();
    }

    /**
     * Tanggal dalam format ISO
     */
    public function getIsoDateAttribute(): string
    {
        return $this->date ? $this->date->toISOString() : '';
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope untuk schedule aktif
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope untuk schedule tidak aktif
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Scope untuk schedule yang selesai
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope untuk schedule yang dibatalkan
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope untuk schedule yang belum selesai
     */
    public function scopeNotCompleted(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_COMPLETED);
    }

    /**
     * Scope untuk schedule hari ini
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('date', today());
    }

    /**
     * Scope untuk schedule minggu ini
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope untuk schedule bulan ini
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('date', now()->month)
                     ->whereYear('date', now()->year);
    }

    /**
     * Scope untuk schedule yang akan datang
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('date', '>=', today())
                     ->orderBy('date', 'asc')
                     ->orderBy('time', 'asc');
    }

    /**
     * Scope untuk schedule yang sudah lewat
     */
    public function scopePast(Builder $query): Builder
    {
        return $query->whereDate('date', '<', today())
                     ->orderBy('date', 'desc');
    }

    /**
     * Scope filter by date range
     */
    public function scopeByDateRange(Builder $query, $startDate, $endDate): Builder
    {
        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Scope filter by gereja
     */
    public function scopeByGereja(Builder $query, int $gerejaId): Builder
    {
        return $query->where('gereja_id', $gerejaId);
    }

    /**
     * Scope filter by service
     */
    public function scopeByService(Builder $query, int $serviceId): Builder
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Scope filter by day
     */
    public function scopeByDay(Builder $query, string $day): Builder
    {
        return $query->where('day', $day);
    }

    /**
     * Scope filter by schedule type
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('schedule_type', $type);
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Get duty assignments by duty
     */
    public function getDutyAssignmentsByDuty(int $dutyId)
    {
        return $this->dutyAssignments()->where('duty_id', $dutyId)->get();
    }

    /**
     * Get users by duty
     */
    public function getUsersByDuty(int $dutyId)
    {
        return $this->dutyAssignments()
                    ->where('duty_id', $dutyId)
                    ->with('user')
                    ->get()
                    ->pluck('user')
                    ->filter();
    }

    /**
     * Get available users by duty
     */
    public function getAvailableUsersByDuty(int $dutyId)
    {
        return $this->dutyAssignments()
                    ->where('duty_id', $dutyId)
                    ->where('availability_status', DutyAssignment::AVAILABILITY_AVAILABLE)
                    ->with('user')
                    ->get()
                    ->pluck('user')
                    ->filter();
    }

    /**
     * Check if schedule has duty assigned
     */
    public function hasDutyAssigned(int $dutyId): bool
    {
        return $this->dutyAssignments()->where('duty_id', $dutyId)->exists();
    }

    /**
     * Check if schedule has user assigned
     */
    public function hasUserAssigned(int $userId): bool
    {
        return $this->dutyAssignments()->where('user_id', $userId)->exists();
    }

    /**
     * Get duty assignments status count
     */
    public function getAssignmentStatusCount(string $status): int
    {
        return $this->dutyAssignments()->where('status', $status)->count();
    }

    /**
     * Get total assignments count
     */
    public function getTotalAssignmentsCount(): int
    {
        return $this->dutyAssignments()->count();
    }

    /**
     * Get pending assignments count
     */
    public function getPendingAssignmentsCount(): int
    {
        return $this->getAssignmentStatusCount(DutyAssignment::STATUS_PENDING);
    }

    /**
     * Get accepted assignments count
     */
    public function getAcceptedAssignmentsCount(): int
    {
        return $this->getAssignmentStatusCount(DutyAssignment::STATUS_ACCEPTED);
    }

    /**
     * Get completed assignments count
     */
    public function getCompletedAssignmentsCount(): int
    {
        return $this->getAssignmentStatusCount(DutyAssignment::STATUS_COMPLETED);
    }

    /**
     * Get rejected assignments count
     */
    public function getRejectedAssignmentsCount(): int
    {
        return $this->getAssignmentStatusCount(DutyAssignment::STATUS_REJECTED);
    }

    /**
     * Mark schedule as completed
     */
    public function markAsCompleted(): bool
    {
        return $this->update(['status' => self::STATUS_COMPLETED]);
    }

    /**
     * Mark schedule as active
     */
    public function markAsActive(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Mark schedule as cancelled
     */
    public function markAsCancelled(): bool
    {
        return $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * Check if schedule is on a specific date
     */
    public function isOnDate($date): bool
    {
        return $this->date && $this->date->isSameDay($date);
    }

    /**
     * Get schedule with all assignments for display
     */
    public function getWithAssignments()
    {
        return $this->load([
            'dutyAssignments' => function ($query) {
                $query->with(['user', 'duty', 'replacementUser']);
            },
            'gereja',
            'service'
        ]);
    }

    /**
     * Get summary of assignments for display
     */
    public function getAssignmentSummaryAttribute(): array
    {
        $assignments = $this->dutyAssignments()->with(['duty', 'user'])->get();
        $summary = [];
        
        foreach ($assignments as $assignment) {
            $dutyName = $assignment->duty->name ?? 'Unknown';
            if (!isset($summary[$dutyName])) {
                $summary[$dutyName] = [];
            }
            $summary[$dutyName][] = [
                'user' => $assignment->user->name ?? 'Unknown',
                'status' => $assignment->status_label,
                'badge' => $assignment->status_badge,
            ];
        }
        
        return $summary;
    }

    /**
     * Get schedule data for calendar
     */
    public function getCalendarDataAttribute(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->name ?? $this->display,
            'start' => $this->date ? $this->date->format('Y-m-d') . 'T' . ($this->time ? $this->time->format('H:i:s') : '00:00:00') : null,
            'allDay' => !$this->time,
            'description' => $this->description,
            'status' => $this->status,
            'gereja' => $this->gereja->nama ?? null,
            'service' => $this->service->name ?? null,
        ];
    }

    /**
     * Get master date formatted
     */
    public function getMasterDateFormattedAttribute(): string
    {
        return $this->master_date ? Carbon::parse($this->master_date)->translatedFormat('d F Y') : '-';
    }

    /**
     * Get master date for input
     */
    public function getMasterDateForInputAttribute(): ?string
    {
        return $this->master_date ? Carbon::parse($this->master_date)->format('Y-m-d') : null;
    }

    /**
     * Get next dates (kelipatan +7 hari dari master_date)
     */
    public function getNextDates(int $limit = 12): array
    {
        if (!$this->master_date) {
            return [];
        }

        $dates = [];
        $currentDate = Carbon::parse($this->master_date);
        $today = Carbon::today();

        $count = 0;
        while ($count < $limit) {
            if ($currentDate >= $today) {
                $dates[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'display' => $currentDate->translatedFormat('l, d F Y') . ' (' . $this->time_format . ')'
                ];
                $count++;
            }
            $currentDate->addWeek();
        }

        return $dates;
    }
}