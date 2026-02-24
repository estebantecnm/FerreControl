<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model {
    protected $table = 'Producto';

    protected $primaryKey = 'id_producto';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'nombre', 
        'marca', 
        'precio_venta', 
        'precio_compra', 
        'utilidad', 
        'codigo_barras',  
        'status', 
        'unidad_medida', 
        'cantidad_presentacion', 
        'color', 
        'id_categoria'
    ];

    public function categoria() {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function especificaciones() {
        return $this->hasMany(Especificacion::class, 'id_producto', 'id_producto');
    }
}