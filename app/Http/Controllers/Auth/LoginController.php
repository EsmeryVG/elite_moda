<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'Ingresa un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $credenciales = $request->only('email', 'password');
        $remember     = $request->boolean('remember');

        if (!Auth::attempt($credenciales, $remember)) {
            return back()->withErrors([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectTo());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectTo(): string
    {
        $rol = Auth::user()->rol?->nombre ?? '';

        return match(strtolower($rol)) {
            'cajero'    => route('ventas.create'),
            'contable'  => route('ventas.index'),
            default     => route('home'),
        };
    }
}