<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FichajeResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'usuario' => new UserResource($this->whenLoaded('user')),
            'tipo' => $this->tipo,
            'fecha' => $this->created_at->format('Y-m-d'),
            'hora' => $this->created_at->format('H:i:s'),
            'fecha_hora' => $this->created_at,
        ];
    }
}
