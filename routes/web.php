<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DocTypeController;
use App\Http\Controllers\Admin\DocItemController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Front\BrowseController;
use App\Http\Controllers\Admin\AccessController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\FormBuilderController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Front\FormEntryController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('home'))
        ->name('dashboard');


  // FRONT: isi form
  Route::get('/form/{form:slug}', [FormEntryController::class,'show'])->name('form.fill');
  Route::post('/form/{form:slug}', [FormEntryController::class,'store'])->name('form.submit');
  Route::get('/entry/{entry}', [FormEntryController::class,'showEntry'])->name('form.entry.show');

  // ADMIN: form builder & approvals
  Route::prefix('admin')->middleware('role:super_admin,admin')->name('admin.')->group(function(){
    Route::get('forms', [FormBuilderController::class,'index'])->name('forms.index');
    Route::get('forms/create', [FormBuilderController::class,'create'])->name('forms.create');
    Route::post('forms', [FormBuilderController::class,'store'])->name('forms.store');
    Route::get('forms/{form:slug}/edit', [FormBuilderController::class,'edit'])->name('forms.edit');
    Route::put('forms/{form:slug}', [FormBuilderController::class,'update'])->name('forms.update');

    Route::post('forms/{form:slug}/fields', [FormBuilderController::class,'addField'])->name('forms.fields.store');
    Route::delete('forms/{form:slug}/fields/{field}', [FormBuilderController::class,'deleteField'])->name('forms.fields.destroy');

    Route::get('approvals', [ApprovalController::class,'index'])->name('approvals.index');
    Route::post('approvals/{entry}/decide', [ApprovalController::class,'decide'])->name('approvals.decide');

    // export
    Route::get('forms/{form:slug}/export/excel', [FormBuilderController::class,'exportExcel'])->name('forms.export.excel');
    Route::get('forms/{form:slug}/export/pdf', [FormBuilderController::class,'exportPdf'])->name('forms.export.pdf');
  });
});

// halaman utama → browse (user)
Route::get('/', [BrowseController::class, 'index'])->name('home');

// USER browse dokumen
Route::prefix('d')->group(function () {
    // /d/hrga
    Route::get('/{department:slug}', [BrowseController::class, 'department'])->name('browse.department');

    // /d/hrga/sop
    Route::get('/{department:slug}/{docType:slug}', [BrowseController::class, 'listing'])->name('browse.list');

    // /d/hrga/sop/mandi
    Route::get('/{department:slug}/{docType:slug}/{item:slug}', [BrowseController::class, 'item'])->name('browse.item');
});

// detail dokumen
Route::get('/doc/{document:slug}', [BrowseController::class, 'show'])->name('browse.show');
Route::get('/doc/{document:slug}/download', [BrowseController::class, 'download'])->name('browse.download');

// =================================================================
// ADMIN AREA
// =================================================================
Route::middleware(['auth','role:super_admin,admin'])->prefix('admin')->name('admin.')->group(function () {
    // hanya super admin
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('departments', DepartmentController::class);
        Route::resource('doc-types', DocTypeController::class);
    });

    // admin departemen & super admin
    Route::resource('doc-items', DocItemController::class);
    Route::resource('documents', DocumentController::class);

     Route::get('departments/{department:slug}/access', [AccessController::class,'index'])
        ->name('departments.access.index');

    Route::post('departments/{department:slug}/access', [AccessController::class,'store'])
        ->name('departments.access.store');

    Route::delete('departments/{department:slug}/access/{access}', [AccessController::class,'destroy'])
        ->name('departments.access.destroy');
});
// Load routes autentikasi (login, register, forgot password, dll)
require __DIR__ . '/auth.php';