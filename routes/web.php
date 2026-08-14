<?php
// routes/web.php

use App\Http\Controllers\{
    DashboardController,
    ScheduleController,
    DutyController,
    AssignmentController,
    AvailabilityController,
    UserController,
    PermissionController,
    ReportController,
    KeuskupanController,
    GerejaController,
    TransactionController,
    ExportController,
    KalenderLiturgiHariController,
    ProfileController
};

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Church Schedule System Multi Keuskupan
|--------------------------------------------------------------------------
*/

// ============================================
// GUEST ROUTES (Tidak perlu login)
// ============================================
Route::get('/', function () {
    return redirect()->route('login');
});

// ============================================
// AUTHENTICATION ROUTES
// ============================================
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    $credentials = request()->only('email', 'password');
    
    if (Auth::attempt($credentials)) {
        request()->session()->regenerate();
        return redirect()->intended('/dashboard');
    }
    
    return back()->withErrors(['email' => 'Email atau password salah.']);
})->name('login.post');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// ============================================
// PROTECTED ROUTES (Requires Authentication)
// ============================================
Route::middleware(['auth'])->group(function () {
    
    // ============================================
    // DASHBOARD
    // ============================================
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // ============================================
    // KEUSKUPANS
    // ============================================
    Route::prefix('keuskupans')->name('keuskupans.')->group(function () {
        Route::get('/export/form', [KeuskupanController::class, 'showExportForm'])->name('export.form');
        Route::get('/export/all', [KeuskupanController::class, 'exportAll'])->name('export.all');
        Route::post('/export/filtered', [KeuskupanController::class, 'exportFiltered'])->name('export.filtered');
        Route::get('/import/form', [KeuskupanController::class, 'showImportForm'])->name('import.form');
        Route::post('/import', [KeuskupanController::class, 'import'])->name('import');
        Route::get('/template', [KeuskupanController::class, 'downloadTemplate'])->name('template');
        
        Route::get('/{keuskupan}/members', [KeuskupanController::class, 'members'])->name('members');
        Route::get('/{keuskupan}/gerejas', [KeuskupanController::class, 'gerejas'])->name('gerejas');
        Route::get('/{keuskupan}/statistics', [KeuskupanController::class, 'statistics'])->name('statistics');
        Route::patch('/{keuskupan}/toggle-status', [KeuskupanController::class, 'toggleStatus'])->name('toggle-status');
        
        Route::get('/', [KeuskupanController::class, 'index'])->name('index');
        Route::get('/create', [KeuskupanController::class, 'create'])->name('create');
        Route::post('/', [KeuskupanController::class, 'store'])->name('store');
        Route::get('/{keuskupan}', [KeuskupanController::class, 'show'])->name('show');
        Route::get('/{keuskupan}/edit', [KeuskupanController::class, 'edit'])->name('edit');
        Route::put('/{keuskupan}', [KeuskupanController::class, 'update'])->name('update');
        Route::delete('/{keuskupan}', [KeuskupanController::class, 'destroy'])->name('destroy');
    });

    // ============================================
    // GEREJAS
    // ============================================
    Route::get('/gerejas/export/form', [GerejaController::class, 'showExportForm'])->name('gerejas.export.form');
    Route::get('/gerejas/export/all', [GerejaController::class, 'exportAll'])->name('gerejas.export.all');
    Route::post('/gerejas/export/filtered', [GerejaController::class, 'exportFiltered'])->name('gerejas.export.filtered');
    Route::get('/gerejas/import/form', [GerejaController::class, 'showImportForm'])->name('gerejas.import.form');
    Route::post('/gerejas/import', [GerejaController::class, 'import'])->name('gerejas.import');
    Route::get('/gerejas/template', [GerejaController::class, 'downloadTemplate'])->name('gerejas.template');

    Route::get('/gerejas/{gereja}/members', [GerejaController::class, 'members'])->name('gerejas.members');
    Route::get('/gerejas/{gereja}/statistics', [GerejaController::class, 'statistics'])->name('gerejas.statistics');
    Route::post('/gerejas/{gereja}/toggle-status', [GerejaController::class, 'toggleStatus'])->name('gerejas.toggle-status');
    Route::resource('gerejas', GerejaController::class);

    // ============================================
    // SCHEDULES (Jadwal Ibadah)
    // ============================================
    Route::resource('schedules', ScheduleController::class);
    Route::post('schedules/{schedule}/toggle-status', [ScheduleController::class, 'toggleStatus'])->name('schedules.toggle-status');

    // ============================================
    // KALENDER LITURGI
    // ============================================
    Route::prefix('kalender-liturgi')->name('kalender-liturgi.')->group(function () {
        Route::get('/export/form', [KalenderLiturgiHariController::class, 'showExportForm'])->name('export.form');
        Route::get('/export/all', [KalenderLiturgiHariController::class, 'exportAll'])->name('export.all');
        Route::post('/export/filtered', [KalenderLiturgiHariController::class, 'exportFiltered'])->name('export.filtered');
        Route::get('/import/form', [KalenderLiturgiHariController::class, 'showImportForm'])->name('import.form');
        Route::post('/import', [KalenderLiturgiHariController::class, 'import'])->name('import');
        Route::get('/template', [KalenderLiturgiHariController::class, 'downloadTemplate'])->name('template');
        
        Route::post('/{id}/toggle-status', [KalenderLiturgiHariController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/calendar', [KalenderLiturgiHariController::class, 'calendar'])->name('calendar');
        Route::get('/today', [KalenderLiturgiHariController::class, 'today'])->name('today');
        
        Route::get('/', [KalenderLiturgiHariController::class, 'index'])->name('index');
        Route::get('/create', [KalenderLiturgiHariController::class, 'create'])->name('create');
        Route::post('/', [KalenderLiturgiHariController::class, 'store'])->name('store');
        Route::get('/{id}', [KalenderLiturgiHariController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [KalenderLiturgiHariController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KalenderLiturgiHariController::class, 'update'])->name('update');
        Route::delete('/{id}', [KalenderLiturgiHariController::class, 'destroy'])->name('destroy');
    });

    // ============================================
    // DUTIES (Tugas Pelayanan)
    // ============================================
    Route::prefix('duties')->name('duties.')->group(function () {
        Route::get('/export/form', [DutyController::class, 'showExportForm'])->name('export.form');
        Route::get('/export/all', [DutyController::class, 'exportAll'])->name('export.all');
        Route::post('/export/filtered', [DutyController::class, 'exportFiltered'])->name('export.filtered');
        Route::get('/import/form', [DutyController::class, 'showImportForm'])->name('import.form');
        Route::post('/import', [DutyController::class, 'import'])->name('import');
        Route::get('/template', [DutyController::class, 'downloadTemplate'])->name('template');
        
        Route::post('/{duty}/toggle-status', [DutyController::class, 'toggleStatus'])->name('toggle-status');
        
        Route::get('/', [DutyController::class, 'index'])->name('index');
        Route::get('/create', [DutyController::class, 'create'])->name('create');
        Route::post('/', [DutyController::class, 'store'])->name('store');
        Route::get('/{duty}', [DutyController::class, 'show'])->name('show');
        Route::get('/{duty}/edit', [DutyController::class, 'edit'])->name('edit');
        Route::put('/{duty}', [DutyController::class, 'update'])->name('update');
        Route::delete('/{duty}', [DutyController::class, 'destroy'])->name('destroy');
    });

   // ============================================
// ASSIGNMENTS (Penugasan)
// ============================================

Route::prefix('assignments')->name('assignments.')->group(function () {
    // ===== TAMBAHKAN ROUTE INI =====
    Route::post('/take', [AssignmentController::class, 'takeAssignment'])->name('take');
    // ================================
    
    // EXPORT ROUTES
    Route::get('/export/form', [AssignmentController::class, 'showExportForm'])->name('export.form');
    Route::match(['get', 'post'], '/export/all', [AssignmentController::class, 'exportAll'])->name('export.all');
    Route::post('/export/filtered', [AssignmentController::class, 'exportFiltered'])->name('export.filtered');
    
    // IMPORT ROUTES
    Route::get('/import/form', [AssignmentController::class, 'showImportForm'])->name('import.form');
    Route::post('/import', [AssignmentController::class, 'import'])->name('import');
    Route::get('/template', [AssignmentController::class, 'downloadTemplate'])->name('template');
    
    // CHECK DUPLICATE
    Route::post('/check-duplicate', [AssignmentController::class, 'checkDuplicate'])->name('check-duplicate');
    
    // USER ROUTES
    Route::get('/my', [AssignmentController::class, 'myAssignments'])->name('my');
    Route::post('/{assignment}/accept', [AssignmentController::class, 'accept'])->name('accept');
    Route::post('/{assignment}/reject', [AssignmentController::class, 'reject'])->name('reject');
    
    Route::post('/assignments/get-schedule-dates', [AssignmentController::class, 'getScheduleDates'])
    ->name('assignments.get-schedule-dates')
    ->middleware('auth');
    // ADMIN ROUTES
    Route::get('/', [AssignmentController::class, 'index'])->name('index');
    Route::get('/create', [AssignmentController::class, 'create'])->name('create');
    Route::post('/', [AssignmentController::class, 'store'])->name('store');
    Route::get('/{assignment}', [AssignmentController::class, 'show'])->name('show');
    Route::get('/{assignment}/edit', [AssignmentController::class, 'edit'])->name('edit');
    Route::put('/{assignment}', [AssignmentController::class, 'update'])->name('update');
    Route::delete('/{assignment}', [AssignmentController::class, 'destroy'])->name('destroy');
    Route::post('/{assignment}/cancel', [AssignmentController::class, 'cancel'])->name('cancel');
    Route::post('/{assignment}/complete', [AssignmentController::class, 'complete'])->name('complete');
    
    // BULK UPDATE
    Route::post('/bulk-status-update', [AssignmentController::class, 'bulkStatusUpdate'])->name('bulk-status-update');
    
    // REPORT
    Route::get('/report', [AssignmentController::class, 'report'])->name('report');
}); 

    // ============================================
    // AVAILABILITY ROUTES (Ketersediaan)
    // ============================================
    Route::prefix('availability')->name('availability.')->group(function () {
        Route::get('/', [AvailabilityController::class, 'index'])->name('index');
        Route::get('/calendar', [AvailabilityController::class, 'calendar'])->name('calendar');
        Route::post('/bulk-update', [AvailabilityController::class, 'bulkUpdate'])->name('bulk-update');
        
        Route::get('/{assignment}/edit', [AvailabilityController::class, 'edit'])->name('edit');
        Route::put('/{assignment}', [AvailabilityController::class, 'update'])->name('update');
        Route::post('/{assignment}/approve-replacement', [AvailabilityController::class, 'approveReplacement'])->name('approve-replacement');
        Route::post('/{assignment}/reject-replacement', [AvailabilityController::class, 'rejectReplacement'])->name('reject-replacement');
        Route::get('/{assignment}', [AvailabilityController::class, 'show'])->name('show');
    });

    // ============================================
    // TRANSACTION ROUTES
    // ============================================
    Route::resource('transactions', TransactionController::class);
    Route::get('transactions/export/excel', [TransactionController::class, 'exportToExcel'])->name('transactions.export.excel');
    Route::get('transactions/export/pdf', [TransactionController::class, 'exportToPdf'])->name('transactions.export.pdf');
    Route::post('transactions/bulk-delete', [TransactionController::class, 'bulkDelete'])->name('transactions.bulk-delete');
    Route::get('transactions/report/summary', [TransactionController::class, 'report'])->name('transactions.report');
    Route::get('transactions/my-transactions', [TransactionController::class, 'myTransactions'])->name('transactions.my');

    // ============================================
    // USER MANAGEMENT
 // ============================================
// USER MANAGEMENT
// ============================================

Route::get('/users/export/form', [UserController::class, 'showExportForm'])->name('users.export.form');
Route::get('/users/export/all', [UserController::class, 'exportAll'])->name('users.export.all');
Route::post('/users/export/filtered', [UserController::class, 'exportFiltered'])->name('users.export.filtered');
Route::get('/users/import/form', [UserController::class, 'showImportForm'])->name('users.import.form');
Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
Route::get('/users/template', [UserController::class, 'downloadTemplate'])->name('users.template');

Route::get('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
Route::post('/users/{user}/assign-gereja', [UserController::class, 'assignGereja'])->name('users.assign-gereja');

Route::get('/users/role/{role}', [UserController::class, 'byRole'])->name('users.by-role');
Route::get('/users/gereja/{gerejaId}', [UserController::class, 'byGereja'])->name('users.by-gereja');
Route::get('/users/keuskupan/{keuskupanId}', [UserController::class, 'byKeuskupan'])->name('users.by-keuskupan');
Route::post('/users/bulk-assign', [UserController::class, 'bulkAssign'])->name('users.bulk-assign');
Route::get('/users/search', [UserController::class, 'search'])->name('users.search');

// RESOURCE ROUTES (harus di akhir)
Route::resource('users', UserController::class);

    // ============================================
    // PROFILE ROUTES
    // ============================================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('photo');
        Route::delete('/photo', [ProfileController::class, 'removePhoto'])->name('photo.remove');
        Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
    });

    // ============================================
    // PERMISSIONS ROUTES (Hanya Super Admin)
    // ============================================
    Route::middleware(['role:super_admin'])->prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::post('/', [PermissionController::class, 'update'])->name('update');
        Route::post('/reset', [PermissionController::class, 'resetToDefault'])->name('reset');
        Route::post('/add-menu', [PermissionController::class, 'addMenu'])->name('add-menu');
        Route::get('/menu/{id}/edit', [PermissionController::class, 'editMenu'])->name('edit-menu');
        Route::put('/menu/{id}', [PermissionController::class, 'updateMenu'])->name('update-menu');
        Route::put('/menu/{id}/access', [PermissionController::class, 'updateMenuAccess'])->name('update-menu-access');
        Route::delete('/menu/{id}', [PermissionController::class, 'deleteMenu'])->name('delete-menu');
    });

    // ============================================
    // REPORT ROUTES
    // ============================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
        Route::get('/export-all', [ReportController::class, 'exportAll'])->name('export-all');
        Route::get('/transactions', [ReportController::class, 'transactions'])->name('transactions');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/members', [ReportController::class, 'members'])->name('members');
        Route::get('/gerejas-report', [ReportController::class, 'churchesReport'])->name('gerejas');
        Route::post('/export', [ReportController::class, 'export'])->name('export');
    });

    // ============================================
    // EXPORT DATABASE
    // ============================================
    Route::get('/export-database', [ExportController::class, 'index'])->name('export.index');
    Route::get('/export-database/download', [ExportController::class, 'export'])->name('export.export');

    // ============================================
    // API ROUTES for AJAX
    // ============================================
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
        Route::get('gerejas/by-keuskupan/{keuskupanId}', [GerejaController::class, 'getByKeuskupan'])->name('gerejas.by-keuskupan');
        Route::get('users/by-gereja/{gerejaId}', [UserController::class, 'byGereja'])->name('users.by-gereja');
        Route::get('users/by-role/{role}', [UserController::class, 'byRole'])->name('users.by-role');
        Route::get('schedules/upcoming', [ScheduleController::class, 'upcoming'])->name('schedules.upcoming');
    });

}); // END OF AUTH MIDDLEWARE GROUP

// ============================================
// FALLBACK ROUTE (404 Handler)
// ============================================
Route::fallback(function () {
    if (Auth::check()) {
        return redirect()->route('dashboard')->with('error', 'Halaman tidak ditemukan!');
    }
    return redirect()->route('login');
});