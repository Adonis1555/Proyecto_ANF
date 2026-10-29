<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('correo', 'password');

        $usuario = Usuario::with('roles')
            ->where('correo', $credentials['correo'])
            ->where('password', $credentials['password']) // sin encriptar
            ->where('enabled', true)
            ->where('account_locked', false)
            ->where('account_no_expired', true)
            ->where('credential_no_expired', true)
            ->first();

        if ($usuario) {
            session([
                'usuario_id' => $usuario->id_usuario,
                'usuario_nombre' => $usuario->nombre,
                'usuario_rol' => $usuario->roles->pluck('nombre_rol')->toArray()
            ]);
            return redirect()->route('dashboard');
        } else {
            return back()->withErrors(['login' => 'Credenciales inválidas o cuenta inactiva']);
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}
