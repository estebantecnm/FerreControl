<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{

    protected $table = 'Proveedor';

    protected $primaryKey = 'id_proveedor';

    public $incrementing = true;

    protected $keyType = 'int';
    
    public $timestamps = true;

    protected $fillable = [
        'nombre_contacto', 
        'telefono_contacto', 
        'correo_contacto', 
        'nombre', 
        'telefono', 
        'tiempo_entrega', 
        'correo', 
        'tipo', 
        'rfc', 
        'num_int', 
        'num_ext', 
        'pais', 
        'estado', 
        'municipio', 
        'ciudad', 
        'colonia', 
        'calle'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function pedidosProveedor()
    {
        return $this->hasMany(Pedido_proveedor::class, 'id_proveedor', 'id_proveedor');
    }

}