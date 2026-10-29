<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReporteFinanciero;
use App\Models\Usuario;
use App\Models\Ratio;
use App\Models\ValorRatio;
use App\Models\Catalogo;

class RatioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;

        // Obtener años que tienen AMBOS reportes (ER y BG)
        $aniosConER = ReporteFinanciero::select('anio')
            ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->where('balance', false)
            ->distinct()
            ->pluck('anio');

        $aniosConBG = ReporteFinanciero::select('anio')
            ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->where('balance', true)
            ->distinct()
            ->pluck('anio');

        // Intersección: años que tienen ambos reportes
        $aniosDisponibles = $aniosConER->intersect($aniosConBG)
            ->sort()
            ->values()
            ->reverse()
            ->toArray();

        return view('Ratios.ratios', [
            'titulo' => 'Ratios Financieros',
            'aniosDisponibles' => $aniosDisponibles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'anio' => 'required|integer|min:2017'
        ]);
        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;
        $anio = $request->anio;

        // Calcular y guardar los ratios
        $ratios = $this->calcularRatiosFinancieros($empresaId, $anio);
        
        return view('Ratios.ratios', [
            'titulo' => 'Ratios Financieros',
            'aniosDisponibles' => [],
            'ratiosCalculados' => $ratios,
            'anioSeleccionado' => $anio
        ]);
    }
    /**
     * Calcular ratios financieros basados en códigos de catálogo
     */
    private function calcularRatiosFinancieros(int $empresaId, int $anio)
    {
        // Obtener todos los reportes del balance general del año
        $reportesBG = ReporteFinanciero::with('catalogo')
            ->whereHas('catalogo', function($query) use ($empresaId) {
                $query->where('empresa_id_empresa', $empresaId);
            })
            ->where('anio', $anio)
            ->where('balance', true)
            ->get();

        // Obtener todos los reportes del estado de resultados del año
        $reportesER = ReporteFinanciero::with('catalogo')
            ->whereHas('catalogo', function($query) use ($empresaId) {
                $query->where('empresa_id_empresa', $empresaId);
            })
            ->where('anio', $anio)
            ->where('balance', false)
            ->orderBy('id_reporte', 'asc')
            ->get();

        // Organizar reportes por código de cuenta
        $cuentasBG = $reportesBG->keyBy(function($item) {
            return $item->catalogo->codigo_cuenta;
        });

        // $cuentasER = $reportesER->keyBy(function($item) {
        //     return $item->catalogo->codigo_cuenta;
        // });

        // ============================================
        // BALANCE GENERAL
        // ============================================
        
        // Activo Corriente (códigos que empiezan con 1.1 o 11)
        $activoCorriente = $this->sumarPorPrefijo($cuentasBG, ['1.1', '11']);
        
        // Efectivo y Equivalentes (1.1.1 o 111)
        $efectivo = $this->sumarPorPrefijo($cuentasBG, ['1.1.01', '1101', '1.1.1', '111']);
        
        // Cuentas por Cobrar (1.1.2 o 112)
        $cuentasPorCobrar = $this->sumarPorPrefijo($cuentasBG, ['1.1.02', '1102', '1.1.2', '112']);
        
        // Inventarios (1.1.4 o 114)
        $inventarios = $this->sumarPorPrefijo($cuentasBG, ['1.1.04', '1104', '1.1.4', '114']);

        // Activo No Corriente (1.2 o 12)
        $activoNoCorriente = $this->sumarPorPrefijo($cuentasBG, ['1.2', '12']);
        
        // Total Activos (1 o códigos que empiecen con 1)
        $totalActivos = $this->sumarPorPrefijo($cuentasBG, ['1']);

        // PASIVOS
        // Pasivo Corriente (2.1 o 21)
        $pasivoCorriente = $this->sumarPorPrefijo($cuentasBG, ['2.1', '21']);
        
        // Pasivo No Corriente (2.2 o 22)
        $pasivoNoCorriente = $this->sumarPorPrefijo($cuentasBG, ['2.2', '22']);
        
        // Total Pasivos (2)
        $totalPasivos = $this->sumarPorPrefijo($cuentasBG, ['2']);

        // PATRIMONIO
        // Total Patrimonio (3)
        $totalPatrimonio = $this->sumarPorPrefijo($cuentasBG, ['3']);

        // ============================================
        // ESTADO DE RESULTADOS
        // ============================================
        
        // Ventas o Ingresos
        $ventas = $reportesER[0]->saldo;
        
        // Costo de Ventas
        $costoVentas = $reportesER[1]->saldo;

        $utilidadNeta = $reportesER->last()->saldo;

        // ============================================
        // CALCULAR RATIOS FINANCIEROS
        // ============================================

        $ratios = [];

        // 1. RATIOS DE LIQUIDEZ
        // Liquidez Corriente = Activo Corriente / Pasivo Corriente
        if ($pasivoCorriente != 0) {
            $ratios['Liquidez Corriente'] = round($activoCorriente / $pasivoCorriente, 2);
        }

        // Prueba Ácida = (Activo Corriente - Inventarios) / Pasivo Corriente
        if ($pasivoCorriente != 0) {
            $ratios['Prueba Ácida'] = round(($activoCorriente - $inventarios) / $pasivoCorriente, 2);
        }

        // Capital de Trabajo = (Activo Corriente - Pasivo Corriente) / Total Activos
        if ($totalActivos != 0) {
            $ratios['Capital de Trabajo'] = round(($activoCorriente - $pasivoCorriente) / $totalActivos, 2);
        }

        // 2. RATIOS DE ENDEUDAMIENTO

        // Grado de Endeudamiento = (Total Pasivos / Total Activos)
        if ($totalActivos != 0) {
            $ratios['Grado de Endeudamiento'] = round(($totalPasivos / $totalActivos), 2);
        }

        // Grado de propiedad = (Total Patrimonio / Total Activos)
        if ($totalActivos != 0) {
            $ratios['Grado de Propiedad'] = round(($totalPatrimonio / $totalActivos), 2);
        }

        // Endeudamiento del Patrimonio = Total Pasivos / Total Patrimonio
        if ($totalPatrimonio != 0) {
            $ratios['Endeudamiento Patrimonial'] = round($totalPasivos / $totalPatrimonio, 2);
        }

        // 3. RATIOS DE RENTABILIDAD
        // Rentabilidad sobre ventas = (Utilidad Neta / Ventas) * 100
        if ($ventas != 0) {
            $ratios['Rentabilidad sobre Ventas'] = round(($utilidadNeta / $ventas) * 100, 2);
        }

        // 4. RATIOS DE ACTIVIDAD
        // Rotación de Inventarios = Costo de Ventas / Inventarios
        if ($inventarios != 0) {
            $ratios['Rotación de Inventario'] = round($costoVentas / $inventarios, 2);
        }
        //Días de Inventario = Inventario Promedio / (Costo de las Ventas / 365)
        if ($costoVentas != 0) {
            $ratios['Días de Inventario'] = round($inventarios / ($costoVentas / 365), 2);
        }

        // Rotación de Cuentas por Cobrar = Ventas / Cuentas por Cobrar
        if ($cuentasPorCobrar != 0) {
            $ratios['Rotación de Cuentas por Cobrar'] = round($ventas / $cuentasPorCobrar, 2);
        }

        //Periodo medio de cobranza = ( Prom. Ctas. por Cob. Comerciales x 365) / Ventas Netas
        if ($ventas != 0) {
            $ratios['Periodo Medio de Cobranza'] = round(($cuentasPorCobrar * 365) / $ventas, 2);
        }

        // Rotación de Activos Totales = Ventas / Total Activos
        if ($totalActivos != 0) {
            $ratios['Rotación de Activos Totales'] = round($ventas / $totalActivos, 2);
        }

        // ============================================
        // GUARDAR RATIOS EN LA BASE DE DATOS
        // ============================================
        // Traer todos los ratios en orden por id_ratio
        $ratiosBase = Ratio::orderBy('id_ratio', 'asc')->get();

        foreach ($ratiosBase as $ratioBase) {
            $nombre = $ratioBase->nombre_ratio; // o el campo real del nombre del ratio
            $valor = $ratios[$nombre] ?? null;

            if ($valor !== null) {
                ValorRatio::updateOrCreate(
                    [
                        'empresa_id_empresa' => $empresaId,
                        'anio_ratio'         => $anio,
                        'ratio_id_ratio'     => $ratioBase->id_ratio
                    ],
                    [
                        'valor' => $valor
                    ]
                );
            }
        }
        return $ratios;
    }
    /**
     * Sumar saldos de cuentas que comienzan con ciertos prefijos
     */
    private function sumarPorPrefijo($cuentasPorCodigo, array $prefijos): float
    {
        $total = 0;

        foreach ($cuentasPorCodigo as $codigo => $reporte) {
            foreach ($prefijos as $prefijo) {
                $codigoNormalizado = $this->normalizarCodigo($codigo);
                // Verificar si el código comienza exactamente con el prefijo
                if (strpos($codigoNormalizado, $prefijo) === 0) {
                    
                    if (($prefijo === '3' && strlen($codigoNormalizado) > 1) ||
                    ($prefijo !== '3' && strlen($codigoNormalizado) > 2)) {
                    
                    $total += $reporte->saldo;
                    break;
                    }
                }
            }
        }

        return $total;
    }
    private function normalizarCodigo(string $codigo): string
    {
        // Eliminar puntos
        $codigo = str_replace('.', '', $codigo);

        return $codigo;
    }
}
