<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo_usuario' => ['required', 'email'],
            'contraseña' => ['required'],
        ], [
            'correo_usuario.required' => 'El correo electrónico es obligatorio',
            'correo_usuario.email' => 'El correo electrónico debe ser válido',
            'contraseña.required' => 'La contraseña es obligatoria'
        ]);

        if (Auth::attempt([
            'correo_usuario' => $credentials['correo_usuario'],
            'password' => $credentials['contraseña']
        ])) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'correo_usuario' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('correo_usuario');
    }

    public function register(Request $request)
    {
        $request->validate([
            'doc_usuario' => ['required', 'string', 'max:45', 'unique:usuarios'],
            'tip_documento' => ['required', 'in:CC,CE,TI,PA,DNI,PEP,PPT'],
            'pri_nombre' => ['required', 'string', 'max:45'],
            'seg_nombre' => ['nullable', 'string', 'max:45'],
            'pri_apellido' => ['required', 'string', 'max:45'],
            'seg_apellido' => ['required', 'string', 'max:45'],
            'fec_nacimiento' => ['required', 'date'],
            'sex_usuario' => ['required', 'in:Masculino,Femenino'],
            'cel_usuario' => ['required', 'string', 'max:45', 'unique:usuarios'],
            'correo_usuario' => ['required', 'string', 'email', 'max:45', 'unique:usuarios'],
            'contraseña' => ['required', 'confirmed', Password::min(8)],
            'rol_usuario' => ['required', 'in:Arrendador,Arrendatario'],
        ], [
            'doc_usuario.required' => 'El documento es obligatorio',
            'doc_usuario.unique' => 'Este documento ya está registrado',
            'tip_documento.required' => 'El tipo de documento es obligatorio',
            'pri_nombre.required' => 'El primer nombre es obligatorio',
            'pri_apellido.required' => 'El primer apellido es obligatorio',
            'seg_apellido.required' => 'El segundo apellido es obligatorio',
            'fec_nacimiento.required' => 'La fecha de nacimiento es obligatoria',
            'sex_usuario.required' => 'El sexo es obligatorio',
            'cel_usuario.required' => 'El celular es obligatorio',
            'cel_usuario.unique' => 'Este número de celular ya está registrado',
            'correo_usuario.required' => 'El correo electrónico es obligatorio',
            'correo_usuario.email' => 'El correo electrónico debe ser válido',
            'correo_usuario.unique' => 'Este correo ya está registrado',
            'contraseña.required' => 'La contraseña es obligatoria',
            'contraseña.confirmed' => 'Las contraseñas no coinciden',
            'rol_usuario.required' => 'El rol es obligatorio'
        ]);

        $usuario = Usuario::create([
            'doc_usuario' => $request->doc_usuario,
            'tip_documento' => $request->tip_documento,
            'pri_nombre' => $request->pri_nombre,
            'seg_nombre' => $request->seg_nombre,
            'pri_apellido' => $request->pri_apellido,
            'seg_apellido' => $request->seg_apellido,
            'fec_nacimiento' => $request->fec_nacimiento,
            'sex_usuario' => $request->sex_usuario,
            'cel_usuario' => $request->cel_usuario,
            'correo_usuario' => $request->correo_usuario,
            'contraseña' => Hash::make($request->contraseña),
            'rol_usuario' => $request->rol_usuario,
        ]);

        Auth::login($usuario);

        return redirect('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}