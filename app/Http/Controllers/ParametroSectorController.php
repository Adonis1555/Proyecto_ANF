<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sector;
use App\Models\Ratio;
use App\Models\RatioBench;

class ParametroSectorController extends Controller
{
    public function edit($id)
    {
        $sector = Sector::findOrFail($id);
        $ratios = Ratio::all();

        // Traer los valores actuales (si ya existen)
        $valoresExistentes = RatioBench::where('sector_id_sector', $id)
            ->pluck('valor_bech', 'ratio_id_ratio')
            ->toArray();
        
        

        return view('sector.parametros', compact('sector', 'ratios', 'valoresExistentes'));
    }

    public function update(Request $request, $id)
    {
        $sector = Sector::findOrFail($id);

        foreach ($request->ratios as $ratioId => $valorRaw) {

            $valor = null;

            if ($valorRaw === null || $valorRaw === '') {
                $valor = 0;
            }else{
                //remover comas y espacios y convertir a float
                $valorSanitizado = str_replace(',', '.', trim($valorRaw));
                $valor = is_numeric($valorSanitizado) ? (float) $valorSanitizado : 0;
            }

            RatioBench::updateOrCreate(
                //a partir del id del sector y el id del ratio actualiza o crea el valor del bench que va a servir como parametro
                ['sector_id_sector' => $id, 'ratio_id_ratio' => $ratioId],
                ['valor_bech' => $valor]
            );
        }

        return redirect()->route('sector.index')->with('success', 'Parámetros del sector actualizados correctamente.');
    }
}

