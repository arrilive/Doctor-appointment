{{-- Vista principal: Lista de tickets de soporte --}}
<x-admin-layout
    title="Soporte"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Soporte'],
    ]"
>
    {{-- Botón para crear un nuevo ticket --}}
    <x-slot name="actions">
        <a href="{{ route('admin.support-tickets.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
            <i class="fa-solid fa-plus"></i>
            Nuevo Ticket
        </a>
    </x-slot>

    <x-wire-card>
        {{-- Tabla de tickets --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Usuario</th>
                        <th class="px-6 py-3">Título</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3">Fecha</th>
                        <th class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">#{{ $ticket->id }}</td>
                            <td class="px-6 py-4">{{ $ticket->user->name }}</td>
                            <td class="px-6 py-4">{{ $ticket->title }}</td>
                            <td class="px-6 py-4">
                                {{-- Badge de estado con color dinámico --}}
                                @switch($ticket->status)
                                    @case('abierto')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Abierto
                                        </span>
                                        @break
                                    @case('en_progreso')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            En Progreso
                                        </span>
                                        @break
                                    @case('cerrado')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Cerrado
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                {{-- Formulario para eliminar con confirmación SweetAlert --}}
                                <form action="{{ route('admin.support-tickets.destroy', $ticket) }}" method="POST" class="delete-form inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-wire-button type="submit" red xs>
                                        <i class="fa-solid fa-trash"></i>
                                    </x-wire-button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        {{-- Mensaje cuando no hay tickets --}}
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-ticket text-4xl mb-3 block"></i>
                                No hay tickets de soporte registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-wire-card>

</x-admin-layout>
