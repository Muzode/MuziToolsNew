<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminLoanController;
use App\Http\Controllers\AdminReturnController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PeminjamController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\UserController;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
// Login & Logout (Semua Role)
Route::get('/', function () {
    // Jika user sudah login, redirect ke dashboard sesuai role
    if (Auth::check()) {
        $role = Auth::user()->role;
        if ($role == 'admin') return redirect('/admin/dashboard');
        if ($role == 'petugas') return redirect('/petugas/dashboard');
        return redirect('/peminjam/dashboard');
    }
    // Jika belum login, tampilkan halaman welcome
    return view('welcome');
})->name('home');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Group Admin (CRUD User, Alat, Kategori, Log)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index']);
    Route::resource('users', UserController::class); // CRUD User
    Route::resource('tools', ToolController::class); // CRUD Alat
    Route::resource('categories', CategoryController::class); // CRUD Kategori
    Route::resource('admin/loans', AdminLoanController::class)->names('admin.loans');
    Route::resource('admin/returns', AdminReturnController::class)->names('admin.returns');
    Route::get('/admin/logs', function () {
        $logs = ActivityLog::with('user')->latest()->get();
        return view('admin.logs', compact('logs'));
    });
    // CRUD Peminjaman (Admin bisa full akses)
});
// Group Petugas (Approval, Memantau, Laporan)
Route::middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/petugas/dashboard', [PetugasController::class, 'index']);
    Route::post('/petugas/approve/{id}', [PetugasController::class, 'approve']); // Menyetujui
    Route::post('/petugas/return/{id}', [PetugasController::class, 'processReturn']); // Pengembalian
    Route::get('/petugas/laporan', [PetugasController::class, 'report']); // Cetak Laporan
});
// Group Peminjam (Lihat alat, Ajukan pinjam)
Route::middleware(['auth', 'role:peminjam'])->group(function () {
    Route::get('/peminjam/dashboard', [PeminjamController::class, 'index']); // Daftar Alat
    Route::post('/peminjam/ajukan', [PeminjamController::class, 'store']); // Mengajukan
    Route::get('/peminjam/riwayat', [PeminjamController::class, 'history']); // Riwayat & Kembalikan
});
