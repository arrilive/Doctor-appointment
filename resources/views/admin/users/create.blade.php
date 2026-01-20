<x-admin-layout 
    title="Usuarios | MediCitas"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Usuarios',  'href' => route('admin.users.index')],
        ['name' => 'Nuevo']
    ]">

    <x-wire-card>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <x-wire-input name="name" label="Nombre" required :value="old('name')" placeholder="Nombre" autocomplete="name" />

                <x-wire-input name="email" label="Correo electrónico" required :value="old('email')" placeholder="correo@ejemplo.com" />
           

            <div class="flex justify-end mt-4">
                <x-wire-button type='submit' blue>Guardar</x-wire-button>
            </div>
        </form>
    </x-wire-card>

</x-admin-layout>
