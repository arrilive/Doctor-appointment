<div class="flex items-center gap-2">
    {{-- Ver/Crear consulta --}}
    <x-wire-button href="{{ route('admin.consultations.show', $appointment) }}" teal xs title="Consulta médica">
        <i class="fa-solid fa-stethoscope"></i>
    </x-wire-button>

    <x-wire-button href="{{ route('admin.appointments.edit', $appointment) }}" blue xs title="Editar cita">
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>

    <form action="{{ route('admin.appointments.destroy', $appointment) }}" method="POST" class="delete-form">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" red xs title="Eliminar">
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>
