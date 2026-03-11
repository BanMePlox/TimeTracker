<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'admin' => $this->whenLoaded('user', fn() => ['id' => $this->user->id, 'nombre' => $this->user->name]),
            'accion' => $this->accion,
            'descripcion' => $this->descripcion,
            'modelo' => $this->modelo,
            'modelo_id' => $this->modelo_id,
            'ip' => $this->ip,
            'fecha_hora' => $this->created_at,
        ];
    }
}
