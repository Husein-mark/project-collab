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
});

// Dashboard placeholder for now
Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isGuru()) {
        return view('dashboard.guru');
    }
    return view('dashboard.siswa');
})->name('dashboard');
