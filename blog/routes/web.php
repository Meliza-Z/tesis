<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Importación de controladores
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CreditoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\CuentaPorCobrarController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\DetallesCreditoController;

// Rutas de autenticación (accesibles sin login)
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    
    // Rutas resource completas
    Route::resource('clientes', ClienteController::class);
    Route::get('clientes/{cliente}/creditos', [ClienteController::class, 'creditos'])->name('clientes.creditos');
    Route::post('clientes/{cliente}/creditos', [ClienteController::class, 'storeCredito'])->name('clientes.creditos.store');
    Route::resource('productos', ProductoController::class);
    Route::resource('creditos', CreditoController::class);
    Route::resource('pagos', PagoController::class);
    Route::resource('detalle_credito', DetallesCreditoController::class);
    
    // Cuenta por cobrar con restricción solo para index, edit, update
    Route::resource('cuenta_cobrar', CuentaPorCobrarController::class)
        ->only(['index', 'edit', 'update']);
    Route::post('cuenta-cobrar/{id}/sincronizar', [CuentaPorCobrarController::class, 'sincronizar'])
        ->name('cuenta_cobrar.sincronizar');
    Route::get('cuenta_cobrar/creditos/{credito}', [CuentaPorCobrarController::class, 'showCredit'])->name('cuenta_cobrar.credito');
    Route::post('creditos/{credito}/mark-paid', [CuentaPorCobrarController::class, 'markPaid'])->name('creditos.markPaid');
    
    // Ruta personalizada para mostrar todos los detalles de un crédito por su ID
    Route::get('detalle_credito/{detalle_credito}', [DetallesCreditoController::class, 'show'])->name('detalle_credito.show');
    
    // Ruta para exportar PDF de créditos
    Route::get('/creditos/{credito}/pdf', [CreditoController::class, 'exportarPDF'])->name('creditos.pdf');
    
    // 🔹 RUTAS DE REPORTES CORREGIDAS
    // Página principal de reportes
    Route::get('/reportes', function () {
        $creditos = \App\Models\Credito::with('cliente')->get();
        return view('reportes.index', compact('creditos'));
    })->name('reportes.index');
    
    // Reporte diario - PDF (método simplificado)
    Route::get('/reporte-diario', [ReporteController::class, 'reporteDiarioSimple'])->name('reporte.diario');
    
    // Reporte por cliente - Vista previa
    Route::get('/reporte-cliente/{credito}', [ReporteController::class, 'vistaReportePorCliente'])->name('reporte.cliente');
    
    // Reporte por cliente - Descargar PDF
    Route::get('/reporte-cliente/{credito}/pdf', [ReporteController::class, 'descargarReportePorCliente'])->name('reporte.cliente.pdf');
    
    // 🔹 RUTA PARA DEBUGGING
    Route::get('/verificar-datos', [ReporteController::class, 'verificarDatos'])->name('verificar.datos');
    
    // Rutas resource para reportes (si las necesitas)
    Route::resource('reportes', ReporteController::class)->except(['index']);
    
    
});
