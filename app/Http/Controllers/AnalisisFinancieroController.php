<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Usuario;
use App\Models\ReporteFinanciero;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AnalisisFinancieroController extends Controller
{
    // Obtener la empresa asociada al usuario autenticado
    private function empresaActual()
    {
        $usuario = Usuario::find(session('usuario_id'));

        if (!$usuario) {
            abort(401, 'Debes iniciar sesión.');
        }

        if (!$usuario->id_empresa) {
            abort(403, 'El usuario no tiene una empresa asignada.');
        }

        return $usuario->id_empresa;
    }

    // Consulta base de los estados financieros
    private function reportesEmpresa($empresaId, $tipoReporte)
    {
        return ReporteFinanciero::with('catalogo')
            ->where('balance', $tipoReporte === 'balance')
            ->whereHas('catalogo', function ($query) use ($empresaId) {
                $query->where('empresa_id_empresa', $empresaId);
            });
    }

    // Página principal
    public function index()
    {
        if (!session()->has('usuario_id')) {
            return redirect()->route('login');
        }

        $empresaId = $this->empresaActual();

        $empresas = Empresa::where('id_empresa', $empresaId)->get();

        $aniosBalance = $this->reportesEmpresa($empresaId, 'balance')
            ->distinct()
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        $aniosResultados = $this->reportesEmpresa($empresaId, 'estado_resultados')
            ->distinct()
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        return view('analisis.index', compact(
            'empresas',
            'aniosBalance',
            'aniosResultados'
        ));
    }

    // ANÁLISIS HORIZONTAL
    public function analisisHorizontal(Request $request)
    {
        $datos = $request->validate([
            'empresa_id' => 'required|integer',
            'tipo_reporte' => 'required|in:balance,estado_resultados',
            'anio_base' => 'required|integer|min:2017|max:2099',
            'anio_actual' => 'required|integer|min:2017|max:2099|gt:anio_base',
        ]);

        $empresaId = $this->empresaActual();

        abort_unless(
            (int) $datos['empresa_id'] === (int) $empresaId,
            403,
            'No tienes permiso para consultar esta empresa.'
        );

        $tipoReporte = $datos['tipo_reporte'];
        $anioBase = (int) $datos['anio_base'];
        $anioActual = (int) $datos['anio_actual'];

        $reportes = $this->reportesEmpresa($empresaId, $tipoReporte)
            ->whereIn('anio', [$anioBase, $anioActual])
            ->get();

        // Verificar que existan reportes de ambos años
        if (
            !$reportes->contains('anio', $anioBase) ||
            !$reportes->contains('anio', $anioActual)
        ) {
            throw ValidationException::withMessages([
                'anio_base' => 'No existen datos de ambos años para el reporte seleccionado.',
            ]);
        }

        // Agrupar registros por cuenta contable
        $comparativa = $reportes
            ->groupBy('catalogo_id_catalogo')
            ->map(function ($registros) use ($anioBase, $anioActual) {
                $catalogo = $registros->first()->catalogo;

                $saldoBase = (float) $registros
                    ->where('anio', $anioBase)
                    ->sum('saldo');

                $saldoActual = (float) $registros
                    ->where('anio', $anioActual)
                    ->sum('saldo');

                $variacionAbsoluta = $saldoActual - $saldoBase;

                // Si el saldo base es cero, no existe un
                // porcentaje de variación matemáticamente definido.
                $variacionRelativa = abs($saldoBase) > 0.00000001
                    ? ($variacionAbsoluta / abs($saldoBase)) * 100
                    : null;

                return (object) [
                    'codigo_cuenta' => $catalogo->codigo_cuenta,
                    'nombre_cuenta' => $catalogo->nombre_cuenta,
                    'saldo_base' => $saldoBase,
                    'saldo_actual' => $saldoActual,
                    'variacion_absoluta' => $variacionAbsoluta,
                    'variacion_relativa' => $variacionRelativa,
                ];
            })
            ->sortBy('codigo_cuenta', SORT_NATURAL)
            ->values();

        return view('analisis.horizontal', compact(
            'empresaId',
            'tipoReporte',
            'anioBase',
            'anioActual',
            'comparativa'
        ));
    }

    // HELPERS DE ANÁLISIS VERTICAL
    private function clasificarCuentaVertical($cuenta)
    {
        $tipo = mb_strtolower(trim((string) $cuenta->tipo));
        $codigo = trim((string) $cuenta->codigo);

        return match ($tipo) {
            'activo' => 'activo',
            'pasivo' => 'pasivo',
            'patrimonio' => 'patrimonio',
            'ingresos' => 'ingresos',
            'gastos' => 'gastos',
            'costos de venta' => 'costos',
            default => match (substr($codigo, 0, 1)) {
                '1' => 'activo',
                '2' => 'pasivo',
                '3' => 'patrimonio',
                '4' => 'ingresos',
                '5' => 'gastos',
                '6' => 'costos',
                default => null,
            },
        };
    }

    /**
     * Normaliza el código contable quitando separadores y ceros de relleno
     * a la derecha para detectar correctamente la jerarquía padre-hijo.
     */
    private function prefijoJerarquico(string $codigo): string
    {
        $limpio = preg_replace('/[^0-9A-Za-z]/', '', trim($codigo));
        $sinCerosFinales = rtrim($limpio, '0');

        return $sinCerosFinales !== '' ? $sinCerosFinales : $limpio;
    }

    /**
     * Identifica las cuentas de detalle y comprueba
     * los saldos de las cuentas acumuladoras.
     */
    private function prepararCuentasVertical($cuentas)
    {
        $advertencias = collect();

        $cuentas = $cuentas->map(function ($cuenta) {
            $cuenta->codigo = trim((string) $cuenta->codigo);
            $cuenta->prefijo = $this->prefijoJerarquico($cuenta->codigo);
            $cuenta->grupo = $this->clasificarCuentaVertical($cuenta);
            $cuenta->es_detalle = true;
            $cuenta->porcentaje = null;
            return $cuenta;
        });

        // 1. Identificar cuentas acumuladoras (las que tienen descendientes)
        foreach ($cuentas as $cuenta) {
            $tieneDescendientes = $cuentas->contains(function ($otra) use ($cuenta) {
                return $otra->grupo === $cuenta->grupo
                    && $otra->codigo !== $cuenta->codigo
                    && strlen($otra->prefijo) > strlen($cuenta->prefijo)
                    && str_starts_with($otra->prefijo, $cuenta->prefijo);
            });

            if ($tieneDescendientes) {
                $cuenta->es_detalle = false;
            }
        }

        // 2. Verificar que los saldos de las cuentas acumuladoras cuadren con sus detalles
        foreach ($cuentas->where('es_detalle', false) as $cuenta) {
            $saldoHijas = $cuentas->filter(function ($otra) use ($cuenta) {
                return $otra->grupo === $cuenta->grupo
                    && $otra->es_detalle
                    && strlen($otra->prefijo) > strlen($cuenta->prefijo)
                    && str_starts_with($otra->prefijo, $cuenta->prefijo);
            })->sum('saldo');

            if (abs($cuenta->saldo - $saldoHijas) > 0.01) {
                $advertencias->push(
                    "La cuenta {$cuenta->codigo} - {$cuenta->nombre} presenta un saldo de $" .
                    number_format($cuenta->saldo, 2) .
                    ", mientras que sus cuentas de detalle suman $" .
                    number_format($saldoHijas, 2) . "."
                );
            }
        }

        return [$cuentas, $advertencias];
    }

    // ANÁLISIS VERTICAL
    public function analisisVertical(Request $request)
    {
        $datos = $request->validate([
            'empresa_id' => 'required|integer',
            'tipo_reporte' => 'required|in:balance,estado_resultados',
            'anio' => 'required|integer|min:2017|max:2099',
        ]);

        $empresaId = $this->empresaActual();

        abort_unless(
            (int) $datos['empresa_id'] === (int) $empresaId,
            403,
            'No tienes permiso para consultar esta empresa.'
        );

        $tipoReporte = $datos['tipo_reporte'];
        $anio = (int) $datos['anio'];

        $reportes = $this->reportesEmpresa($empresaId, $tipoReporte)
            ->where('anio', $anio)
            ->get();

        if ($reportes->isEmpty()) {
            throw ValidationException::withMessages([
                'anio' => 'No existen reportes para el año seleccionado.',
            ]);
        }

        $cuentas = $reportes
            ->groupBy('catalogo_id_catalogo')
            ->map(function ($registros) {
                $catalogo = $registros->first()->catalogo;

                return (object) [
                    'id' => $catalogo->id_catalogo,
                    'codigo' => $catalogo->codigo_cuenta,
                    'nombre' => $catalogo->nombre_cuenta,
                    'tipo' => $catalogo->tipo_cuenta,
                    'saldo' => (float) $registros->sum('saldo'),
                    'porcentaje' => null,
                ];
            })
            ->sortBy('codigo', SORT_NATURAL)
            ->values();

        [$cuentas, $advertencias] = $this->prepararCuentasVertical($cuentas);

        $sinClasificar = $cuentas->filter(
            fn ($cuenta) => $cuenta->grupo === null
        );

        if ($sinClasificar->isNotEmpty()) {
            throw ValidationException::withMessages([
                'tipo_reporte' =>
                    'Existen cuentas sin clasificación contable: ' .
                    $sinClasificar->pluck('codigo')->implode(', '),
            ]);
        }

        $gruposPermitidos = $tipoReporte === 'balance'
            ? ['activo', 'pasivo', 'patrimonio']
            : ['ingresos', 'costos', 'gastos'];

        $cuentasInvalidas = $cuentas->filter(
            fn ($cuenta) => !in_array($cuenta->grupo, $gruposPermitidos, true)
        );

        if ($cuentasInvalidas->isNotEmpty()) {
            throw ValidationException::withMessages([
                'tipo_reporte' =>
                    'El reporte contiene cuentas que no corresponden ' .
                    'al estado financiero seleccionado: ' .
                    $cuentasInvalidas->pluck('codigo')->implode(', '),
            ]);
        }

        // Sumar únicamente cuentas de detalle para evitar duplicar cuentas acumuladoras
        $detalle = $cuentas
            ->where('es_detalle', true)
            ->values();

        $totales = [];

        if ($tipoReporte === 'balance') {
            $activos = (float) $detalle
                ->where('grupo', 'activo')->sum('saldo');

            $pasivos = (float) $detalle
                ->where('grupo', 'pasivo')->sum('saldo');

            $patrimonio = (float) $detalle
                ->where('grupo', 'patrimonio')->sum('saldo');

            $pasivoPatrimonio = $pasivos + $patrimonio;

            $totales = [
                'activos' => $activos,
                'pasivos' => $pasivos,
                'patrimonio' => $patrimonio,
                'pasivo_patrimonio' => $pasivoPatrimonio,
                'diferencia' => $activos - $pasivoPatrimonio,
            ];

            foreach ($cuentas as $cuenta) {
                $base = $cuenta->grupo === 'activo'
                    ? $activos
                    : $pasivoPatrimonio;

                $cuenta->porcentaje = abs($base) > 0.00000001
                    ? ($cuenta->saldo / $base) * 100
                    : null;
            }
        } else {
            $ingresos = (float) $detalle
                ->where('grupo', 'ingresos')->sum('saldo');

            $costos = (float) $detalle
                ->where('grupo', 'costos')->sum('saldo');

            $gastos = (float) $detalle
                ->where('grupo', 'gastos')->sum('saldo');

            $utilidadBruta = $ingresos - $costos;
            $resultado = $utilidadBruta - $gastos;

            $totales = [
                'ingresos' => $ingresos,
                'costos' => $costos,
                'gastos' => $gastos,
                'utilidad_bruta' => $utilidadBruta,
                'utilidad_neta' => $resultado,
            ];

            foreach ($cuentas as $cuenta) {
                $cuenta->porcentaje = abs($ingresos) > 0.00000001
                    ? ($cuenta->saldo / $ingresos) * 100
                    : null;
            }
        }

        // Organizar las cuentas para la vista
        $grupos = collect($gruposPermitidos)
            ->mapWithKeys(function ($grupo) use ($cuentas) {
                return [
                    $grupo => $cuentas
                        ->where('grupo', $grupo)
                        ->values(),
                ];
            });

        return view('analisis.vertical', compact(
            'empresaId',
            'tipoReporte',
            'anio',
            'cuentas',
            'grupos',
            'totales',
            'advertencias'
        ));
    }

    public function historialVertical()
    {
        $empresaId = $this->empresaActual();

        $reportes = ReporteFinanciero::query()
            ->whereHas('catalogo', function ($query) use ($empresaId) {
                $query->where('empresa_id_empresa', $empresaId);
            })
            ->select('anio', 'balance')
            ->distinct()
            ->orderBy('anio', 'desc')
            ->get();

        $historicos = $reportes->map(function ($reporte) use ($empresaId) {
            return (object) [
                'empresa_id' => $empresaId,
                'anio' => $reporte->anio,
                'tipo_reporte' => $reporte->balance
                    ? 'balance'
                    : 'estado_resultados',
                'nombre_reporte' => $reporte->balance
                    ? 'Balance General'
                    : 'Estado de Resultados',
            ];
        });

        return view('analisis.historial-vertical', compact('historicos'));
    }

    public function historialHorizontal()
    {
        $empresaId = $this->empresaActual();

        $reportes = ReporteFinanciero::query()
            ->whereHas('catalogo', function ($query) use ($empresaId) {
                $query->where('empresa_id_empresa', $empresaId);
            })
            ->select('anio', 'balance')
            ->distinct()
            ->orderBy('anio', 'asc')
            ->get();

        $historicos = collect();

        foreach ([true, false] as $esBalance) {
            $anios = $reportes
                ->filter(function ($reporte) use ($esBalance) {
                    return (bool) $reporte->balance === $esBalance;
                })
                ->pluck('anio')
                ->unique()
                ->sort()
                ->values();

            for ($i = 0; $i < $anios->count(); $i++) {
                for ($j = $i + 1; $j < $anios->count(); $j++) {
                    $historicos->push((object) [
                        'empresa_id' => $empresaId,
                        'anio_base' => $anios[$i],
                        'anio_actual' => $anios[$j],
                        'tipo_reporte' => $esBalance
                            ? 'balance'
                            : 'estado_resultados',
                        'nombre_reporte' => $esBalance
                            ? 'Balance General'
                            : 'Estado de Resultados',
                    ]);
                }
            }
        }

        $historicos = $historicos
            ->sortByDesc('anio_actual')
            ->values();

        return view('analisis.historial-horizontal', compact('historicos'));
    }
}