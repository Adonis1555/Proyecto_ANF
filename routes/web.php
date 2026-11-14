<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect()->route('login');
});


Route::get('/dashboard', function () {
    if (!session()->has('usuario_id')) {
        return redirect()->route('login');
    }
    return view('dashboard');
})->name('dashboard');

use App\Http\Controllers\SectorController;

Route::get('/sectores', [SectorController::class, 'index'])->name('sector.index');
Route::post('/sectores', [SectorController::class, 'store'])->name('sector.store');
Route::put('/sectores/{id}', [SectorController::class, 'update'])->name('sector.update');
Route::delete('/sectores/{id}', [SectorController::class, 'destroy'])->name('sector.destroy');

use App\Http\Controllers\EmpresaController;

Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresa.index');
Route::post('/empresas', [EmpresaController::class, 'store'])->name('empresa.store');
Route::put('/empresas/{id}', [EmpresaController::class, 'update'])->name('empresa.update');
Route::delete('/empresas/{id}', [EmpresaController::class, 'destroy'])->name('empresa.destroy');

use App\Http\Controllers\CatalogoController;

Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');
Route::post('/catalogo', [CatalogoController::class, 'store'])->name('catalogo.store');
Route::put('/catalogo/{id}', [CatalogoController::class, 'update'])->name('catalogo.update');
Route::delete('/catalogo/{id}', [CatalogoController::class, 'destroy'])->name('catalogo.destroy');

Route::get('/catalogo/importar', [CatalogoController::class, 'importarVista'])->name('catalogo.importarVista');
Route::post('/catalogo/importar', [CatalogoController::class, 'importar'])->name('catalogo.importar');

use App\Http\Controllers\BalanceGeneralController;

Route::get('/balance', [BalanceGeneralController::class, 'index'])->name('balance.index');
Route::get('/balance/ver/{anio}', [BalanceGeneralController::class, 'verPorAnio'])->name('balance.ver');
Route::get('/balance/importar', [BalanceGeneralController::class, 'importarVista'])->name('balance.importarVista');
Route::post('/balance/importar', [BalanceGeneralController::class, 'importar'])->name('balance.importar');
Route::delete('/balance/{anio}', [BalanceGeneralController::class, 'destroy'])->name('balance.eliminar');

use App\Http\Controllers\ComparacionSectorController;
//comparacion por sector
Route::get('/comparacion/sector', [ComparacionSectorController::class, 'index'])-> name('comparacion.sector.index');
Route::post('/comparacion/sector', [ComparacionSectorController::class, 'comparar'])->name('comparacion.sector');

use App\Http\Controllers\ComparacionPromedioController;
//comparacion por promedio en todas las empresas registradas
Route::get('/comparacion/promedio', [ComparacionPromedioController::class, 'index'])->name('comparacion.promedio.index');
Route::post('/comparacion/promedio', [ComparacionPromedioController::class, 'comparar'])->name('comparacion.promedio');

use App\Http\Controllers\ParametroSectorController;
//Estos sirven para poner los parametros de los ratios  por sectores
Route::get('/sector/{id}/parametros', [ParametroSectorController::class, 'edit'])->name('sector.parametros');
Route::post('/sector/{id}/parametros', [ParametroSectorController::class, 'update'])->name('sector.parametros.update');





use App\Http\Controllers\ComparacionRatiosController;
// Rutas para Comparación de Ratios
Route::prefix('comparacion-ratios')->name('comparacion.')->group(function () {
    Route::get('/', [ComparacionRatiosController::class, 'index'])->name('index');
    Route::get('/obtener-sectores', [ComparacionRatiosController::class, 'getSectores'])->name('sectores');
    Route::get('/obtener-empresas-sector', [ComparacionRatiosController::class, 'getEmpresasPorSector'])->name('empresas.sector');
    Route::get('/obtener-empresas', [ComparacionRatiosController::class, 'getTodasEmpresas'])->name('empresas');
    Route::get('/obtener-anios', [ComparacionRatiosController::class, 'getAniosDisponibles'])->name('anios');
    Route::post('/comparar', [ComparacionRatiosController::class, 'compararRatios'])->name('comparar');
});




use App\Http\Controllers\GraficoController;

Route::get('/graficos', [GraficoController::class, 'index'])->name('graficos.index');
Route::get('/graficos/empresas/{sectorId}', [GraficoController::class, 'getEmpresas'])->name('graficos.empresas');
Route::get('/graficos/ratios', [GraficoController::class, 'getRatios'])->name('graficos.ratios');
Route::post('/graficos/datos', [GraficoController::class, 'getDatosGrafico'])->name('graficos.datos');
use App\Http\Controllers\EstadoResultadosController;
Route::get('/estado-resultados', [EstadoResultadosController::class, 'index'])->name('estado-resultados.index');
Route::get('/estado-resultados/create', [EstadoResultadosController::class, 'create'])->name('estado-resultados.create');
Route::get('/estado-resultados/analisis', [EstadoResultadosController::class, 'analisis'])->name('estado-resultados.analisis');
Route::post('/estado-resultados', [EstadoResultadosController::class, 'store'])->name('estado-resultados.store');
Route::get('/estado-resultados/{id}', [EstadoResultadosController::class, 'show'])->name('estado-resultados.show');
Route::delete('/estado-resultados/{id}', [EstadoResultadosController::class, 'destroy'])->name('estado-resultados.destroy');

use App\Http\Controllers\RatioController;
Route::get('/ratios', [RatioController::class, 'index'])->name('ratios.index');
Route::post('/ratios', [RatioController::class, 'store'])->name('ratios.store');

Route::get('/balance/analisis', [BalanceGeneralController::class, 'analisisBalance'])->name('balance.analisis');

//Proyeccion
use App\Http\Controllers\ProyeccionVentasController;

Route::prefix('proyeccion-ventas')->name('proyeccion.')->group(function () {
    Route::get('/', [ProyeccionVentasController::class, 'index'])->name('index');
    Route::post('/proyectar', [ProyeccionVentasController::class, 'proyectar'])->name('proyectar');
    Route::get('/descargar-plantilla', [ProyeccionVentasController::class, 'descargarPlantilla'])->name('plantilla');
});


// Importa la clase correcta del controlador:
use App\Http\Controllers\AnalisisFinancieroController;

Route::prefix('analisis')->name('analisis.')->group(function () {
    // Vista principal para seleccionar tipo de análisis, empresa y años
    Route::get('/', [AnalisisFinancieroController::class, 'index'])->name('index');
    
    // Procesar / Obtener datos del Análisis Vertical
    Route::get('/vertical', [AnalisisFinancieroController::class, 'analisisVertical'])->name('vertical');
    
    // Procesar / Obtener datos del Análisis Horizontal
    Route::get('/horizontal', [AnalisisFinancieroController::class, 'analisisHorizontal'])->name('horizontal');
    
    // Opción combinada
    Route::post('/generar', [AnalisisFinancieroController::class, 'generar'])->name('generar');


    Route::get('/historial-vertical', [AnalisisFinancieroController::class, 'historialVertical'])->name('historial.vertical');
    
    Route::get('/historial-horizontal', [AnalisisFinancieroController::class, 'historialHorizontal'])->name('historial.horizontal');

});

