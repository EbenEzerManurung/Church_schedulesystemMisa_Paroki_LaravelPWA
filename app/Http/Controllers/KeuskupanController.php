<?php

namespace App\Http\Controllers;

use App\Models\Keuskupan;
use App\Models\Gereja;
use App\Imports\KeuskupanImport;
use App\Exports\KeuskupanExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KeuskupanTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeuskupanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            $keuskupans = Keuskupan::withCount('gerejas')->paginate(15);
        } elseif ($user->isAdminKeuskupan()) {
            $keuskupans = Keuskupan::where('id', $user->keuskupan_id)
                ->withCount('gerejas')
                ->paginate(15);
        } else {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        return view('keuskupans.index', compact('keuskupans'));
    }
    
    public function create()
    {
        $this->authorizeCreate();
        return view('keuskupans.create');
    }
    
    public function store(Request $request)
    {
        $this->authorizeCreate();
        
        $request->validate([
            'name' => 'required|string|max:255|unique:keuskupans,name',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        try {
            // Generate kode otomatis
            $code = Keuskupan::generateCode($request->name);
            
            $keuskupan = Keuskupan::create([
                'code' => $code,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'description' => $request->description,
                'is_active' => true,
            ]);
            
            DB::commit();
            
            return redirect()->route('keuskupans.index')
                ->with('success', "Keuskupan {$keuskupan->name} berhasil ditambahkan. Kode: {$code}");
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menambahkan keuskupan: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $keuskupan = $this->findKeuskupan($id);
        $statistics = [
            'total_churches' => $keuskupan->gerejas()->count(),
            'active_churches' => $keuskupan->gerejas()->where('is_active', true)->count(),
            'total_users' => $keuskupan->users()->count(),
            'total_priests' => $keuskupan->users()->where('level_akses', 'admin_gereja')->count(),
        ];
        
        return view('keuskupans.show', compact('keuskupan', 'statistics'));
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        $this->authorizeCreate();
        return view('keuskupans.import');
    }
    
    /**
     * Handle file import
     */
    public function import(Request $request)
    {
        $this->authorizeCreate();
        
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            DB::beginTransaction();
            
            $import = new KeuskupanImport();
            Excel::import($import, $request->file('file'));
            
            DB::commit();
            
            $successCount = $import->getSuccessCount();
            $failures = $import->getFailures();
            
            $message = "Berhasil mengimport {$successCount} data keuskupan.";
            
            if (!empty($failures)) {
                session()->flash('import_failures', $failures);
                $message .= " Terdapat " . count($failures) . " data yang gagal diimport.";
            }
            
            return redirect()->route('keuskupans.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Show export form with preview
     */
    public function showExportForm()
    {
        $keuskupans = Keuskupan::withCount('gerejas')->paginate(10);
        return view('keuskupans.export', compact('keuskupans'));
    }

    /**
     * Export all data
     */
    public function exportAll()
    {
        try {
            $fileName = 'data_keuskupan_' . date('Y-m-d_His') . '.xlsx';
            return Excel::download(new KeuskupanExport(), $fileName);
        } catch (\Exception $e) {
            return redirect()->route('keuskupans.index')
                ->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }

    /**
     * Export filtered data
     */
    public function exportFiltered(Request $request)
    {
        try {
            $query = Keuskupan::withCount('gerejas');
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('code', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            }
            
            if ($request->filled('status') && $request->status != '') {
                $query->where('is_active', $request->status);
            }
            
            $keuskupans = $query->get();
            
            if ($keuskupans->isEmpty()) {
                return redirect()->route('keuskupans.index')
                    ->with('error', 'Tidak ada data yang sesuai dengan filter');
            }
            
            $format = $request->get('format', 'xlsx');
            $fileName = 'data_keuskupan_filtered_' . date('Y-m-d_His');
            
            if ($format === 'csv') {
                $fileName .= '.csv';
                return Excel::download(new KeuskupanExport($keuskupans), $fileName, \Maatwebsite\Excel\Excel::CSV);
            }
            
            $fileName .= '.xlsx';
            return Excel::download(new KeuskupanExport($keuskupans), $fileName);
            
        } catch (\Exception $e) {
            return redirect()->route('keuskupans.index')
                ->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }

    /**
     * Download template import
     */
/**
 * Download template import
 */
/**
 * Download template import (Excel .xlsx)
 */
public function downloadTemplate()
{
    try {
        // Buat data template
        $data = [
            ['kode', 'nama_keuskupan', 'email', 'telepon', 'deskripsi', 'status'],
            ['KSK001', 'Keuskupan Agung Jakarta', 'info@keuskupanjakarta.or.id', '021-1234567', 'Keuskupan Agung Jakarta adalah keuskupan metropolitan...', 'Aktif'],
            ['KSK002', 'Keuskupan Bandung', 'keuskupan@bandung.or.id', '022-7654321', 'Deskripsi Keuskupan Bandung', 'Aktif'],
            ['', '', '', '', 'Catatan:', ''],
            ['', '', '', '', '- Kolom kode dan nama_keuskupan WAJIB diisi', ''],
            ['', '', '', '', '- Email harus format yang valid', ''],
            ['', '', '', '', '- Status: Aktif atau Nonaktif', ''],
        ];
        
        // Gunakan Excel::download dengan collection
        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\ShouldAutoSize, \Maatwebsite\Excel\Concerns\WithStyles {
            private $data;
            
            public function __construct($data)
            {
                $this->data = $data;
            }
            
            public function array(): array
            {
                // Skip headers untuk data (ambil dari baris 2 sampai akhir)
                return array_slice($this->data, 1);
            }
            
            public function headings(): array
            {
                // Header dari baris pertama
                return $this->data[0];
            }
            
            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                return [
                    // Header bold
                    1 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']]],
                    // Wajib diisi
                    'A2:B2' => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']]],
                ];
            }
        }, 'template_import_keuskupan.xlsx');
        
    } catch (\Exception $e) {
        return redirect()->route('keuskupans.index')
            ->with('error', 'Gagal mendownload template: ' . $e->getMessage());
    }
}
    
    public function edit($id)
    {
        $this->authorizeEdit();
        $keuskupan = $this->findKeuskupan($id);
        return view('keuskupans.edit', compact('keuskupan'));
    }
    
    public function update(Request $request, $id)
    {
        $this->authorizeEdit();
        $keuskupan = $this->findKeuskupan($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:keuskupans,name,' . $id,
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $keuskupan->update($request->all());
        
        return redirect()->route('keuskupans.index')
            ->with('success', 'Keuskupan berhasil diupdate');
    }
    
    public function destroy($id)
    {
        $this->authorizeDelete();
        $keuskupan = $this->findKeuskupan($id);
        
        // Cek apakah ada gereja terkait
        if ($keuskupan->gerejas()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus keuskupan yang masih memiliki gereja.');
        }
        
        $keuskupan->delete();
        
        return redirect()->route('keuskupans.index')
            ->with('success', 'Keuskupan berhasil dihapus');
    }
    
    /**
     * Menampilkan daftar gereja dalam keuskupan
     */
    public function gerejas($id)
    {
        $keuskupan = $this->findKeuskupan($id);
        $gerejas = $keuskupan->gerejas()->paginate(15);
        
        return view('keuskupans.gerejas', compact('keuskupan', 'gerejas'));
    }
    
    public function members($id)
    {
        $keuskupan = $this->findKeuskupan($id);
        $members = $keuskupan->users()->with('gereja')->paginate(15);
        return view('keuskupans.members', compact('keuskupan', 'members'));
    }
    
public function statistics($id)
{
    $keuskupan = $this->findKeuskupan($id);
    
    $statistics = [
        'total_churches' => $keuskupan->gerejas()->count(),
        'active_churches' => $keuskupan->gerejas()->where('is_active', true)->count(),
        'total_users' => $keuskupan->users()->count(),
        'admin_keuskupan' => $keuskupan->users()->where('level_akses', 'admin_keuskupan')->count(),
        'admin_gereja' => $keuskupan->users()->where('level_akses', 'admin_gereja')->count(),
        'regular_users' => $keuskupan->users()->where('level_akses', 'user')->count(),
        'churches_data' => $keuskupan->gerejas()->withCount('users')->get(),
    ];
    
    // Kirim variabel yang benar
    return view('keuskupans.statistics', compact('keuskupan', 'statistics'));
}
    public function toggleStatus($id)
    {
        $this->authorizeEdit();
        $keuskupan = $this->findKeuskupan($id);
        $keuskupan->update(['is_active' => !$keuskupan->is_active]);
        
        $status = $keuskupan->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Keuskupan berhasil {$status}");
    }
    
    // ==================== PRIVATE METHODS ====================
    
    private function findKeuskupan($id)
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return Keuskupan::findOrFail($id);
        } elseif ($user->isAdminKeuskupan()) {
            return Keuskupan::where('id', $id)
                ->where('id', $user->keuskupan_id)
                ->firstOrFail();
        } else {
            abort(403, 'Anda tidak memiliki akses.');
        }
    }
    
    private function authorizeCreate()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat menambah keuskupan.');
        }
    }
    
    private function authorizeEdit()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->isAdminKeuskupan()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit.');
        }
    }
    
    private function authorizeDelete()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat menghapus keuskupan.');
        }
    }
}