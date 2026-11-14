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
            ->sortBy('codigo_cuenta')
            ->values();

        return view('analisis.horizontal', compact(
            'empresaId',
            'tipoReporte',
            'anioBase',
            'anioActual',
            'comparativa'
        ));
    }

    // ANÁLISIS VERTICAL
    
    public function analisisVertical(Request $request)
    {
        $datos = $request->validate([
            'empresa_id' => 'required|integer',
            'tipo_reporte' => 'required|in:balance,estado_resultados',
            'anio' => 'required|integer|min:2017|max:2099',
            'cuenta_base_id' => 'nullable|integer',
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
    
        // Agrupar saldos por cuenta contable
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
            ->sortBy('codigo')
            ->values();
    
        // Cuentas que pueden servir como referencia
        $candidatas = $cuentas
            ->filter(fn ($cuenta) => abs($cuenta->saldo) > 0.00000001)
            ->values();
    
        $cuentaBase = null;
    
        // Prioridad: cuenta seleccionada manualmente
        if ($request->filled('cuenta_base_id')) {
            $cuentaBase = $candidatas->first(
                fn ($cuenta) =>
                    (int) $cuenta->id === (int) $datos['cuenta_base_id']
            );
    
            if (!$cuentaBase) {
                throw ValidationException::withMessages([
                    'cuenta_base_id' =>
                        'La cuenta base seleccionada no es válida para este reporte.',
                ]);
            }
        }
    
        // Identificación automática: solo si hay una candidata
        if (!$cuentaBase && $candidatas->count() === 1) {
            $cuentaBase = $candidatas->first();
        }
    
        // Si no hay una base inequívoca, pedir selección
        if (!$cuentaBase) {
            return view('analisis.seleccionar-base', compact(
                'empresaId',
                'tipoReporte',
                'anio',
                'candidatas'
            ));
        }
    
        $saldoBase = $cuentaBase->saldo;
    
        $cuentas = $cuentas->map(function ($cuenta) use ($saldoBase) {
            $cuenta->porcentaje = ($cuenta->saldo / $saldoBase) * 100;
            return $cuenta;
        });
    
        return view('analisis.vertical', compact(
            'empresaId',
            'tipoReporte',
            'anio',
            'cuentas',
            'cuentaBase'
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
