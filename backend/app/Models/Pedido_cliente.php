<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido_cliente extends Model
{
    protected $table = 'Pedido_cliente';

    protected $primaryKey = 'id_pedido_cliente';

    public $incrementing = true;

    protected $keyType = 'int';

        public $timestamps = true;

        protected $fillable = [
            'fecha_pedido',
            'total', 
            'impuesto', 
            'estado', 
            'tipo_pedido', 
            'id_usuario', 
            'id_cliente',
            ];

        protected $dates = [
            'created_at',
            'updated_at'
        ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pedido) {
            // Se ejecuta justo antes de insertar en la DB
            if (!$pedido->fecha_pedido) {
                $pedido->fecha_pedido = now();
            }
        });
    }
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function venta()
    {
        return $this->hasOne(Venta::class, 'id_pedido_cliente', 'id_pedido_cliente');
    }

    public function detalles()
    {
        return $this->hasMany(Detalle_pedido::class, 'id_pedido_cliente', 'id_pedido_cliente');
    }

}