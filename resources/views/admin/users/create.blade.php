<x-admin-layout
    title="Usuarios | MediCitas"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Usuarios',  'href' => route('admin.users.index')],
        ['name' => 'Nuevo']
    ]">

    <x-wire-card>
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Hay errores en el formulario:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-wire-input
                        name="name"
                        label="Nombre Completo"
                        required
                        :value="old('name')"
                        placeholder="Nombre completo del usuario" />
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-wire-input
                        name="email"
                        label="Correo Electrónico"
                        type="email"
                        required
                        :value="old('email')"
                        placeholder="correo@ejemplo.com" />
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <x-wire-input
                        name="id_number"
                        label="Número de Identificación"
                        minlength="8"
                        maxlength="20"
                        title="El número de identificación debe tener entre 8 y 20 caracteres"
                        :value="old('id_number')"
                        placeholder="Entre 8 y 20 caracteres" />
                </div>

                <div>
                    <x-wire-input
                        name="phone"
                        label="Teléfono"
                        type="number"
                        pattern="[0-9]{10}"
                        minlength="10"
                        maxlength="10"
                        placeholder="Exactamente 10 dígitos"
                        title="El número de teléfono debe ser de exactamente 10 dígitos"
                        :value="old('phone')" />
                </div>
            </div>

            <div class="mt-4">
                <x-wire-input
                    name="address"
                    label="Dirección"
                    maxlength="500"
                    :value="old('address')"
                    placeholder="Dirección completa" />
            </div>

            <div class="mt-4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                    Rol <span class="text-red-500">*</span>
                </label>
                <select
                    name="role"
                    id="role"
                    required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Seleccione un rol...</option>
                    @foreach($roles as $roleName)
                        <option value="{{ $roleName }}" {{ old('role') == $roleName ? 'selected' : '' }}>
                            {{ $roleName }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t border-gray-200 mt-6 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Contraseña</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-wire-input
                            name="password"
                            label="Contraseña"
                            type="password"
                            required
                            placeholder="Mínimo 8 caracteres" />
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-wire-input
                            name="password_confirmation"
                            label="Confirmar Contraseña"
                            type="password"
                            required
                            placeholder="Repita la contraseña" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <x-wire-button type='submit' blue>Crear Usuario</x-wire-button>
            </div>
        </form>
    </x-wire-card>

</x-admin-layout>
