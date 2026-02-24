<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detalle_pedido_proveedor extends Model
{
    protected $table = 'Detalle_pedido_proveedor';

    protected $primaryKey = 'id_detalle_pedido_prov';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'cantidad',
        'precio_compra',
        'id_pedido',
        'id_producto',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function pedidoProveedor()
    {
        return $this->belongsTo(Pedido_proveedor::class, 'id_pedido', 'id_pedido');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}