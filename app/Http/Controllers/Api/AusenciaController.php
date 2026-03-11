<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\AusenciaResource;
use App\Models\ActivityLog;
use App\Models\Ausencia;
use Illuminate\Http\Request;

class AusenciaController extends Controller {
    public function index(Request $request) {
        $query = Ausencia::with('user')->latest();
        if ($request->filled('usuario_id')) $query->where('user_id', $request->usuario_id);
        if ($request->filled('tipo')) $query->where('tipo', $request->tipo);
        if ($request->filled('estado')) $query->where('estado', $request->estado);
        return AusenciaResource::collection($query->paginate(20));
    }

    public function store(Request $request) {
        $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'tipo' => 'required|in:vacaciones,baja_medica,ausencia_justificada,ausencia_injustificada',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'sometimes|in:pendiente,aprobada,rechazada',
        ]);
        $overlap = Ausencia::where('user_id', $request->usuario_id)
            ->where('fecha_inicio', '<=', $request->fecha_fin)
            ->where('fecha_fin', '>=', $request->fecha_inicio)->exists();
        if ($overlap) return response()->json(['mensaje' => 'Rango de fechas solapado con otra ausencia.'], 422);

        $ausencia = Ausencia::create([
            'user_id' => $request->usuario_id,
            'tipo' => $request->tipo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'descripcion' => $request->descripcion,
            'estado' => $request->estado ?? 'pendiente',
        ]);
        ActivityLog::registrar('ausencia_creada', "Ausencia creada vía API para usuario #{$request->usuario_id}", 'Ausencia', $ausencia->id);
        return (new AusenciaResource($ausencia->load('user')))->response()->setStatusCode(201);
    }

    public function show(Ausencia $ausencia) {
        return new AusenciaResource($ausencia->load('user'));
    }

    public function update(Request $request, Ausencia $ausencia) {
        $request->validate([
            'tipo' => 'sometimes|in:vacaciones,baja_medica,ausencia_justificada,ausencia_injustificada',
            'fecha_inicio' => 'sometimes|date',
            'fecha_fin' => 'sometimes|date|after_or_equal:fecha_inicio',
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'sometimes|in:pendiente,aprobada,rechazada',
        ]);
        $ausencia->update($request->only(['tipo', 'fecha_inicio', 'fecha_fin', 'descripcion', 'estado']));
        ActivityLog::registrar('ausencia_actualizada', "Ausencia #{$ausencia->id} actualizada vía API", 'Ausencia', $ausencia->id);
        return new AusenciaResource($ausencia->fresh()->load('user'));
    }

    public function destroy(Ausencia $ausencia) {
        ActivityLog::registrar('ausencia_eliminada', "Ausencia #{$ausencia->id} eliminada vía API", 'Ausencia', $ausencia->id);
        $ausencia->delete();
        return response()->json(['mensaje' => 'Ausencia eliminada correctamente.']);
    }

    public function aprobar(Ausencia $ausencia) {
        $ausencia->update(['estado' => 'aprobada']);
        ActivityLog::registrar('ausencia_aprobada', "Ausencia #{$ausencia->id} aprobada vía API", 'Ausencia', $ausencia->id);
        return new AusenciaResource($ausencia->fresh()->load('user'));
    }

    public function rechazar(Ausencia $ausencia) {
        $ausencia->update(['estado' => 'rechazada']);
        ActivityLog::registrar('ausencia_rechazada', "Ausencia #{$ausencia->id} rechazada vía API", 'Ausencia', $ausencia->id);
        return new AusenciaResource($ausencia->fresh()->load('user'));
    }
}
