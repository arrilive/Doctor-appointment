<div class="flex items-center gap-2">
    <x-wire-button href="{{ route('admin.doctors.edit', $doctor) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>
    <x-wire-button href="{{ route('admin.doctors.schedule.edit', $doctor) }}" teal xs>
        <i class="fa-solid fa-calendar-days"></i>
    </x-wire-button>
</div>
