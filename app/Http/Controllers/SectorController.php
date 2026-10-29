<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sector;

class SectorController extends Controller
{
    public function index()
    {
        $sectores = Sector::all();
        return view('sector.index', compact('sectores'));
    }

    public function store(Request $request)
    {
        $request->validate(['nombre_sector' => 'required']);
        Sector::create($request->only('nombre_sector'));
        return redirect()->route('sector.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nombre_sector' => 'required']);
        $sector = Sector::findOrFail($id);
        $sector->update($request->only('nombre_sector'));
        return redirect()->route('sector.index');
    }

    public function destroy($id)
    {
        Sector::destroy($id);
        return redirect()->route('sector.index');
    }
}
