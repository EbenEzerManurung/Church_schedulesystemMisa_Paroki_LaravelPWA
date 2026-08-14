<?php
// app/Http/Controllers/KalenderLiturgiHariController.php

namespace App\Http\Controllers;

use App\Models\KalenderLiturgiHari;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KalenderLiturgiExport;
use App\Exports\KalenderLiturgiTemplateExport;
use App\Imports\KalenderLiturgiImport;

class KalenderLiturgiHariController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KalenderLiturgiHari::query();

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter berdasarkan rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan bulan dan tahun
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun)
                  ->whereMonth('tanggal', $request->bulan);
        }

        // Filter berdasarkan warna liturgi
        if ($request->filled('warna_liturgi')) {
            $query->where('warna_liturgi', $request->warna_liturgi);
        }

        // Filter aktif
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('keterangan_hari', 'LIKE', "%{$search}%")
                  ->orWhere('bacaan1', 'LIKE', "%{$search}%")
                  ->orWhere('mazmur_tanggapan', 'LIKE', "%{$search}%")
                  ->orWhere('bait_pengantarinjil', 'LIKE', "%{$search}%")
                  ->orWhere('bacaan_injil', 'LIKE', "%{$search}%")
                  ->orWhere('catatan', 'LIKE', "%{$search}%");
            });
        }

        // Default sorting
        $kalender = $query->orderBy('tanggal', 'desc')->paginate(15);
        
        // Data untuk filter dropdown
        $warnaList = ['putih', 'merah', 'ungu', 'hijau', 'kuning', 'hitam', 'pink', 'biru'];
        
        // Tambahkan data untuk filter bulan dan tahun
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $tahunList = range(date('Y'), date('Y') + 1);
        
        return view('kalender-liturgi.index', compact('kalender', 'warnaList', 'bulanList', 'tahunList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warnaList = ['putih', 'merah', 'ungu', 'hijau', 'kuning', 'hitam', 'pink', 'biru'];
        return view('kalender-liturgi.create', compact('warnaList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date|unique:kalender_liturgi_hari,tanggal',
            'keterangan_hari' => 'required|string|max:255',
            'warna_liturgi' => 'required|string|max:50',
            'bacaan1' => 'required|string',
            'mazmur_tanggapan' => 'required|string',
            'bait_pengantarinjil' => 'required|string',
            'bacaan_injil' => 'required|string',
            'catatan' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        KalenderLiturgiHari::create($request->all());

        return redirect()->route('kalender-liturgi.index')
            ->with('success', 'Data kalender liturgi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kalender = KalenderLiturgiHari::findOrFail($id);
        return view('kalender-liturgi.show', compact('kalender'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $kalender = KalenderLiturgiHari::findOrFail($id);
        $warnaList = ['putih', 'merah', 'ungu', 'hijau', 'kuning', 'hitam', 'pink', 'biru'];
        return view('kalender-liturgi.edit', compact('kalender', 'warnaList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $kalender = KalenderLiturgiHari::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date|unique:kalender_liturgi_hari,tanggal,' . $id,
            'keterangan_hari' => 'required|string|max:255',
            'warna_liturgi' => 'required|string|max:50',
            'bacaan1' => 'required|string',
            'mazmur_tanggapan' => 'required|string',
            'bait_pengantarinjil' => 'required|string',
            'bacaan_injil' => 'required|string',
            'catatan' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $kalender->update($request->all());

        return redirect()->route('kalender-liturgi.index')
            ->with('success', 'Data kalender liturgi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kalender = KalenderLiturgiHari::findOrFail($id);
        $kalender->delete();

        return redirect()->route('kalender-liturgi.index')
            ->with('success', 'Data kalender liturgi berhasil dihapus');
    }

    /**
     * Bulk delete multiple records
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('warning', 'Tidak ada data yang dipilih');
        }

        KalenderLiturgiHari::whereIn('id', $ids)->delete();

        return redirect()->route('kalender-liturgi.index')
            ->with('success', 'Berhasil menghapus ' . count($ids) . ' data');
    }

    /**
     * Toggle status aktif/non-aktif
     */
    public function toggleStatus($id)
    {
        $kalender = KalenderLiturgiHari::findOrFail($id);
        $kalender->is_active = !$kalender->is_active;
        $kalender->save();

        $status = $kalender->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('kalender-liturgi.index')
            ->with('success', "Status kalender liturgi berhasil {$status}");
    }

    /**
     * Bulk toggle status
     */
    public function bulkToggleStatus(Request $request)
    {
        $ids = $request->input('ids', []);
        $status = $request->input('status', 1);
        
        if (empty($ids)) {
            return redirect()->back()->with('warning', 'Tidak ada data yang dipilih');
        }

        KalenderLiturgiHari::whereIn('id', $ids)->update(['is_active' => $status]);

        $statusText = $status == 1 ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('kalender-liturgi.index')
            ->with('success', 'Berhasil ' . $statusText . ' ' . count($ids) . ' data');
    }

    /**
     * Tampilkan kalender dalam bentuk calendar view
     */
    public function calendar(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan', date('m'));
        
        $kalender = KalenderLiturgiHari::where('is_active', true)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal')
            ->get()
            ->groupBy(function($item) {
                return $item->tanggal->format('Y-m-d');
            });

        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('kalender-liturgi.calendar', compact('kalender', 'tahun', 'bulan', 'bulanList'));
    }

    /**
     * Tampilkan liturgi hari ini
     */
    public function today()
    {
        $today = now()->format('Y-m-d');
        $kalender = KalenderLiturgiHari::whereDate('tanggal', $today)->first();
        
        // Jika tidak ada data hari ini, cari data terdekat
        if (!$kalender) {
            $kalender = KalenderLiturgiHari::where('tanggal', '>=', $today)
                ->where('is_active', true)
                ->orderBy('tanggal')
                ->first();
        }
        
        return view('kalender-liturgi.today', compact('kalender'));
    }

    /**
     * Get data for API (JSON)
     */
    public function getData(Request $request)
    {
        $query = KalenderLiturgiHari::query();

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        if ($request->filled('warna_liturgi')) {
            $query->where('warna_liturgi', $request->warna_liturgi);
        }

        $data = $query->orderBy('tanggal')->get();

        return response()->json($data);
    }

    // ============================================
    // EXPORT METHODS
    // ============================================

    /**
     * Show export form
     */
    public function showExportForm()
    {
        $warnaList = ['putih', 'merah', 'ungu', 'hijau', 'kuning', 'hitam', 'pink', 'biru'];
        return view('kalender-liturgi.export', compact('warnaList'));
    }

    /**
     * Export all data
     */
    public function exportAll()
    {
        return Excel::download(new KalenderLiturgiExport, 'kalender-liturgi-all-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export filtered data
     */
    public function exportFiltered(Request $request)
    {
        $query = KalenderLiturgiHari::query();

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        if ($request->filled('warna_liturgi')) {
            $query->where('warna_liturgi', $request->warna_liturgi);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status == '1');
        }

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun)
                  ->whereMonth('tanggal', $request->bulan);
        }

        $data = $query->orderBy('tanggal', 'asc')->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('warning', 'Tidak ada data yang sesuai dengan filter');
        }

        return Excel::download(new KalenderLiturgiExport($data), 'kalender-liturgi-filtered-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export data per bulan
     */
    public function exportBulan(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2000|max:2100',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $data = KalenderLiturgiHari::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal')
            ->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('warning', 'Tidak ada data untuk bulan tersebut');
        }

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return Excel::download(
            new KalenderLiturgiExport($data),
            "kalender-liturgi-{$namaBulan[$bulan]}-{$tahun}.xlsx"
        );
    }

    // ============================================
    // IMPORT METHODS
    // ============================================

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('kalender-liturgi.import');
    }

    /**
     * Import data from Excel/CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120' // 5MB
        ]);

        try {
            $import = new KalenderLiturgiImport();
            Excel::import($import, $request->file('file'));
            
            // Cek apakah ada error
            if (session()->has('import_errors')) {
                $errors = session('import_errors');
                return redirect()->route('kalender-liturgi.index')
                    ->with('warning', "Import selesai dengan {$errors->count()} error. Silakan periksa log untuk detail.");
            }
            
            // Cek apakah ada failures
            if (session()->has('import_failures')) {
                $failures = session('import_failures');
                return redirect()->route('kalender-liturgi.index')
                    ->with('warning', "Import selesai dengan " . count($failures) . " baris gagal diimport.");
            }
            
            $count = $import->getRowCount() ?? 0;
            return redirect()->route('kalender-liturgi.index')
                ->with('success', "Berhasil mengimport {$count} data kalender liturgi");
                
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessage = 'Terdapat ' . count($failures) . ' error pada data: ';
            
            foreach ($failures as $failure) {
                $errorMessage .= 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . '. ';
            }
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengimport data: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ============================================
    // TEMPLATE METHODS
    // ============================================

    /**
     * Download template import Excel (.xlsx)
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new KalenderLiturgiTemplateExport(), 
            'template_kalender_liturgi.xlsx'
        );
    }

    /**
     * Download template import CSV (.csv)
     */
    public function downloadTemplateCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_kalender_liturgi.csv"',
        ];

        $columns = [
            'KETERANGAN_HARI*',
            'WARNA_LITURGI*',
            'BACAAN_1*',
            'MAZMUR_TANGGAPAN*',
            'BAIT_PENGANTAR_INJIL*',
            'BACAAN_INJIL*',
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            
            // BOM untuk UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, $columns);
            
            // Contoh data
            $sampleData = [
                [
                    'Minggu Biasa II',
                    'hijau',
                    'Yes 49:1-3,5-6',
                    'Mzm 40:2,4,7-10',
                    'Alleluia, Alleluia, ...',
                    'Yoh 1:29-34'
                ],
                [
                    'Hari Raya Paskah',
                    'putih',
                    'Kis 10:34,37-43',
                    'Mzm 118:1-2,16-17,22-23',
                    'Alleluia, Alleluia, ...',
                    'Mat 28:1-10'
                ],
                [
                    'Minggu Prapaskah III',
                    'ungu',
                    'Kel 17:3-7',
                    'Mzm 95:1-2,6-9',
                    'Alleluia, Alleluia, ...',
                    'Yoh 4:5-42'
                ]
            ];
            
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            
            // Tambahkan baris kosong dengan catatan
            fputcsv($file, []);
            fputcsv($file, ['===================================================']);
            fputcsv($file, ['CATATAN PENTING:']);
            fputcsv($file, ['===================================================']);
            fputcsv($file, ['1. SEMUA KOLOM WAJIB DIISI (tidak boleh kosong)']);
            fputcsv($file, ['2. Warna liturgi yang valid: putih, merah, ungu, hijau']);
            fputcsv($file, ['3. Format penulisan bacaan: Kitab Pasal:Ayat (contoh: Yes 49:1-3,5-6)']);
            fputcsv($file, ['4. Singkatan kitab yang umum digunakan (Kej, Kel, Im, Bil, dll)']);
            fputcsv($file, ['5. HAPUS baris contoh sebelum melakukan import']);
            fputcsv($file, ['6. Pastikan tidak ada duplikasi data']);
            fputcsv($file, ['7. Jangan mengubah struktur kolom']);
            fputcsv($file, ['===================================================']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download template dengan contoh data (untuk referensi)
     */
    public function downloadTemplateWithSample()
    {
        return Excel::download(
            new KalenderLiturgiTemplateExport(true), 
            'template_kalender_liturgi_dengan_contoh.xlsx'
        );
    }
}