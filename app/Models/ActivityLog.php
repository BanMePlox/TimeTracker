<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use LogicException;

class ActivityLog extends Model
{
    // Solo created_at, sin updated_at
    const UPDATED_AT = null;

    protected $fillable = ['user_id', 'accion', 'descripcion', 'modelo', 'modelo_id', 'ip'];

    // ── Bloquear cualquier modificación ───────────────────────────────────────

    public function update(array $attributes = [], array $options = []): bool
    {
        throw new LogicException('Los registros de actividad son inmutables.');
    }

    public function delete(): bool|null
    {
        throw new LogicException('Los registros de actividad no pueden eliminarse.');
    }

    public function forceDelete(): bool|null
    {
        throw new LogicException('Los registros de actividad no pueden eliminarse.');
    }

    // ── Relación ──────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helper estático para registrar ────────────────────────────────────────

    public static function registrar(
        string $accion,
        string $descripcion,
        ?string $modelo = null,
        ?int $modeloId = null
    ): void {
        static::create([
            'user_id'    => Auth::id(),
            'accion'     => $accion,
            'descripcion' => $descripcion,
            'modelo'     => $modelo,
            'modelo_id'  => $modeloId,
            'ip'         => Request::ip(),
        ]);
    }
}
