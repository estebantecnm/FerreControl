<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Cliente extends Model
{
    protected $table = 'Cliente';

    protected $primaryKey = 'id_cliente';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'nombre', 
        'ap_paterno', 
        'ap_materno', 
        'fecha_nacimiento', 
        'sexo', 
        'correo', 
        'rfc', 
        'telefono', 
        'num_ext', 
        'num_int', 
        'calle', 
        'colonia', 
        'municipio', 
        'estado',
        'status',
        'limite_credito',
        'saldo_pendiente',
        'dias_credito',
        'tipo_cliente',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    //Relaciones que tiene con otras tablas
    public function pedidos()
    {
        return $this->hasMany(Pedido_cliente::class, 'id_cliente', 'id_cliente');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_cliente', 'id_cliente');
    }

    //funcion para consultar historial de compras del cliente(para eso accede a la tabla ventas y de ahi a los detalles de venta)
    public function consultarHistorialCompras()
    {
        return $this->ventas()->with('Detalle_venta')->get();
    }

    //funcion para gestionar el saldo pendiente del cliente, se actualiza cada vez que se realiza una venta o un pago
    public function gestionarSaldoPendiente($id)
    {
        return $this->where('id_cliente', $id)->first()->saldo_pendiente;
    }

}