<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ausencia extends Model
{
    protected $fillable = ['user_id', 'tipo', 'fecha_inicio', 'fecha_fin', 'descripcion', 'estado'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDiasAttribute()
    {
        return $this->fecha_inicio->diffInDays($this->fecha_fin) + 1;
    }

    public function getTipoLabelAttribute()
    {
        return match($this->tipo) {
            'vacaciones' => 'Vacaciones',
            'baja_medica' => 'Baja médica',
            'ausencia_justificada' => 'Ausencia justificada',
            'ausencia_injustificada' => 'Ausencia injustificada',
            default => $this->tipo,
        };
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'yellow',
            'aprobada' => 'green',
            'rechazada' => 'red',
            default => 'gray',
        };
    }
}
