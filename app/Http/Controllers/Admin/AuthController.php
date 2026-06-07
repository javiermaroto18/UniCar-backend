<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    // GET /admin/login — muestra el formulario de acceso (Vue)
    public function showLogin()
    {
        return Inertia::render('Admin/Login');
    }

    // POST /admin/login — autentica por sesión web y exige rol admin
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Las credenciales no son correctas.',
            ])->onlyInput('email');
        }

        // Solo los administradores pueden entrar al panel
        if (! Auth::user()->hasRole('admin')) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Esta cuenta no tiene permisos de administrador.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended('/admin');
    }

    // POST /admin/logout — cierra la sesión web del panel
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
