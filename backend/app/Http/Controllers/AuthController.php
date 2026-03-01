<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validamos que el ID sea un número y la contraseña esté presente
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|numeric',
            'contrasena' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Buscamos directamente por el ID (Llave primaria)
        $user = Usuario::find($request->id_usuario);

        // 3. Verificamos si existe y si la contraseña coincide (Texto Plano)
        if (!$user || $user->contrasena !== $request->contrasena) {
            return response()->json([
                'msj' => 'ID de usuario o contraseña incorrectos.'
            ], 401);
        }

        // 4. Verificamos si el usuario está activo (Opcional pero recomendado)
        if ($user->status !== 'activo') {
            return response()->json(['msj' => 'Este usuario está desactivado.'], 403);
        }

        // 5. Generar Token de Sanctum
        $token = $user->createToken('token-ferrecontrol')->plainTextToken;

        return response()->json([
            'msj' => 'Acceso concedido. ¡Hola, ' . $user->nombre . '!',
            'token' => $token,
            'usuario' => [
                'id' => $user->id_usuario,
                'nombre' => $user->nombre,
                'rol' => $user->id_rol
            ]
        ]);
    }

    public function logout(Request $request)
    {
        // Revocar el token actual
        $request->user()->currentAccessToken()->delete();
        return response()->json(['msj' => 'Sesión cerrada correctamente']);
    }
}