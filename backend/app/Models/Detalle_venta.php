<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detalle_venta extends Model
{
    protected $table = 'Detalle_venta';

    protected $primaryKey = 'id_detalle_venta';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'cantidad', 
        'precio_unitario', 
        'id_venta', 
        'id_producto'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}