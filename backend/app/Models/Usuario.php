<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

}
