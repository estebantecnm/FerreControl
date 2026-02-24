<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Pedido_proveedor extends Model
{
    protected $table = 'Pedido_proveedor';

    protected $primaryKey = 'id_pedido';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'folio',
        'fecha_pedido',
        'fecha_entrega',
        'fecha_recepcion',
        'total',
        'estado',
        'condiciones',
        'notas',
        'id_proveedor',
        'id_usuario',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}