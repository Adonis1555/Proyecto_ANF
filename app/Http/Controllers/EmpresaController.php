<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Sector;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::with('sector')->get();
        $sectores = Sector::all();
        return view('empresa.index', compact('empresas', 'sectores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nit' => 'required|unique:empresa,nit',
            'nombre_empresa' => 'required',
            'id_sector' => 'required|exists:sector,id_sector'
        ]);

        Empresa::create($request->only('nit', 'nombre_empresa', 'id_sector'));
        return redirect()->route('empresa.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nit' => 'required|unique:empresa,nit,' . $id . ',id_empresa',
            'nombre_empresa' => 'required',
            'id_sector' => 'required|exists:sector,id_sector'
        ]);

        $empresa = Empresa::findOrFail($id);
        $empresa->update($request->only('nit', 'nombre_empresa', 'id_sector'));
        return redirect()->route('empresa.index');
    }

    public function destroy($id)
    {
        Empresa::destroy($id);
        return redirect()->route('empresa.index');
    }
}
