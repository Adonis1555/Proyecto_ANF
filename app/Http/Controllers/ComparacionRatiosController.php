<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use App\Models\Empresa;
use App\Models\Ratio;
use App\Models\ValorRatio;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ComparacionRatiosController extends Controller
{

/**
 * Mostrar la vista principal de comparación de ratios
 */
public function index()
{
    if (!session()->has('usuario_id')) {
        return redirect()->route('login');
    }
    return view('comparacion-ratios.index');
}

    /**
     * Obtener todos los sectores para el selector
     */
    public function getSectores(): JsonResponse
    {
        try {
            $sectores = Sector::select('id_sector', 'nombre_sector')
                ->orderBy('nombre_sector')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $sectores
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener sectores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener empresas por sector para el selector de Empresa A
     */
    public function getEmpresasPorSector(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'id_sector' => 'required|integer|exists:sector,id_sector'
            ]);

            $empresas = Empresa::select('id_empresa', 'nombre_empresa', 'nit')
                ->where('id_sector', $request->id_sector)
                ->orderBy('nombre_empresa')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $empresas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener empresas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todas las empresas para el selector de Empresa B
     */
    public function getTodasEmpresas(): JsonResponse
    {
        try {
            $empresas = Empresa::select('id_empresa', 'nombre_empresa', 'nit', 'id_sector')
                ->with('sector:id_sector,nombre_sector')
                ->orderBy('nombre_empresa')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $empresas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener empresas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener años disponibles para una empresa
     */
    public function getAniosDisponibles(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'id_empresa' => 'required|integer|exists:empresa,id_empresa'
            ]);

            $anios = ValorRatio::where('empresa_id_empresa', $request->id_empresa)
                ->distinct()
                ->orderBy('anio_ratio', 'desc')
                ->pluck('anio_ratio');

            return response()->json([
                'success' => true,
                'data' => $anios
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener años: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Comparar ratios entre dos empresas
     */
    public function compararRatios(Request $request): JsonResponse
    {
        try {
            // Validar datos de entrada
            $request->validate([
                'id_empresa_a' => 'required|integer|exists:empresa,id_empresa',
                'anio_empresa_a' => 'required|integer',
                'id_empresa_b' => 'required|integer|exists:empresa,id_empresa',
                'anio_empresa_b' => 'required|integer'
            ]);

            // Verificar que no sean la misma empresa en el mismo año
            if ($request->id_empresa_a == $request->id_empresa_b && 
                $request->anio_empresa_a == $request->anio_empresa_b) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes comparar la misma empresa en el mismo año'
                ], 400);
            }

            // Obtener información de las empresas
            $empresaA = Empresa::with('sector')->findOrFail($request->id_empresa_a);
            $empresaB = Empresa::with('sector')->findOrFail($request->id_empresa_b);

            // Obtener ratios de Empresa A con la relación ratio cargada
            $ratiosEmpresaA = ValorRatio::where('empresa_id_empresa', $request->id_empresa_a)
                ->where('anio_ratio', $request->anio_empresa_a)
                ->with('ratio')
                ->get()
                ->keyBy('ratio_id_ratio');

            // Obtener ratios de Empresa B con la relación ratio cargada
            $ratiosEmpresaB = ValorRatio::where('empresa_id_empresa', $request->id_empresa_b)
                ->where('anio_ratio', $request->anio_empresa_b)
                ->with('ratio')
                ->get()
                ->keyBy('ratio_id_ratio');

            // Si no hay datos
            if ($ratiosEmpresaA->isEmpty() && $ratiosEmpresaB->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay datos de ratios para las empresas y años seleccionados'
                ], 404);
            }

            // Obtener todos los IDs de ratios únicos
            $idsRatios = $ratiosEmpresaA->keys()->merge($ratiosEmpresaB->keys())->unique();

            // Obtener todos los ratios únicos
            $todosRatios = Ratio::whereIn('id_ratio', $idsRatios)->get();

            // Construir la comparación
            $comparacion = [];

            foreach ($todosRatios as $ratio) {
                $valorA = $ratiosEmpresaA->get($ratio->id_ratio)?->valor ?? null;
                $valorB = $ratiosEmpresaB->get($ratio->id_ratio)?->valor ?? null;

                // Calcular diferencia
                $diferencia = null;
                $porcentajeDiferencia = null;

                if ($valorA !== null && $valorB !== null) {
                    $diferencia = $valorB - $valorA;
                    
                    // Calcular porcentaje de diferencia
                    if ($valorA != 0) {
                        $porcentajeDiferencia = (($valorB - $valorA) / abs($valorA)) * 100;
                    }
                }

                $comparacion[] = [
                    'id_ratio' => $ratio->id_ratio,
                    'nombre_ratio' => $ratio->nombre_ratio,
                    'valor_empresa_a' => $valorA !== null ? round($valorA, 4) : null,
                    'valor_empresa_b' => $valorB !== null ? round($valorB, 4) : null,
                    'diferencia' => $diferencia !== null ? round($diferencia, 4) : null,
                    'porcentaje_diferencia' => $porcentajeDiferencia !== null ? round($porcentajeDiferencia, 2) : null,
                    'favorable_a' => $diferencia !== null ? ($diferencia < 0) : null,
                    'favorable_b' => $diferencia !== null ? ($diferencia > 0) : null
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'empresa_a' => [
                        'id' => $empresaA->id_empresa,
                        'nombre' => $empresaA->nombre_empresa,
                        'nit' => $empresaA->nit,
                        'sector' => $empresaA->sector->nombre_sector,
                        'anio' => $request->anio_empresa_a
                    ],
                    'empresa_b' => [
                        'id' => $empresaB->id_empresa,
                        'nombre' => $empresaB->nombre_empresa,
                        'nit' => $empresaB->nit,
                        'sector' => $empresaB->sector->nombre_sector,
                        'anio' => $request->anio_empresa_b
                    ],
                    'comparacion' => $comparacion,
                    'total_ratios' => count($comparacion)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al comparar ratios: ' . $e->getMessage()
            ], 500);
        }
    }
}