<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Pelapor;
use App\Http\Controllers\Petugas;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Publik
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1')->name('login.store');

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1')->name('register.store');
});

/*
|--------------------------------------------------------------------------
| Semua pengguna yang sudah login
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', fn () => redirect(auth()->user()->dashboardRoute()))->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'password'])->name('profile.password');

    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikasi/baca-semua', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::get('/notifikasi/{id}', [NotificationController::class, 'read'])->name('notifications.read');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', Admin\DashboardController::class)->name('dashboard');

    // Laporan & verifikasi
    Route::get('laporan', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('verifikasi', [Admin\ReportController::class, 'verification'])->name('verifications');
    Route::get('laporan/{report}', [Admin\ReportController::class, 'show'])->name('reports.show');
    Route::post('laporan/{report}/terima', [Admin\ReportController::class, 'accept'])->name('reports.accept');
    Route::post('laporan/{report}/tolak', [Admin\ReportController::class, 'reject'])->name('reports.reject');
    Route::post('laporan/{report}/setujui', [Admin\ReportController::class, 'approve'])->name('reports.approve');
    Route::post('laporan/{report}/kembalikan', [Admin\ReportController::class, 'returnWork'])->name('reports.return');
    Route::delete('laporan/{report}', [Admin\ReportController::class, 'destroy'])->name('reports.destroy');

    // Penugasan petugas
    Route::get('penugasan', [Admin\AssignmentController::class, 'index'])->name('assignments.index');
    Route::post('laporan/{report}/penugasan', [Admin\AssignmentController::class, 'store'])->name('assignments.store');
    Route::put('penugasan/{assignment}', [Admin\AssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('penugasan/{assignment}', [Admin\AssignmentController::class, 'destroy'])->name('assignments.destroy');

    // Master data
    Route::resource('pengguna', Admin\UserController::class)->parameters(['pengguna' => 'user'])->names('users')->except('show');
    Route::resource('kategori', Admin\CategoryController::class)->parameters(['kategori' => 'category'])->names('categories')->except('show');
    Route::resource('ruangan', Admin\RoomController::class)->parameters(['ruangan' => 'room'])->names('rooms')->except('show');

    // Pemantauan
    Route::get('progres', [Admin\ProgressController::class, 'index'])->name('progress');
    Route::get('aktivitas', [Admin\ActivityController::class, 'index'])->name('activities');

    // Cetak PDF
    Route::get('cetak', [Admin\ExportController::class, 'index'])->name('exports.index');
    Route::get('cetak/pdf', [Admin\ExportController::class, 'pdf'])->name('exports.pdf');

    // Pengaturan website
    Route::get('pengaturan', [Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::put('pengaturan', [Admin\SettingController::class, 'update'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| Pelapor (siswa / guru)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:pelapor'])->prefix('pelapor')->name('pelapor.')->group(function () {
    Route::get('dashboard', Pelapor\DashboardController::class)->name('dashboard');
    Route::resource('laporan', Pelapor\ReportController::class)->parameters(['laporan' => 'report'])->names('reports');
});

/*
|--------------------------------------------------------------------------
| Petugas sarana & prasarana
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('dashboard', Petugas\DashboardController::class)->name('dashboard');
    Route::get('tugas', [Petugas\TaskController::class, 'index'])->name('tasks.index');
    Route::get('riwayat', [Petugas\TaskController::class, 'history'])->name('tasks.history');
    Route::get('tugas/{report}', [Petugas\TaskController::class, 'show'])->name('tasks.show');
    Route::post('tugas/{report}/mulai', [Petugas\TaskController::class, 'start'])->name('tasks.start');
    Route::post('tugas/{report}/kirim', [Petugas\TaskController::class, 'submit'])->name('tasks.submit');
    Route::post('tugas/{report}/catatan', [Petugas\TaskController::class, 'storeLog'])->name('logs.store');
    Route::put('catatan/{log}', [Petugas\TaskController::class, 'updateLog'])->name('logs.update');
    Route::delete('catatan/{log}', [Petugas\TaskController::class, 'destroyLog'])->name('logs.destroy');
});
