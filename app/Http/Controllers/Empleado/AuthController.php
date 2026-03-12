<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'empleado') {
            return redirect()->route('empleado.dashboard');
        }
        return view('empleado.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if (Auth::user()->role !== 'empleado') {
                Auth::logout();
                return back()->withErrors(['email' => 'Esta área es solo para empleados.']);
            }
            if (!Auth::user()->active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Tu cuenta está desactivada. Contacta con el administrador.'])->onlyInput('email');
            }
            $request->session()->regenerate();
            return redirect()->route('empleado.dashboard');
        }

        return back()->withErrors(['email' => 'Las credenciales no son correctas.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('empleado.login');
    }
}
