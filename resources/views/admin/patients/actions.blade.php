<div class="flex items-center space-x-2">
    <x-wire-button href="{{ route('admin.patients.show', $patient) }}" secondary xs>
        <i class="fa-solid fa-eye"></i>
    </x-wire-button>

    <x-wire-button href="{{ route('admin.patients.edit', $patient) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>

    <form action="{{ route('admin.patients.destroy', $patient) }}" method="POST" class="delete-form">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" red xs>
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>