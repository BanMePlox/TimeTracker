<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AusenciaResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'usuario' => new UserResource($this->whenLoaded('user')),
            'tipo' => $this->tipo,
            'tipo_label' => $this->tipo_label,
            'fecha_inicio' => $this->fecha_inicio->format('Y-m-d'),
            'fecha_fin' => $this->fecha_fin->format('Y-m-d'),
            'dias' => $this->dias,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'creado_en' => $this->created_at,
        ];
    }
}
