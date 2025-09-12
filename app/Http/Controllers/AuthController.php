<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\RegistroCuentas;

class AuthController extends Controller
{
    // LOGIN
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo' => 'required|email',
            'contrasena' => 'required|string',
        ]);

        // Intento de login
        $token = auth('api')->attempt($credentials);

        if (! $token) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    // REGISTER (para crear usuarios desde la API)
    public function register(Request $request)
    {
        $data = $request->validate([
            'id_clienteG' => 'required|integer|exists:Cliente_g,id_clienteG',
            'correo'      => 'required|email|unique:RegistroCuentas,correo',
            'contrasena'  => 'required|string|min:6',
        ]);

        $user = RegistroCuentas::create([
            'id_clienteG' => $data['id_clienteG'],
            'correo'      => $data['correo'],
            'contrasena'  => Hash::make($data['contrasena']), // bcrypt
        ]);

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user'    => $user
        ]);
    }

    // INFO DEL USUARIO AUTENTICADO
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    // LOGOUT
    public function logout()
    {
        try {
            auth('api')->logout();
        } catch (JWTException $e) {
            return response()->json(['error' => 'No se pudo cerrar sesión'], 500);
        }

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}
