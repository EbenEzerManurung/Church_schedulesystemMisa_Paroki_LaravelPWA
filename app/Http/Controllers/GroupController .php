<?php
// app/Http/Controllers/GroupController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Duty;
use App\Models\Schedule;
use App\Models\DutyAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:pic_group']);
    }

    /**
     * Dashboard PIC Group
     */
    public function dashboard()
    {
        $user = Auth::user();
        $duty = $user->duty;
        
        $stats = [
            'total_users' => User::where('duty_id', $user->duty_id)->count(),
            'total_schedules' => Schedule::whereHas('dutyAssignments', function($q) use ($user) {
                $q->where('duty_id', $user->duty_id);
            })->count(),
            'pending' => DutyAssignment::where('duty_id', $user->duty_id)->where('status', 'pending')->count(),
            'completed' => DutyAssignment::where('duty_id', $user->duty_id)->where('status', 'completed')->count(),
        ];

        $groupUsers = User::where('duty_id', $user->duty_id)->where('id', '!=', $user->id)->get();
        
        $upcomingSchedules = Schedule::whereHas('dutyAssignments', function($q) use ($user) {
            $q->where('duty_id', $user->duty_id);
        })->where('date', '>=', now())->orderBy('date')->limit(10)->get();

        return view('group.dashboard', compact('user', 'duty', 'stats', 'groupUsers', 'upcomingSchedules'));
    }

    /**
     * List users in group
     */
    public function users(Request $request)
    {
        $user = Auth::user();
        
        $query = User::where('duty_id', $user->duty_id)->where('id', '!=', $user->id);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('name')->paginate(15);
        $duty = $user->duty;

        return view('group.users', compact('users', 'duty'));
    }

    /**
     * List schedules for group
     */
    public function schedules(Request $request)
    {
        $user = Auth::user();
        
        $query = Schedule::whereHas('dutyAssignments', function($q) use ($user) {
            $q->where('duty_id', $user->duty_id);
        });

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $schedules = $query->orderBy('date', 'desc')->paginate(15);
        return view('group.schedules', compact('schedules'));
    }

    /**
     * List duties for group
     */
    public function duties()
    {
        $user = Auth::user();
        $duty = $user->duty;
        $duties = Duty::where('id', $user->duty_id)->get();
        return view('group.duties', compact('duties', 'duty'));
    }

    /**
     * List assignments for group
     */
    public function assignments(Request $request)
    {
        $user = Auth::user();
        
        $query = DutyAssignment::where('duty_id', $user->duty_id)
                    ->with(['user', 'schedule', 'replacementUser']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assignments = $query->orderBy('created_at', 'desc')->paginate(15);
        $users = User::where('duty_id', $user->duty_id)->where('id', '!=', $user->id)->get();
        $duty = $user->duty;

        return view('group.assignments', compact('assignments', 'users', 'duty'));
    }

    /**
     * Show create assignment form
     */
    public function createAssignment(Request $request)
    {
        $user = Auth::user();
        $duty = $user->duty;
        $users = User::where('duty_id', $user->duty_id)->where('id', '!=', $user->id)->get();
        $schedules = Schedule::whereHas('dutyAssignments', function($q) use ($user) {
            $q->where('duty_id', $user->duty_id);
        })->where('date', '>=', now())->orderBy('date')->get();

        return view('group.create-assignment', compact('duty', 'users', 'schedules'));
    }

    /**
     * Store assignment
     */
    public function storeAssignment(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $targetUser = User::find($request->user_id);
        
        if ($targetUser->duty_id !== $user->duty_id) {
            return back()->with('error', 'User tidak berada dalam duty group Anda.');
        }

        $existing = DutyAssignment::where('schedule_id', $request->schedule_id)
                                  ->where('duty_id', $user->duty_id)
                                  ->first();

        if ($existing) {
            return back()->with('error', 'Sudah ada penugasan untuk jadwal ini.');
        }

        DutyAssignment::create([
            'schedule_id' => $request->schedule_id,
            'duty_id' => $user->duty_id,
            'user_id' => $request->user_id,
            'status' => 'pending',
            'notes' => $request->notes,
            'event_date' => Schedule::find($request->schedule_id)->date ?? now(),
        ]);

        return redirect()->route('group.assignments')->with('success', 'Penugasan berhasil dibuat.');
    }

    /**
     * Approve assignment
     */
    public function approveAssignment(Request $request, DutyAssignment $assignment)
    {
        $user = Auth::user();
        
        if ($assignment->duty_id !== $user->duty_id) {
            abort(403, 'Anda tidak memiliki akses ke assignment ini.');
        }

        if ($assignment->status !== 'pending') {
            return back()->with('error', 'Assignment ini sudah diproses.');
        }

        $assignment->update([
            'status' => 'accepted',
            'responded_at' => now(),
            'notes' => $request->notes ?? $assignment->notes,
        ]);

        return back()->with('success', 'Assignment berhasil disetujui.');
    }

    /**
     * Reject assignment
     */
    public function rejectAssignment(Request $request, DutyAssignment $assignment)
    {
        $user = Auth::user();
        
        if ($assignment->duty_id !== $user->duty_id) {
            abort(403, 'Anda tidak memiliki akses ke assignment ini.');
        }

        if ($assignment->status !== 'pending') {
            return back()->with('error', 'Assignment ini sudah diproses.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        $assignment->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'responded_at' => now(),
        ]);

        return back()->with('success', 'Assignment berhasil ditolak.');
    }

    /**
     * Delete assignment
     */
    public function deleteAssignment(DutyAssignment $assignment)
    {
        $user = Auth::user();
        
        if ($assignment->duty_id !== $user->duty_id) {
            abort(403, 'Anda tidak memiliki akses ke assignment ini.');
        }

        $assignment->delete();
        return redirect()->route('group.assignments')->with('success', 'Assignment berhasil dihapus.');
    }

    /**
     * Availability management
     */
    public function availability()
    {
        $user = Auth::user();
        $users = User::where('duty_id', $user->duty_id)->where('id', '!=', $user->id)->get();
        return view('group.availability', compact('users'));
    }

    /**
     * Update availability
     */
    public function updateAvailability(Request $request, User $targetUser)
    {
        $user = Auth::user();
        
        if ($targetUser->duty_id !== $user->duty_id) {
            abort(403, 'Anda tidak memiliki akses ke user ini.');
        }

        $request->validate([
            'availability_status' => 'required|in:available,unavailable,pending',
            'unavailable_reason' => 'nullable|string|required_if:availability_status,unavailable',
        ]);

        DutyAssignment::where('user_id', $targetUser->id)
                     ->where('duty_id', $user->duty_id)
                     ->update([
                         'availability_status' => $request->availability_status,
                         'unavailable_reason' => $request->unavailable_reason,
                         'availability_updated_at' => now(),
                     ]);

        return back()->with('success', 'Ketersediaan user berhasil diupdate.');
    }

    /**
     * Reports for group
     */
    public function reports()
    {
        $user = Auth::user();
        
        $stats = [
            'total' => DutyAssignment::where('duty_id', $user->duty_id)->count(),
            'pending' => DutyAssignment::where('duty_id', $user->duty_id)->where('status', 'pending')->count(),
            'accepted' => DutyAssignment::where('duty_id', $user->duty_id)->where('status', 'accepted')->count(),
            'rejected' => DutyAssignment::where('duty_id', $user->duty_id)->where('status', 'rejected')->count(),
            'completed' => DutyAssignment::where('duty_id', $user->duty_id)->where('status', 'completed')->count(),
        ];

        return view('group.reports', compact('stats'));
    }
}