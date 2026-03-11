<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'nombre' => $this->name,
            'email' => $this->email,
            'pin' => $this->pin,
            'rol' => $this->role,
            'creado_en' => $this->created_at,
        ];
    }
}
