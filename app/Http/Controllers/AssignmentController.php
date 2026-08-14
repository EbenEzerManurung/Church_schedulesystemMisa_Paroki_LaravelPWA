<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Duty;
use App\Models\DutyAssignment;
use App\Models\KalenderLiturgiHari;
use App\Models\User;
use App\Exports\AssignmentExport;
use App\Exports\AssignmentTemplateExport;
use App\Imports\AssignmentImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Helper method untuk cek apakah user adalah admin
     */
    private function isAdmin()
    {
        $user = auth()->user();
        return in_array($user->level_akses, ['super_admin', 'admin_keuskupan', 'admin_gereja']);
    }

    /**
     * Helper method untuk cek apakah user adalah super admin
     */
    private function isSuperAdmin()
    {
        $user = auth()->user();
        return $user->level_akses === 'super_admin';
    }

    /**
     * Helper method untuk cek apakah user adalah PIC Group
     */
    private function isPicGroup()
    {
        $user = auth()->user();
        return $user->level_akses === 'pic_group';
    }

    /**
     * Helper method untuk mendapatkan duty_id user (untuk PIC Group)
     */
    private function getUserDutyId()
    {
        $user = auth()->user();
        return $user->duty_id;
    }

    /**
     * Helper method untuk mendapatkan schedule_id user (untuk PIC Group)
     */
    private function getUserScheduleId()
    {
        $user = auth()->user();
        return $user->schedule_id;
    }

    /**
     * Helper method untuk mengecek akses assignment (untuk PIC Group)
     */
    private function canAccessAssignment(DutyAssignment $assignment)
    {
        $user = auth()->user();
        
        if ($this->isAdmin()) {
            return true;
        }
        
        if ($this->isPicGroup() && $user->duty_id) {
            return $assignment->duty_id == $user->duty_id;
        }
        
        return false;
    }

    // ============================================
    // GET SCHEDULE DATES (AJAX)
    // ============================================

    public function getScheduleDates(Request $request)
    {
        try {
            $scheduleId = $request->schedule_id;
            if (!$scheduleId) {
                return response()->json(['dates' => []]);
            }
            
            $schedule = Schedule::find($scheduleId);
            if (!$schedule) {
                return response()->json(['dates' => []]);
            }
            
            $masterDate = $schedule->master_date ?? $schedule->created_at->format('Y-m-d');
            $baseDate = new \DateTime($masterDate);
            $today = new \DateTime();
            $today->setTime(0, 0, 0);
            
            $dates = [];
            $currentDate = clone $baseDate;
            $maxWeeks = 52;
            
            $dayMap = [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu'
            ];
            
            $monthMap = [
                'January' => 'Januari',
                'February' => 'Februari',
                'March' => 'Maret',
                'April' => 'April',
                'May' => 'Mei',
                'June' => 'Juni',
                'July' => 'Juli',
                'August' => 'Agustus',
                'September' => 'September',
                'October' => 'Oktober',
                'November' => 'November',
                'December' => 'Desember'
            ];
            
            for ($i = 0; $i < $maxWeeks; $i++) {
                if ($currentDate >= $today) {
                    $dayName = $dayMap[$currentDate->format('l')] ?? $currentDate->format('l');
                    $monthName = $monthMap[$currentDate->format('F')] ?? $currentDate->format('F');
                    $dates[] = [
                        'value' => $currentDate->format('Y-m-d'),
                        'label' => $dayName . ', ' . $currentDate->format('d') . ' ' . $monthName . ' ' . $currentDate->format('Y') . ' (' . $schedule->time . ')'
                    ];
                }
                $currentDate->modify('+7 days');
            }
            
            return response()->json(['dates' => $dates]);
            
        } catch (\Exception $e) {
            Log::error('Get schedule dates error: ' . $e->getMessage());
            return response()->json(['dates' => [], 'error' => $e->getMessage()], 500);
        }
    }

    // ============================================
    // IMPORT METHODS
    // ============================================

    public function showImportForm()
    {
        $this->authorizeAssignment();
        return view('assignments.import');
    }

    public function import(Request $request)
    {
        $this->authorizeAssignment();
        
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            DB::beginTransaction();
            
            $import = new AssignmentImport();
            Excel::import($import, $request->file('file'));
            
            DB::commit();
            
            $successCount = $import->getSuccessCount();
            $failureCount = $import->getFailureCount();
            $failures = $import->getFailures();
            
            $message = "✅ Berhasil mengimport {$successCount} data penugasan.";
            
            if ($failureCount > 0) {
                $message .= " ⚠️ {$failureCount} data gagal diimport.";
                session()->flash('import_failures', $failures);
            }
            
            if ($successCount == 0 && $failureCount > 0) {
                return redirect()->route('assignments.index')
                    ->with('error', 'Tidak ada data yang berhasil diimport. Silakan cek format file.');
            }
            
            return redirect()->route('assignments.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import assignment error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        try {
            return Excel::download(new AssignmentTemplateExport(), 'template_import_penugasan.xlsx');
        } catch (\Exception $e) {
            Log::error('Download template error: ' . $e->getMessage());
            return redirect()->route('assignments.index')
                ->with('error', 'Gagal mendownload template: ' . $e->getMessage());
        }
    }

    // ============================================
    // EXPORT METHODS
    // ============================================

    public function showExportForm()
    {
        $this->authorizeAssignment();
        
        $query = DutyAssignment::with(['schedule', 'duty', 'user']);
        
        if ($this->isPicGroup()) {
            $dutyId = $this->getUserDutyId();
            if ($dutyId) {
                $query->where('duty_id', $dutyId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        
        $assignments = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $statuses = [
            'pending' => 'Menunggu',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak'
        ];
        
        return view('assignments.export', compact('assignments', 'statuses'));
    }

    public function exportAll()
    {
        $this->authorizeAssignment();
        
        try {
            $query = DutyAssignment::with(['schedule', 'duty', 'user']);
            
            if ($this->isPicGroup()) {
                $dutyId = $this->getUserDutyId();
                if ($dutyId) {
                    $query->where('duty_id', $dutyId);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
            
            $assignments = $query->get();
            $fileName = 'data_penugasan_' . date('Y-m-d_His') . '.xlsx';
            return Excel::download(new AssignmentExport($assignments), $fileName);
        } catch (\Exception $e) {
            Log::error('Export all error: ' . $e->getMessage());
            return redirect()->route('assignments.index')
                ->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }

    public function exportFiltered(Request $request)
    {
        $this->authorizeAssignment();
        
        try {
            $query = DutyAssignment::with(['schedule', 'duty', 'user']);
            
            if ($this->isPicGroup()) {
                $dutyId = $this->getUserDutyId();
                if ($dutyId) {
                    $query->where('duty_id', $dutyId);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('start_date')) {
                $query->whereDate('event_date', '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $query->whereDate('event_date', '<=', $request->end_date);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($qu) use ($search) {
                        $qu->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('duty', function($qu) use ($search) {
                        $qu->where('name', 'like', '%' . $search . '%');
                    });
                });
            }
            
            $assignments = $query->get();
            
            if ($assignments->isEmpty()) {
                return redirect()->route('assignments.index')
                    ->with('error', 'Tidak ada data yang sesuai dengan filter');
            }
            
            $format = $request->get('format', 'xlsx');
            $fileName = 'data_penugasan_filtered_' . date('Y-m-d_His');
            
            if ($format === 'csv') {
                $fileName .= '.csv';
                return Excel::download(new AssignmentExport($assignments), $fileName, \Maatwebsite\Excel\Excel::CSV);
            }
            
            $fileName .= '.xlsx';
            return Excel::download(new AssignmentExport($assignments), $fileName);
            
        } catch (\Exception $e) {
            Log::error('Export filtered error: ' . $e->getMessage());
            return redirect()->route('assignments.index')
                ->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }

    // ============================================
    // CHECK AVAILABILITY (AJAX)
    // ============================================

    public function checkDuplicate(Request $request)
    {
        try {
            $scheduleId = $request->schedule_id;
            $dutyId = $request->duty_id;
            $userId = $request->user_id;
            $eventDate = $request->event_date;

            $exists = DutyAssignment::where('schedule_id', $scheduleId)
                ->where('duty_id', $dutyId)
                ->where('user_id', $userId)
                ->where('event_date', $eventDate)
                ->exists();

            $totalAccepted = DutyAssignment::where('schedule_id', $scheduleId)
                ->where('duty_id', $dutyId)
                ->where('event_date', $eventDate)
                ->where('status', 'accepted')
                ->count();

            $duty = Duty::find($dutyId);
            $maxPerson = $duty ? $duty->max_person : 999;
            $minPerson = $duty ? $duty->min_person : 1;

            $userOtherDuty = DutyAssignment::where('user_id', $userId)
                ->where('event_date', $eventDate)
                ->where('schedule_id', $scheduleId)
                ->where('duty_id', '!=', $dutyId)
                ->exists();

            $isValid = !$exists && !$userOtherDuty;

            $message = '';
            if ($exists) {
                $message = '⚠️ User ini sudah memiliki tugas ini pada tanggal tersebut.';
            } elseif ($userOtherDuty) {
                $message = '⚠️ User ini sudah memiliki tugas lain pada jadwal dan tanggal yang sama!';
            } elseif ($totalAccepted >= $maxPerson && $maxPerson != 999) {
                $message = '📊 Kuota sudah penuh (' . $totalAccepted . '/' . $maxPerson . '). Namun Anda tetap bisa menambahkan.';
            } elseif ($totalAccepted >= $minPerson) {
                $message = '✅ Kuota terpenuhi (' . $totalAccepted . '/' . ($maxPerson != 999 ? $maxPerson : '∞') . '). Minimal ' . $minPerson . ' orang.';
            } elseif ($totalAccepted > 0) {
                $message = '👥 ' . $totalAccepted . ' petugas sudah mengambil, butuh ' . ($minPerson - $totalAccepted) . ' orang lagi.';
            } else {
                $message = '📋 Belum ada petugas yang mengambil tugas ini.';
            }

            return response()->json([
                'valid' => $isValid,
                'exists' => $exists,
                'duty_taken' => false,
                'user_other_duty' => $userOtherDuty,
                'total_accepted' => $totalAccepted,
                'max_person' => $maxPerson,
                'min_person' => $minPerson,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // USER ROUTES
    // ============================================

    public function myAssignments()
    {
        $user = auth()->user();
        
        $pendingAssignments = DutyAssignment::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with(['schedule', 'duty'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $acceptedAssignments = DutyAssignment::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->with(['schedule', 'duty'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $completedAssignments = DutyAssignment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with(['schedule', 'duty'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        $rejectedAssignments = DutyAssignment::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->with(['schedule', 'duty'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('assignments.my', compact(
            'pendingAssignments', 
            'acceptedAssignments', 
            'completedAssignments',
            'rejectedAssignments'
        ));
    }

    public function accept(DutyAssignment $assignment)
    {
        $this->authorizeUserAccess($assignment);
        
        if ($assignment->status !== 'pending') {
            return back()->with('error', 'Tugas sudah diproses sebelumnya.');
        }
        
        try {
            $assignment->update([
                'status' => 'accepted',
                'availability_status' => 'available',
                'responded_at' => now(),
            ]);
            
            return redirect()->route('assignments.my')
                ->with('success', 'Anda telah menerima tugas ini. Terima kasih!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menerima tugas: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, DutyAssignment $assignment)
    {
        $this->authorizeUserAccess($assignment);
        
        if ($assignment->status !== 'pending') {
            return back()->with('error', 'Tugas sudah diproses sebelumnya.');
        }
        
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);
        
        try {
            $assignment->update([
                'status' => 'rejected',
                'availability_status' => 'unavailable',
                'rejection_reason' => $request->rejection_reason,
                'unavailable_reason' => $request->rejection_reason,
                'responded_at' => now(),
            ]);
            
            return redirect()->route('assignments.my')
                ->with('info', 'Tugas ditolak. Admin akan mencari pengganti.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menolak tugas: ' . $e->getMessage());
        }
    }

    public function unconfirm(DutyAssignment $assignment)
    {
        $this->authorizeUserAccess($assignment);
        
        if ($assignment->event_date && \Carbon\Carbon::parse($assignment->event_date)->isPast()) {
            return back()->with('error', 'Tidak dapat mengubah konfirmasi karena tanggal event sudah lewat.');
        }
        
        if (!in_array($assignment->status, ['accepted', 'rejected'])) {
            return back()->with('error', 'Status tugas tidak dapat diubah.');
        }
        
        try {
            $assignment->update([
                'status' => 'pending',
                'availability_status' => 'pending',
                'responded_at' => null,
                'rejection_reason' => null,
                'unavailable_reason' => null,
            ]);
            
            return redirect()->route('assignments.my')
                ->with('success', 'Konfirmasi berhasil dibatalkan. Anda dapat memilih ulang.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan konfirmasi: ' . $e->getMessage());
        }
    }

    // ============================================
    // ADMIN & PIC GROUP ROUTES
    // ============================================

    public function index(Request $request)
    {
        $user = auth()->user();
        
        if ($user->isUser()) {
            return redirect()->route('assignments.my')
                ->with('info', 'Anda hanya dapat melihat penugasan Anda sendiri.');
        }
        
        $query = DutyAssignment::with(['schedule', 'duty', 'user']);
        
        if ($this->isPicGroup()) {
            $dutyId = $this->getUserDutyId();
            if (!$dutyId) {
                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak memiliki duty group.');
            }
            $request->merge(['duty_id' => $dutyId]);
        }
        
        if ($request->filled('duty_id')) {
            $query->where('duty_id', $request->duty_id);
            $selectedDuty = Duty::find($request->duty_id);
        } else {
            $selectedDuty = null;
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($qu) use ($search) {
                    $qu->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('duty', function($qu) use ($search) {
                    $qu->where('name', 'like', '%' . $search . '%');
                });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('event_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('event_date', '<=', $request->end_date);
        }
        
        if ($request->filled('schedule_id')) {
            $query->where('schedule_id', $request->schedule_id);
        }
        
        $assignments = $query->orderBy('event_date', 'desc')->paginate(15);
        
        $statuses = [
            'pending' => ['label' => 'Menunggu', 'badge' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fa-clock'],
            'accepted' => ['label' => 'Diterima', 'badge' => 'bg-green-100 text-green-800', 'icon' => 'fa-check-circle'],
            'rejected' => ['label' => 'Ditolak', 'badge' => 'bg-red-100 text-red-800', 'icon' => 'fa-times-circle'],
            'completed' => ['label' => 'Selesai', 'badge' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-check-double'],
            'cancelled' => ['label' => 'Dibatalkan', 'badge' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-ban']
        ];
        
        $schedulesQuery = Schedule::where('status', 'active');
        if ($this->isPicGroup() && $user->schedule_id) {
            $schedulesQuery->where('id', $user->schedule_id);
        }
        $schedules = $schedulesQuery->orderBy('day')->orderBy('time')->get();
        
        $duties = Duty::where('is_active', true)->orderBy('name')->get();
        
        return view('assignments.index', compact('assignments', 'statuses', 'schedules', 'duties', 'selectedDuty'));
    }

    // ============================================
    // TAKE ASSIGNMENT (USER INITIATIVE)
    // ============================================

    public function takeAssignment(Request $request)
    {
        if (!auth()->user()->isUser()) {
            return back()->with('error', 'Hanya user biasa yang bisa mengambil tugas.');
        }
        
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'duty_id' => 'required|exists:duties,id',
            'event_date' => 'required|date|after_or_equal:today',
        ]);
        
        $userId = auth()->user()->id;
        $scheduleId = $request->schedule_id;
        $dutyId = $request->duty_id;
        $eventDate = $request->event_date;
        
        try {
            $schedule = Schedule::find($scheduleId);
            if ($schedule) {
                $selectedDate = new \DateTime($eventDate);
                $dayMap = [
                    'sunday' => 0, 'minggu' => 0,
                    'monday' => 1, 'senin' => 1,
                    'tuesday' => 2, 'selasa' => 2,
                    'wednesday' => 3, 'rabu' => 3,
                    'thursday' => 4, 'kamis' => 4,
                    'friday' => 5, 'jumat' => 5,
                    'saturday' => 6, 'sabtu' => 6,
                ];
                
                $scheduleDay = strtolower($schedule->day);
                $expectedDayOfWeek = $dayMap[$scheduleDay] ?? null;
                
                if ($expectedDayOfWeek !== null && $selectedDate->format('w') != $expectedDayOfWeek) {
                    return back()->with('error', 'Tanggal pelaksanaan harus sesuai dengan hari jadwal (' . ucfirst($schedule->day) . ').');
                }
            }
            
            $hasAssignment = DutyAssignment::where('schedule_id', $scheduleId)
                ->where('user_id', $userId)
                ->whereIn('status', ['accepted', 'pending'])
                ->exists();
            
            if ($hasAssignment) {
                return back()->with('error', 'Anda sudah memiliki tugas pada jadwal ini.');
            }
            
            $currentCount = DutyAssignment::where('schedule_id', $scheduleId)
                ->where('duty_id', $dutyId)
                ->where('status', 'accepted')
                ->count();
            
            $duty = Duty::find($dutyId);
            if (!$duty) {
                return back()->with('error', 'Tugas tidak ditemukan.');
            }
            
            if ($currentCount >= $duty->max_person) {
                session()->flash('warning', '⚠️ Tugas ini sudah penuh (max ' . $duty->max_person . ' orang yang sudah menerima). Namun Anda tetap bisa mengambil.');
            }
            
            $assignment = DutyAssignment::create([
                'schedule_id' => $scheduleId,
                'duty_id' => $dutyId,
                'user_id' => $userId,
                'event_date' => $eventDate,
                'status' => 'pending',
                'availability_status' => 'pending',
                'notes' => 'siap melayani',
            ]);
            
            $schedule = Schedule::find($scheduleId);
            
            return redirect()->route('availability.edit', $assignment->id)
                ->with('success', 'Berhasil mengambil tugas ' . $duty->name . ' untuk ' . ($schedule->name ?? $schedule->day) . '. Menunggu konfirmasi anda.');
            
        } catch (\Exception $e) {
            Log::error('Take assignment error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengambil tugas: ' . $e->getMessage());
        }
    }

    // ============================================
    // CREATE ASSIGNMENT (ADMIN & PIC GROUP)
    // ============================================
    public function create(Request $request)
    {
        $user = auth()->user();
        $isPicGroup = $this->isPicGroup();
        $isAdmin = $this->isAdmin();
        
        if ($isPicGroup) {
            $dutyId = $this->getUserDutyId();
            if (!$dutyId) {
                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak memiliki duty group.');
            }
            
            $request->merge(['duty_id' => $dutyId]);
            session(['selected_duty_id' => $dutyId]);
        } else {
            $this->authorizeAssignment();
        }
        
        $schedulesQuery = Schedule::where('status', 'active');
        
        if ($isPicGroup && $user->schedule_id) {
            $schedulesQuery->where('id', $user->schedule_id);
        }
        
        $schedules = $schedulesQuery
            ->orderByRaw("FIELD(day, 'sabtu', 'minggu')")
            ->orderBy('time')
            ->get();
        
        $dutiesQuery = Duty::where('is_active', true);
        if ($isPicGroup) {
            $dutyId = $this->getUserDutyId();
            if ($dutyId) {
                $dutiesQuery->where('id', $dutyId);
            } else {
                $dutiesQuery->whereRaw('1 = 0');
            }
        }
        $duties = $dutiesQuery->orderBy('name')->get();
        
        $dutyId = $request->query('duty_id') ?? session('selected_duty_id');
        
        if ($dutyId) {
            $selectedDuty = Duty::find($dutyId);
            
            if ($selectedDuty) {
                $petugasQuery = User::where('is_active', true)->with('duty');
                
                if ($isPicGroup) {
                    $userDutyId = $this->getUserDutyId();
                    if ($userDutyId) {
                        $petugasQuery->where('duty_id', $userDutyId);
                    } else {
                        $petugasQuery->whereRaw('1 = 0');
                    }
                } else {
                    $petugasQuery->where('duty_id', $dutyId);
                }
                
                $petugasList = $petugasQuery->orderBy('name')->get();
            } else {
                $petugasList = collect();
                $selectedDuty = null;
            }
        } else {
            $selectedDuty = null;
            $petugasQuery = User::where('is_active', true)->with('duty');
            
            if ($isPicGroup) {
                $userDutyId = $this->getUserDutyId();
                if ($userDutyId) {
                    $petugasQuery->where('duty_id', $userDutyId);
                } else {
                    $petugasQuery->whereRaw('1 = 0');
                }
            }
            
            $petugasList = $petugasQuery->orderBy('name')->get();
        }
        
        $defaultDate = now()->format('Y-m-d');
        $schedule = $schedules->first();
        
        return view('assignments.create', compact(
            'schedules', 
            'duties', 
            'petugasList',
            'defaultDate', 
            'schedule', 
            'selectedDuty',
            'dutyId'
        ));
    }
    
    // ============================================
    // STORE ASSIGNMENT
    // ============================================

    public function store(Request $request)
    {
        $user = auth()->user();
        $isPicGroup = $this->isPicGroup();
        
        // ============================================
        // VALIDASI UNTUK PIC GROUP
        // ============================================
        if ($isPicGroup) {
            $userDutyId = $this->getUserDutyId();
            if (!$userDutyId) {
                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak memiliki duty group.');
            }
            
            // Validasi: duty_id harus sama dengan duty_id user
            if ($request->duty_id != $userDutyId) {
                return back()->with('error', 'Anda hanya dapat membuat penugasan untuk duty group Anda.')->withInput();
            }
            
            // Validasi: user_id harus berupa array
            if (!$request->has('user_id') || !is_array($request->user_id)) {
                return back()->with('error', 'Silakan pilih minimal satu petugas.')->withInput();
            }
            
            // Validasi: semua user yang dipilih harus memiliki duty_id yang sama
            foreach ($request->user_id as $userId) {
                $targetUser = User::find($userId);
                if (!$targetUser) {
                    return back()->with('error', 'User tidak ditemukan.')->withInput();
                }
                if ($targetUser->duty_id != $userDutyId) {
                    return back()->with('error', 'Anda hanya dapat menugaskan ke anggota group Anda.')->withInput();
                }
            }
        } else {
            // Admin: cek akses
            $this->authorizeAssignment();
            
            // Admin: user_id bisa single atau array
            if ($request->has('user_id') && is_array($request->user_id)) {
                // Jika array, validasi minimal 1
                if (count($request->user_id) < 1) {
                    return back()->with('error', 'Silakan pilih minimal satu petugas.')->withInput();
                }
            } else {
                // Jika single, validasi required
                $request->validate([
                    'user_id' => 'required|exists:users,id',
                ]);
            }
        }
        
        // ============================================
        // VALIDASI UMUM
        // ============================================
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'duty_id' => 'required|exists:duties,id',
            'event_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string'
        ]);
        
        // ============================================
        // DETERMINE STATUS
        // ============================================
        // PIC Group: status langsung 'accepted' (confirmed)
        // Admin: status 'pending' (menunggu konfirmasi)
        $status = $isPicGroup ? 'accepted' : 'pending';
        $availabilityStatus = $isPicGroup ? 'available' : 'pending';
        
        try {
            // Validasi tanggal sesuai schedule
            $schedule = Schedule::find($request->schedule_id);
            if ($schedule) {
                $selectedDate = new \DateTime($request->event_date);
                $dayMap = [
                    'sunday' => 0, 'minggu' => 0,
                    'monday' => 1, 'senin' => 1,
                    'tuesday' => 2, 'selasa' => 2,
                    'wednesday' => 3, 'rabu' => 3,
                    'thursday' => 4, 'kamis' => 4,
                    'friday' => 5, 'jumat' => 5,
                    'saturday' => 6, 'sabtu' => 6,
                ];
                
                $scheduleDay = strtolower($schedule->day);
                $expectedDayOfWeek = $dayMap[$scheduleDay] ?? null;
                
                if ($expectedDayOfWeek !== null && $selectedDate->format('w') != $expectedDayOfWeek) {
                    return back()->with('error', 'Tanggal pelaksanaan harus sesuai dengan hari jadwal (' . ucfirst($schedule->day) . ').')->withInput();
                }
            }
            
            // ============================================
            // PROSES USER ID (ARRAY ATAU SINGLE)
            // ============================================
            $userIds = [];
            if ($request->has('user_id')) {
                if (is_array($request->user_id)) {
                    $userIds = $request->user_id;
                } else {
                    $userIds = [$request->user_id];
                }
            }
            
            if (empty($userIds)) {
                return back()->with('error', 'Silakan pilih minimal satu petugas.')->withInput();
            }
            
            $createdCount = 0;
            $errors = [];
            
            foreach ($userIds as $userId) {
                // Cek duplikat
                $exists = DutyAssignment::where('schedule_id', $request->schedule_id)
                    ->where('duty_id', $request->duty_id)
                    ->where('user_id', $userId)
                    ->where('event_date', $request->event_date)
                    ->exists();
                
                if ($exists) {
                    $user = User::find($userId);
                    $errors[] = "User {$user->name} sudah memiliki tugas ini pada tanggal tersebut.";
                    continue;
                }
                
                // Cek user punya tugas lain di jadwal sama
                $userOtherDuty = DutyAssignment::where('user_id', $userId)
                    ->where('event_date', $request->event_date)
                    ->where('schedule_id', $request->schedule_id)
                    ->exists();
                
                if ($userOtherDuty) {
                    $user = User::find($userId);
                    $errors[] = "User {$user->name} sudah memiliki tugas lain pada tanggal dan jadwal yang sama.";
                    continue;
                }
                
                // Buat assignment
                DutyAssignment::create([
                    'schedule_id' => $request->schedule_id,
                    'duty_id' => $request->duty_id,
                    'user_id' => $userId,
                    'event_date' => $request->event_date,
                    'status' => $status,
                    'availability_status' => $availabilityStatus,
                    'responded_at' => $isPicGroup ? now() : null,
                    'notes' => $request->notes,
                ]);
                
                $createdCount++;
            }
            
            session()->forget('selected_duty_id');
            
            // ============================================
            // REDIRECT DENGAN PESAN
            // ============================================
            $message = "✅ {$createdCount} penugasan berhasil dibuat.";
            
            if ($isPicGroup) {
                $message .= " Status langsung <strong>Confirmed</strong>.";
            } else {
                $message .= " Menunggu konfirmasi petugas.";
            }
            
            if (!empty($errors)) {
                $message .= "<br><br>⚠️ Gagal untuk: <br>" . implode('<br>', $errors);
            }
            
            $redirectUrl = route('assignments.index', ['duty_id' => $request->duty_id]);
            
            return redirect($redirectUrl)
                ->with('success', $message);
            
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'Integrity constraint violation')) {
                return back()->with('error', 'Data duplikat! Penugasan ini sudah ada.')->withInput();
            }
            Log::error('Store assignment error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat penugasan: ' . $e->getMessage())->withInput();
        } catch (\Exception $e) {
            Log::error('Store assignment error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat penugasan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(DutyAssignment $assignment)
    {
        if ($this->isPicGroup() && !$this->canAccessAssignment($assignment)) {
            abort(403, 'Anda tidak memiliki akses ke assignment ini.');
        }
        
        $this->authorizeAssignment();
        
        $assignment->load(['schedule', 'duty', 'user', 'user.keuskupan', 'user.gereja']);
        
        $statuses = [
            'pending' => ['label' => 'Menunggu', 'badge' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fa-clock'],
            'accepted' => ['label' => 'Diterima', 'badge' => 'bg-green-100 text-green-800', 'icon' => 'fa-check-circle'],
            'rejected' => ['label' => 'Ditolak', 'badge' => 'bg-red-100 text-red-800', 'icon' => 'fa-times-circle'],
            'completed' => ['label' => 'Selesai', 'badge' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-check-double'],
            'cancelled' => ['label' => 'Dibatalkan', 'badge' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-ban']
        ];

        $liturgi = null;
        if ($assignment->event_date) {
            $liturgi = KalenderLiturgiHari::where('tanggal', $assignment->event_date)->first();
        }
        
        return view('assignments.show', compact('assignment', 'statuses','liturgi'));
    }

    public function edit(DutyAssignment $assignment)
    {
        if ($this->isPicGroup() && !$this->canAccessAssignment($assignment)) {
            abort(403, 'Anda tidak memiliki akses ke assignment ini.');
        }
        
        $this->authorizeAssignment();
        
        $duties = Duty::where('is_active', true)->orderBy('name')->get();
        
        $usersQuery = User::where('is_active', true);
        if ($this->isPicGroup()) {
            $dutyId = $this->getUserDutyId();
            if ($dutyId) {
                $usersQuery->where('duty_id', $dutyId);
            }
        } else {
            $usersQuery->whereNotNull('duty_id');
        }
        $users = $usersQuery->orderBy('name')->get();
        
        $statuses = [
            'pending' => 'Menunggu',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];
        
        return view('assignments.edit', compact('assignment', 'duties', 'users', 'statuses'));
    }

    public function update(Request $request, DutyAssignment $assignment)
    {
        if ($this->isPicGroup() && !$this->canAccessAssignment($assignment)) {
            abort(403, 'Anda tidak memiliki akses ke assignment ini.');
        }
        
        $this->authorizeAssignment();
        
        $request->validate([
            'duty_id' => 'required|exists:duties,id',
            'user_id' => 'required|exists:users,id',
            'event_date' => 'nullable|date',
            'status' => 'required|in:pending,accepted,rejected,completed,cancelled',
            'notes' => 'nullable|string'
        ]);
        
        try {
            if ($request->filled('event_date')) {
                $schedule = Schedule::find($assignment->schedule_id);
                if ($schedule) {
                    $selectedDate = new \DateTime($request->event_date);
                    $dayMap = [
                        'sunday' => 0, 'minggu' => 0,
                        'monday' => 1, 'senin' => 1,
                        'tuesday' => 2, 'selasa' => 2,
                        'wednesday' => 3, 'rabu' => 3,
                        'thursday' => 4, 'kamis' => 4,
                        'friday' => 5, 'jumat' => 5,
                        'saturday' => 6, 'sabtu' => 6,
                    ];
                    
                    $scheduleDay = strtolower($schedule->day);
                    $expectedDayOfWeek = $dayMap[$scheduleDay] ?? null;
                    
                    if ($expectedDayOfWeek !== null && $selectedDate->format('w') != $expectedDayOfWeek) {
                        return back()->with('error', 'Tanggal pelaksanaan harus sesuai dengan hari jadwal (' . ucfirst($schedule->day) . ').')->withInput();
                    }
                }
            }
            
            $exists = DutyAssignment::where('schedule_id', $assignment->schedule_id)
                ->where('duty_id', $request->duty_id)
                ->where('user_id', $request->user_id)
                ->where('event_date', $request->event_date)
                ->where('id', '!=', $assignment->id)
                ->exists();
            
            if ($exists) {
                return back()->with('error', 'Penugasan sudah ada!')->withInput();
            }
            
            $data = [
                'duty_id' => $request->duty_id,
                'user_id' => $request->user_id,
                'status' => $request->status,
                'event_date' => $request->event_date,
                'notes' => $request->notes,
            ];
            
            if ($request->status == 'accepted') {
                $data['availability_status'] = 'available';
            } elseif ($request->status == 'rejected') {
                $data['availability_status'] = 'unavailable';
            } else {
                $data['availability_status'] = 'pending';
            }
            
            if ($request->status != 'pending' && !$assignment->responded_at) {
                $data['responded_at'] = now();
            }
            
            $assignment->update($data);
            
            return redirect()->route('assignments.index')
                ->with('success', 'Penugasan berhasil diupdate');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update penugasan: ' . $e->getMessage());
        }
    }

    public function destroy(DutyAssignment $assignment)
    {
        if ($this->isPicGroup() && !$this->canAccessAssignment($assignment)) {
            abort(403, 'Anda tidak memiliki akses ke assignment ini.');
        }
        
        $this->authorizeAssignment();
        
        try {
            $assignment->delete();
            
            return redirect()->route('assignments.index')
                ->with('success', 'Penugasan berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal hapus penugasan: ' . $e->getMessage());
        }
    }

    public function cancel(DutyAssignment $assignment)
    {
        if ($this->isPicGroup() && !$this->canAccessAssignment($assignment)) {
            abort(403, 'Anda tidak memiliki akses ke assignment ini.');
        }
        
        $this->authorizeAssignment();
        
        try {
            $assignment->update([
                'status' => 'cancelled',
                'availability_status' => 'pending',
                'notes' => 'Dibatalkan oleh admin',
                'responded_at' => now(),
            ]);
            
            return redirect()->route('assignments.index')
                ->with('success', 'Penugasan dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan penugasan: ' . $e->getMessage());
        }
    }

    public function complete(DutyAssignment $assignment)
    {
        if ($this->isPicGroup() && !$this->canAccessAssignment($assignment)) {
            abort(403, 'Anda tidak memiliki akses ke assignment ini.');
        }
        
        $this->authorizeAssignment();
        
        try {
            $assignment->update([
                'status' => 'completed',
                'responded_at' => now(),
            ]);
            
            return redirect()->route('assignments.index')
                ->with('success', 'Penugasan ditandai selesai.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menandai selesai: ' . $e->getMessage());
        }
    }

    public function bulkStatusUpdate(Request $request)
    {
        $this->authorizeAssignment();
        
        $request->validate([
            'assignment_ids' => 'required|array',
            'assignment_ids.*' => 'exists:duty_assignments,id',
            'status' => 'required|in:pending,accepted,rejected,completed,cancelled',
        ]);
        
        try {
            $query = DutyAssignment::whereIn('id', $request->assignment_ids);
            
            if ($this->isPicGroup()) {
                $dutyId = $this->getUserDutyId();
                if ($dutyId) {
                    $query->where('duty_id', $dutyId);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
            
            $count = $query->update([
                'status' => $request->status,
                'responded_at' => $request->status != 'pending' ? now() : null,
            ]);
            
            return redirect()->route('assignments.index')
                ->with('success', "{$count} penugasan berhasil diupdate statusnya.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update bulk: ' . $e->getMessage());
        }
    }

    public function report(Request $request)
    {
        $this->authorizeAssignment();
        
        $query = DutyAssignment::with(['schedule', 'duty', 'user']);
        
        if ($this->isPicGroup()) {
            $dutyId = $this->getUserDutyId();
            if ($dutyId) {
                $query->where('duty_id', $dutyId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('event_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('event_date', '<=', $request->end_date);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $assignments = $query->orderBy('event_date', 'desc')->get();
        
        $summary = [
            'total' => $assignments->count(),
            'pending' => $assignments->where('status', 'pending')->count(),
            'accepted' => $assignments->where('status', 'accepted')->count(),
            'rejected' => $assignments->where('status', 'rejected')->count(),
            'completed' => $assignments->where('status', 'completed')->count(),
            'cancelled' => $assignments->where('status', 'cancelled')->count(),
        ];
        
        return view('assignments.report', compact('assignments', 'summary'));
    }

    // ============================================
    // AUTHORIZATION METHODS
    // ============================================

    private function authorizeAssignment()
    {
        if ($this->isPicGroup()) {
            $dutyId = $this->getUserDutyId();
            if (!$dutyId) {
                abort(403, 'Anda tidak memiliki duty group.');
            }
            return true;
        }
        
        if ($this->isAdmin()) {
            return true;
        }
        
        abort(403, 'Anda tidak memiliki akses.');
    }

    private function authorizeUserAccess(DutyAssignment $assignment)
    {
        $user = auth()->user();
        $isAdmin = $this->isAdmin();
        
        if ($isAdmin) {
            return true;
        }
        
        if ($this->isPicGroup() && $user->duty_id) {
            if ($assignment->duty_id == $user->duty_id) {
                return true;
            }
            abort(403, 'Anda tidak memiliki akses ke assignment ini.');
        }
        
        if ($assignment->user_id === $user->id) {
            return true;
        }
        
        abort(403, 'Anda tidak memiliki akses.');
    }
}