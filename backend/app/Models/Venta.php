<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Venta extends Model
{
    protected $table = 'Venta';

    protected $primaryKey = 'id_venta'; 
    
    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'fecha_venta', 
        'total_venta', 
        'metodo_pago', 
        'pago_cliente', 
        'cambio',
        'id_cliente',
        'id_pedido_cliente',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];  

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($venta) {
            // Se ejecuta justo antes de insertar en la DB
            if (!$venta->fecha_venta) {
                $venta->fecha_venta = now();
            }
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function pedidoCliente()
    {
        return $this->belongsTo(Pedido_cliente::class, 'id_pedido_cliente', 'id_pedido_cliente');
    }

    public function detalles() {
        return $this->hasMany(Detalle_venta::class, 'id_venta', 'id_venta');
    }
}