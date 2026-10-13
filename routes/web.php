<?php

use App\Http\Controllers\Admin\AgendaController;
use App\Http\Controllers\Admin\CandidateInfoController;
use App\Http\Controllers\Admin\ComplainantController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\DemostracionController as AdminDemostracionController;
use App\Http\Controllers\Admin\EncuestaController as AdminEncuestaController;
use App\Http\Controllers\Admin\EncuestaDenunciaController;
use App\Http\Controllers\Admin\LonaController;
use App\Http\Controllers\Admin\PersonaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DemostracionController;
use App\Http\Controllers\EncuestaController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
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
Route::get('/api/secciones-municipios', [MapController::class, 'seccionesMunicipios'])->name('api.secciones_municipios');

// Módulo de Demostración (público, sin contraseña)
Route::get('/demostracion', [DemostracionController::class, 'index'])->name('demostracion.index');
Route::post('/demostracion', [DemostracionController::class, 'store'])->name('demostracion.store');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->name('admin.')->group(function () {

        Route::resource('personas', PersonaController::class)->except(['show']);

        Route::middleware('role:admin')->group(function () {
            Route::resource('info', CandidateInfoController::class)->except(['show']);
            Route::resource('agenda', AgendaController::class)->except(['show']);
            Route::get('programa-conoceme', [AdminEncuestaController::class, 'index'])->name('programa_conoceme.index');
            Route::resource('users', UserController::class)->except(['show']);

            Route::get('/mapa', [MapController::class, 'index'])->name('mapa.index');

            // Módulo de Demostración (listado + envío de SMS)
            Route::prefix('demostracion')->name('demostracion.')->group(function () {
                Route::get('/', [AdminDemostracionController::class, 'index'])->name('index');
                Route::post('/enviar-sms', [AdminDemostracionController::class, 'enviarSms'])->name('sms');
                Route::delete('/{demostracion}', [AdminDemostracionController::class, 'destroy'])->name('destroy');
            });
        });

        // BACKUP: denuncias antiguo → lonas-old (accesible solo por URL)
        Route::prefix('lonas-old')->name('lonas-old.')->group(function () {
            Route::get('/', [ComplaintController::class, 'index'])->name('index');
            Route::get('/create', [ComplaintController::class, 'create'])->name('create');
            Route::post('/', [ComplaintController::class, 'store'])->name('store');
            Route::get('/{complaint}', [ComplaintController::class, 'show'])->name('show');
            Route::get('/{complaint}/edit', [ComplaintController::class, 'edit'])->name('edit');
            Route::put('/{complaint}', [ComplaintController::class, 'update'])->name('update');
            Route::delete('/{complaint}', [ComplaintController::class, 'destroy'])->name('destroy');
            Route::get('/{complaint}/word', [ComplaintController::class, 'generateWord'])->name('word');
            Route::get('/{complaint}/pdf', [ComplaintController::class, 'generatePdf'])->name('pdf');
        });

        // Catálogo de Denunciantes (menu Jurídico, admin only)
        Route::middleware('role:admin')->group(function () {
            Route::resource('denunciantes', ComplainantController::class)->except(['show']);
        });

        // NUEVO: hub de denuncias
        Route::get('denuncias', function () {
            return Inertia::render('Admin/Denuncias/HubIndex');
        })->name('denuncias.index');

        // NUEVO: submódulos de denuncias
        Route::prefix('denuncias')->name('denuncias.')->group(function () {
            Route::get('lonas', [LonaController::class, 'index'])->name('lonas.index');
            Route::get('lonas/create', [LonaController::class, 'create'])->name('lonas.create');
            Route::post('lonas', [LonaController::class, 'store'])->name('lonas.store');
            Route::get('lonas/{lona}', [LonaController::class, 'show'])->name('lonas.show');
            Route::get('lonas/{lona}/edit', [LonaController::class, 'edit'])->name('lonas.edit');
            Route::put('lonas/{lona}', [LonaController::class, 'update'])->name('lonas.update');
            Route::get('lonas/{lona}/verify', [LonaController::class, 'verify'])->name('lonas.verify');
            Route::post('lonas/{lona}/verify', [LonaController::class, 'confirmVerify'])->name('lonas.verify.confirm');
            Route::delete('lonas/{lona}', [LonaController::class, 'destroy'])->name('lonas.destroy');
            Route::get('lonas/{lona}/word', [LonaController::class, 'generateWord'])->name('lonas.word');
            Route::get('servidores', function () {
                return Inertia::render('Admin/Denuncias/Servidores/Index');
            })->name('servidores.index');
            Route::get('encuestas', [EncuestaDenunciaController::class, 'index'])->name('encuestas.index');
            Route::get('encuestas/create', [EncuestaDenunciaController::class, 'create'])->name('encuestas.create');
            Route::post('encuestas', [EncuestaDenunciaController::class, 'store'])->name('encuestas.store');
            Route::get('encuestas/{encuesta}', [EncuestaDenunciaController::class, 'show'])->name('encuestas.show');
            Route::get('encuestas/{encuesta}/edit', [EncuestaDenunciaController::class, 'edit'])->name('encuestas.edit');
            Route::put('encuestas/{encuesta}', [EncuestaDenunciaController::class, 'update'])->name('encuestas.update');
            Route::get('encuestas/{encuesta}/verify', [EncuestaDenunciaController::class, 'verify'])->name('encuestas.verify');
            Route::post('encuestas/{encuesta}/verify', [EncuestaDenunciaController::class, 'confirmVerify'])->name('encuestas.verify.confirm');
            Route::delete('encuestas/{encuesta}', [EncuestaDenunciaController::class, 'destroy'])->name('encuestas.destroy');
            Route::get('encuestas/{encuesta}/word', [EncuestaDenunciaController::class, 'generateWord'])->name('encuestas.word');
            Route::get('sonrie', function () {
                return Inertia::render('Admin/Denuncias/Sonrie/Index');
            })->name('sonrie.index');
        });
    });
});

require __DIR__.'/auth.php';
