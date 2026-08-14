<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Gereja;
use App\Models\Keuskupan;
use App\Models\DutyAssignment;
use App\Models\KalenderLiturgiHari;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $currentUserId = $user->id;
        $isUserRole = $user->isUser();
        
        // ==================== STATISTIK ====================
        if ($user->isSuperAdmin()) {
            $stats = (object)[
                'total_keuskupan' => Keuskupan::count(),
                'total_gereja' => Gereja::count(),
                'total_users' => User::count(),
                'total_schedules' => Schedule::count(),
                'total_assignments' => DutyAssignment::count(),
                'pending_assignments' => DutyAssignment::where('status', 'pending')->count(),
                'accepted_assignments' => DutyAssignment::where('status', 'accepted')->count(),
                'completed_assignments' => DutyAssignment::where(function($q) use ($today) {
                        $q->where('status', 'completed')
                          ->orWhere(function($sub) use ($today) {
                              $sub->where('status', 'accepted')
                                  ->whereDate('event_date', '<', $today);
                          });
                    })->count(),
                'rejected_assignments' => DutyAssignment::where('status', 'rejected')->count(),
            ];
        } elseif ($user->isAdminKeuskupan()) {
            $stats = (object)[
                'total_keuskupan' => 1,
                'total_gereja' => Gereja::where('keuskupan_id', $user->keuskupan_id)->count(),
                'total_users' => User::where('keuskupan_id', $user->keuskupan_id)->count(),
                'total_schedules' => Schedule::count(),
                'total_assignments' => DutyAssignment::whereHas('user', function($q) use ($user) {
                    $q->where('keuskupan_id', $user->keuskupan_id);
                })->count(),
                'pending_assignments' => DutyAssignment::where('status', 'pending')
                    ->whereHas('user', function($q) use ($user) {
                        $q->where('keuskupan_id', $user->keuskupan_id);
                    })->count(),
                'accepted_assignments' => DutyAssignment::where('status', 'accepted')
                    ->whereHas('user', function($q) use ($user) {
                        $q->where('keuskupan_id', $user->keuskupan_id);
                    })->count(),
                'completed_assignments' => DutyAssignment::where(function($q) use ($today) {
                        $q->where('status', 'completed')
                          ->orWhere(function($sub) use ($today) {
                              $sub->where('status', 'accepted')
                                  ->whereDate('event_date', '<', $today);
                          });
                    })
                    ->whereHas('user', function($q) use ($user) {
                        $q->where('keuskupan_id', $user->keuskupan_id);
                    })
                    ->count(),
                'rejected_assignments' => DutyAssignment::where('status', 'rejected')
                    ->whereHas('user', function($q) use ($user) {
                        $q->where('keuskupan_id', $user->keuskupan_id);
                    })->count(),
            ];
        } elseif ($user->isAdminGereja()) {
            $stats = (object)[
                'total_keuskupan' => 0,
                'total_gereja' => 1,
                'total_users' => User::where('gereja_id', $user->gereja_id)->count(),
                'total_schedules' => Schedule::count(),
                'total_assignments' => DutyAssignment::whereHas('user', function($q) use ($user) {
                    $q->where('gereja_id', $user->gereja_id);
                })->count(),
                'pending_assignments' => DutyAssignment::where('status', 'pending')
                    ->whereHas('user', function($q) use ($user) {
                        $q->where('gereja_id', $user->gereja_id);
                    })->count(),
                'accepted_assignments' => DutyAssignment::where('status', 'accepted')
                    ->whereHas('user', function($q) use ($user) {
                        $q->where('gereja_id', $user->gereja_id);
                    })->count(),
                'completed_assignments' => DutyAssignment::where(function($q) use ($today) {
                        $q->where('status', 'completed')
                          ->orWhere(function($sub) use ($today) {
                              $sub->where('status', 'accepted')
                                  ->whereDate('event_date', '<', $today);
                          });
                    })
                    ->whereHas('user', function($q) use ($user) {
                        $q->where('gereja_id', $user->gereja_id);
                    })
                    ->count(),
                'rejected_assignments' => DutyAssignment::where('status', 'rejected')
                    ->whereHas('user', function($q) use ($user) {
                        $q->where('gereja_id', $user->gereja_id);
                    })->count(),
            ];
        } else {
            // User biasa
            $stats = (object)[
                'total_keuskupan' => 0,
                'total_gereja' => 0,
                'total_users' => 0,
                'total_schedules' => Schedule::count(),
                'total_assignments' => DutyAssignment::where('user_id', $user->id)->count(),
                'pending_assignments' => DutyAssignment::where('user_id', $user->id)->where('status', 'pending')->count(),
                'accepted_assignments' => DutyAssignment::where('user_id', $user->id)->where('status', 'accepted')->count(),
                'completed_assignments' => DutyAssignment::where('user_id', $user->id)
                    ->where(function($q) use ($today) {
                        $q->where('status', 'completed')
                          ->orWhere(function($sub) use ($today) {
                              $sub->where('status', 'accepted')
                                  ->whereDate('event_date', '<', $today);
                          });
                    })->count(),
                'rejected_assignments' => DutyAssignment::where('user_id', $user->id)->where('status', 'rejected')->count(),
            ];
        }
        
        // ==================== JADWAL ====================
        $schedules = Schedule::where('status', 'active')
            ->orderByRaw("FIELD(day, 'sabtu', 'minggu')")
            ->orderBy('time')
            ->get();
        
        // ==================== UPCOMING ASSIGNMENTS (FIX) ====================
        $allUpcoming = DutyAssignment::with(['schedule', 'duty', 'user'])
            ->whereDate('event_date', '>=', $today)
            ->whereIn('status', ['accepted', 'pending'])
            ->when($isUserRole, function($query) use ($currentUserId) {
                return $query->where('user_id', $currentUserId);
            })
            ->orderBy('event_date', 'asc')
            ->get()
            ->filter(function($assignment) use ($today) {
                $daysDiff = $today->diffInDays(Carbon::parse($assignment->event_date));
                return $daysDiff <= 12;
            });
        
        $upcomingAssignments = $allUpcoming->groupBy('duty_id')
            ->sortBy(function($assignments) {
                return $assignments->min('event_date');
            });
        
        // ==================== PENUGASAN TERBARU ====================
        $recentAssignments = DutyAssignment::with(['schedule', 'duty', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // ==================== STATISTIK PER STATUS ====================
        $statusStats = [
            'pending' => DutyAssignment::where('status', 'pending')->count(),
            'accepted' => DutyAssignment::where('status', 'accepted')->count(),
            'rejected' => DutyAssignment::where('status', 'rejected')->count(),
            'completed' => DutyAssignment::where(function($q) use ($today) {
                    $q->where('status', 'completed')
                      ->orWhere(function($sub) use ($today) {
                          $sub->where('status', 'accepted')
                              ->whereDate('event_date', '<', $today);
                      });
                })->count(),
            'cancelled' => DutyAssignment::where('status', 'cancelled')->count(),
        ];
        
        // ==================== NOTIFIKASI ====================
        $notificationCount = $this->getNotificationCount();
        $notifications = $this->getNotifications();

        // ==================== LITURGI ====================
        // 1. Ambil liturgi berdasarkan parameter date (jika ada)
        $selectedDate = request('date') 
            ? Carbon::parse(request('date')) 
            : $today;
        
        $selectedLiturgi = KalenderLiturgiHari::where('tanggal', $selectedDate)->first();
        
        // 2. Ambil liturgi untuk hari ini
        $liturgiHariIni = KalenderLiturgiHari::where('tanggal', $today)->first();
        
        // 3. Ambil liturgi untuk setiap assignment terbaru
        $recentAssignmentsWithLiturgi = $recentAssignments->map(function($assignment) {
            $liturgi = null;
            if ($assignment->event_date) {
                $liturgi = KalenderLiturgiHari::where('tanggal', $assignment->event_date)->first();
            }
            $assignment->liturgi = $liturgi;
            return $assignment;
        });
        
        // 4. Daftar tanggal yang tersedia (30 hari ke belakang dan 30 hari ke depan)
        $availableDates = KalenderLiturgiHari::select('tanggal')
            ->where('tanggal', '>=', Carbon::now()->subDays(30))
            ->where('tanggal', '<=', Carbon::now()->addDays(30))
            ->orderBy('tanggal')
            ->get()
            ->pluck('tanggal')
            ->map(function($date) {
                return $date->format('Y-m-d');
            })
            ->toArray();
        
        // 5. Liturgi terbaru (untuk widget/notifikasi)
        $latestLiturgi = KalenderLiturgiHari::where('tanggal', '>=', $today)
            ->orderBy('tanggal', 'asc')
            ->first();
        
        // ==================== JADWAL MISA DENGAN TANGGAL DINAMIS ====================
        $nextSaturday = $today->copy()->next('Saturday');
        $nextSunday = $today->copy()->next('Sunday');
        
        if ($today->isSaturday()) {
            $nextSaturday = $today->copy();
            $nextSunday = $today->copy()->addDay();
        }
        
        if ($today->isSunday()) {
            $nextSunday = $today->copy();
            $nextSaturday = $today->copy()->subDay();
        }
        
        $saturdayDate = $nextSaturday->format('d F Y');
        $sundayDate = $nextSunday->format('d F Y');

        return view('dashboard.index', compact(
            'stats', 
            'schedules', 
            'upcomingAssignments',
            'recentAssignmentsWithLiturgi', 
            'statusStats',
            'notificationCount',
            'notifications',
            'selectedDate',
            'selectedLiturgi',
            'liturgiHariIni',
            'availableDates',
            'latestLiturgi',
            'nextSaturday',
            'nextSunday',
            'saturdayDate',
            'sundayDate'
        ));
    }
    
    /**
     * Get notification count based on user role
     */
    private function getNotificationCount()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return DutyAssignment::where('status', 'pending')->count();
        } elseif ($user->isAdminKeuskupan()) {
            return DutyAssignment::where('status', 'pending')
                ->whereHas('user', function($q) use ($user) {
                    $q->where('keuskupan_id', $user->keuskupan_id);
                })->count();
        } elseif ($user->isAdminGereja()) {
            return DutyAssignment::where('status', 'pending')
                ->whereHas('user', function($q) use ($user) {
                    $q->where('gereja_id', $user->gereja_id);
                })->count();
        } else {
            return DutyAssignment::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count();
        }
    }
    
    /**
     * Get notifications list based on user role
     */
    private function getNotifications()
    {
        $user = auth()->user();
        $notifications = [];
        
        if ($user->isSuperAdmin()) {
            $assignments = DutyAssignment::with(['schedule', 'duty', 'user'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            foreach ($assignments as $item) {
                $notifications[] = [
                    'message' => "Penugasan baru: {$item->duty->name} untuk {$item->user->name}",
                    'icon' => 'fa-tasks',
                    'icon_bg' => 'bg-blue-100',
                    'icon_color' => 'text-blue-500',
                    'link' => route('assignments.show', $item->id),
                    'time' => $item->created_at->diffForHumans(),
                    'read' => false,
                ];
            }
        } elseif ($user->isAdminKeuskupan()) {
            $assignments = DutyAssignment::with(['schedule', 'duty', 'user', 'user.gereja'])
                ->where('status', 'pending')
                ->whereHas('user', function($q) use ($user) {
                    $q->where('keuskupan_id', $user->keuskupan_id);
                })
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            foreach ($assignments as $item) {
                $notifications[] = [
                    'message' => "Penugasan di {$item->user->gereja->nama}: {$item->duty->name}",
                    'icon' => 'fa-church',
                    'icon_bg' => 'bg-green-100',
                    'icon_color' => 'text-green-500',
                    'link' => route('assignments.show', $item->id),
                    'time' => $item->created_at->diffForHumans(),
                    'read' => false,
                ];
            }
        } elseif ($user->isAdminGereja()) {
            $assignments = DutyAssignment::with(['schedule', 'duty', 'user'])
                ->where('status', 'pending')
                ->whereHas('user', function($q) use ($user) {
                    $q->where('gereja_id', $user->gereja_id);
                })
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            foreach ($assignments as $item) {
                $notifications[] = [
                    'message' => "Penugasan {$item->duty->name} untuk {$item->user->name}",
                    'icon' => 'fa-user-check',
                    'icon_bg' => 'bg-purple-100',
                    'icon_color' => 'text-purple-500',
                    'link' => route('assignments.show', $item->id),
                    'time' => $item->created_at->diffForHumans(),
                    'read' => false,
                ];
            }
        } else {
            // User biasa
            $assignments = DutyAssignment::with(['schedule', 'duty'])
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            foreach ($assignments as $item) {
                $notifications[] = [
                    'message' => "Penugasan {$item->duty->name} pada {$item->schedule->display}",
                    'icon' => 'fa-clock',
                    'icon_bg' => 'bg-yellow-100',
                    'icon_color' => 'text-yellow-500',
                    'link' => route('assignments.show', $item->id),
                    'time' => $item->created_at->diffForHumans(),
                    'read' => false,
                ];
            }
        }
        
        return $notifications;
    }
    
    // API endpoint untuk stats (AJAX)
    public function stats()
    {
        $user = auth()->user();
        $today = Carbon::today();
        
        $data = [
            'total_keuskupan' => Keuskupan::count(),
            'total_gereja' => Gereja::count(),
            'total_users' => User::count(),
            'total_schedules' => Schedule::count(),
            'total_assignments' => DutyAssignment::count(),
            'pending_assignments' => DutyAssignment::where('status', 'pending')->count(),
            'accepted_assignments' => DutyAssignment::where('status', 'accepted')->count(),
            'completed_assignments' => DutyAssignment::where(function($q) use ($today) {
                    $q->where('status', 'completed')
                      ->orWhere(function($sub) use ($today) {
                          $sub->where('status', 'accepted')
                              ->whereDate('event_date', '<', $today);
                      });
                })->count(),
            'rejected_assignments' => DutyAssignment::where('status', 'rejected')->count(),
        ];
        
        return response()->json($data);
    }
}