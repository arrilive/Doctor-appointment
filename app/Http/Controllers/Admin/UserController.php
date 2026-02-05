<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use Spatie\Permission\Models\Role;

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
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación completa
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
            'id_number' => 'nullable|string|max:50',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
        ]);

        // Crear el usuario
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => $request->password,
            'id_number' => $request->id_number,
            'phone'     => $request->phone,
            'address'   => $request->address,
        ]);

        // Asignar el rol seleccionado
        $role = Role::findById($request->role_id);
        $user->assignRole($role);

        // Si el rol es 'Paciente', crear automáticamente el registro en la tabla patients
        if ($role->name === 'Paciente') {
            $user->patient()->create([]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Usuario creado correctamente',
                'text'  => 'El usuario ha sido creado exitosamente con el rol ' . $role->name,
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
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => 'nullable|string|min:8|confirmed',
            'role_id'   => 'required|exists:roles,id',
            'id_number' => 'nullable|string|max:50',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string|max:255',
        ]);

        // Detectar cambios
        $rolActual = $user->roles->first()?->id;
        $sinCambios = (
            $user->name === $request->name &&
            $user->email === $request->email &&
            $user->id_number === $request->id_number &&
            $user->phone === $request->phone &&
            $user->address === $request->address &&
            $rolActual == $request->role_id &&
            !$request->filled('password')
        );

        if ($sinCambios) {
            return redirect()
                ->route('admin.users.index')
                ->with('swal', [
                    'icon'  => 'info',
                    'title' => 'Sin cambios',
                    'text'  => 'No se detectaron modificaciones.',
                ]);
        }

        // Preparar datos para actualizar
        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'id_number' => $request->id_number,
            'phone'     => $request->phone,
            'address'   => $request->address,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        // Actualizar rol si cambió
        if ($rolActual != $request->role_id) {
            $nuevoRol = Role::findById($request->role_id);
            $user->syncRoles([$nuevoRol]);

            // Si el nuevo rol es 'Paciente' y no tiene registro de paciente, crearlo
            if ($nuevoRol->name === 'Paciente' && !$user->patient) {
                $user->patient()->create([]);
            }
        }

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
        // Regla 1: solo admins pueden eliminar usuarios
        if (! auth()->user()->can('access-admin')) {
            abort(403, 'No tienes permisos para eliminar usuarios.');
        }

        // Regla 2: nadie puede eliminarse a sí mismo
        if (auth()->id() === $user->id) {
            abort(403, 'No puedes eliminar tu propio usuario.');
        }

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
