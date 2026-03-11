<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fichaje extends Model
{
    protected $fillable = [
        'user_id',
        'tipo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
