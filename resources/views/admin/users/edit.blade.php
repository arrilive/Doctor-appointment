<x-admin-layout
    title="Usuarios | MediCitas"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Usuarios',  'href' => route('admin.users.index')],
        ['name' => 'Editar']
    ]">

    <x-wire-card>
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-wire-input
                        label="Nombre Completo"
                        name="name"
                        placeholder="Nombre del usuario"
                        value="{{ old('name', $user->name) }}">
                    </x-wire-input>
                </div>

                <div>
                    <x-wire-input
                        label="Correo electrónico"
                        name="email"
                        type="email"
                        placeholder="correo@ejemplo.com"
                        value="{{ old('email', $user->email) }}">
                    </x-wire-input>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <x-wire-input
                        label="Número de Identificación"
                        name="id_number"
                        placeholder="DNI, Cédula, etc."
                        value="{{ old('id_number', $user->id_number) }}">
                    </x-wire-input>
                </div>

                <div>
                    <x-wire-input
                        label="Teléfono"
                        name="phone"
                        placeholder="+1234567890"
                        value="{{ old('phone', $user->phone) }}">
                    </x-wire-input>
                </div>
            </div>

            <div class="mt-4">
                <x-wire-input
                    label="Dirección"
                    name="address"
                    placeholder="Dirección completa"
                    value="{{ old('address', $user->address) }}">
                </x-wire-input>
            </div>

            <div class="mt-4">
                <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Rol <span class="text-red-500">*</span>
                </label>
                <select
                    name="role_id"
                    id="role_id"
                    required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Seleccione un rol...</option>
                    @foreach(\Spatie\Permission\Models\Role::all() as $role)
                        <option value="{{ $role->id }}"
                            {{ old('role_id', $user->roles->first()?->id) == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t border-gray-200 mt-6 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Cambiar Contraseña</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Si no deseas cambiar la contraseña, deja los siguientes campos vacíos.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-wire-input
                            label="Nueva contraseña (opcional)"
                            name="password"
                            type="password"
                            placeholder="••••••••">
                        </x-wire-input>
                    </div>

                    <div>
                        <x-wire-input
                            label="Confirmar nueva contraseña"
                            name="password_confirmation"
                            type="password"
                            placeholder="••••••••">
                        </x-wire-input>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <x-wire-button type='submit' blue>Actualizar Usuario</x-wire-button>
            </div>
        </form>
    </x-wire-card>

</x-admin-layout>
