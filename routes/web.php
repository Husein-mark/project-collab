<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Items / Katalog
Route::middleware('auth')->group(function () {
    Route::get('/items', [\App\Http\Controllers\ItemController::class, 'index'])->name('items.index');

    // Pengajuan & Peminjaman Siswa (US #2, #8)
    Route::get('/loans/create', [\App\Http\Controllers\LoanController::class, 'create'])->name('loans.create');
    Route::post('/loans', [\App\Http\Controllers\LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans/my', [\App\Http\Controllers\LoanController::class, 'myLoans'])->name('loans.my');

    // Approval Guru (US #3)
    Route::get('/loans', [\App\Http\Controllers\LoanController::class, 'index'])->name('loans.index');
    Route::post('/loans/{loan}/approve', [\App\Http\Controllers\LoanController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/reject', [\App\Http\Controllers\LoanController::class, 'reject'])->name('loans.reject');

    // Pencatatan Kondisi Barang (US #5)
    Route::get('/loans/{loan}/inspect', [\App\Http\Controllers\LoanController::class, 'showInspect'])->name('loans.inspect');
    Route::post('/loans/{loan}/handover', [\App\Http\Controllers\LoanController::class, 'updateHandover'])->name('loans.handover');
    Route::post('/loans/{loan}/return', [\App\Http\Controllers\LoanController::class, 'processReturn'])->name('loans.processReturn');

    // Notice & Notifikasi Kerusakan (US #6)
    Route::get('/notices', [\App\Http\Controllers\DamageNoticeController::class, 'index'])->name('notices.index');
    Route::post('/notices/{notice}/status', [\App\Http\Controllers\DamageNoticeController::class, 'updateStatus'])->name('notices.updateStatus');

    // Laporan & Monitoring Otomatis (US #10)
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
});

// Dashboard placeholder for now
Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isGuru()) {
        return view('dashboard.guru');
    }
    return view('dashboard.siswa');
})->name('dashboard');
