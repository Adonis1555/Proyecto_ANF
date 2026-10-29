<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sector;
use App\Models\Empresa;
use App\Models\Ratio;
use App\Models\ValorRatio;
use App\Models\ParametroSector;

class ComparacionSectorController extends Controller
{
    public function index()
    {
        $sectores = Sector::all();
        $anios = ValorRatio::select('anio_ratio')->distinct()->pluck('anio_ratio');
        return view('comparaciones.sector', compact('sectores', 'anios'));
    }

    public function comparar(Request $request)
    {
        $sector_id = $request->input('sector_id');
        $nombre_sector = Sector::find($sector_id)->nombre_sector;
        $anio = $request->input('anio');

        $empresas = Empresa::where('id_sector', $sector_id)->get();
        $ratios = Ratio::all();
        
        $parametros = \App\Models\RatioBench::where('sector_id_sector', $sector_id)
        ->get()
        ->keyBy('ratio_id_ratio');

        $resultados = [];

        foreach ($ratios as $ratio) {
            $fila = [
                'nombre_ratio' => $ratio->nombre_ratio,
                'valor_aceptable' => $parametros[$ratio->id_ratio]->valor_bech ?? null,
                'valores_empresas' => [],
                'cumplen' => []
            ];

            foreach ($empresas as $empresa) {
                $valor = ValorRatio::where('empresa_id_empresa', $empresa->id_empresa)
                    ->where('ratio_id_ratio', $ratio->id_ratio)
                    ->where('anio_ratio', $anio)
                    ->value('valor');

                $fila['valores_empresas'][$empresa->nombre_empresa] = $valor;

                if ($valor !== null && $fila['valor_aceptable'] !== null && $valor >= $fila['valor_aceptable']) {
                    $fila['cumplen'][] = $empresa->nombre_empresa;
                }
            }

            $resultados[] = $fila;
        }

        return view('comparaciones.resultado_sector', compact('resultados','nombre_sector' ,'empresas', 'anio'));
    }
}
