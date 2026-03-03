{{-- Vista para crear un nuevo ticket de soporte --}}
<x-admin-layout
    title="Nuevo Ticket"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Soporte', 'href' => route('admin.support-tickets.index')],
        ['name' => 'Nuevo Ticket'],
    ]"
>

    <x-wire-card>
        {{-- Encabezado del formulario --}}
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Reportar un problema</h2>
        <p class="text-sm text-gray-500 mb-6">Describe tu problema o duda y nuestro equipo de soporte se pondrá en contacto contigo.</p>

        <form action="{{ route('admin.support-tickets.store') }}" method="POST">
            @csrf

            {{-- Campo: Título del problema --}}
            <x-wire-input
                label="Título del problema"
                name="title"
                placeholder="Escribe un título breve para tu problema"
                value="{{ old('title') }}"
            />

            {{-- Campo: Descripción detallada --}}
            <div class="mt-4">
                <x-wire-textarea
                    label="Descripción detallada"
                    name="description"
                    placeholder="Describe tu problema con el mayor detalle posible"
                    value="{{ old('description') }}"
                />
            </div>

            {{-- Botones de acción --}}
            <div class="flex justify-end gap-3 mt-6">
                <x-wire-button outline href="{{ route('admin.support-tickets.index') }}">
                    Cancelar
                </x-wire-button>
                <x-wire-button type="submit" blue>
                    <i class="fa-solid fa-paper-plane me-1"></i>
                    Enviar Ticket
                </x-wire-button>
            </div>
        </form>
    </x-wire-card>

</x-admin-layout>
