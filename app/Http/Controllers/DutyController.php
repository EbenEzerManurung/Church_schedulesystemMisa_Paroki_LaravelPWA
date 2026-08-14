<?php

namespace App\Http\Controllers;

use App\Models\Duty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Imports\DutyImport;
use App\Exports\DutyExport;
use App\Exports\DutyTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class DutyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
{
    $user = auth()->user();
    $query = Duty::query();
    
    // ============================================
    // FILTER UNTUK PIC GROUP
    // ============================================
    if ($user->level_akses === 'pic_group') {
        // PIC Group hanya melihat duty_id yang sama dengan user
        if ($user->duty_id) {
            $query->where('id', $user->duty_id);
        } else {
            // Jika PIC Group tidak punya duty_id, tidak tampil apa-apa
            $query->whereRaw('1 = 0');
        }
    }
    
    // Filter search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('code', 'like', '%' . $search . '%')
              ->orWhere('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }
    
    // Filter status
    if ($request->filled('status')) {
        $query->where('is_active', $request->status == 'active');
    }
    
    $duties = $query->orderBy('code')->paginate(15);
    
    // Cek akses untuk tombol tambah/edit/hapus
    $hasAccess = $user->isSuperAdmin() || $user->isAdminKeuskupan() || $user->isAdminGereja();
    
    return view('duties.index', compact('duties', 'hasAccess'));
}
    
    public function create()
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses untuk menambah tugas.');
        }
        
        $generatedCode = Duty::generateUniqueCode();
        
        return view('duties.create', compact('generatedCode'));
    }
    
    public function store(Request $request)
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses untuk menambah tugas.');
        }
        
        $request->validate([
            'code' => 'nullable|string|max:20|unique:duties,code',
            'name' => 'required|string|max:100|unique:duties,name',
            'description' => 'nullable|string',
            'min_person' => 'required|integer|min:0',
            'max_person' => 'nullable|integer|min:0|gte:min_person',
            'is_active' => 'boolean'
        ]);
        
        $duty = Duty::create([
            'code' => $request->code ?: Duty::generateUniqueCode(),
            'name' => $request->name,
            'description' => $request->description,
            'min_person' => $request->min_person ?? 1,
            'max_person' => $request->max_person,
            'is_active' => $request->is_active ?? true,
        ]);
        
        return redirect()->route('duties.index')
            ->with('success', 'Tugas pelayanan "' . $duty->name . '" (Kode: ' . $duty->code . ') berhasil ditambahkan');
    }

    /**
     * Display the specified duty.
     */
    public function show(Duty $duty)
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        // Hitung statistik
        $totalTerdaftar = $duty->petugas_count;
        $totalAktif = $duty->petugas_aktif_count ?? $totalTerdaftar;
        $ketersediaan = $duty->ketersediaan_status;
        
        return view('duties.show', compact('duty', 'hasAccess', 'totalTerdaftar', 'totalAktif', 'ketersediaan'));
    }

    public function edit(Duty $duty)
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan() || auth()->user()->isAdminGereja();
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit tugas.');
        }
        
        return view('duties.edit', compact('duty'));
    }
    
    public function update(Request $request, Duty $duty)
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan()|| auth()->user()->isAdminGereja();
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit tugas.');
        }
        
        $request->validate([
            'code' => 'required|string|max:20|unique:duties,code,' . $duty->id,
            'name' => 'required|string|max:100|unique:duties,name,' . $duty->id,
            'description' => 'nullable|string',
            'min_person' => 'required|integer|min:0',
            'max_person' => 'nullable|integer|min:0|gte:min_person',
            'is_active' => 'boolean'
        ]);
        
        $duty->update([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'min_person' => $request->min_person ?? 1,
            'max_person' => $request->max_person,
            'is_active' => $request->is_active ?? true,
        ]);
        
        return redirect()->route('duties.index')
            ->with('success', 'Tugas pelayanan "' . $duty->name . '" berhasil diupdate');
    }

    public function destroy(Duty $duty)
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus tugas.');
        }
        
        $duty->delete();
        
        return redirect()->route('duties.index')
            ->with('success', 'Tugas pelayanan "' . $duty->name . '" berhasil dihapus');
    }
    
    public function toggleStatus(Duty $duty)
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status tugas.');
        }
        
        $duty->update(['is_active' => !$duty->is_active]);
        $status = $duty->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return back()->with('success', "Tugas pelayanan \"{$duty->name}\" berhasil {$status}");
    }

    public function showExportForm()
    {
        $duties = Duty::paginate(10);
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        return view('duties.export', compact('duties', 'hasAccess'));
    }

    public function exportAll()
    {
        try {
            $duties = Duty::orderBy('code')->get();
            $fileName = 'data_tugas_pelayanan_' . date('Y-m-d_His') . '.xlsx';
            return Excel::download(new DutyExport($duties), $fileName);
        } catch (\Exception $e) {
            return redirect()->route('duties.index')
                ->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }

    public function exportFiltered(Request $request)
    {
        try {
            $query = Duty::query();
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('code', 'like', '%' . $search . '%')
                      ->orWhere('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            }
            
            if ($request->filled('status') && $request->status != '') {
                $query->where('is_active', $request->status == 'active');
            }
            
            $duties = $query->orderBy('code')->get();
            
            if ($duties->isEmpty()) {
                return redirect()->route('duties.index')
                    ->with('error', 'Tidak ada data yang sesuai dengan filter');
            }
            
            $format = $request->get('format', 'xlsx');
            $fileName = 'data_tugas_pelayanan_filtered_' . date('Y-m-d_His');
            
            if ($format === 'csv') {
                $fileName .= '.csv';
                return Excel::download(new DutyExport($duties), $fileName, \Maatwebsite\Excel\Excel::CSV);
            }
            
            $fileName .= '.xlsx';
            return Excel::download(new DutyExport($duties), $fileName);
            
        } catch (\Exception $e) {
            return redirect()->route('duties.index')
                ->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }

    public function showImportForm()
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses untuk import tugas.');
        }
        
        return view('duties.import');
    }

    public function import(Request $request)
    {
        $hasAccess = auth()->user()->isSuperAdmin() || auth()->user()->isAdminKeuskupan();
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses untuk import tugas.');
        }
        
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            DB::beginTransaction();
            
            $import = new DutyImport();
            Excel::import($import, $request->file('file'));
            
            DB::commit();
            
            $successCount = $import->getSuccessCount();
            $failures = $import->getFailures();
            
            $message = "Berhasil mengimport {$successCount} data tugas pelayanan.";
            
            if (!empty($failures)) {
                session()->flash('import_failures', $failures);
                $message .= " Terdapat " . count($failures) . " data yang gagal diimport.";
            }
            
            return redirect()->route('duties.index')
                ->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        try {
            return Excel::download(new DutyTemplateExport(), 'template_import_tugas.xlsx');
        } catch (\Exception $e) {
            return redirect()->route('duties.index')
                ->with('error', 'Gagal mendownload template: ' . $e->getMessage());
        }
    }
}