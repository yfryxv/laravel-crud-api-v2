<?php

namespace App\Http\Controllers;

use App\Models\RegistroCuentas;
use Illuminate\Http\Request;

class RegistroCuentasController extends Controller
{
    public function index()
    {
        return response()->json(RegistroCuentas::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_clienteG' => 'required|integer',
            'correo' => 'required|email|unique:RegistroCuentas,correo',
            'contrasena' => 'required|min:6'
        ]);

        $usuario = RegistroCuentas::create([
            'id_clienteG' => $request->id_clienteG,
            'correo' => $request->correo,
            'contrasena' => bcrypt($request->contrasena)
        ]);

        return response()->json($usuario, 201);
    }
}


