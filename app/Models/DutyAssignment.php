<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DutyAssignment extends Model
{
    use HasFactory;

    protected $table = 'duty_assignments';
    
    protected $fillable = [
        'schedule_id',
        'duty_id',
        'user_id',
        'replacement_user_id',
        'status',
        'event_date',
        'availability_status',
        'notes',
        'rejection_reason',
        'unavailable_reason',
        'replacement_request_id',
        'responded_at',
        'availability_updated_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'responded_at' => 'datetime',
        'availability_updated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // STATUS CONSTANTS
    // ============================================
    
    // Status penugasan
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    
    // Status ketersediaan
    const AVAILABILITY_PENDING = 'pending';
    const AVAILABILITY_AVAILABLE = 'available';
    const AVAILABILITY_UNAVAILABLE = 'unavailable';

    /**
     * Daftar status penugasan
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => [
                'label' => 'Menunggu Konfirmasi', 
                'badge' => 'bg-yellow-100 text-yellow-800', 
                'icon' => 'fa-clock'
            ],
            self::STATUS_ACCEPTED => [
                'label' => 'Diterima', 
                'badge' => 'bg-green-100 text-green-800', 
                'icon' => 'fa-check-circle'
            ],
            self::STATUS_REJECTED => [
                'label' => 'Ditolak', 
                'badge' => 'bg-red-100 text-red-800', 
                'icon' => 'fa-times-circle'
            ],
            self::STATUS_COMPLETED => [
                'label' => 'Selesai', 
                'badge' => 'bg-blue-100 text-blue-800', 
                'icon' => 'fa-check-double'
            ],
            self::STATUS_CANCELLED => [
                'label' => 'Dibatalkan', 
                'badge' => 'bg-gray-100 text-gray-800', 
                'icon' => 'fa-ban'
            ],
        ];
    }
    
    /**
     * Daftar status ketersediaan
     */
    public static function availabilityStatuses(): array
    {
        return [
            self::AVAILABILITY_PENDING => [
                'label' => 'Belum Konfirmasi', 
                'badge' => 'bg-yellow-100 text-yellow-800', 
                'icon' => 'fa-clock'
            ],
            self::AVAILABILITY_AVAILABLE => [
                'label' => 'Bersedia', 
                'badge' => 'bg-green-100 text-green-800', 
                'icon' => 'fa-check-circle'
            ],
            self::AVAILABILITY_UNAVAILABLE => [
                'label' => 'Tidak Bersedia', 
                'badge' => 'bg-red-100 text-red-800', 
                'icon' => 'fa-times-circle'
            ],
        ];
    }

    // ============================================
    // ACCESSORS
    // ============================================
    
    /**
     * Format tanggal event
     */
    public function getEventDateFormattedAttribute(): string
    {
        return $this->event_date ? $this->event_date->translatedFormat('d F Y') : '-';
    }

    public function getEventDayAttribute(): string
    {
        return $this->event_date ? $this->event_date->translatedFormat('l') : '-';
    }

    /**
     * Display lengkap: Tanggal + Hari + Waktu dari schedule
     */
    public function getEventDisplayAttribute(): string
    {
        if (!$this->event_date) return '-';
        
        $display = $this->event_date->translatedFormat('d F Y');
        $display .= ' (' . $this->event_date->translatedFormat('l') . ')';
        
        if ($this->schedule && $this->schedule->time) {
            $display .= ' • ' . \Carbon\Carbon::parse($this->schedule->time)->format('H:i');
        }
        
        return $display;
    }

    /**
     * Display singkat untuk tabel
     */
    public function getEventShortAttribute(): string
    {
        if (!$this->event_date) return '-';
        return $this->event_date->translatedFormat('d/m/Y');
    }

    /**
     * Status Label
     */
    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status]['label'] ?? $this->status;
    }

    public function getStatusBadgeAttribute(): string
    {
        return self::statuses()[$this->status]['badge'] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusIconAttribute(): string
    {
        return self::statuses()[$this->status]['icon'] ?? 'fa-question';
    }
    
    /**
     * Availability Status
     */
    public function getAvailabilityStatusLabelAttribute(): string
    {
        return self::availabilityStatuses()[$this->availability_status]['label'] ?? 'Belum Konfirmasi';
    }
    
    public function getAvailabilityStatusBadgeAttribute(): string
    {
        return self::availabilityStatuses()[$this->availability_status]['badge'] ?? 'bg-yellow-100 text-yellow-800';
    }
    
    public function getAvailabilityStatusIconAttribute(): string
    {
        return self::availabilityStatuses()[$this->availability_status]['icon'] ?? 'fa-clock';
    }
    
    /**
     * Cek apakah ini assignment untuk replacement
     */
    public function getIsReplacementAttribute(): bool
    {
        return !is_null($this->replacement_user_id);
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }
    
    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }
    
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
    
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
    
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }
    
    public function scopeNotCancelled(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_CANCELLED);
    }

    /**
     * Scope untuk assignment yang belum selesai
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            self::STATUS_COMPLETED, 
            self::STATUS_CANCELLED
        ]);
    }

    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
    
    public function scopeByDuty(Builder $query, int $dutyId): Builder
    {
        return $query->where('duty_id', $dutyId);
    }
    
    public function scopeBySchedule(Builder $query, int $scheduleId): Builder
    {
        return $query->where('schedule_id', $scheduleId);
    }
    
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('availability_status', self::AVAILABILITY_AVAILABLE);
    }
    
    public function scopeUnavailable(Builder $query): Builder
    {
        return $query->where('availability_status', self::AVAILABILITY_UNAVAILABLE);
    }
    
    public function scopeAvailabilityPending(Builder $query): Builder
    {
        return $query->where('availability_status', self::AVAILABILITY_PENDING);
    }

    /**
     * Scope filter by event date range
     */
    public function scopeByEventDateRange(Builder $query, $startDate, $endDate): Builder
    {
        if ($startDate) {
            $query->whereDate('event_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('event_date', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Scope untuk assignment yang perlu respon (pending)
     */
    public function scopeNeedResponse(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING)
                     ->whereNull('responded_at');
    }

    /**
     * Scope untuk assignment hari ini
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('event_date', today());
    }

    /**
     * Scope untuk assignment minggu ini
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('event_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function duty()
    {
        return $this->belongsTo(Duty::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replacementUser()
    {
        return $this->belongsTo(User::class, 'replacement_user_id');
    }
    
    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Cek apakah sudah direspon
     */
    public function hasResponded(): bool
    {
        return !is_null($this->responded_at);
    }
    
    /**
     * Cek status ketersediaan
     */
    public function isAvailable(): bool
    {
        return $this->availability_status === self::AVAILABILITY_AVAILABLE;
    }
    
    public function isUnavailable(): bool
    {
        return $this->availability_status === self::AVAILABILITY_UNAVAILABLE;
    }
    
    public function isAvailabilityPending(): bool
    {
        return $this->availability_status === self::AVAILABILITY_PENDING;
    }
    
    /**
     * Cek status penugasan
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
    
    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }
    
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
    
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
    
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }
    
    public function isActive(): bool
    {
        return !$this->isCompleted() && !$this->isCancelled();
    }
    
    /**
     * Cek apakah ini assignment replacement
     */
    public function isReplacement(): bool
    {
        return !is_null($this->replacement_user_id);
    }
    
    /**
     * Cek apakah user adalah replacement
     */
    public function isReplacementFor(User $user): bool
    {
        return $this->replacement_user_id === $user->id;
    }
    
    /**
     * Cek apakah user adalah yang ditugaskan
     */
    public function isAssignedTo(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Approve assignment
     */
    public function approve(?string $notes = null): bool
    {
        if (!$this->isPending()) {
            return false;
        }
        
        return $this->update([
            'status' => self::STATUS_ACCEPTED,
            'responded_at' => now(),
            'notes' => $notes ?? $this->notes,
        ]);
    }

    /**
     * Reject assignment
     */
    public function reject(string $reason, ?string $notes = null): bool
    {
        if (!$this->isPending()) {
            return false;
        }
        
        return $this->update([
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'responded_at' => now(),
            'notes' => $notes ?? $this->notes,
        ]);
    }

    /**
     * Complete assignment
     */
    public function complete(?string $notes = null): bool
    {
        if (!$this->isAccepted()) {
            return false;
        }
        
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'notes' => $notes ?? $this->notes,
        ]);
    }

    /**
     * Cancel assignment
     */
    public function cancel(?string $reason = null): bool
    {
        if ($this->isCompleted() || $this->isCancelled()) {
            return false;
        }
        
        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'notes' => $reason ?? $this->notes,
        ]);
    }

    /**
     * Update availability status
     */
    public function updateAvailability(string $status, ?string $reason = null): bool
    {
        if (!in_array($status, [self::AVAILABILITY_AVAILABLE, self::AVAILABILITY_UNAVAILABLE])) {
            return false;
        }
        
        return $this->update([
            'availability_status' => $status,
            'unavailable_reason' => $status === self::AVAILABILITY_UNAVAILABLE ? $reason : null,
            'availability_updated_at' => now(),
        ]);
    }

    /**
     * Mark as available
     */
    public function markAsAvailable(): bool
    {
        return $this->updateAvailability(self::AVAILABILITY_AVAILABLE);
    }

    /**
     * Mark as unavailable
     */
    public function markAsUnavailable(string $reason): bool
    {
        return $this->updateAvailability(self::AVAILABILITY_UNAVAILABLE, $reason);
    }

    /**
     * Get user name (either assigned or replacement)
     */
    public function getAssignedUserNameAttribute(): string
    {
        if ($this->isReplacement() && $this->replacementUser) {
            return $this->replacementUser->name . ' (Pengganti)';
        }
        return $this->user->name ?? 'Unknown';
    }

    /**
     * Get user email (either assigned or replacement)
     */
    public function getAssignedUserEmailAttribute(): string
    {
        if ($this->isReplacement() && $this->replacementUser) {
            return $this->replacementUser->email;
        }
        return $this->user->email ?? '';
    }

    /**
     * Get status for display with color
     */
    public function getStatusDisplayAttribute(): array
    {
        return [
            'label' => $this->status_label,
            'badge' => $this->status_badge,
            'icon' => $this->status_icon,
        ];
    }

    /**
     * Get availability status for display with color
     */
    public function getAvailabilityDisplayAttribute(): array
    {
        return [
            'label' => $this->availability_status_label,
            'badge' => $this->availability_status_badge,
            'icon' => $this->availability_status_icon,
        ];
    }
}