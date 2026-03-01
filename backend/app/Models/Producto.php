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
        'cantidad_inicial',
        'id_categoria',
        'id_usuario',
    ];

    // Agregamos el atributo 'stock' a los atributos que se incluirán en la respuesta JSON
    // Esto permite que al convertir el modelo a JSON, se incluya el stock calculado
    // El método getStockAttribute se define más abajo para calcular el stock dinámicamente
    // Esto es útil para mostrar el stock actual sin necesidad de almacenarlo en la base de datos
    protected $appends = ['stock'];


    public function getStockAttribute()
    {
        // Suma cantidades de entradas y resta cantidades de salidas
        $entradas = $this->movimientos()->where('tipo_movimiento', 'Entrada')->sum('cantidad');
        $salidas = $this->movimientos()->where('tipo_movimiento', 'Salida')->sum('cantidad');
        
        return $entradas - $salidas;
    }

    public function categoria() {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function especificaciones() {
        return $this->hasMany(Especificacion::class, 'id_producto');
    }
    public function movimientos(){
        return $this->hasMany(Movimiento_stock::class, 'id_producto');
    }
}