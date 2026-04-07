<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IncidentController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Worker\DashboardController as WorkerDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isInstructor()) {
        return redirect()->route('instructor.dashboard');
    } elseif ($user->isTrabajador()) {
        return redirect()->route('worker.dashboard');
    }

    // Si no tiene un rol válido, cerrar sesión y redirigir al login
    Auth::logout();
    return redirect()->route('login')->with('error', 'No tienes permisos para acceder al sistema.');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::view('/settings', 'settings.index')->name('settings.index');
    Route::view('/support', 'support.index')->name('support.index');

    // Rutas de notificaciones
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{id}/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
});

Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('/tasks', \App\Http\Controllers\Admin\TaskController::class)->except(['create']);
    Route::get('/tasks-export-pdf', [\App\Http\Controllers\Admin\TaskController::class, 'exportPDF'])->name('tasks.export-pdf');
    Route::put('/tasks/{task}/review', [\App\Http\Controllers\Admin\TaskController::class, 'reviewTask'])->name('tasks.review');
    Route::resource('/incidents', IncidentController::class)->only(['index', 'show', 'store']);
    Route::post('/incidents/{incident}/convert-to-task', [IncidentController::class, 'convertToTask'])->name('incidents.convert-to-task');
});

Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
    Route::resource('/incidents', \App\Http\Controllers\Instructor\IncidentController::class)->only(['index', 'show', 'store']);
});

Route::middleware(['auth', 'role:trabajador'])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/dashboard', [WorkerDashboardController::class, 'index'])->name('dashboard');
    Route::resource('/tasks', \App\Http\Controllers\Worker\TaskController::class)->only(['index', 'show', 'update']);
});

require __DIR__ . '/auth.php';
