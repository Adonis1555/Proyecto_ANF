<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Catalogo;
use App\Models\Empresa;
use App\Models\Usuario;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CatalogoController extends Controller
{
    public function index()
    {
        $usuario = Usuario::with('empresa')->find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;
        $empresaNombre = $usuario->empresa->nombre_empresa;

        $catalogos = Catalogo::where('empresa_id_empresa', $empresaId)->get();

        return view('catalogo.index', compact('catalogos', 'empresaNombre'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_cuenta' => 'required|string',
            'nombre_cuenta' => 'required|string'
        ]);

        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;

        $codigo = $request->codigo_cuenta;
        $nombre = $request->nombre_cuenta;
        $primerCaracter = substr($codigo, 0, 1);

        if (!ctype_digit($primerCaracter)) {
            return redirect()->route('catalogo.index')->with('warning', 'Error: El código de cuenta es inválido');
        }

        if (Catalogo::where('empresa_id_empresa', $empresaId)->where('codigo_cuenta', $codigo)->exists()) {
            return redirect()->route('catalogo.index')->with('warning', 'Error: El código ya existe en el catálogo');
        }

        if (Catalogo::where('empresa_id_empresa', $empresaId)->where('nombre_cuenta', $nombre)->exists()) {
            return redirect()->route('catalogo.index')->with('warning', 'Error: El nombre ya existe en el catálogo');
        }

        $tipo = match ($primerCaracter) {
            '1' => 'Activo',
            '2' => 'Pasivo',
            '3' => 'Patrimonio',
            '4' => 'Ingresos',
            '5' => 'Gastos',
            '6' => 'Costos de venta',
            default => 'Otro'
        };

        Catalogo::create([
            'codigo_cuenta' => $codigo,
            'nombre_cuenta' => $nombre,
            'tipo_cuenta' => $tipo,
            'empresa_id_empresa' => $empresaId
        ]);

        return redirect()->route('catalogo.index')->with('success', 'La cuenta ha sido creada exitosamente');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'codigo_cuenta' => 'required|string',
            'nombre_cuenta' => 'required|string'
        ]);

        $codigo = $request->codigo_cuenta;
        $nombre = $request->nombre_cuenta;
        $primerCaracter = substr($codigo, 0, 1);

        if (!ctype_digit($primerCaracter)) {
            return redirect()->route('catalogo.index')->with('warning', 'Error: El código de cuenta es inválido');
        }

        $tipo = match ($primerCaracter) {
            '1' => 'Activo',
            '2' => 'Pasivo',
            '3' => 'Patrimonio',
            '4' => 'Ingresos',
            '5' => 'Gastos',
            '6' => 'Costos de venta',
            default => 'Otro'
        };

        $catalogo = Catalogo::findOrFail($id);
        $catalogo->update([
            'codigo_cuenta' => $codigo,
            'nombre_cuenta' => $nombre,
            'tipo_cuenta' => $tipo
        ]);

        return redirect()->route('catalogo.index')->with('success', 'La cuenta ha sido actualizada');
    }

    public function destroy($id)
    {
        Catalogo::destroy($id);
        return redirect()->route('catalogo.index')->with('success', 'La cuenta ha sido eliminada');
    }

    public function importarVista()
    {
        return view('catalogo.importar');
    }

    public function importar(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        $usuario = Usuario::find(session('usuario_id'));
        $empresaId = $usuario->id_empresa;

        $archivo = $request->file('file');
        $spreadsheet = IOFactory::load($archivo->getPathname());
        $hoja = $spreadsheet->getActiveSheet();
        $filas = $hoja->toArray();

        $errores = [];
        $exitos = 0;

        foreach ($filas as $index => $fila) {
            if ($index === 0) continue; // omitir encabezado

            $codigo = trim($fila[0] ?? '');
            $nombre = trim($fila[1] ?? '');

            if ($codigo === '' || $nombre === '') {
                $errores[] = "Fila " . ($index + 1) . ": campos vacíos";
                continue;
            }

            $primerCaracter = substr($codigo, 0, 1);
            if (!ctype_digit($primerCaracter)) {
                $errores[] = "Fila " . ($index + 1) . ": código inválido";
                continue;
            }

            if (Catalogo::where('empresa_id_empresa', $empresaId)->where('codigo_cuenta', $codigo)->exists()) {
                $errores[] = "Fila " . ($index + 1) . ": código duplicado";
                continue;
            }

            $tipo = match ($primerCaracter) {
                '1' => 'Activo',
                '2' => 'Pasivo',
                '3' => 'Patrimonio',
                '4' => 'Ingresos',
                '5' => 'Gastos',
                '6' => 'Costos de venta',
                default => 'Otro'
            };

            Catalogo::create([
                'codigo_cuenta' => $codigo,
                'nombre_cuenta' => $nombre,
                'tipo_cuenta' => $tipo,
                'empresa_id_empresa' => $empresaId
            ]);

            $exitos++;
        }

        if (count($errores) > 0) {
            return redirect()->route('catalogo.index')->with('warning', 'Importación parcial: ' . $exitos . ' cuentas agregadas. Errores: ' . implode(', ', $errores));
        }

        return redirect()->route('catalogo.index')->with('success', 'Importación exitosa: ' . $exitos . ' cuentas agregadas');
    }
}
