<?php

namespace App\Http\Controllers;

use App\Models\DutyAssignment;
use App\Models\Schedule;
use App\Models\Duty;
use App\Models\User;
use App\Models\KalenderLiturgiHari;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AvailabilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of availability.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = DutyAssignment::with(['schedule', 'duty', 'user']);
        
        // Cek role user
        $isUser = $user->level_akses === 'user';
        if ($isUser) {
            $query->where('user_id', $user->id);
        }
        
        // Search
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
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by availability status
        if ($request->filled('availability_status')) {
            $query->where('availability_status', $request->availability_status);
        }
        
        // Filter by tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('event_date', $request->tanggal);
        }
        
        // Filter by bulan dan tahun
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereYear('event_date', $request->tahun)
                  ->whereMonth('event_date', $request->bulan);
        }
        
        $assignments = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Data untuk filter
        $statusList = ['pending', 'accepted', 'rejected', 'completed', 'cancelled'];
        $availabilityStatusList = ['available', 'unavailable', 'pending'];
        $bulanList = $this->getBulanList();
        $tahunList = range(date('Y') - 1, date('Y') + 1);
        
        return view('availability.index', compact('assignments', 'statusList', 'availabilityStatusList', 'bulanList', 'tahunList'));
    }

    /**
     * Show detail availability dengan data liturgi dari event_date
     */
    public function show($id)
    {
        // Cari assignment dengan relasi
        $assignment = DutyAssignment::with([
            'schedule', 
            'duty', 
            'user', 
            'user.keuskupan', 
            'user.gereja'
        ])->findOrFail($id);
        
        // Authorize access
        $this->authorizeAccess($assignment);
        
        // Ambil data liturgi berdasarkan event_date (tanggal penugasan)
        $liturgi = null;
        if ($assignment->event_date) {
            $liturgi = KalenderLiturgiHari::where('tanggal', $assignment->event_date)->first();
            
            // Debug (opsional - hapus setelah berhasil)
            // \Log::info('Event Date: ' . $assignment->event_date);
            // \Log::info('Liturgi ditemukan: ' . ($liturgi ? 'Ya' : 'Tidak'));
        }
        
        return view('availability.show', compact('assignment', 'liturgi'));
    }

    /**
     * Edit availability (konfirmasi ketersediaan)
     */
    // app/Http/Controllers/AvailabilityController.php

public function edit($id)
{
    $assignment = DutyAssignment::with(['schedule', 'duty', 'user'])->findOrFail($id);
    $this->authorizeAccess($assignment);
    
    // Validasi data relasi
    if (!$assignment->schedule) {
        return redirect()->route('availability.index')
            ->with('error', 'Data jadwal tidak ditemukan.');
    }
    
    // ===== PERBAIKAN: Izinkan edit jika status pending, accepted, atau rejected =====
    // Dan event_date belum lewat
    $allowedStatuses = ['pending', 'accepted', 'rejected'];
    
    if (!in_array($assignment->status, $allowedStatuses)) {
        return redirect()->route('availability.index')
            ->with('error', 'Penugasan ini sudah ' . ($assignment->status_label ?? $assignment->status) . ' dan tidak dapat diubah lagi.');
    }
    
    // Cek apakah event_date sudah lewat
    if ($assignment->event_date && \Carbon\Carbon::parse($assignment->event_date)->isPast()) {
        return redirect()->route('availability.index')
            ->with('error', 'Tidak dapat mengubah karena tanggal penugasan sudah lewat.');
    }
    
    // Ambil daftar user yang bisa dijadikan pengganti
    $availableUsers = User::where('is_active', true)
        ->where('id', '!=', $assignment->user_id)
        ->orderBy('name')
        ->get();
    
    // Ambil data liturgi berdasarkan event_date
    $liturgi = null;
    if ($assignment->event_date) {
        $liturgi = KalenderLiturgiHari::where('tanggal', $assignment->event_date)->first();
    }
    
    return view('availability.edit', compact('assignment', 'availableUsers', 'liturgi'));
}

    /**
     * Update availability (konfirmasi ketersediaan)
     */
    // app/Http/Controllers/AvailabilityController.php

// app/Http/Controllers/AvailabilityController.php

