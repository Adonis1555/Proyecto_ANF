<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Ratio;
use App\Models\ValorRatio;

class ComparacionPromedioController extends Controller
{
    public function index()
    {
        $anios = ValorRatio::select('anio_ratio')->distinct()->pluck('anio_ratio');
        return view('comparaciones.promedio', compact('anios'));
    }

    public function comparar(Request $request)
    {
        $anio = $request->input('anio');
        $ratios = Ratio::all();
        $empresas = Empresa::all();

        $resultados = [];

        foreach ($ratios as $ratio) {
            $valores = ValorRatio::where('ratio_id_ratio', $ratio->id_ratio)
                ->where('anio_ratio', $anio)
                ->pluck('valor', 'empresa_id_empresa');

            if ($valores->count() == 0) continue;

            $promedio = $valores->avg();

            $cumplen = [];
            

            foreach ($valores as $id_empresa => $valor) {
                if ($valor >= $promedio) {
                    $cumplen[] = Empresa::find($id_empresa)->nombre_empresa;
                }
            }
            

            $resultados[] = [
                'nombre_ratio' => $ratio->nombre_ratio,
                'promedio' => $promedio,
                'valores_empresas' => $valores,
                'cumplen' => $cumplen
            ];
        }

        return view('comparaciones.resultado_promedio', compact('resultados', 'empresas', 'anio'));
    }
}
