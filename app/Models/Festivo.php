<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Festivo extends Model
{
    protected $fillable = ['fecha', 'nombre', 'descripcion'];

    protected $casts = [
        'fecha' => 'date',
    ];
}
