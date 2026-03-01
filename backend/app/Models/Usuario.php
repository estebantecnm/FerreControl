<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, Notifiable;

    protected $table = 'Usuario'; 

    protected $primaryKey = 'id_usuario';
    
    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true; 

    protected $fillable = [
        'nombre', 
        'ap_paterno', 
        'ap_materno', 
        'fecha_nacimiento', 
        'sexo', 
        'telefono', 
        'correo', 
        'rfc', 
        'curp', 
        'salario', 
        'status', 
        'ultimo_login', 
        'intentos_fallidos', 
        'fecha_entrada', 
        'contrasena', 
        'num_ext', 
        'num_int', 
        'calle', 
        'colonia', 
        'municipio', 
        'estado', 
        'id_rol'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // Se llama 'contrasena', decirle a Laravel dónde buscar:
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

}
