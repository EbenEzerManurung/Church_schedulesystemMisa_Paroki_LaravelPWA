<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Gereja;
use App\Models\Keuskupan;
use App\Models\Duty;
use App\Traits\HasKeuskupanAccess;
use Illuminate\Http\Request;
use App\Imports\UserImport;
use App\Exports\UserExport;
use App\Exports\UserTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;

class UserController extends Controller
{
    use HasKeuskupanAccess;
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = User::with(['keuskupan', 'gereja', 'duty']);
        
        // Filter berdasarkan role user
        if ($user->isSuperAdmin()) {
            // Super Admin: lihat SEMUA user (tanpa filter)
            // Tidak ada filter tambahan
        } elseif ($user->isAdminKeuskupan()) {
            $query->where('keuskupan_id', $user->keuskupan_id);
        } elseif ($user->isAdminGereja()) {
            $query->where('gereja_id', $user->gereja_id);
        } elseif ($user->level_akses === 'pic_group') {
            // PIC Group: hanya melihat user dengan duty_id yang sama (kecuali dirinya sendiri)
            if ($user->duty_id) {
                $query->where('duty_id', $user->duty_id)
                      ->where('id', '!=', $user->id);
            } else {
                $query->where('id', null);
            }
        } elseif ($user->isUser()) {
            $query->where('id', $user->id);
        }
        
        // Filter search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filter duties dengan whereHas
        if ($request->filled('duty_id')) {
            $query->whereHas('duty', function($q) use ($request) {
                $q->where('id', $request->duty_id);
            });
        }
        
        // Filter gereja
        if ($request->filled('gereja_id') && ($user->isSuperAdmin() || $user->isAdminKeuskupan())) {
            $query->where('gereja_id', $request->gereja_id);
        }
        
        // Filter keuskupan
        if ($request->filled('keuskupan_id') && $user->isSuperAdmin()) {
            $query->where('keuskupan_id', $request->keuskupan_id);
        }
        
