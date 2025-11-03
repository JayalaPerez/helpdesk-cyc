<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\AdminOnly;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\PasswordEntryController; // 👈 NUEVO

// Landing
Route::get('/', fn () => view('welcome'));

// Dashboard (requiere login + verificación)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ===== Rutas protegidas por auth =====
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

    // Descarga de adjuntos de comentarios
    Route::get('/comments/{comment}/download', [CommentController::class, 'download'])
        ->name('comments.download');

    // ======================
    // SOLO ADMIN
    // ======================
    Route::middleware([AdminOnly::class])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Dashboard Admin
            Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

            // Gestión de usuarios (CRUD)
            Route::get('/users',              [AdminUserController::class, 'index'])->name('users.index');
            Route::post('/users',             [AdminUserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit',  [AdminUserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}',       [AdminUserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}',    [AdminUserController::class, 'destroy'])->name('users.destroy');

            // Acciones sobre tickets (estado y asignación)
            //Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])
            //    ->name('tickets.updateStatus');
            //Route::patch('/tickets/{ticket}/assign', [TicketController::class, 'assign'])
            //    ->name('tickets.assign');

            // 👇 NUEVA ruta combinada
            Route::patch('/tickets/{ticket}/admin-update', [TicketController::class, 'adminUpdate'])
                ->name('tickets.adminUpdate');

            // Departamentos
            Route::get('/departments', [\App\Http\Controllers\AdminDepartmentController::class, 'index'])->name('departments.index');
            Route::post('/departments', [\App\Http\Controllers\AdminDepartmentController::class, 'store'])->name('departments.store');
            Route::put('/departments/{department}', [\App\Http\Controllers\AdminDepartmentController::class, 'update'])->name('departments.update');
            Route::delete('/departments/{department}', [\App\Http\Controllers\AdminDepartmentController::class, 'destroy'])->name('departments.destroy');

            // Categorías de tickets
            Route::get('/ticket-categories', [\App\Http\Controllers\AdminTicketCategoryController::class, 'index'])->name('ticket-categories.index');
            Route::post('/ticket-categories', [\App\Http\Controllers\AdminTicketCategoryController::class, 'store'])->name('ticket-categories.store');
            Route::put('/ticket-categories/{ticketCategory}', [\App\Http\Controllers\AdminTicketCategoryController::class, 'update'])->name('ticket-categories.update');
            Route::delete('/ticket-categories/{ticketCategory}', [\App\Http\Controllers\AdminTicketCategoryController::class, 'destroy'])->name('ticket-categories.destroy');
            
            // Eliminar ticket desde panel admin
            Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])
                ->name('tickets.destroy');

            // ======================
            // Gestor de Contraseñas
            // ======================
            // /admin/passwords ...
            Route::resource('passwords', PasswordEntryController::class)
                ->except(['show'])
                ->names('passwords');

            Route::put('/passwords/{password}/baja', [PasswordEntryController::class, 'darDeBaja'])
                ->name('passwords.baja');

            // Tendrás:
            // GET    /admin/passwords            -> admin.passwords.index
            // GET    /admin/passwords/create     -> admin.passwords.create
            // POST   /admin/passwords            -> admin.passwords.store
            // GET    /admin/passwords/{id}/edit  -> admin.passwords.edit
            // PUT    /admin/passwords/{id}       -> admin.passwords.update
            // DELETE /admin/passwords/{id}       -> admin.passwords.destroy
        });
});

// Auth scaffolding (login/logout/etc.)
require __DIR__.'/auth.php';
