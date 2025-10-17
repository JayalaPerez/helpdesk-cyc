<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminOnly;

// Página principal
Route::get('/', fn () => view('welcome'));

// Dashboard (todos los usuarios logueados lo ven)
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Grupo de rutas protegidas (requiere login)
Route::middleware('auth')->group(function () {
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tickets (CRUD)
    Route::resource('tickets', TicketController::class);

    // Comentarios en tickets
    Route::post('/tickets/{ticket}/comments', [CommentController::class, 'store'])
        ->name('tickets.comments.store');

    // Panel de Administración (solo admin)
    Route::middleware([AdminOnly::class])->group(function () {
        Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::patch('/admin/users/{user}/role', [AdminController::class, 'updateUserRole'])
        ->name('admin.users.updateRole');
    });
});

// Rutas de autenticación (login, register, forgot password, etc.)
require __DIR__.'/auth.php';
