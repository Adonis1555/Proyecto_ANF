<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnalisisFinancieroController extends Controller
{
    public function index()
    {
        $empresas = Empresa::all();
        return view('analisis.index', compact('empresas'));
    }

    public function analisisVertical(Request $request)
    {
        $empresaId = $request->get('empresa_id');
        $anio = $request->get('anio');
        $tipoReporte = $request->get('tipo_reporte'); // 'balance' o 'estado_resultados'

        // Lógica de consulta de cuentas y cálculo porcentual...

        return view('analisis.vertical', compact('empresaId', 'anio', 'tipoReporte'));
    }

    public function analisisHorizontal(Request $request)
    {
        $empresaId = $request->get('empresa_id');
        $anioBase = $request->get('anio_base');
        $anioActual = $request->get('anio_actual');

        // Lógica de comparación de periodos y diferencia porcentual...

        return view('analisis.horizontal', compact('empresaId', 'anioBase', 'anioActual'));
    }
}
