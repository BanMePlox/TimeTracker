<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\FichajeResource;
use App\Models\ActivityLog;
use App\Models\Fichaje;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FichajeController extends Controller {
    public function index(Request $request) {
        $query = Fichaje::with('user')->latest();
        if ($request->filled('usuario_id')) $query->where('user_id', $request->usuario_id);
        if ($request->filled('tipo')) $query->where('tipo', $request->tipo);
        if ($request->filled('fecha_desde')) $query->whereDate('created_at', '>=', $request->fecha_desde);
        if ($request->filled('fecha_hasta')) $query->whereDate('created_at', '<=', $request->fecha_hasta);
        return FichajeResource::collection($query->paginate(20));
    }

    public function show(Fichaje $fichaje) {
        return new FichajeResource($fichaje->load('user'));
    }

    public function update(Request $request, Fichaje $fichaje) {
        $request->validate([
            'tipo' => 'required|in:entrada,salida',
            'fecha_hora' => 'required|date',
        ]);
        $datetime = Carbon::parse($request->fecha_hora);
        $tipoAnterior = $fichaje->tipo;
        $fechaAnterior = $fichaje->created_at->format('d/m/Y H:i');
        $fichaje->update(['tipo' => $request->tipo, 'created_at' => $datetime, 'updated_at' => $datetime]);
        ActivityLog::registrar('fichaje_corregido', "Fichaje #{$fichaje->id} corregido vía API: {$tipoAnterior} {$fechaAnterior} → {$request->tipo} {$datetime->format('d/m/Y H:i')}", 'Fichaje', $fichaje->id);
        return new FichajeResource($fichaje->fresh()->load('user'));
    }

    public function destroy(Fichaje $fichaje) {
        $fichaje->load('user');
        ActivityLog::registrar('fichaje_eliminado', "Fichaje #{$fichaje->id} de {$fichaje->user->name} eliminado vía API", 'Fichaje', $fichaje->id);
        $fichaje->delete();
        return response()->json(['mensaje' => 'Fichaje eliminado correctamente.']);
    }
}
