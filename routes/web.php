<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CandidateInfoController;
use App\Http\Controllers\Admin\AgendaController;
use App\Http\Controllers\Admin\PersonaController;
use App\Http\Controllers\Admin\EncuestaController as AdminEncuestaController;
use App\Http\Controllers\EncuestaController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat', [ChatController::class, 'send'])->name('chat.send');
Route::post('/chat/registrar', [ChatController::class, 'registrar'])->name('chat.register');
Route::get('/api/seccion', [ChatController::class, 'buscarSeccion'])->name('api.seccion');
Route::get('/conoceme', [EncuestaController::class, 'index'])->name('conoceme.index');
Route::post('/api/encuesta', [EncuestaController::class, 'store'])->name('encuesta.store');
Route::get('/mapa', [MapController::class, 'index'])->name('mapa.index');
Route::get('/api/ranking', [MapController::class, 'ranking'])->name('api.ranking');
Route::get('/api/metas', [MapController::class, 'metas'])->name('api.metas');
Route::get('/api/dashboard', [MapController::class, 'dashboard'])->name('api.dashboard');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/mapa', [MapController::class, 'index'])->name('mapa.index');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('info', CandidateInfoController::class)->except(['show']);
        Route::resource('agenda', AgendaController::class)->except(['show']);
        Route::resource('personas', PersonaController::class)->except(['show']);
        Route::get('encuestas', [AdminEncuestaController::class, 'index'])->name('encuestas.index');
    });
});

require __DIR__.'/auth.php';
