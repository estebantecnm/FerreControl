<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Movimiento_stock extends Model
{
    protected $table = 'Movimiento_stock';

    protected $primaryKey = 'id_movimiento';

    public $incrementing = true;

    protected $keyType = 'int';
    
    protected $fillable = [
        'fecha_movimiento',
        'tipo_movimiento',
        'cantidad',
        'stock_anterior',
        'stock_nuevo',
        'id_producto',
        'id_usuario',
    ];
    
    
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}