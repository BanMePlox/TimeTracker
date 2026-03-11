<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function login(Request $request) {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['mensaje' => 'Credenciales incorrectas.'], 401);
        }
        $user = Auth::user();
        if (!$user->isAdmin()) {
            Auth::logout();
            return response()->json(['mensaje' => 'Sin permisos de administrador.'], 403);
        }
        $token = $user->createToken('api-token')->plainTextToken;
        ActivityLog::registrar('login', 'Inicio de sesión vía API.');
        return response()->json([
            'token' => $token,
            'tipo' => 'Bearer',
            'usuario' => new UserResource($user),
        ]);
    }

    public function logout(Request $request) {
        ActivityLog::registrar('logout', 'Cierre de sesión vía API.');
        $request->user()->currentAccessToken()->delete();
        return response()->json(['mensaje' => 'Sesión cerrada correctamente.']);
    }

    public function me(Request $request) {
        return new UserResource($request->user());
    }
}
