<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $schedules = Schedule::orderByRaw("FIELD(day, 'sabtu', 'minggu')")
            ->orderBy('time')
            ->get();
        
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        return view('schedules.index', compact('schedules', 'hasAccess'));
    }
    
    public function create()
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses untuk menambah jadwal.');
        }
        
        return view('schedules.create');
    }
    
    public function store(Request $request)
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses untuk menambah jadwal.');
        }
        
        $request->validate([
            'day' => 'required|in:sabtu,minggu',
            'time' => 'required|date_format:H:i',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        Schedule::create([
            'day' => $request->day,
            'time' => $request->time . ':00',
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'active',
        ]);
        
        return redirect()->route('schedules.index')
            ->with('success', 'Jadwal berhasil ditambahkan');
    }
    
    public function edit(Schedule $schedule)
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit jadwal.');
        }
        
        return view('schedules.edit', compact('schedule'));
    }
    
    public function update(Request $request, Schedule $schedule)
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit jadwal.');
        }
        
        $request->validate([
            'day' => 'required|in:sabtu,minggu',
            'time' => 'required|date_format:H:i',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);
        
        $schedule->update([
            'day' => $request->day,
            'time' => $request->time . ':00',
            'name' => $request->name,
            'status' => $request->status,
            'description' => $request->description,
        ]);
        
        return redirect()->route('schedules.index')
            ->with('success', 'Jadwal berhasil diupdate');
    }
    
    public function destroy(Schedule $schedule)
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus jadwal.');
        }
        
        $schedule->delete();
        
        return redirect()->route('schedules.index')
            ->with('success', 'Jadwal berhasil dihapus');
    }
    
    public function toggleStatus(Schedule $schedule)
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status jadwal.');
        }
        
        $schedule->update([
            'status' => $schedule->status == 'active' ? 'inactive' : 'active'
        ]);
        
        $status = $schedule->status == 'active' ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Jadwal {$schedule->name} berhasil {$status}");
    }
}