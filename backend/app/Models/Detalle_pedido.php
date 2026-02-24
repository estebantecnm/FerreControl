<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detalle_pedido extends Model
{
    protected $table = 'Detalle_pedido';

    protected $primaryKey = 'id_detalle';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'cantidad', 
        'precio_unitario', 
        'id_pedido_cliente', 
        'id_producto',
        ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function pedidoCliente()
    {
        return $this->belongsTo(Pedido_cliente::class, 'id_pedido_cliente', 'id_pedido_cliente');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}