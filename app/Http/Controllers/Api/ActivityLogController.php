<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller {
    public function index(Request $request) {
        $query = ActivityLog::with('user')->latest('created_at');
        if ($request->filled('usuario_id')) $query->where('user_id', $request->usuario_id);
        if ($request->filled('accion')) $query->where('accion', $request->accion);
        if ($request->filled('fecha_desde')) $query->whereDate('created_at', '>=', $request->fecha_desde);
        if ($request->filled('fecha_hasta')) $query->whereDate('created_at', '<=', $request->fecha_hasta);
        return ActivityLogResource::collection($query->paginate(50));
    }
}
