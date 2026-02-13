<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\AdminDomainController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Community profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User domain routes
    Route::resource('domains', DomainController::class)->only(['index', 'create', 'store', 'destroy']);
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/domains', [AdminDomainController::class, 'index'])->name('domains.index');
    Route::post('/domains/{domain}/approve', [AdminDomainController::class, 'approve'])->name('domains.approve');
    Route::post('/domains/{domain}/reject', [AdminDomainController::class, 'reject'])->name('domains.reject');
    Route::post('/domains/{domain}/suspend', [AdminDomainController::class, 'suspend'])->name('domains.suspend');
    Route::post('/domains/{domain}/retry', [AdminDomainController::class, 'retryProvision'])->name('domains.retryProvision');
});

require __DIR__.'/auth.php';
