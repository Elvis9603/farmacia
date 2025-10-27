<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CategoriasController;


use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportesController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth:web'])->group(function () {
    Route::get('/',[HomeController::class,'index'])->name('home');
    Route::get('/productos', [ProductosController::class, 'index'])->name('productos.index');
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/categorias', [CategoriasController::class, 'index'])->name('categorias.index');
    Route::get('/reportes/ventas', [ReportesController::class, 'ventas'])->name('reportes.ventas');
    Route::get('/reportes/ventas/pdf', [ReportesController::class, 'exportPDF'])->name('reportes.ventas.pdf');
    Route::get('/reportes/inventario', [ReportesController::class, 'inventario'])->name('reportes.inventario');
    Route::get('/reportes/inventario/pdf', [ReportesController::class, 'exportInventarioPDF'])->name('reportes.inventario.pdf');
    Route::get('/reportes/felcc/pdf', [ReportesController::class, 'exportInventario2PDF'])->name('reportes.felcc.pdf');
    
    
});
// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });
