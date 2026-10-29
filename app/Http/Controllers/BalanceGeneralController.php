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

        // Verifica si ya existe balance para ese año y empresa
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
                if ($i === 0) continue; // omitir encabezado

                $nombreCuenta = trim($fila[0] ?? '');
                $saldo = floatval($fila[1] ?? 0);

                if (!$nombreCuenta) {
                    $errores[] = "Fila " . ($i + 1) . ": nombre de cuenta vacío";
                    continue;
                }

                $cuenta = Catalogo::where('empresa_id_empresa', $empresaId)
                    ->whereRaw('LOWER(nombre_cuenta) = ?', [strtolower($nombreCuenta)])
                    ->first();


                if (!$cuenta) {
                    $errores[] = "Fila " . ($i + 1) . ": cuenta no encontrada ($codigo)";
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

    public function analisisBalance(Request $request)
    {
        $anio_menor = $request->query('pasado');
        $anio_mayor = $request->query('actual');

        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;

        $datosMenor = ReporteFinanciero::with('catalogo')
            ->where('anio', $anio_menor)
            ->where('balance', true)
            ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->get();

        $datosMayor = ReporteFinanciero::with('catalogo')
            ->where('anio', $anio_mayor)
            ->where('balance', true)
            ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
            ->get();

        $totalMenor = $datosMenor->sum('saldo');
        $totalMayor = $datosMayor->sum('saldo');

        $cuentas = $datosMenor->pluck('catalogo.nombre_cuenta')
            ->merge($datosMayor->pluck('catalogo.nombre_cuenta'))
            ->unique();

        $resultadosCompletos = $cuentas->map(function ($nombre) use ($datosMenor, $datosMayor, $totalMenor, $totalMayor) {
            $saldoMenor = $datosMenor->firstWhere('catalogo.nombre_cuenta', $nombre)?->saldo ?? 0;
            $saldoMayor = $datosMayor->firstWhere('catalogo.nombre_cuenta', $nombre)?->saldo ?? 0;

            $cambio = $saldoMayor - $saldoMenor;
            $porcentajeCambio = $saldoMenor != 0 ? ($cambio / $saldoMenor) * 100 : 0;

            $verticalMenor = $totalMenor != 0 ? ($saldoMenor / $totalMenor) * 100 : 0;
            $verticalMayor = $totalMayor != 0 ? ($saldoMayor / $totalMayor) * 100 : 0;

            return [
                'cuenta' => $nombre,
                'saldo_menor' => $saldoMenor,
                'saldo_mayor' => $saldoMayor,
                'cambio' => $cambio,
                'porcentaje_cambio' => $porcentajeCambio,
                'vertical_menor' => $verticalMenor,
                'vertical_mayor' => $verticalMayor
            ];
        });

        return view('balance.analisis', compact('anio_menor', 'anio_mayor', 'resultadosCompletos'));
    }
    public function destroy(string $anio)
    {
        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;

        try {
            DB::beginTransaction();

            // Eliminar los reportes financieros del Balance General
            $reportesEliminados = ReporteFinanciero::where('anio', $anio)
                ->where('balance', true)
                ->whereHas('catalogo', fn($q) => $q->where('empresa_id_empresa', $empresaId))
                ->delete();

            // Eliminar los valores de ratios del año
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
