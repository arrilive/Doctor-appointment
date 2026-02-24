<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->toArray();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'id_number' => 'required|string|min:8|max:20|unique:users,id_number',
            'phone' => 'required|numeric|digits:10',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string|max:500',
            'role' => 'required|string|exists:roles,name',
        ]);

        // Crear el usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'id_number' => $validated['id_number'],
            'phone' => $validated['phone'],
            'password' => bcrypt($validated['password']),
            'address' => $validated['address'],
        ]);

        // Asignar el rol al usuario
        $user->assignRole($validated['role']);

        // Si el rol es Paciente, crear registro en la tabla patients (relación 1:1)
        if ($validated['role'] === 'Paciente') {
            Patient::create([
                'user_id' => $user->id,
                'emergency_contact_name' => 'Por definir',
                'emergency_contact_phone' => '0000000000',
                'emergency_relationship' => 'Por definir',
            ]);

            return redirect()->route('admin.patients.index')
                ->with('swal', [
                    'title' => 'Paciente creado',
                    'text' => 'Complete la información médica del paciente.',
                    'icon' => 'success',
                ]);
        }

        // Si el rol es Doctor, crear registro en la tabla doctors (relación 1:1)
        if ($validated['role'] === 'Doctor') {
            $doctor = $user->doctor()->create([]);

            return redirect()->route('admin.doctors.edit', $doctor)
                ->with('swal', [
                    'title' => 'Doctor creado',
                    'text' => 'Complete la información del doctor.',
                    'icon' => 'success',
                ]);
        }

        // Redirigir con mensaje de éxito
        return redirect()->route('admin.users.index')
            ->with('swal', [
                'title' => 'Usuario creado',
                'text' => 'El usuario ha sido creado exitosamente.',
                'icon' => 'success',
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'id_number' => 'required|string|min:8|max:20|unique:users,id_number,' . $user->id,
            'phone' => 'required|numeric|digits:10',
            'address' => 'nullable|string|max:500',
            'role_id' => 'required|exists:roles,id',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->id_number = $validated['id_number'];
        $user->phone = $validated['phone'];
        $user->address = $validated['address'] ?? $user->address;

        if ($request->filled('password')) {
            $user->password = bcrypt($validated['password']);
        }

        $role = Role::findById($validated['role_id']);
        $roleChanged = !$user->hasRole($role->name);

        logger()->info('Dirty fields: ', $user->getDirty());

        if (!$user->isDirty() && !$roleChanged && !$request->filled('password')) {
            session()->flash('swal', [
                'icon' => 'info',
                'title' => 'Sin cambios',
                'text' => 'No se detectaron cambios en la información del usuario.',
            ]);
            return redirect()->back();
        }

        $user->save();
        $user->syncRoles([$role->name]);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario actualizado',
            'text' => 'La información del usuario ha sido actualizada correctamente.',
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            abort(403, 'No puedes eliminarte a ti mismo.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('swal', [
                'title' => 'Usuario eliminado',
                'text' => 'El usuario ha sido eliminado exitosamente.',
                'icon' => 'success',
            ]);
    }
}