        // Filter status aktif
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }
        
        $users = $query->orderBy('name')->paginate(15);
        
        $roles = Role::all();
        $gerejas = $user->getAccessibleChurches();
        $duties = Duty::where('is_active', true)->orderBy('name')->get();
        
        // Data keuskupan
        if ($user->isSuperAdmin()) {
            $keuskupans = Keuskupan::all();
        } elseif ($user->isAdminKeuskupan() || $user->isAdminGereja()) {
            $keuskupans = Keuskupan::where('id', $user->keuskupan_id)->get();
        } else {
            $keuskupans = collect();
        }
        
        return view('users.index', compact('users', 'roles', 'gerejas', 'keuskupans', 'duties'));
    }
    
    /**
     * Show form to create new user.
     */
    public function create()
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin() && !$user->isAdminKeuskupan() && !$user->isAdminGereja() && $user->level_akses !== 'pic_group') {
            abort(403, 'Anda tidak memiliki akses untuk menambah user.');
        }
        
        $roles = Role::all();
        $gerejas = $user->getAccessibleChurches();
        $duties = Duty::where('is_active', true)->orderBy('name')->get();
        
        if ($user->isSuperAdmin()) {
            $keuskupans = Keuskupan::all();
        } elseif ($user->isAdminKeuskupan() || $user->isAdminGereja()) {
            $keuskupans = Keuskupan::where('id', $user->keuskupan_id)->get();
        } else {
            $keuskupans = collect();
        }
        
        return view('users.create', compact('roles', 'gerejas', 'keuskupans', 'duties'));
    }
    
    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $loggedInUser = auth()->user();
        
        if (!$loggedInUser->isSuperAdmin() && !$loggedInUser->isAdminKeuskupan() && !$loggedInUser->isAdminGereja() && $loggedInUser->level_akses !== 'pic_group') {
            abort(403, 'Anda tidak memiliki akses untuk menambah user.');
        }
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'level_akses' => 'required|in:super_admin,admin_keuskupan,admin_gereja,pic_group,user',
            'is_active' => 'boolean',
            'duty_id' => 'nullable|exists:duties,id',
        ];
        
        if ($request->level_akses === 'admin_keuskupan') {
            $rules['keuskupan_id'] = 'required|exists:keuskupans,id';
        } elseif ($request->level_akses === 'admin_gereja' || $request->level_akses === 'pic_group' || $request->level_akses === 'user') {
            $rules['gereja_id'] = 'required|exists:gerejas,id';
        }
        
        // Validasi Super Admin
        if ($request->level_akses === 'super_admin' && !$loggedInUser->isSuperAdmin()) {
            return back()->withErrors(['level_akses' => 'Hanya Super Admin yang dapat membuat akun Super Admin.']);
        }
        
        // Validasi PIC Group
        if ($loggedInUser->level_akses === 'pic_group') {
            if (in_array($request->level_akses, ['super_admin', 'admin_keuskupan', 'admin_gereja'])) {
                return back()->withErrors(['level_akses' => 'PIC Group tidak dapat membuat user dengan role admin.']);
            }
            
            if ($request->duty_id != $loggedInUser->duty_id) {
                return back()->withErrors(['duty_id' => 'Anda hanya dapat membuat user dalam duty group yang sama.']);
            }
            
            if ($request->gereja_id != $loggedInUser->gereja_id) {
                return back()->withErrors(['gereja_id' => 'Anda hanya dapat membuat user untuk gereja yang sama.']);
            }
        }
        
        $request->validate($rules);
        
        $password = $request->filled('password') ? $request->password : 'password';
        
        if ($request->filled('password') && strlen($request->password) < 6) {
            return back()->withErrors(['password' => 'Password minimal 6 karakter.']);
        }
        
        // Logika penentuan keuskupan_id dan gereja_id
        $keuskupanId = null;
        $gerejaId = null;
        
        if ($loggedInUser->isAdminKeuskupan()) {
            $keuskupanId = $loggedInUser->keuskupan_id;
            if ($request->filled('gereja_id')) {
                $gereja = Gereja::find($request->gereja_id);
                if ($gereja && $gereja->keuskupan_id != $keuskupanId) {
                    return back()->withErrors(['gereja_id' => 'Gereja tidak berada di keuskupan Anda.']);
                }
                $gerejaId = $request->gereja_id;
            }
        } elseif ($loggedInUser->isAdminGereja() || $loggedInUser->level_akses === 'pic_group') {
            $gerejaId = $loggedInUser->gereja_id;
            $keuskupanId = $loggedInUser->keuskupan_id;
            
            if ($request->filled('gereja_id') && $request->gereja_id != $gerejaId) {
                return back()->withErrors(['gereja_id' => 'Anda hanya dapat membuat user untuk gereja Anda sendiri.']);
            }
        } else {
            // Super admin
            $keuskupanId = $request->keuskupan_id;
            $gerejaId = $request->gereja_id;
            
            if ($gerejaId) {
                $gereja = Gereja::find($gerejaId);
                if ($gereja) {
                    $keuskupanId = $gereja->keuskupan_id;
                }
            }
        }
        
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->is_active ?? true,
            'level_akses' => $request->level_akses,
            'keuskupan_id' => $keuskupanId,
            'gereja_id' => $gerejaId,
            'duty_id' => $request->duty_id ?? null,
        ];
        
        $user = User::create($userData);
        $user->assignRole($request->level_akses);
        
        $passwordMsg = $request->filled('password') ? 'Password: ' . $password : 'Password default: password';
        return redirect()->route('users.index')
            ->with('success', 'User "' . $user->name . '" berhasil ditambahkan. ' . $passwordMsg);
    }
    
    /**
     * Show import form
     */
    public function showImportForm()
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin() && !$user->isAdminKeuskupan()) {
            abort(403, 'Anda tidak memiliki akses untuk mengimpor data user.');
        }
        
        return view('users.import');
    }

    /**
     * Import users from Excel/CSV
     */
    public function import(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin() && !$user->isAdminKeuskupan()) {
            abort(403, 'Anda tidak memiliki akses untuk mengimpor data user.');
        }
        
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);
        
        try {
            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            array_shift($rows);
            
            $imported = 0;
            $errors = [];
            $failures = [];
            
            foreach ($rows as $index => $row) {
                if (empty(array_filter($row))) {
                    continue;
                }
                
                $nama = trim($row[0] ?? '');
                $email = trim($row[1] ?? '');
                $levelAkses = trim($row[2] ?? 'user');
                $gereja = trim($row[3] ?? '');
                $keuskupan = trim($row[4] ?? '');
                $phone = trim($row[5] ?? '');
                $address = trim($row[6] ?? '');
                
                if (empty($nama) || empty($email)) {
                    $errors[] = "Baris " . ($index + 2) . ": Nama dan Email wajib diisi.";
                    $failures[] = ['row' => $index + 2, 'errors' => ['Nama dan Email wajib diisi']];
                    continue;
                }
                
                if (User::where('email', $email)->exists()) {
                    $errors[] = "Baris " . ($index + 2) . ": Email '$email' sudah terdaftar.";
                    $failures[] = ['row' => $index + 2, 'errors' => ["Email '$email' sudah terdaftar"]];
                    continue;
                }
                
                $validRoles = ['super_admin', 'admin_keuskupan', 'admin_gereja', 'pic_group', 'user'];
                if (!in_array($levelAkses, $validRoles)) {
                    $errors[] = "Baris " . ($index + 2) . ": Level akses '$levelAkses' tidak valid.";
                    $failures[] = ['row' => $index + 2, 'errors' => ["Level akses '$levelAkses' tidak valid"]];
                    continue;
                }
                
                if ($levelAkses == 'admin_keuskupan' && empty($keuskupan)) {
                    $errors[] = "Baris " . ($index + 2) . ": Admin keuskupan wajib mengisi kolom keuskupan.";
                    $failures[] = ['row' => $index + 2, 'errors' => ['Admin keuskupan wajib mengisi kolom keuskupan']];
                    continue;
                }
                
                if (($levelAkses == 'admin_gereja' || $levelAkses == 'pic_group' || $levelAkses == 'user') && empty($gereja)) {
                    $errors[] = "Baris " . ($index + 2) . ": $levelAkses wajib mengisi kolom gereja.";
                    $failures[] = ['row' => $index + 2, 'errors' => ["$levelAkses wajib mengisi kolom gereja"]];
                    continue;
                }
                
                $gerejaId = null;
                $keuskupanId = null;
                
                if (!empty($gereja)) {
                    $gerejaModel = Gereja::where('nama', $gereja)->first();
                    if (!$gerejaModel) {
                        $errors[] = "Baris " . ($index + 2) . ": Gereja '$gereja' tidak ditemukan.";
                        $failures[] = ['row' => $index + 2, 'errors' => ["Gereja '$gereja' tidak ditemukan"]];
                        continue;
                    }
                    $gerejaId = $gerejaModel->id;
                    $keuskupanId = $gerejaModel->keuskupan_id;
                }
                
                if (!empty($keuskupan)) {
                    $keuskupanModel = Keuskupan::where('name', $keuskupan)->first();
                    if (!$keuskupanModel) {
                        $errors[] = "Baris " . ($index + 2) . ": Keuskupan '$keuskupan' tidak ditemukan.";
                        $failures[] = ['row' => $index + 2, 'errors' => ["Keuskupan '$keuskupan' tidak ditemukan"]];
                        continue;
                    }
                    $keuskupanId = $keuskupanModel->id;
                }
                
                $userData = [
                    'name' => $nama,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'level_akses' => $levelAkses,
                    'phone' => $phone,
                    'address' => $address,
                    'is_active' => true,
                    'gereja_id' => $gerejaId,
                    'keuskupan_id' => $keuskupanId,
                ];
                
                $newUser = User::create($userData);
                $newUser->assignRole($levelAkses);
                $imported++;
            }
            
            $message = "Berhasil mengimpor $imported user.";
            
            if (!empty($errors)) {
                $message .= " Namun ada " . count($errors) . " error.";
                return redirect()->route('users.index')
                    ->with('warning', $message)
                    ->with('import_failures', $failures);
            }
            
            return redirect()->route('users.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        $user = auth()->user();
        
        if (!$user->isSuperAdmin() && !$user->isAdminKeuskupan()) {
            abort(403, 'Anda tidak memiliki akses untuk mendownload template.');
        }
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template User');
        
        $headers = ['Nama*', 'Email*', 'Level Akses*', 'Gereja', 'Keuskupan', 'No. Telepon', 'Alamat'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }
        
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        
        $exampleData = [
            ['John Doe', 'john@example.com', 'user', 'Gereja Santo Petrus', '', '08123456789', 'Jl. Contoh No. 1'],
            ['Jane Smith', 'jane@example.com', 'admin_gereja', 'Gereja Santa Maria', '', '08198765432', 'Jl. Contoh No. 2'],
            ['Admin Keuskupan', 'admin@keuskupan.com', 'admin_keuskupan', '', 'Keuskupan Jakarta', '08111222333', 'Jl. Keuskupan No. 1'],
            ['PIC Koor', 'pic@koor.com', 'pic_group', 'Gereja Santo Petrus', '', '08111222344', 'Jl. Contoh No. 4'],
        ];
        
        $row = 2;
        foreach ($exampleData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        $infoRow = $row + 2;
        $sheet->setCellValue('A' . $infoRow, 'Keterangan:');
        $sheet->setCellValue('A' . ($infoRow + 1), 'Level Akses: super_admin, admin_keuskupan, admin_gereja, pic_group, user');
        $sheet->setCellValue('A' . ($infoRow + 2), 'Kolom dengan * wajib diisi');
        $sheet->setCellValue('A' . ($infoRow + 3), 'Untuk admin_gereja, pic_group, dan user: wajib mengisi kolom Gereja');
        $sheet->setCellValue('A' . ($infoRow + 4), 'Untuk admin_keuskupan: wajib mengisi kolom Keuskupan');
        $sheet->setCellValue('A' . ($infoRow + 5), 'Password default: password');
        
        $sheet->getStyle('A' . $infoRow . ':G' . ($infoRow + 5))->applyFromArray([
            'font' => ['size' => 9, 'color' => ['argb' => 'FF666666']],
        ]);
        
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        $filename = 'user_template_import.xlsx';
        
        return response()->stream(
            function() use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }
    
    /**
     * Export filtered users to Excel
     */
    public function exportFiltered(Request $request)
    {
        $user = auth()->user();
        
        $query = User::with(['keuskupan', 'gereja']);
        
        // Filter berdasarkan role yang login
        if ($user->isSuperAdmin()) {
            // Super Admin: export SEMUA user (tanpa filter)
        } elseif ($user->isAdminKeuskupan()) {
            $query->where('keuskupan_id', $user->keuskupan_id);
        } elseif ($user->isAdminGereja()) {
            $query->where('gereja_id', $user->gereja_id);
        } elseif ($user->level_akses === 'pic_group') {
            // PIC Group: hanya export user dengan duty_id yang sama (kecuali dirinya sendiri)
            if ($user->duty_id) {
                $query->where('duty_id', $user->duty_id)
                      ->where('id', '!=', $user->id);
            } else {
                $query->where('id', null);
            }
        } elseif ($user->isUser()) {
            $query->where('id', $user->id);
        }
        
        // Filter tambahan dari request
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('role')) {
            $query->where('level_akses', $request->role);
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == '1');
        }
        
        if ($request->filled('gereja_id')) {
            $query->where('gereja_id', $request->gereja_id);
        }
        
        if ($request->filled('keuskupan_id')) {
            $query->where('keuskupan_id', $request->keuskupan_id);
        }
        
        $users = $query->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Users');
        
        $headers = ['ID', 'Nama', 'Email', 'Level Akses', 'Keuskupan', 'Gereja', 'No. Telepon', 'Status', 'Tanggal Dibuat'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }
        
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        
        $row = 2;
        foreach ($users as $userItem) {
            $sheet->setCellValue('A' . $row, $userItem->id);
            $sheet->setCellValue('B' . $row, $userItem->name);
            $sheet->setCellValue('C' . $row, $userItem->email);
            $sheet->setCellValue('D' . $row, $this->getLevelAksesLabel($userItem->level_akses));
            $sheet->setCellValue('E' . $row, $userItem->keuskupan->name ?? '-');
            $sheet->setCellValue('F' . $row, $userItem->gereja->nama ?? '-');
            $sheet->setCellValue('G' . $row, $userItem->phone ?? '-');
            $sheet->setCellValue('H' . $row, $userItem->is_active ? 'Aktif' : 'Nonaktif');
            $sheet->setCellValue('I' . $row, $userItem->created_at ? $userItem->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') : '-');
            $row++;
        }
        
        $lastRow = $row - 1;
        $range = 'A1:I' . $lastRow;
        
        $sheet->getStyle($range)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':I' . $i)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                ]);
            }
        }
        
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->freezePane('A2');
        
        $format = $request->input('format', 'xlsx');
        $extension = $format == 'xlsx' ? 'xlsx' : 'csv';
        $filename = 'users_export_filtered_' . date('Y-m-d_His') . '.' . $extension;
        
        if ($extension == 'csv') {
            $contentType = 'text/csv; charset=UTF-8';
        } else {
            $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        }
        
        return response()->stream(
            function() use ($spreadsheet, $format) {
                if ($format == 'csv') {
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
                    $writer->setUseBOM(true);
                } else {
                    $writer = new Xlsx($spreadsheet);
                }
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    /**
     * Export all users to Excel
     */
    public function exportAll()
    {
        $user = auth()->user();
        
        $query = User::with(['keuskupan', 'gereja']);
        
        // Filter berdasarkan role yang login
        if ($user->isSuperAdmin()) {
            // Super Admin: export SEMUA user
        } elseif ($user->isAdminKeuskupan()) {
            $query->where('keuskupan_id', $user->keuskupan_id);
        } elseif ($user->isAdminGereja()) {
            $query->where('gereja_id', $user->gereja_id);
        } elseif ($user->level_akses === 'pic_group') {
            // PIC Group: hanya export user dengan duty_id yang sama (kecuali dirinya sendiri)
            if ($user->duty_id) {
                $query->where('duty_id', $user->duty_id)
                      ->where('id', '!=', $user->id);
            } else {
                $query->where('id', null);
            }
        } elseif ($user->isUser()) {
            $query->where('id', $user->id);
        }
        
        $users = $query->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Users');
        
        $headers = ['ID', 'Nama', 'Email', 'Level Akses', 'Keuskupan', 'Gereja', 'No. Telepon', 'Status', 'Tanggal Dibuat'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }
        
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        
        $row = 2;
        foreach ($users as $userItem) {
            $sheet->setCellValue('A' . $row, $userItem->id);
            $sheet->setCellValue('B' . $row, $userItem->name);
            $sheet->setCellValue('C' . $row, $userItem->email);
            $sheet->setCellValue('D' . $row, $this->getLevelAksesLabel($userItem->level_akses));
            $sheet->setCellValue('E' . $row, $userItem->keuskupan->name ?? '-');
            $sheet->setCellValue('F' . $row, $userItem->gereja->nama ?? '-');
            $sheet->setCellValue('G' . $row, $userItem->phone ?? '-');
            $sheet->setCellValue('H' . $row, $userItem->is_active ? 'Aktif' : 'Nonaktif');
            $sheet->setCellValue('I' . $row, $userItem->created_at ? $userItem->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') : '-');
            $row++;
        }
        
        $lastRow = $row - 1;
        $range = 'A1:I' . $lastRow;
        
        $sheet->getStyle($range)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':I' . $i)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                ]);
            }
        }
        
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->freezePane('A2');
        
        $filename = 'users_export_' . date('Y-m-d_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Get label for level akses
     */
    private function getLevelAksesLabel($level)
    {
        $labels = [
            'super_admin' => 'Super Admin',
            'admin_keuskupan' => 'Admin Keuskupan',
            'admin_gereja' => 'Admin Gereja',
            'pic_group' => 'PIC Group',
            'user' => 'User'
        ];
        return $labels[$level] ?? $level;
    }
    
    /**
     * Display user details.
     */
    public function show(User $user)
    {
        $this->checkAccess($user);
        $user->load(['keuskupan', 'gereja', 'duty']);
        
        return view('users.show', compact('user'));
    }

    /**
     * Show form to edit user.
     */
    public function edit(User $user)
    {
        $this->checkAccess($user);
        
        $loggedInUser = auth()->user();
        $roles = Role::all();
        $userRole = $user->roles->first();
        $gerejas = $loggedInUser->getAccessibleChurches();
        $duties = Duty::where('is_active', true)->orderBy('name')->get();
        
        if ($loggedInUser->isSuperAdmin()) {
            $keuskupans = Keuskupan::all();
        } elseif ($loggedInUser->isAdminKeuskupan() || $loggedInUser->isAdminGereja()) {
            $keuskupans = Keuskupan::where('id', $loggedInUser->keuskupan_id)->get();
        } else {
            $keuskupans = collect();
        }
        
        return view('users.edit', compact('user', 'roles', 'userRole', 'gerejas', 'keuskupans', 'duties'));
    }
    
    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        $this->checkAccess($user);
        
        $loggedInUser = auth()->user();
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'level_akses' => 'required|in:super_admin,admin_keuskupan,admin_gereja,pic_group,user',
            'is_active' => 'boolean',
            'duty_id' => 'nullable|exists:duties,id',
        ];
        
        if ($request->level_akses === 'admin_keuskupan') {
            $rules['keuskupan_id'] = 'required|exists:keuskupans,id';
        } elseif ($request->level_akses === 'admin_gereja' || $request->level_akses === 'pic_group' || $request->level_akses === 'user') {
            $rules['gereja_id'] = 'required|exists:gerejas,id';
        }
        
        if ($request->level_akses === 'super_admin' && !$loggedInUser->isSuperAdmin()) {
            return back()->withErrors(['level_akses' => 'Hanya Super Admin yang dapat mengubah ke Super Admin.']);
        }
        
        if ($loggedInUser->level_akses === 'pic_group') {
            if (in_array($request->level_akses, ['super_admin', 'admin_keuskupan', 'admin_gereja'])) {
                return back()->withErrors(['level_akses' => 'PIC Group tidak dapat mengubah user menjadi admin.']);
            }
            
            if ($user->duty_id !== $loggedInUser->duty_id) {
                abort(403, 'Anda tidak memiliki akses ke user ini.');
            }
            
            if ($user->id === $loggedInUser->id) {
                return back()->withErrors(['error' => 'Anda tidak dapat mengedit data diri sendiri.']);
            }
        }
        
        $request->validate($rules);
        
        $keuskupanId = null;
        $gerejaId = null;
        
        if ($loggedInUser->isAdminKeuskupan()) {
            $keuskupanId = $loggedInUser->keuskupan_id;
            $gerejaId = $user->gereja_id;
            
            if ($request->filled('gereja_id')) {
                $gereja = Gereja::find($request->gereja_id);
                if ($gereja && $gereja->keuskupan_id != $keuskupanId) {
                    return back()->withErrors(['gereja_id' => 'Gereja tidak berada di keuskupan Anda.']);
                }
                $gerejaId = $request->gereja_id;
            }
        } elseif ($loggedInUser->isAdminGereja() || $loggedInUser->level_akses === 'pic_group') {
            $gerejaId = $loggedInUser->gereja_id;
            $keuskupanId = $loggedInUser->keuskupan_id;
            
            if ($request->filled('gereja_id') && $request->gereja_id != $gerejaId) {
                return back()->withErrors(['gereja_id' => 'Anda hanya dapat mengedit user untuk gereja Anda sendiri.']);
            }
        } else {
            $keuskupanId = $request->keuskupan_id;
            $gerejaId = $request->gereja_id;
            
            if ($gerejaId) {
                $gereja = Gereja::find($gerejaId);
                if ($gereja) {
                    $keuskupanId = $gereja->keuskupan_id;
                }
            }
        }
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
            'level_akses' => $request->level_akses,
            'keuskupan_id' => $keuskupanId,
            'gereja_id' => $gerejaId,
            'duty_id' => $request->duty_id,
        ];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);
        $user->syncRoles([$request->level_akses]);
        
        return redirect()->route('users.index')
            ->with('success', 'User "' . $user->name . '" berhasil diupdate.');
    }
    
    /**
     * Delete user.
     */
    public function destroy(User $user)
    {
        $this->checkAccess($user);
        
        if (auth()->id() == $user->id) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Hanya Super Admin yang dapat menghapus akun Super Admin.');
        }
        
        $userName = $user->name;
        $user->delete();
        
        return redirect()->route('users.index')
            ->with('success', 'User "' . $userName . '" berhasil dihapus.');
    }
    
    /**
     * Toggle user status (active/inactive).
     */
    public function toggleStatus(User $user)
    {
        $this->checkAccess($user);
        
        if (auth()->id() == $user->id) {
            return back()->with('error', 'Tidak dapat mengubah status akun sendiri.');
        }
        
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return back()->with('success', 'User "' . $user->name . '" berhasil ' . $status . '.');
    }
    
    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $this->checkAccess($user);
        
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);
        
        $user->update(['password' => Hash::make($request->password)]);
        
        return back()->with('success', 'Password user "' . $user->name . '" berhasil direset.');
    }
    
    /**
     * Assign gereja to user.
     */
    public function assignGereja(Request $request, User $user)
    {
        $this->checkAccess($user);
        
        $request->validate([
            'gereja_id' => 'required|exists:gerejas,id',
        ]);
        
        $gereja = Gereja::find($request->gereja_id);
        
        if ($gereja) {
            $user->update([
                'gereja_id' => $gereja->id,
                'keuskupan_id' => $gereja->keuskupan_id,
            ]);
            
            return redirect()->route('users.index')
                ->with('success', 'User "' . $user->name . '" berhasil ditugaskan ke gereja "' . $gereja->nama . '".');
        }
        
        return back()->with('error', 'Gereja tidak ditemukan.');
    }
    
    /**
     * Get users by role (API).
     */
    public function byRole($roleName)
    {
        $users = User::role($roleName)->get(['id', 'name', 'email']);
        return response()->json($users);
    }
    
    /**
     * Get users by gereja (API).
     */
    public function byGereja($gerejaId)
    {
        $users = User::where('gereja_id', $gerejaId)->get(['id', 'name', 'email']);
        return response()->json($users);
    }
    
    /**
     * Get users by keuskupan (API).
     */
    public function byKeuskupan($keuskupanId)
    {
        $users = User::where('keuskupan_id', $keuskupanId)->get(['id', 'name', 'email']);
        return response()->json($users);
    }
    
    /**
     * Bulk assign users to gereja.
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'gereja_id' => 'required|exists:gerejas,id',
        ]);
        
        $gereja = Gereja::find($request->gereja_id);
        
        if (!$gereja) {
            return back()->with('error', 'Gereja tidak ditemukan.');
        }
        
        $count = 0;
        
        foreach ($request->user_ids as $userId) {
            $user = User::find($userId);
            if ($user && $this->checkAccessSilent($user)) {
                if (!$user->isAdminKeuskupan() && !$user->isSuperAdmin()) {
                    $user->update([
                        'gereja_id' => $gereja->id,
                        'keuskupan_id' => $gereja->keuskupan_id,
                    ]);
                    $count++;
                }
            }
        }
        
        return redirect()->route('users.index')
            ->with('success', $count . ' user berhasil ditugaskan ke gereja "' . $gereja->nama . '".');
    }
    
    /**
     * Search users (API for AJAX).
     */
    public function search(Request $request)
    {
        $query = User::query();
        
        $loggedInUser = auth()->user();
        
        // Filter berdasarkan role yang login
        if ($loggedInUser->isSuperAdmin()) {
            // Super Admin: cari SEMUA user
        } elseif ($loggedInUser->isAdminKeuskupan()) {
            $query->where('keuskupan_id', $loggedInUser->keuskupan_id);
        } elseif ($loggedInUser->isAdminGereja()) {
            $query->where('gereja_id', $loggedInUser->gereja_id);
        } elseif ($loggedInUser->level_akses === 'pic_group') {
            // PIC Group: hanya cari user dengan duty_id yang sama (kecuali dirinya sendiri)
            if ($loggedInUser->duty_id) {
                $query->where('duty_id', $loggedInUser->duty_id)
                      ->where('id', '!=', $loggedInUser->id);
            } else {
                $query->where('id', null);
            }
        } elseif ($loggedInUser->isUser()) {
            $query->where('id', $loggedInUser->id);
        }
        
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }
        
        if ($request->filled('role')) {
            $query->role($request->role);
        }
        
        $users = $query->limit(10)->get(['id', 'name', 'email', 'level_akses']);
        
        return response()->json($users);
    }
    
    /**
     * Export users to Excel/CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        
        $query = User::with(['keuskupan', 'gereja']);
        
        // Filter berdasarkan role yang login
        if ($user->isSuperAdmin()) {
            // Super Admin: export SEMUA user
        } elseif ($user->isAdminKeuskupan()) {
            $query->where('keuskupan_id', $user->keuskupan_id);
        } elseif ($user->isAdminGereja()) {
            $query->where('gereja_id', $user->gereja_id);
        } elseif ($user->level_akses === 'pic_group') {
            // PIC Group: hanya export user dengan duty_id yang sama (kecuali dirinya sendiri)
            if ($user->duty_id) {
                $query->where('duty_id', $user->duty_id)
                      ->where('id', '!=', $user->id);
            } else {
                $query->where('id', null);
            }
        } elseif ($user->isUser()) {
            $query->where('id', $user->id);
        }
        
        $users = $query->get();
        
        $filename = 'users_export_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');
        
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($handle, ['ID', 'Nama', 'Email', 'Level Akses', 'Keuskupan', 'Gereja', 'No. Telepon', 'Status', 'Tanggal Dibuat']);
        
        foreach ($users as $userItem) {
            fputcsv($handle, [
                $userItem->id,
                $userItem->name,
                $userItem->email,
                $userItem->level_akses,
                $userItem->keuskupan->name ?? '-',
                $userItem->gereja->nama ?? '-',
                $userItem->phone ?? '-',
                $userItem->is_active ? 'Aktif' : 'Nonaktif',
                $userItem->created_at ? $userItem->created_at->format('d/m/Y H:i') : '-'
            ]);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Check if current user has access to target user.
     */
    private function checkAccess(User $targetUser)
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        if ($user->isAdminKeuskupan()) {
            if ($targetUser->keuskupan_id !== $user->keuskupan_id) {
                abort(403, 'Anda tidak memiliki akses ke user ini.');
            }
            return true;
        }
        
        if ($user->isAdminGereja()) {
            if ($targetUser->gereja_id !== $user->gereja_id) {
                abort(403, 'Anda tidak memiliki akses ke user ini.');
            }
            return true;
        }
        
        if ($user->level_akses === 'pic_group') {
            if (!$user->duty_id) {
                abort(403, 'Anda tidak memiliki duty group.');
            }
            
            if ($targetUser->duty_id !== $user->duty_id) {
                abort(403, 'Anda hanya dapat mengakses user dalam duty group yang sama.');
            }
            
            if ($targetUser->id === $user->id) {
                abort(403, 'Anda tidak dapat mengakses data diri sendiri.');
            }
            
            return true;
        }
        
        if ($user->isUser() && $targetUser->id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke user ini.');
        }
        
        return true;
    }
    
    /**
     * Check access silently (without abort) for bulk operations
     */
    private function checkAccessSilent(User $targetUser)
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        if ($user->isAdminKeuskupan()) {
            return $targetUser->keuskupan_id === $user->keuskupan_id;
        }
        
        if ($user->isAdminGereja()) {
            return $targetUser->gereja_id === $user->gereja_id;
        }
        
        if ($user->level_akses === 'pic_group') {
            return $user->duty_id === $targetUser->duty_id && $user->id !== $targetUser->id;
        }
        
        if ($user->isUser()) {
            return $targetUser->id === $user->id;
        }
        
        return false;
    }
}