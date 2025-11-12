<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        // Si usas guards no estándar, añade: 'guard_name' => 'web'
        Role::create([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('admin.roles.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Rol creado correctamente',
                'text'  => 'El rol ha sido creado exitosamente',
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        // Restringir la acción para los primeros 4 roles fijos
        if ($role->id <= 4) {
            return redirect()
                ->route('admin.roles.index')
                ->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Acción no permitida',
                    'text'  => 'Este rol no puede editarse.',
                ]);
        }

        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Opcional: impedir actualizar también estos roles
        if ($role->id <= 4) {
            return redirect()
                ->route('admin.roles.index')
                ->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Acción no permitida',
                    'text'  => 'Este rol no puede editarse.',
                ]);
        }

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
        ]);

        // Si no cambió, avisar
        if ($role->name === $request->name) {
            return redirect()
                ->route('admin.roles.index')
                ->with('swal', [
                    'icon'  => 'info',
                    'title' => 'Sin cambios',
                    'text'  => 'No se detectaron modificaciones.',
                ]);
        }

        // Actualizar
        $role->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('admin.roles.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Rol actualizado correctamente',
                'text'  => 'El rol ha sido actualizado exitosamente.',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Restringir la acción para los primeros 4 roles fijos
        if ($role->id <= 4) {
            return redirect()
                ->route('admin.roles.index')
                ->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Acción no permitida',
                    'text'  => 'Este rol no puede eliminarse.',
                ]);
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Rol eliminado',
                'text'  => 'El rol ha sido eliminado correctamente.',
            ]);
    }
}
