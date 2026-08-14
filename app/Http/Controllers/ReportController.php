<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\DutyAssignment;
use App\Models\Substitution;
use App\Helpers\ExportHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
       
    }
    
    public function generate(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);
        
        $query = DutyAssignment::with(['schedule.service', 'duty', 'user', 'replacementUser']);
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->where('date', '>=', $request->date_from);
            });
        }
        
        if ($request->filled('date_to')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->where('date', '<=', $request->date_to);
            });
        }
        
        $assignments = $query->orderBy('created_at', 'desc')->get();
        
        // Statistics
        $stats = [
            'total_assignments' => $assignments->count(),
            'total_available' => $assignments->where('availability_status', 'available')->count(),
            'total_unavailable' => $assignments->where('availability_status', 'unavailable')->count(),
            'total_replaced' => $assignments->where('availability_status', 'replaced')->count(),
            'total_pending' => $assignments->where('availability_status', 'pending')->count(),
            'total_users' => $assignments->groupBy('user_id')->count(),
            'total_duties' => $assignments->groupBy('duty_id')->count(),
        ];
        
        // Data for table
        $data = $assignments->map(function($assignment) {
            return [
                'Tanggal Ibadah' => $assignment->schedule->date ?? '-',
                'Ibadah' => $assignment->schedule->service->name ?? '-',
                'Tugas' => $assignment->duty->name ?? '-',
                'Petugas' => $assignment->user->name ?? '-',
                'Status Ketersediaan' => $this->getStatusText($assignment->availability_status),
                'Alasan (Jika Tidak Bersedia)' => $assignment->unavailable_reason ?? '-',
                'Usulan Pengganti' => $assignment->replacementUser->name ?? '-',
                'Status Penugasan' => ucfirst($assignment->status),
                'Dibuat Pada' => $assignment->created_at ? $assignment->created_at->format('d/m/Y H:i') : '-',
            ];
        });
        
        // Filter params for view
        $filters = [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ];
        
        // Export Excel jika ada parameter export
        if ($request->has('export')) {
            $filename = 'laporan_pelayanan_' . date('Y-m-d');
            if ($request->filled('date_from') || $request->filled('date_to')) {
                $filename .= '_' . ($request->date_from ?? 'awal') . '_sd_' . ($request->date_to ?? 'akhir');
            }
            return ExportHelper::toExcel($data, $filename);
        }
        
        return view('reports.result', compact('data', 'stats', 'filters', 'assignments'));
    }
    
    public function exportAll()
    {
        $assignments = DutyAssignment::with(['schedule.service', 'duty', 'user', 'replacementUser'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $data = $assignments->map(function($assignment) {
            return [
                'Tanggal Ibadah' => $assignment->schedule->date ?? '-',
                'Ibadah' => $assignment->schedule->service->name ?? '-',
                'Tugas' => $assignment->duty->name ?? '-',
                'Petugas' => $assignment->user->name ?? '-',
                'Status Ketersediaan' => $this->getStatusText($assignment->availability_status),
                'Alasan (Jika Tidak Bersedia)' => $assignment->unavailable_reason ?? '-',
                'Usulan Pengganti' => $assignment->replacementUser->name ?? '-',
                'Status Penugasan' => ucfirst($assignment->status),
                'Dibuat Pada' => $assignment->created_at ? $assignment->created_at->format('d/m/Y H:i') : '-',
            ];
        });
        
        return ExportHelper::toExcel($data, 'laporan_semua_data_' . date('Y-m-d'));
    }
    
    private function getStatusText($status)
    {
        return match($status) {
            'pending' => 'Menunggu Konfirmasi',
            'available' => 'Bersedia',
            'unavailable' => 'Tidak Bersedia',
            'replaced' => 'Digantikan',
            default => ucfirst($status)
        };
    }
}