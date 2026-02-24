<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'Categoria';

    protected $primaryKey = 'id_categoria';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'nombre_categoria',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function productos() {
        return $this->hasMany(Producto::class, 'id_categoria', 'id_categoria');
    }
}