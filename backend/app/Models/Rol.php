<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Rol extends Model
{
    protected $table = 'Rol';

    protected $primaryKey = 'id_rol';

    public $timestamps = true; 

    public $incrementing = true;

    public $keyType = 'int';

    protected $fillable = [
        'nombre'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}