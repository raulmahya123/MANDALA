<?php

use Illuminate\Support\Facades\Route;

// =====================
// Admin Controllers
// =====================
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DocTypeController;
use App\Http\Controllers\Admin\DocItemController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\AccessController;
use App\Http\Controllers\Admin\FormBuilderController;
use App\Http\Controllers\Admin\ApprovalController;

// =====================
// Front Controllers
// =====================
use App\Http\Controllers\Front\BrowseController;
use App\Http\Controllers\Front\FormEntryController;

/*
|--------------------------------------------------------------------------
| Public (Guest + Auth)
|--------------------------------------------------------------------------
*/

// Halaman utama → browse (user)
Route::get('/', [BrowseController::class, 'index'])->name('home');

// USER browse dokumen (slug binding: department, docType, item)
Route::prefix('d')->group(function () {
    // /d/{department}
    Route::get('/{department:slug}', [BrowseController::class, 'department'])->name('browse.department');

    // /d/{department}/{docType}
    Route::get('/{department:slug}/{docType:slug}', [BrowseController::class, 'listing'])->name('browse.list');

    // /d/{department}/{docType}/{item}
    Route::get('/{department:slug}/{docType:slug}/{item:slug}', [BrowseController::class, 'item'])->name('browse.item');
});

// Detail dokumen + download
Route::get('/doc/{document:slug}', [BrowseController::class, 'show'])->name('browse.show');
Route::get('/doc/{document:slug}/download', [BrowseController::class, 'download'])->name('browse.download');


/*
|--------------------------------------------------------------------------
| Authenticated (User)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Dashboard → redirect ke home
    Route::get('/dashboard', fn () => redirect()->route('home'))->name('dashboard');

    // Isi form (FormEntry)
    Route::get('/form/{form:slug}', [FormEntryController::class,'show'])->name('form.fill');
    Route::post('/form/{form:slug}', [FormEntryController::class,'store'])->name('form.submit');
    Route::get('/entry/{entry}', [FormEntryController::class,'showEntry'])->name('form.entry.show');
});


/*
|--------------------------------------------------------------------------
| Admin Area (super_admin + admin)
|--------------------------------------------------------------------------
|
| Catatan:
| - Middleware 'role:super_admin,admin' mengizinkan kedua peran.
| - Subgroup 'role:super_admin' khusus super admin.
|
*/
Route::middleware(['auth','role:super_admin,admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Shortcut /admin/access → ke daftar departemen
    Route::get('/access', fn () => redirect()->route('admin.departments.index'))
        ->name('access.dashboard');

    /*
    |---------------------------------------
    | Hanya Super Admin
    |---------------------------------------
    */
    Route::middleware('role:super_admin')->group(function () {
        // Master Department & Doc Types
        Route::resource('departments', DepartmentController::class);
        Route::resource('doc-types', DocTypeController::class);

        // Kelola Admin Departemen (angkat/cabut)
        Route::post('departments/{department:slug}/admins', [DepartmentController::class,'addAdmin'])
            ->name('departments.admins.add');
        Route::delete('departments/{department:slug}/admins/{user}', [DepartmentController::class,'removeAdmin'])
            ->name('departments.admins.remove');
    });

    /*
    |---------------------------------------
    | Admin Departemen + Super Admin
    |---------------------------------------
    */
    // Item & Dokumen
    Route::resource('doc-items', DocItemController::class);
    Route::resource('documents', DocumentController::class);

    // Akses per Department (kelola visibilitas/kontributor)
    Route::get('departments/{department:slug}/access', [AccessController::class,'index'])
        ->name('departments.access.index');
    Route::post('departments/{department:slug}/access', [AccessController::class,'store'])
        ->name('departments.access.store');
    Route::delete('departments/{department:slug}/access/{access}', [AccessController::class,'destroy'])
        ->name('departments.access.destroy');

    // Form Builder
    Route::get('forms', [FormBuilderController::class,'index'])->name('forms.index');
    Route::get('forms/create', [FormBuilderController::class,'create'])->name('forms.create');
    Route::post('forms', [FormBuilderController::class,'store'])->name('forms.store');
    Route::get('forms/{form:slug}/edit', [FormBuilderController::class,'edit'])->name('forms.edit');
    Route::put('forms/{form:slug}', [FormBuilderController::class,'update'])->name('forms.update');

    // Kelola Fields Form
    Route::post('forms/{form:slug}/fields', [FormBuilderController::class,'addField'])->name('forms.fields.store');
    Route::delete('forms/{form:slug}/fields/{field}', [FormBuilderController::class,'deleteField'])->name('forms.fields.destroy');

    // Approvals (pengajuan form)
    Route::get('approvals', [ApprovalController::class,'index'])->name('approvals.index');
    Route::post('approvals/{entry}/decide', [ApprovalController::class,'decide'])->name('approvals.decide');

    // Export (letakkan sekali di sini, tidak duplikat)
    Route::get('forms/{form:slug}/export/excel', [FormBuilderController::class,'exportExcel'])->name('forms.export.excel');
    Route::get('forms/{form:slug}/export/pdf',   [FormBuilderController::class,'exportPdf'])->name('forms.export.pdf');
});

// Routes autentikasi (login, register, forgot password, dll)
require __DIR__ . '/auth.php';
