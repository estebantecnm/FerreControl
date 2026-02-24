<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();

        if ($usuarios->isEmpty()) {
            return response()->json(['message' => 'No se encontraron usuarios'], 404);
        }

        return response()->json($usuarios);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:25',
            'ap_paterno' => 'required|string|max:25',
            'ap_materno' => 'required|string|max:25',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string|max:9',
            'telefono' => 'required|string|max:10',
            'correo' => 'required|string|max:64',
            'rfc' => 'required|string|max:13|unique:Usuario,rfc',
            'curp' => 'required|string|max:18|unique:Usuario,curp',
            'salario' => 'required|numeric',
            'status' => 'required|string|max:9',
            'ultimo_login' => 'required|date',
            'intentos_fallidos' => 'required|integer',
            'fecha_entrada' => 'required|date',
            'contrasena' => 'required|string|max:60',
            'num_ext' => 'required|integer',
            'num_int' => 'nullable|integer',
            'calle' => 'required|string|max:30',
            'colonia' => 'required|string|max:30',
            'municipio' => 'required|string|max:30',
            'estado' => 'required|string|max:30',
            'id_rol' => 'required|exists:Rol,id_rol',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $usuario = Usuario::create($request->all());

        return response()->json($usuario, 201);
    }

    public function show($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:25',
            'ap_paterno' => 'sometimes|required|string|max:25',
            'ap_materno' => 'sometimes|required|string|max:25',
            'fecha_nacimiento' => 'sometimes|required|date',
            'sexo' => 'sometimes|required|string|max:9',
            'telefono' => 'sometimes|required|string|max:10',
            'correo' => 'sometimes|required|string|max:64',
            'rfc' => "sometimes|required|string|max:13|unique:Usuario,rfc,$id,id_usuario",
            'curp' => "sometimes|required|string|max:18|unique:Usuario,curp,$id,id_usuario",
            'salario' => 'sometimes|required|numeric',
            'status' => 'sometimes|required|string|max:9',
            'ultimo_login' => 'sometimes|required|date',
            'intentos_fallidos' => 'sometimes|required|integer',
            'fecha_entrada' => 'sometimes|required|date',
            'contrasena' => 'sometimes|required|string|max:60',
            'num_ext' => 'sometimes|required|integer',
            'num_int' => 'nullable|integer',
            'calle' => 'sometimes|required|string|max:30',
            'colonia' => 'sometimes|required|string|max:30',
            'municipio' => 'sometimes|required|string|max:30',
            'estado' => 'sometimes|required|string|max:30',
            'id_rol' => 'sometimes|required|exists:Rol,id_rol',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $usuario->update($request->all());

        return response()->json($usuario);
    }

    public function destroy($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}