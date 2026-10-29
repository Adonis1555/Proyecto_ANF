<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sector;
use App\Models\Empresa;
use App\Models\Ratio;
use App\Models\ValorRatio;

class GraficoController extends Controller
{
    /**
     * Muestra la vista principal de gráficos
     */
    public function index()
    {
        // Obtener todos los sectores para el selector
        $sectores = Sector::all();
        
        return view('graficos.vergraficos', compact('sectores'));
    }

    /**
     * Obtiene las empresas de un sector específico (AJAX)
     */
    public function getEmpresas($sectorId)
    {
        $empresas = Empresa::where('id_sector', $sectorId)->get();
        return response()->json($empresas);
    }

    /**
     * Obtiene todos los ratios disponibles (AJAX)
     */
    public function getRatios()
    {
        $ratios = Ratio::all();
        return response()->json($ratios);
    }

    /**
     * Obtiene los datos del gráfico según los filtros (AJAX)
     */
    public function getDatosGrafico(Request $request)
    {
        $empresaId = $request->empresa_id;
        $ratioId = $request->ratio_id;

        // Consulta para obtener los valores del ratio por año
        $datos = ValorRatio::where('empresa_id_empresa', $empresaId)
            ->where('ratio_id_ratio', $ratioId)
            ->orderBy('anio_ratio', 'asc')
            ->get(['anio_ratio', 'valor']);

        // Obtener información adicional
        $empresa = Empresa::find($empresaId);
        $ratio = Ratio::find($ratioId);

        return response()->json([
            'datos' => $datos,
            'empresa' => $empresa,
            'ratio' => $ratio
        ]);
    }
}