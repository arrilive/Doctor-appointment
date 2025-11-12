<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Igual que en roles: sólo devolver la vista de listado
        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación básica
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed', // password_confirmation
        ]);

        // Como en tu modelo el password está casteado como 'hashed',
        // no necesitamos llamar a bcrypt: Laravel lo hace solo.
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Usuario creado correctamente',
                'text'  => 'El usuario ha sido creado exitosamente',
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(User $user)
    {
        // En roles restringes id <= 4. Aquí lo dejamos libre,
        // pero si quieres luego protegemos al usuario 1, etc.
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Detectar “sin cambios” como en roles (si no se manda password)
        $sinCambiosNombre  = $user->name === $request->name;
        $sinCambiosCorreo  = $user->email === $request->email;
        $sinPasswordNueva  = !$request->filled('password');

        if ($sinCambiosNombre && $sinCambiosCorreo && $sinPasswordNueva) {
            return redirect()
                ->route('admin.users.index')
                ->with('swal', [
                    'icon'  => 'info',
                    'title' => 'Sin cambios',
                    'text'  => 'No se detectaron modificaciones.',
                ]);
        }

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password; 
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Usuario actualizado correctamente',
                'text'  => 'El usuario ha sido actualizado exitosamente.',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Usuario eliminado',
                'text'  => 'El usuario ha sido eliminado correctamente.',
            ]);
    }
}