public function update(Request $request, $id)
{
    $assignment = DutyAssignment::findOrFail($id);
    $this->authorizeAccess($assignment);
    
    $request->validate([
        'availability_status' => 'required|in:available,unavailable,pending',
        'unavailable_reason' => 'nullable|string|max:500',
        'notes' => 'nullable|string|max:500',
    ]);
    
    try {
        DB::beginTransaction();
        
        // Cek apakah event_date sudah lewat
        if ($assignment->event_date && \Carbon\Carbon::parse($assignment->event_date)->isPast()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat mengubah karena tanggal penugasan sudah lewat.');
        }
        
        $updateData = [
            'availability_status' => $request->availability_status,
            'availability_updated_at' => now(),
            'notes' => $request->notes,
        ];
        
        // Set status berdasarkan pilihan
        if ($request->availability_status == 'available') {
            $updateData['status'] = DutyAssignment::STATUS_ACCEPTED;
            $updateData['unavailable_reason'] = null;
            $message = 'Anda telah mengubah ke bersedia. Terima kasih!';
        } elseif ($request->availability_status == 'unavailable') {
            if (empty($request->unavailable_reason)) {
                return redirect()->back()
                    ->with('error', 'Silakan isi alasan mengapa Anda tidak bersedia.')
                    ->withInput();
            }
            $updateData['status'] = DutyAssignment::STATUS_REJECTED;
            $updateData['unavailable_reason'] = $request->unavailable_reason;
            $message = 'Anda telah mengubah ke tidak bersedia. Admin akan mencari pengganti.';
        } else {
            // Pending / Menunggu
            $updateData['status'] = DutyAssignment::STATUS_PENDING;
            $updateData['unavailable_reason'] = null;
            $updateData['responded_at'] = null;
            $message = 'Status dikembalikan ke menunggu. Silakan konfirmasi nanti.';
        }
        
        $assignment->update($updateData);
        
        DB::commit();
        
        return redirect()->route('availability.index')
            ->with('success', $message);
            
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating availability: ' . $e->getMessage());
        
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Calendar view for availability
     */
    public function calendar(Request $request)
    {
        $user = auth()->user();
        
        $query = DutyAssignment::with(['schedule', 'duty', 'user']);
        
        $isUser = $user->level_akses === 'user';
        if ($isUser) {
            $query->where('user_id', $user->id);
        }
        
        // Filter by bulan dan tahun berdasarkan event_date
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            $query->whereYear('event_date', $tahun)
                  ->whereMonth('event_date', $bulan);
        }
        
        $assignments = $query->get();
        
        $events = $assignments->map(function($assignment) {
            // Gunakan event_date dari duty_assignments
            $date = $assignment->event_date 
                    ? $assignment->event_date->format('Y-m-d')
                    : now()->format('Y-m-d');
                    
            $title = ($assignment->duty ? $assignment->duty->name : 'Tugas') . ' - ' . 
                     ($assignment->user ? $assignment->user->name : 'Petugas');
            
            // Determine color based on status
            $color = '#ef4444'; // red default
            if ($assignment->status == 'accepted') {
                $color = '#22c55e'; // green
            } elseif ($assignment->status == 'pending') {
                $color = '#eab308'; // yellow
            } elseif ($assignment->status == 'completed') {
                $color = '#3b82f6'; // blue
            } elseif ($assignment->status == 'rejected') {
                $color = '#ef4444'; // red
            }
            
            return [
                'id' => $assignment->id,
                'title' => $title,
                'start' => $date,
                'url' => route('availability.show', $assignment->id),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'description' => $assignment->notes,
                'status' => $assignment->status,
            ];
        });
        
        // Data untuk filter
        $bulanList = $this->getBulanList();
        $tahunList = range(date('Y') - 1, date('Y') + 1);
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        
        return view('availability.calendar', compact('events', 'bulanList', 'tahunList', 'bulan', 'tahun'));
    }

    /**
     * Get liturgi by date for AJAX/API
     */
    public function getLiturgiByDate(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date'
        ]);
        
        $liturgi = KalenderLiturgiHari::where('tanggal', $request->tanggal)->first();
        
        if (!$liturgi) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Data liturgi tidak ditemukan untuk tanggal tersebut'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $liturgi->id,
                'keterangan_hari' => $liturgi->keterangan_hari,
                'warna_liturgi' => $liturgi->warna_liturgi,
                'warna_badge' => $liturgi->warna_liturgi_badge,
                'bacaan1' => $liturgi->bacaan1,
                'mazmur_tanggapan' => $liturgi->mazmur_tanggapan,
                'bait_pengantarinjil' => $liturgi->bait_pengantarinjil,
                'bacaan_injil' => $liturgi->bacaan_injil,
                'catatan' => $liturgi->catatan,
                'is_active' => $liturgi->is_active,
            ]
        ]);
    }

    /**
     * Get liturgi for a specific assignment based on event_date
     */
    public function getLiturgiForAssignment($id)
    {
        $assignment = DutyAssignment::with(['schedule'])->findOrFail($id);
        $this->authorizeAccess($assignment);
        
        $liturgi = null;
        if ($assignment->event_date) {
            $liturgi = KalenderLiturgiHari::where('tanggal', $assignment->event_date)->first();
        }
        
        if (!$liturgi) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Data liturgi tidak ditemukan untuk tanggal penugasan ini'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $liturgi
        ]);
    }

    /**
     * Get bulan list
     */
    private function getBulanList(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    }

    /**
     * Authorize access untuk user
     */
    private function authorizeAccess(DutyAssignment $assignment)
    {
        $user = auth()->user();
        
        // Cek apakah admin (super_admin, admin_keuskupan, admin_gereja)
        $isAdmin = in_array($user->level_akses, ['super_admin', 'admin_keuskupan', 'admin_gereja']);
        
        if ($isAdmin) {
            return true;
        }
        
        // Cek apakah pemilik assignment
        if ($user->level_akses === 'user' && $assignment->user_id == $user->id) {
            return true;
        }
        
        abort(403, 'Anda tidak memiliki akses ke data ini.');
    }

    /**
     * Authorize access untuk admin saja
     */
    private function authorizeAdminAccess()
    {
        $user = auth()->user();
        
        $isAdmin = in_array($user->level_akses, ['super_admin', 'admin_keuskupan', 'admin_gereja']);
        
        if (!$isAdmin) {
            abort(403, 'Anda tidak memiliki akses sebagai administrator.');
        }
        
        return true;
    }
}