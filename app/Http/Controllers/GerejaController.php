<?php
namespace App\Http\Controllers;

use App\Models\Gereja;
use App\Models\Keuskupan;
use App\Imports\GerejaImport;
use App\Exports\GerejaExport;
use App\Exports\GerejaTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GerejaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Gereja::with('keuskupan');
        
        // Filter berdasarkan role
        if ($user->isAdminKeuskupan()) {
            $query->where('keuskupan_id', $user->keuskupan_id);
        } elseif ($user->isAdminGereja()) {
            $query->where('id', $user->gereja_id);
        } elseif ($user->isUser()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        // Filter by keuskupan
        if ($request->filled('keuskupan_id') && $user->isSuperAdmin()) {
            $query->where('keuskupan_id', $request->keuskupan_id);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('kode', 'like', '%' . $request->search . '%')
                  ->orWhere('lokasi', 'like', '%' . $request->search . '%');
            });
        }
        
        $gerejas = $query->orderBy('nama')->paginate(15);
        $keuskupans = $user->isSuperAdmin() ? Keuskupan::all() : collect();
        
        return view('gerejas.index', compact('gerejas', 'keuskupans'));
    }
    
    public function create(Request $request)
    {
        $this->authorizeCreate();
        
        $user = auth()->user();
        $keuskupanId = $request->query('keuskupan_id');
        
        // Jika ada parameter keuskupan_id, cari keuskupan tersebut
        if ($keuskupanId) {
            $keuskupan = Keuskupan::find($keuskupanId);
            if (!$keuskupan) {
                abort(404, 'Keuskupan tidak ditemukan');
            }
        } else {
            $keuskupan = null;
        }
        
        // Untuk dropdown keuskupan
        if ($user->isSuperAdmin()) {
            $keuskupans = Keuskupan::all();
        } else {
            $keuskupans = Keuskupan::where('id', $user->keuskupan_id)->get();
        }
        
        return view('gerejas.create', compact('keuskupans', 'keuskupan'));
    }
    
    public function store(Request $request)
    {
        $this->authorizeCreate();
        
        $user = auth()->user();
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'keuskupan_id' => 'required|exists:keuskupans,id',
            'lokasi' => 'required|string|max:255',
            'alamat_lengkap' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'pastor' => 'nullable|string|max:255',
            'jumlah_umat' => 'nullable|integer',
            'deskripsi' => 'nullable|string',
        ]);
        
        // Validasi akses untuk admin keuskupan
        if ($user->isAdminKeuskupan() && $request->keuskupan_id != $user->keuskupan_id) {
            abort(403, 'Anda hanya dapat menambah gereja di keuskupan Anda sendiri.');
        }
        
        DB::beginTransaction();
        try {
            // Generate kode gereja otomatis
            $keuskupan = Keuskupan::find($request->keuskupan_id);
            $prefix = substr($keuskupan->code, 0, 2);
            $suffix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->nama), 0, 3));
            $kode = $prefix . '-' . $suffix . rand(10, 99);
            
            // Cek uniqueness kode
            while (Gereja::where('kode', $kode)->exists()) {
                $kode = $prefix . '-' . $suffix . rand(10, 99);
            }
            
            $gereja = Gereja::create([
                'nama' => $request->nama,
                'kode' => $kode,
                'keuskupan_id' => $request->keuskupan_id,
                'lokasi' => $request->lokasi,
                'alamat_lengkap' => $request->alamat_lengkap,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'pastor' => $request->pastor,
                'jumlah_umat' => $request->jumlah_umat ?? 0,
                'deskripsi' => $request->deskripsi,
                'is_active' => true,
            ]);
            
            DB::commit();
            
            // Redirect kembali ke halaman gereja di keuskupan yang sama
            return redirect()->route('keuskupans.gerejas', $request->keuskupan_id)
                ->with('success', "Gereja {$gereja->nama} berhasil ditambahkan. Kode: {$kode}");
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menambahkan gereja: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $gereja = $this->findGereja($id);
        $statistics = [
            'total_users' => $gereja->users()->count(),
            'active_users' => $gereja->users()->where('is_active', true)->count(),
            'admin_count' => $gereja->users()->where('level_akses', 'admin_gereja')->count(),
            'regular_users' => $gereja->users()->where('level_akses', 'user')->count(),
        ];
        
        return view('gerejas.show', compact('gereja', 'statistics'));
    }
    
    public function edit($id)
    {
        $this->authorizeEdit();
        $gereja = $this->findGereja($id);
        
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            $keuskupans = Keuskupan::all();
        } else {
            $keuskupans = Keuskupan::where('id', $user->keuskupan_id)->get();
        }
        
        return view('gerejas.edit', compact('gereja', 'keuskupans'));
    }
    
    public function update(Request $request, $id)
    {
        $this->authorizeEdit();
        $gereja = $this->findGereja($id);
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'keuskupan_id' => 'required|exists:keuskupans,id',
            'lokasi' => 'required|string|max:255',
            'alamat_lengkap' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'pastor' => 'nullable|string|max:255',
            'jumlah_umat' => 'nullable|integer',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $gereja->update($request->all());
        
        return redirect()->route('keuskupans.gerejas', $gereja->keuskupan_id)
            ->with('success', "Gereja {$gereja->nama} berhasil diupdate");
    }
    
    public function destroy($id)
    {
        $this->authorizeDelete();
        $gereja = $this->findGereja($id);
        
        // Cek apakah ada user yang terikat
        if ($gereja->users()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus gereja yang masih memiliki user.');
        }
        
        $keuskupanId = $gereja->keuskupan_id;
        $nama = $gereja->nama;
        $gereja->delete();
        
        return redirect()->route('keuskupans.gerejas', $keuskupanId)
            ->with('success', "Gereja {$nama} berhasil dihapus");
    }
    
    public function members($id)
    {
        $gereja = $this->findGereja($id);
        $members = $gereja->users()->paginate(15);
        
        return view('gerejas.members', compact('gereja', 'members'));
    }
    
    public function statistics($id)
    {
        $gereja = $this->findGereja($id);
        
        $statistics = [
            'total_users' => $gereja->users()->count(),
            'active_users' => $gereja->users()->where('is_active', true)->count(),
            'inactive_users' => $gereja->users()->where('is_active', false)->count(),
            'admin_count' => $gereja->users()->where('level_akses', 'admin_gereja')->count(),
            'regular_users' => $gereja->users()->where('level_akses', 'user')->count(),
            'users_by_role' => $gereja->users()->select('level_akses', \DB::raw('count(*) as total'))
                ->groupBy('level_akses')
                ->get(),
        ];
        
        return view('gerejas.statistics', compact('gereja', 'statistics'));
    }
    
    public function toggleStatus($id)
    {
        $this->authorizeEdit();
        $gereja = $this->findGereja($id);
        $gereja->update(['is_active' => !$gereja->is_active]);
        
        $status = $gereja->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Gereja {$gereja->nama} berhasil {$status}");
    }
    
    public function getByKeuskupan($keuskupanId)
    {
        $gerejas = Gereja::where('keuskupan_id', $keuskupanId)
            ->where('is_active', true)
            ->get(['id', 'nama', 'kode']);
        
        return response()->json($gerejas);
    }
    
    // ==================== PRIVATE METHODS ====================
    
    private function findGereja($id)
    {
        $user = auth()->user();
        $query = Gereja::with('keuskupan');
        
        if ($user->isSuperAdmin()) {
            return $query->findOrFail($id);
        } elseif ($user->isAdminKeuskupan()) {
            return $query->where('keuskupan_id', $user->keuskupan_id)
                ->findOrFail($id);
        } elseif ($user->isAdminGereja()) {
            return $query->where('id', $user->gereja_id)
                ->where('id', $id)
                ->firstOrFail();
        } else {
            abort(403, 'Anda tidak memiliki akses.');
        }
    }
    
    private function authorizeCreate()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->isAdminKeuskupan()) {
            abort(403, 'Anda tidak memiliki akses untuk menambah gereja.');
        }
    }
    
    private function authorizeEdit()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->isAdminKeuskupan() && !$user->isAdminGereja()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit gereja.');
        }
    }
    
    private function authorizeDelete()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->isAdminKeuskupan()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus gereja.');
        }
    }

    /**
 * Show export form with preview
 */
