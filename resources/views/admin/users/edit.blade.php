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

            <x-wire-input 
                label="Nombre" 
                name="name" 
                placeholder="Nombre del usuario" 
                value="{{ old('name', $user->name) }}">
            </x-wire-input>

            <x-wire-input 
                class="mt-4"
                label="Correo electrónico" 
                name="email" 
                type="email"
                placeholder="correo@ejemplo.com" 
                value="{{ old('email', $user->email) }}">
            </x-wire-input>

            <p class="text-sm text-gray-500 mt-4">
                Si no deseas cambiar la contraseña, deja los siguientes campos vacíos.
            </p>

            <x-wire-input 
                class="mt-4"
                label="Nueva contraseña (opcional)" 
                name="password" 
                type="password"
                placeholder="••••••••">
            </x-wire-input>

            <x-wire-input 
                class="mt-4"
                label="Confirmar nueva contraseña" 
                name="password_confirmation" 
                type="password"
                placeholder="••••••••">
            </x-wire-input>

            <div class="flex justify-end mt-4">
                <x-wire-button type='submit' blue>Actualizar</x-wire-button>
            </div>
        </form>
    </x-wire-card>

</x-admin-layout>
