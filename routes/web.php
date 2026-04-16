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
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminLogsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AIChatController;

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
    // ✅ BARU (Di route admin group):
    Route::get('/admin/logs', [AdminLogsController::class, 'index'])->name('admin.logs');    // CRUD Peminjaman (Admin bisa full akses)
    Route::resource('returns', AdminReturnController::class);
    Route::post('/returns/{id}', [AdminReturnController::class, 'store'])->name('returns.store');
});
// Group Petugas (Approval, Memantau, Laporan)
Route::middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/petugas/dashboard', [PetugasController::class, 'index']);
    Route::post('/petugas/approve/{id}', [PetugasController::class, 'approve']); // Menyetujui
    Route::post('/petugas/reject/{id}', [PetugasController::class, 'reject']); // Menolak
    Route::post('/petugas/return/{id}', [PetugasController::class, 'processReturn']); // Pengembalian
    Route::get('/petugas/laporan', [PetugasController::class, 'report']);
});
// Group Peminjam (Lihat alat, Ajukan pinjam)
Route::middleware(['auth', 'role:peminjam'])->group(function () {
    Route::get('/peminjam/dashboard', [PeminjamController::class, 'index']); // Daftar Alat
    Route::post('/peminjam/ajukan', [PeminjamController::class, 'store']); // Mengajukan
    Route::get('/peminjam/riwayat', [PeminjamController::class, 'history'])->name('peminjam.riwayat'); // Riwayat & Kembalikan
    // Route baru untuk ajukan pengembalian
    Route::patch('/request-return/{id}', [PeminjamController::class, 'requestReturn'])->name('peminjam.request-return');

    Route::get('/payments/denda/{loan}', [PaymentController::class, 'showDendaPayment'])->name('payments.denda');
    Route::post('/payments/create-transaction/{loan}', [PaymentController::class, 'createTransaction'])->name('payments.create-transaction');
    Route::get('/payments/check/{orderId}', [PaymentController::class, 'checkStatus'])->name('payments.check');
    Route::get('/payments/finish', [PaymentController::class, 'finish'])->name('payments.finish');
    // routes/web.php - Tambahkan di dalam group auth
    Route::post('/payments/cancel/{orderId}', [PaymentController::class, 'cancelPayment'])->name('payments.cancel');
    Route::post('/payments/manual-update', [PaymentController::class, 'manualUpdate'])->name('payments.manual-update');
});
Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
// Route untuk proses pengembalian dengan method POST
Route::post('/petugas/return/{id}', [PetugasController::class, 'processReturn'])->name('petugas.return');
Route::post('/admin/returns', [AdminReturnController::class, 'store'])
    ->name('admin.returns.store');

// Webhook untuk Midtrans (tanpa auth)
Route::post('/payments/manual-update', [PaymentController::class, 'manualUpdate'])->name('payments.manual-update');

// routes/web.php
Route::post('/ai-chat', [AIChatController::class, 'chat'])->name('ai.chat')->middleware('auth');
