<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller {
    public function index(Request $request) {
        $query = User::query();
        if ($request->filled('rol')) $query->where('role', $request->rol);
        if ($request->filled('buscar')) $query->where('name', 'like', '%'.$request->buscar.'%');
        return UserResource::collection($query->orderBy('name')->paginate(20));
    }

    public function store(Request $request) {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'rol' => 'required|in:admin,empleado',
        ]);
        // Auto-generate unique PIN
        do { $pin = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT); } while (User::where('pin', $pin)->exists());
        $user = User::create([
            'name' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->rol,
            'pin' => $pin,
        ]);
        ActivityLog::registrar('usuario_creado', "Empleado creado vía API: {$user->name}", 'User', $user->id);
        return new UserResource($user);
    }

    public function show(User $usuario) {
        return new UserResource($usuario);
    }

    public function update(Request $request, User $usuario) {
        $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$usuario->id,
            'rol' => 'sometimes|in:admin,empleado',
            'pin' => 'sometimes|size:4|unique:users,pin,'.$usuario->id,
            'password' => 'sometimes|min:6',
        ]);
        $data = array_filter([
            'name' => $request->nombre,
            'email' => $request->email,
            'role' => $request->rol,
            'pin' => $request->pin,
            'password' => $request->filled('password') ? Hash::make($request->password) : null,
        ], fn($v) => $v !== null);
        $usuario->update($data);
        ActivityLog::registrar('usuario_actualizado', "Empleado actualizado vía API: {$usuario->name}", 'User', $usuario->id);
        return new UserResource($usuario->fresh());
    }

    public function destroy(User $usuario) {
        if ($usuario->id === auth()->id()) {
            return response()->json(['mensaje' => 'No puedes eliminarte a ti mismo.'], 422);
        }
        ActivityLog::registrar('usuario_eliminado', "Empleado eliminado vía API: {$usuario->name}", 'User', $usuario->id);
        $usuario->delete();
        return response()->json(['mensaje' => 'Empleado eliminado correctamente.']);
    }
}
