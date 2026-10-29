<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\ReporteFinanciero;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Catalogo;
use Illuminate\Support\Facades\DB;
use App\Models\ValorRatio;

class EstadoResultadosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;
        $empresa = Empresa::find($empresaId);
        
        // Consulta para obtener años únicos
        $listaAnios = ReporteFinanciero::select('anio')
            ->whereHas('catalogo', function($query) use ($empresa) {
                $query->where('empresa_id_empresa', $empresa->id_empresa);
            })
            ->where('balance', false)
            ->distinct()
            ->orderBy('anio', 'desc')
            ->pluck('anio')
            ->toArray();
        $aniosDisponibles = $listaAnios; 
        

        return view('EstadoResultados.listaEstadoResultados', [
            'titulo' => 'Estado de Resultados',
            'listaAnios' => $listaAnios,
            'aniosDisponibles' => $aniosDisponibles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $anio = $request->query('anio');
        
        return view('EstadoResultados.cargarExcel', [
            'titulo' => 'Subir ER',
            'anio' => $anio 
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'anio' => 'required|integer|min:2017'
        ]);

        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;
        $anio = $request->anio;

        $existe = ReporteFinanciero::where('anio', $anio)
            ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->where('anio', $anio)
            ->where('balance', false)
            ->exists();

        if ($existe) {
            return redirect()->route('estado-resultados.index')->with('error', 'Error: El año ya existe');
        }

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getPathname());
            $hoja = $spreadsheet->getActiveSheet()->toArray();

            $errores = [];
            $exitos = 0;

            foreach ($hoja as $i => $fila) {
                if ($i === 0) continue;

                $nombreCuenta = trim($fila[0] ?? '');
                $saldo = floatval($fila[1] ?? 0);

                $cuenta = Catalogo::where('empresa_id_empresa', $empresaId)
                    ->where('nombre_cuenta', $nombreCuenta)->first();

                if (!$cuenta) {
                    // $errores[] = "Fila " . ($i + 1) . ": cuenta no encontrada";
                    // continue;
                   $cuenta = Catalogo::create([
                    'empresa_id_empresa' => $empresaId,
                    'nombre_cuenta'      => $nombreCuenta,
                    'codigo_cuenta'      => '9999', // puedes definir tu lógica
                    'tipo_cuenta'        => 'Otro', // o usar tu match para asignar tipo según código
                ]);
                }

                ReporteFinanciero::create([
                    'anio' => $anio,
                    'balance' => false,
                    'saldo' => $saldo,
                    'catalogo_id_catalogo' => $cuenta->id_catalogo
                ]);

                $exitos++;
            }

            if ($errores) {
                return redirect()->route('estado-resultados.index')->with('warning', "Importación parcial: $exitos cuentas. Errores: " . implode(', ', $errores));
            }

            return redirect()->route('estado-resultados.index')->with('success', "ER cargado exitosamente ($exitos cuentas)");
        } catch (\Exception $e) {
            return redirect()->route('estado-resultados.index')->with('error', 'Error al procesar el archivo');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $anio)
    {
        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;

        $estadoResultados = ReporteFinanciero::with('catalogo')
            ->where('anio', $anio)
            ->where('balance', false)
            ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->orderBy('id_reporte', 'asc')
            ->get();

        return view('EstadoResultados.verEstadoResultados', compact('estadoResultados', 'anio'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $anio)
    {
        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;

    try {
        DB::beginTransaction();

        // Eliminar los reportes financieros del Estado de Resultados
        $reportesEliminados = ReporteFinanciero::where('anio', $anio)
            ->where('balance', false)
            ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->delete();

        // Eliminar los valores de ratios del año
        $ratiosEliminados = \App\Models\ValorRatio::where('anio_ratio', $anio)
            ->where('empresa_id_empresa', $empresaId)
            ->delete();

        DB::commit();

        if ($reportesEliminados > 0 || $ratiosEliminados > 0) {
            return redirect()->route('estado-resultados.index')
                ->with('success', "Estado de Resultados del año {$anio} eliminado exitosamente ({$reportesEliminados} reportes, {$ratiosEliminados} ratios)");
        } else {
            return redirect()->route('estado-resultados.index')
                ->with('warning', "No se encontraron datos del año {$anio} para eliminar");
        }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('estado-resultados.index')
                ->with('error', 'Error al eliminar el Estado de Resultados: ' . $e->getMessage());
        }
    }

    public function analisis(Request $request)
    {
        $request->validate([
            'pasado' => 'required|integer|min:2017|different:actual',
            'actual' => 'required|integer|min:2017|gte:pasado'
        ]);

        $anio_menor = $request->pasado;
        $anio_mayor = $request->actual;

        $analisisHorizontal = $this->horizontal($anio_menor, $anio_mayor);
        $verticalMenorMap = $this->vertical($anio_menor)->keyBy('catalogo.nombre_cuenta');
        $verticalMayorMap = $this->vertical($anio_mayor)->keyBy('catalogo.nombre_cuenta');
        
        if (empty($analisisHorizontal) || $verticalMenorMap->isEmpty() || $verticalMayorMap->isEmpty()) {
            return redirect()->route('estado-resultados.index')->with('error', 'No se encontraron datos suficientes para comparar ' . $anio_menor . ' y ' . $anio_mayor);
        }
        $resultadosCompletos = [];
        foreach ($analisisHorizontal as $itemH) {
            $cuenta = $itemH['cuenta'];
            
            // Buscar los datos verticales
            $itemV_Menor = $verticalMenorMap->get($cuenta);
            $itemV_Mayor = $verticalMayorMap->get($cuenta);

            $resultadosCompletos[] = [
                'cuenta' => $cuenta,
                'saldo_menor' => $itemH['saldo_menor'],
                'saldo_mayor' => $itemH['saldo_mayor'],
                'cambio' => $itemH['cambio'],
                'porcentaje_cambio' => $itemH['porcentaje_cambio'],
                
                // Añadir los porcentajes verticales
                'vertical_menor' => $itemV_Menor ? $itemV_Menor->analisisVertical : 0,
                'vertical_mayor' => $itemV_Mayor ? $itemV_Mayor->analisisVertical : 0,
            ];
        }
        return view('EstadoResultados.analisisVerHor', [
            'titulo' => 'Análisis de Estado de Resultados',
            'anio_menor' => $anio_menor,
            'anio_mayor' => $anio_mayor,
            'resultadosCompletos' => $resultadosCompletos, // <-- La nueva variable
        ]);
    }
    public function vertical(string $anio)
    {
        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;

        $estadoResultados = ReporteFinanciero::with('catalogo')
            ->where('anio', $anio)
            ->where('balance', false)
            ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->orderBy('id_reporte', 'asc')
            ->get();

        if ($estadoResultados->isEmpty()) {
            return $estadoResultados; // Devolver vacío        
        }
        $ventasNetas = $estadoResultados[0]->saldo ?? 0; 

        foreach ($estadoResultados as $item) {
            if ($ventasNetas != 0) {
                $item->analisisVertical = ($item->saldo / $ventasNetas) * 100;
            } else {
                $item->analisisVertical = 0; // Evitar división por cero
            }
        }
        return $estadoResultados;
    }
    // public function horizontal(string $anio_menor, string $anio_mayor)
    // {
    //     $usuario = Usuario::find(session('usuario_id'));
    //     $empresaId = $usuario->id_empresa;

    //     $estadosResultados = ReporteFinanciero::with('catalogo')
    //         ->whereIn('anio', [$anio_menor, $anio_mayor])
    //         ->where('balance', false)
    //         ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
    //         ->orderBy('catalogo_id_catalogo', 'asc')
    //         ->get()
    //         ->groupBy('anio');

    //     $resultadosMenor = $estadosResultados[$anio_menor] ?? collect();
    //     $resultadosMayor = $estadosResultados[$anio_mayor] ?? collect();

    //     $analisisHorizontal = [];

    //     foreach ($resultadosMenor as $itemMenor) {
    //         $itemMayor = $resultadosMayor->firstWhere('catalogo_id_catalogo', $itemMenor->catalogo_id_catalogo);
    //         if ($itemMayor) {
    //             $cambio = $itemMayor->saldo - $itemMenor->saldo;
    //             $porcentajeCambio = ($itemMenor->saldo != 0) ? ($cambio / abs($itemMenor->saldo)) * 100 : 0;

    //             $analisisHorizontal[] = [
    //                 'cuenta' => $itemMenor->catalogo->nombre_cuenta,
    //                 'saldo_menor' => $itemMenor->saldo,
    //                 'saldo_mayor' => $itemMayor->saldo,
    //                 'cambio' => $cambio,
    //                 'porcentaje_cambio' => $porcentajeCambio
    //             ];
    //         }
    //     }

    //     return $analisisHorizontal;
    // }
    public function horizontal(string $anio_menor, string $anio_mayor)
    {
        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;

        $datosMayor = ReporteFinanciero::with('catalogo')
            ->where('anio', $anio_mayor)
            ->where('balance', false)
            ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->orderBy('id_reporte', 'asc')
            ->get();

        if ($datosMayor->isEmpty()) {
            return [];
        }

        $datosMenorMap = ReporteFinanciero::with('catalogo')
            ->where('anio', $anio_menor)
            ->where('balance', false)
            ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->get()
            ->keyBy('catalogo_id_catalogo');
            
        if ($datosMenorMap->isEmpty()) {
            return [];
        }
        $analisisHorizontal = [];

        foreach ($datosMayor as $itemMayor) {
            $idCuenta = $itemMayor->catalogo_id_catalogo;
            $saldoMayor = $itemMayor->saldo;
            $itemMenor = $datosMenorMap->get($idCuenta);
            $saldoMenor = $itemMenor ? $itemMenor->saldo : 0; 
            $cambio = $saldoMayor - $saldoMenor;
            $porcentajeCambio = 0;
            if ($saldoMenor != 0) {
                $porcentajeCambio = ($cambio / abs($saldoMenor)) * 100; 
            } elseif ($saldoMayor != 0) {
                $porcentajeCambio = 100.0;
            }

            $analisisHorizontal[] = [
                'cuenta' => $itemMayor->catalogo->nombre_cuenta,
                'saldo_menor' => $saldoMenor,
                'saldo_mayor' => $saldoMayor,
                'cambio' => $cambio,
                'porcentaje_cambio' => $porcentajeCambio
            ];
        }

        return $analisisHorizontal;
    }
    /**
     * Método privado para leer archivo Excel
     */
    // private function leerEstadoExcel($file, int $idEmpresa, int $anio): array
    // {
    //     $reporteList = [];
        
    //     // Nombres de cuentas válidas que se pueden crear automáticamente
    //     $nombresCuentasValidas = [
    //         "Ingresos de operación", "Total ingresos de operación",
    //         "Costos de operación", "Total costos de operación",
    //         "Gastos de operación", "Total gastos de operación",
    //         "Otros ingresos", "Total otros ingresos",
    //         "Otros gastos", "Total otros gastos",
    //         "Otros ingresos y gastos, neto", "Otros ingresos y gastos, netos",
    //         "Utilidad antes de gastos", "Utilidad de operación", "Utilidad en operación", "Utilidad operativa",
    //         "Utilidad antes de impuestos", "Utilidad neta", "Utilidad Bruta",
    //         "Total activos", "Activo total", "Activos total", "Total activo", 
    //         "Total activo circulante", "Total activos circulantes", 
    //         "Total activo corriente", "Total activos corrientes",
    //         "Total pasivos", "Total pasivo", "Total pasivos circulantes", 
    //         "Total pasivo circulante", "Total pasivo corriente", 
    //         "Total patrimonio", "Total pasivo y patrimonio",
    //         "Total ingresos", "Total egresos", "Resultado del ejercicio", 
    //         "Resultado acumulado", "Resultados del período - Utilidad neta del año", 
    //         "Resultado integrales totales del año", "Patrimonio neto", 
    //         "Total patrimonio neto", "Capital contable", "Total capital contable",
    //         "Total otros gastos, netos", "Utilidad antes de impuestos sobre la renta",
    //         "Total otros activos", "Total otros pasivos", 
    //         "Total otros ingresos y gastos", "Total de gastos de operación", 
    //         "Total Otros gastos, neto", "Ventas totales", "Administrativos",
    //         "Gastos de Administración", "Gastos de administración", 
    //         "Ventas", "ventas", "Venta", "venta", 
    //         "Ventas Netas", "Ventas netas", "Impuestos"
    //     ];

    //     try {
    //         $spreadsheet = IOFactory::load($file->getPathname());
    //         $sheet = $spreadsheet->getActiveSheet()->toArray();

    //         $errores = [];
    //         $exitos = 0;

    //         foreach ($hoja as $i => $fila) {
    //             if ($i === 0) continue;

    //             $nombreCuenta = trim($fila[0] ?? '');
    //             $saldo = floatval($fila[1] ?? 0);

    //             $cuenta = Catalogo::where('empresa_id_empresa', $empresaId)
    //                 ->where('nombre_cuenta', $nombreCuenta)->first();

    //             // Si no existe, verificar si es una cuenta válida para crear
    //             if (!$cuenta) {
    //                 $cuentaValida = false;
                    
    //                 foreach ($nombresCuentasValidas as $cuentaPermitida) {
    //                     if (strcasecmp($cuentaPermitida, trim($nombreCuenta)) === 0) {
    //                         $cuentaValida = true;
    //                         break;
    //                     }
    //                 }
    //                 if ($cuentaValida) {
    //                     // Crear nueva cuenta
    //                     $catalogo = Catalogo::create([
    //                         'codigo_cuenta' => ' ',
    //                         'nombre_cuenta' => $nombreCuenta,
    //                         'empresa_id_empresa' => $idEmpresa,
    //                         'tipo_cuenta' => 'ER'
    //                     ]);
                            
    //                 } else {
    //                     break;
    //                 }
    //                 // Agregar a la lista de reportes para insertar
    //                 $reporteList[] = [
    //                     'anio' => $anio,
    //                     'saldo' => $saldo,
    //                     'balance' => false,
    //                     'catalogo_id_catalogo' => $catalogo->id_catalogo
    //                 ];
    //             }
    //         }
    //         return $reporteList;
    //     } catch (\Exception $e) {
    //         throw new \Exception("Error al procesar el archivo: " . $e->getMessage());
    //     }
        
    // }
}
