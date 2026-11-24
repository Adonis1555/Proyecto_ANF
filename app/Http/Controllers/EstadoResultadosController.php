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
    public function index()
    {
        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;
        $empresa = Empresa::find($empresaId);

        $listaAnios = ReporteFinanciero::select('anio')
            ->whereHas('catalogo', function ($query) use ($empresa) {
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

    public function create(Request $request)
    {
        $anio = $request->query('anio');

        return view('EstadoResultados.cargarExcel', [
            'titulo' => 'Subir ER',
            'anio' => $anio
        ]);
    }

    private function esFilaSubtotal(string $nombreCuenta): bool
    {
        $normalizado = mb_strtolower(trim($nombreCuenta));

        if ($normalizado === '') {
            return true;
        }

        $patronesSubtotal = [
            'utilidad bruta',
            'utilidad de operacion',
            'utilidad de operación',
            'utilidad en operacion',
            'utilidad en operación',
            'utilidad operativa',
            'utilidad antes de',
            'utilidad neta',
            'utilidad del ejercicio',
            'perdida neta',
            'pérdida neta',
            'perdida del ejercicio',
            'pérdida del ejercicio',
            'resultado del ejercicio',
            'resultado integral',
            'total ingresos',
            'total de ingresos',
            'total costos',
            'total de costos',
            'total gastos',
            'total de gastos',
            'total egresos',
            'total otros ingresos',
            'total otros gastos',
        ];

        foreach ($patronesSubtotal as $patron) {
            if (str_contains($normalizado, $patron)) {
                return true;
            }
        }

        return false;
    }

    private function inferirDatosCuentaER(string $nombreCuenta): array
    {
        $nombre = mb_strtolower($nombreCuenta);

        if (str_contains($nombre, 'venta') || str_contains($nombre, 'ingreso')) {
            return ['codigo' => '4101', 'tipo' => 'Ingresos'];
        }

        if (str_contains($nombre, 'costo')) {
            return ['codigo' => '6101', 'tipo' => 'Costos de venta'];
        }

        return ['codigo' => '5101', 'tipo' => 'Gastos'];
    }

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
            ->where('balance', false)
            ->exists();

        if ($existe) {
            return redirect()->route('estado-resultados.index')->with('error', 'Error: El año ya existe');
        }

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getPathname());
            $hoja = $spreadsheet->getActiveSheet()->toArray();

            DB::beginTransaction();

            $omitidas = 0;
            $exitos = 0;

            foreach ($hoja as $i => $fila) {
                if ($i === 0) {
                    continue;
                }

                $nombreCuenta = trim((string) ($fila[0] ?? ''));
                $saldo = floatval($fila[1] ?? 0);

                if ($this->esFilaSubtotal($nombreCuenta)) {
                    $omitidas++;
                    continue;
                }

                $cuenta = Catalogo::where('empresa_id_empresa', $empresaId)
                    ->whereRaw('LOWER(TRIM(nombre_cuenta)) = ?', [mb_strtolower($nombreCuenta)])
                    ->first();

                if (!$cuenta) {
                    $datosCuenta = $this->inferirDatosCuentaER($nombreCuenta);

                    $cuenta = Catalogo::create([
                        'empresa_id_empresa' => $empresaId,
                        'nombre_cuenta'      => $nombreCuenta,
                        'codigo_cuenta'      => $datosCuenta['codigo'],
                        'tipo_cuenta'        => $datosCuenta['tipo'],
                    ]);
                }

                ReporteFinanciero::create([
                    'anio'                 => $anio,
                    'balance'              => false,
                    'saldo'                => $saldo,
                    'catalogo_id_catalogo' => $cuenta->id_catalogo
                ]);

                $exitos++;
            }

            DB::commit();

            return redirect()
                ->route('estado-resultados.index')
                ->with('success', "ER cargado exitosamente ({$exitos} cuentas importadas, {$omitidas} filas de totales/vacías omitidas).");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('estado-resultados.index')
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

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

        $ingresos = $estadoResultados
            ->filter(fn($r) => mb_strtolower(trim((string) $r->catalogo->tipo_cuenta)) === 'ingresos')
            ->sum('saldo');

        $costos = $estadoResultados
            ->filter(fn($r) => mb_strtolower(trim((string) $r->catalogo->tipo_cuenta)) === 'costos de venta')
            ->sum('saldo');

        $gastos = $estadoResultados
            ->filter(fn($r) => mb_strtolower(trim((string) $r->catalogo->tipo_cuenta)) === 'gastos')
            ->sum('saldo');

        $utilidadBruta = $ingresos - $costos;
        $utilidadNeta  = $utilidadBruta - $gastos;

        return view('EstadoResultados.verEstadoResultados', compact(
            'estadoResultados',
            'anio',
            'ingresos',
            'costos',
            'gastos',
            'utilidadBruta',
            'utilidadNeta'
        ));
    }

    public function destroy(string $anio)
    {
        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;

        try {
            DB::beginTransaction();

            $reportesEliminados = ReporteFinanciero::where('anio', $anio)
                ->where('balance', false)
                ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
                ->delete();

            $ratiosEliminados = ValorRatio::where('anio_ratio', $anio)
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

    /**
     * Clasifica una cuenta del catálogo en ingresos, costos o gastos.
     */
    private function clasificarCuentaER($catalogo): ?string
    {
        $tipo = mb_strtolower(trim((string) ($catalogo->tipo_cuenta ?? '')));
        $codigo = trim((string) ($catalogo->codigo_cuenta ?? ''));

        return match ($tipo) {
            'ingresos', 'ingreso'            => 'ingresos',
            'costos de venta', 'costos', 'costo' => 'costos',
            'gastos', 'gasto'                => 'gastos',
            default => match (substr($codigo, 0, 1)) {
                '4' => 'ingresos',
                '6' => 'costos',
                '5' => 'gastos',
                default => null,
            },
        };
    }

    /**
     * Normaliza el código para detectar jerarquía padre-hijo sin duplicar sumas.
     */
    private function prefijoJerarquico(string $codigo): string
    {
        $limpio = preg_replace('/[^0-9A-Za-z]/', '', trim($codigo));
        $sinCerosFinales = rtrim($limpio, '0');

        return $sinCerosFinales !== '' ? $sinCerosFinales : $limpio;
    }

    /**
     * Calcula ingresos, costos, gastos, utilidades y porcentaje vertical sobre Ingresos (100%)
     * de forma coherente con AnalisisFinancieroController.
     */
    private function calcularERAnio(int $empresaId, int $anio): array
    {
        $reportes = ReporteFinanciero::with('catalogo')
            ->where('anio', $anio)
            ->where('balance', false)
            ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->orderBy('id_reporte', 'asc')
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
                    'grupo'      => $this->clasificarCuentaER($catalogo),
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

        $ingresos      = (float) $detalle->where('grupo', 'ingresos')->sum('saldo');
        $costos        = (float) $detalle->where('grupo', 'costos')->sum('saldo');
        $gastos        = (float) $detalle->where('grupo', 'gastos')->sum('saldo');
        $utilidadBruta = $ingresos - $costos;
        $utilidadNeta  = $utilidadBruta - $gastos;

        foreach ($cuentas as $cuenta) {
            $cuenta->porcentaje = abs($ingresos) > 0.00000001
                ? ($cuenta->saldo / $ingresos) * 100
                : null;
        }

        return [
            'cuentas' => $cuentas->keyBy('id'),
            'totales' => [
                'ingresos'       => $ingresos,
                'costos'         => $costos,
                'gastos'         => $gastos,
                'utilidad_bruta' => $utilidadBruta,
                'utilidad_neta'  => $utilidadNeta,
            ],
        ];
    }

    public function analisis(Request $request)
    {
        $request->validate([
            'pasado' => 'required|integer|min:2017|different:actual',
            'actual' => 'required|integer|min:2017|gte:pasado'
        ]);

        $usuario   = Usuario::find(session('usuario_id'));
        $empresaId = (int) $usuario->id_empresa;
        $anioMenor = (int) $request->pasado;
        $anioMayor = (int) $request->actual;

        $dataMenor = $this->calcularERAnio($empresaId, $anioMenor);
        $dataMayor = $this->calcularERAnio($empresaId, $anioMayor);

        if (!$dataMenor['totales'] || !$dataMayor['totales']) {
            return redirect()->route('estado-resultados.index')
                ->with('error', "No se encontraron datos suficientes para comparar {$anioMenor} y {$anioMayor}");
        }

        $todasLasCuentas = $dataMenor['cuentas']
            ->union($dataMayor['cuentas'])
            ->sortBy('codigo', SORT_NATURAL)
            ->values();

        $gruposCuentas = [
            'ingresos' => [],
            'costos'   => [],
            'gastos'   => [],
        ];

        $resultadosCompletos = [];

        foreach ($todasLasCuentas as $baseCuenta) {
            $id    = $baseCuenta->id;
            $cMen  = $dataMenor['cuentas']->get($id);
            $cMay  = $dataMayor['cuentas']->get($id);
            $grupo = $baseCuenta->grupo ?? 'gastos';

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
            $resultadosCompletos[] = $fila;
        }

        $armarTotal = function (float $menor, float $mayor, float $ingMenor, float $ingMayor) {
            $cambio = $mayor - $menor;
            $varRel = abs($menor) > 0.00000001
                ? ($cambio / abs($menor)) * 100
                : (abs($mayor) > 0.00000001 ? 100.0 : null);
            $vMenor = abs($ingMenor) > 0.00000001 ? ($menor / $ingMenor) * 100 : 0.0;
            $vMayor = abs($ingMayor) > 0.00000001 ? ($mayor / $ingMayor) * 100 : 0.0;

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

        $totalesER = [
            'ingresos'       => $armarTotal($tMen['ingresos'], $tMay['ingresos'], $tMen['ingresos'], $tMay['ingresos']),
            'costos'         => $armarTotal($tMen['costos'], $tMay['costos'], $tMen['ingresos'], $tMay['ingresos']),
            'gastos'         => $armarTotal($tMen['gastos'], $tMay['gastos'], $tMen['ingresos'], $tMay['ingresos']),
            'utilidad_bruta' => $armarTotal($tMen['utilidad_bruta'], $tMay['utilidad_bruta'], $tMen['ingresos'], $tMay['ingresos']),
            'utilidad_neta'  => $armarTotal($tMen['utilidad_neta'], $tMay['utilidad_neta'], $tMen['ingresos'], $tMay['ingresos']),
        ];

        return view('EstadoResultados.analisisVerHor', [
            'titulo'              => 'Análisis de Estado de Resultados',
            'anio_menor'          => $anioMenor,
            'anio_mayor'          => $anioMayor,
            'resultadosCompletos' => $resultadosCompletos,
            'gruposCuentas'       => $gruposCuentas,
            'totalesER'           => $totalesER,
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
            return $estadoResultados;
        }

        $ingresos = $estadoResultados
            ->filter(fn($r) => $this->clasificarCuentaER($r->catalogo) === 'ingresos')
            ->sum('saldo');

        $base = abs($ingresos) > 0.00000001 ? $ingresos : ($estadoResultados[0]->saldo ?? 0);

        foreach ($estadoResultados as $item) {
            $item->analisisVertical = abs($base) > 0.00000001
                ? ($item->saldo / $base) * 100
                : 0;
        }

        return $estadoResultados;
    }

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
}