public function showExportForm()
{
    $user = auth()->user();
    
    $query = Gereja::with('keuskupan');
    
    if ($user->isAdminKeuskupan()) {
        $query->where('keuskupan_id', $user->keuskupan_id);
    }
    
    $gerejas = $query->paginate(10);
    $keuskupans = $user->isSuperAdmin() ? Keuskupan::all() : collect();
    
    return view('gerejas.export', compact('gerejas', 'keuskupans'));
}

/**
 * Export all data
 */
public function exportAll()
{
    try {
        $user = auth()->user();
        
        $query = Gereja::with('keuskupan');
        
        if ($user->isAdminKeuskupan()) {
            $query->where('keuskupan_id', $user->keuskupan_id);
        }
        
        $gerejas = $query->get();
        
        $fileName = 'data_gereja_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new GerejaExport($gerejas), $fileName);
    } catch (\Exception $e) {
        return redirect()->route('gerejas.index')
            ->with('error', 'Gagal mengexport data: ' . $e->getMessage());
    }
}

/**
 * Export filtered data
 */
public function exportFiltered(Request $request)
{
    try {
        $user = auth()->user();
        $query = Gereja::with('keuskupan');
        
        if ($user->isAdminKeuskupan()) {
            $query->where('keuskupan_id', $user->keuskupan_id);
        }
        
        if ($request->filled('keuskupan_id')) {
            $query->where('keuskupan_id', $request->keuskupan_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('kode', 'like', '%' . $search . '%')
                  ->orWhere('lokasi', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->filled('status') && $request->status != '') {
            $query->where('is_active', $request->status);
        }
        
        $gerejas = $query->get();
        
        if ($gerejas->isEmpty()) {
            return redirect()->route('gerejas.index')
                ->with('error', 'Tidak ada data yang sesuai dengan filter');
        }
        
        $format = $request->get('format', 'xlsx');
        $fileName = 'data_gereja_filtered_' . date('Y-m-d_His');
        
        if ($format === 'csv') {
            $fileName .= '.csv';
            return Excel::download(new GerejaExport($gerejas), $fileName, \Maatwebsite\Excel\Excel::CSV);
        }
        
        $fileName .= '.xlsx';
        return Excel::download(new GerejaExport($gerejas), $fileName);
        
    } catch (\Exception $e) {
        return redirect()->route('gerejas.index')
            ->with('error', 'Gagal mengexport data: ' . $e->getMessage());
    }
}

/**
 * Show import form
 */
public function showImportForm()
{
    $this->authorizeCreate();
    return view('gerejas.import');
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
        
        $import = new GerejaImport();
        Excel::import($import, $request->file('file'));
        
        DB::commit();
        
        $successCount = $import->getSuccessCount();
        $failures = $import->getFailures();
        
        $message = "Berhasil mengimport {$successCount} data gereja.";
        
        if (!empty($failures)) {
            session()->flash('import_failures', $failures);
            $message .= " Terdapat " . count($failures) . " data yang gagal diimport.";
        }
        
        return redirect()->route('gerejas.index')
            ->with('success', $message);
            
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Gagal mengimport data: ' . $e->getMessage());
    }
}

/**
 * Download template import
 */
public function downloadTemplate()
{
    try {
        return Excel::download(new GerejaTemplateExport(), 'template_import_gereja.xlsx');
    } catch (\Exception $e) {
        return redirect()->route('gerejas.index')
            ->with('error', 'Gagal mendownload template: ' . $e->getMessage());
    }
}
}