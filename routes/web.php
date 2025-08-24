<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DocTypeController;
use App\Http\Controllers\Admin\DocItemController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Front\BrowseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

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
});
// Load routes autentikasi (login, register, forgot password, dll)
require __DIR__ . '/auth.php';