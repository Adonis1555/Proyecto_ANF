<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReporteFinanciero;
use App\Models\Usuario;
use App\Models\Catalogo;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use App\Models\ValorRatio;

class BalanceGeneralController extends Controller
{
    public function index()
    {
        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;

        $anios = ReporteFinanciero::whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->where('balance', true)
            ->distinct()->orderBy('anio', 'desc')->pluck('anio');

        return view('balance.index', compact('anios'));
    }

    public function verPorAnio($anio)
    {
        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;

        $balance = ReporteFinanciero::with('catalogo')
            ->where('anio', $anio)
            ->where('balance', true) 
            ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->get();

        return view('balance.ver', compact('balance', 'anio'));
    }

    public function importarVista(Request $request)
    {
        $anio = $request->query('anio');
        return view('balance.importar', compact('anio'));
    }

    public function importar(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'anio' => 'required|integer|min:2017'
        ]);

        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;
        $anio = $request->anio;

        $existe = ReporteFinanciero::where('anio', $anio)
            ->where('balance', true)
            ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->exists();

        if ($existe) {
            return redirect()->route('balance.index')->with('error', 'Error: Ya existe un balance para ese año.');
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

                if (!$nombreCuenta) {
                    $errores[] = "Fila " . ($i + 1) . ": nombre de cuenta vacío";
                    continue;
                }

                $cuenta = Catalogo::where('empresa_id_empresa', $empresaId)
                    ->whereRaw('LOWER(TRIM(nombre_cuenta)) = ?', [mb_strtolower($nombreCuenta)])
                    ->first();

                if (!$cuenta) {
                    $errores[] = "Fila " . ($i + 1) . ": cuenta no encontrada ({$nombreCuenta})";
                    continue;
                }

                ReporteFinanciero::create([
                    'anio' => $anio,
                    'balance' => true,
                    'saldo' => $saldo,
                    'catalogo_id_catalogo' => $cuenta->id_catalogo
                ]);

                $exitos++;
            }

            if ($errores) {
                return redirect()->route('balance.index')->with('warning', "Importación parcial: $exitos cuentas. Errores: " . implode(', ', $errores));
            }

            return redirect()->route('balance.index')->with('success', "Balance general cargado exitosamente ($exitos cuentas)");
        } catch (\Exception $e) {
            return redirect()->route('balance.index')->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    private function clasificarCuentaBalance($catalogo): ?string
    {
        $tipo = mb_strtolower(trim((string) ($catalogo->tipo_cuenta ?? '')));
        $codigo = trim((string) ($catalogo->codigo_cuenta ?? ''));

        return match ($tipo) {
            'activo'     => 'activo',
            'pasivo'     => 'pasivo',
            'patrimonio' => 'patrimonio',
            default      => match (substr($codigo, 0, 1)) {
                '1' => 'activo',
                '2' => 'pasivo',
                '3' => 'patrimonio',
                default => null,
            },
        };
    }

    private function prefijoJerarquico(string $codigo): string
    {
        $limpio = preg_replace('/[^0-9A-Za-z]/', '', trim($codigo));
        $sinCerosFinales = rtrim($limpio, '0');

        return $sinCerosFinales !== '' ? $sinCerosFinales : $limpio;
    }

    private function calcularBalanceAnio(int $empresaId, int $anio): array
    {
        $reportes = ReporteFinanciero::with('catalogo')
            ->where('anio', $anio)
            ->where('balance', true)
            ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->get();

        if ($reportes->isEmpty()) {
            return ['cuentas' => collect(), 'totales' => null];
        }

        $cuentas = $reportes
            ->groupBy('catalogo_id_catalogo')
            ->map(function ($registros) {
                $catalogo = $registros->first()->catalogo;
                $codigo = trim((string) $catalogo->codigo_cuenta);

                return (object) [
                    'id'         => $catalogo->id_catalogo,
                    'codigo'     => $codigo,
                    'prefijo'    => $this->prefijoJerarquico($codigo),
                    'nombre'     => $catalogo->nombre_cuenta,
                    'grupo'      => $this->clasificarCuentaBalance($catalogo),
                    'saldo'      => (float) $registros->sum('saldo'),
                    'es_detalle' => true,
                    'porcentaje' => null,
                ];
            })
            ->sortBy('codigo', SORT_NATURAL)
            ->values();

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

        $detalle = $cuentas->where('es_detalle', true);

        $activos          = (float) $detalle->where('grupo', 'activo')->sum('saldo');
        $pasivos          = (float) $detalle->where('grupo', 'pasivo')->sum('saldo');
        $patrimonio       = (float) $detalle->where('grupo', 'patrimonio')->sum('saldo');
        $pasivoPatrimonio = $pasivos + $patrimonio;

        foreach ($cuentas as $cuenta) {
            $base = $cuenta->grupo === 'activo' ? $activos : $pasivoPatrimonio;
            $cuenta->porcentaje = abs($base) > 0.00000001
                ? ($cuenta->saldo / $base) * 100
                : null;
        }

        return [
            'cuentas' => $cuentas->keyBy('id'),
            'totales' => [
                'activos'           => $activos,
                'pasivos'           => $pasivos,
                'patrimonio'        => $patrimonio,
                'pasivo_patrimonio' => $pasivoPatrimonio,
            ],
        ];
    }

    public function analisisBalance(Request $request)
    {
        $anio_menor = (int) $request->query('pasado');
        $anio_mayor = (int) $request->query('actual');

        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = (int) $usuario->id_empresa;

        $dataMenor = $this->calcularBalanceAnio($empresaId, $anio_menor);
        $dataMayor = $this->calcularBalanceAnio($empresaId, $anio_mayor);

        if (!$dataMenor['totales'] || !$dataMayor['totales']) {
            return redirect()->route('balance.index')
                ->with('error', "No se encontraron datos suficientes para comparar {$anio_menor} y {$anio_mayor}.");
        }

        $todasLasCuentas = $dataMenor['cuentas']
            ->union($dataMayor['cuentas'])
            ->sortBy('codigo', SORT_NATURAL)
            ->values();

        $gruposCuentas = [
            'activo'     => [],
            'pasivo'     => [],
            'patrimonio' => [],
        ];

        $resultadosCompletos = collect();

        foreach ($todasLasCuentas as $baseCuenta) {
            $id    = $baseCuenta->id;
            $cMen  = $dataMenor['cuentas']->get($id);
            $cMay  = $dataMayor['cuentas']->get($id);
            $grupo = $baseCuenta->grupo ?? 'activo';

            $saldoMenor = $cMen ? $cMen->saldo : 0.0;
            $saldoMayor = $cMay ? $cMay->saldo : 0.0;
            $cambio     = $saldoMayor - $saldoMenor;

            $porcentajeCambio = abs($saldoMenor) > 0.00000001
                ? ($cambio / abs($saldoMenor)) * 100
                : (abs($saldoMayor) > 0.00000001 ? 100.0 : null);

            $fila = [
                'codigo'            => $baseCuenta->codigo,
                'cuenta'            => $baseCuenta->nombre,
                'grupo'             => $grupo,
                'es_detalle'        => $baseCuenta->es_detalle,
                'saldo_menor'       => $saldoMenor,
                'saldo_mayor'       => $saldoMayor,
                'cambio'            => $cambio,
                'porcentaje_cambio' => $porcentajeCambio,
                'vertical_menor'    => $cMen ? $cMen->porcentaje : 0.0,
                'vertical_mayor'    => $cMay ? $cMay->porcentaje : 0.0,
            ];

            if (isset($gruposCuentas[$grupo])) {
                $gruposCuentas[$grupo][] = $fila;
            }
            $resultadosCompletos->push($fila);
        }

        $armarTotal = function (float $menor, float $mayor, float $baseVertMenor, float $baseVertMayor) {
            $cambio = $mayor - $menor;
            $varRel = abs($menor) > 0.00000001
                ? ($cambio / abs($menor)) * 100
                : (abs($mayor) > 0.00000001 ? 100.0 : null);
            $vMenor = abs($baseVertMenor) > 0.00000001 ? ($menor / $baseVertMenor) * 100 : 0.0;
            $vMayor = abs($baseVertMayor) > 0.00000001 ? ($mayor / $baseVertMayor) * 100 : 0.0;

            return [
                'menor'          => $menor,
                'mayor'          => $mayor,
                'cambio'         => $cambio,
                'var_relativa'   => $varRel,
                'vertical_menor' => $vMenor,
                'vertical_mayor' => $vMayor,
            ];
        };

        $tMen = $dataMenor['totales'];
        $tMay = $dataMayor['totales'];

        $totalesBalance = [
            'activos' => $armarTotal(
                $tMen['activos'], $tMay['activos'],
                $tMen['activos'], $tMay['activos']
            ),
            'pasivos' => $armarTotal(
                $tMen['pasivos'], $tMay['pasivos'],
                $tMen['pasivo_patrimonio'], $tMay['pasivo_patrimonio']
            ),
            'patrimonio' => $armarTotal(
                $tMen['patrimonio'], $tMay['patrimonio'],
                $tMen['pasivo_patrimonio'], $tMay['pasivo_patrimonio']
            ),
            'pasivo_patrimonio' => $armarTotal(
                $tMen['pasivo_patrimonio'], $tMay['pasivo_patrimonio'],
                $tMen['pasivo_patrimonio'], $tMay['pasivo_patrimonio']
            ),
        ];

        return view('balance.analisis', compact(
            'anio_menor',
            'anio_mayor',
            'resultadosCompletos',
            'gruposCuentas',
            'totalesBalance'
        ));
    }

    public function destroy(string $anio)
    {
        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;

        try {
            DB::beginTransaction();

            $reportesEliminados = ReporteFinanciero::where('anio', $anio)
                ->where('balance', true)
                ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
                ->delete();

            $ratiosEliminados = ValorRatio::where('anio_ratio', $anio)
                ->where('empresa_id_empresa', $empresaId)
                ->delete();

            DB::commit();

            if ($reportesEliminados > 0 || $ratiosEliminados > 0) {
                return redirect()->route('balance.index')
                    ->with('success', "Balance General del año {$anio} eliminado exitosamente ({$reportesEliminados} reportes, {$ratiosEliminados} ratios)");
            } else {
                return redirect()->route('balance.index')
                    ->with('warning', "No se encontraron datos del año {$anio} para eliminar");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('balance.index')
                ->with('error', 'Error al eliminar el Balance General: ' . $e->getMessage());
        }
    }
}