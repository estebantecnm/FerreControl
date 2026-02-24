<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especificacion extends Model
{
    protected $table = 'Especificacion';

    protected $primaryKey = 'id_especificacion';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'nombre_atributo', 
        'valor', 
        'id_producto',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}