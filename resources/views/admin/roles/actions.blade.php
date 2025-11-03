<div class="flex items-center spacex-2">
    <x-wire-button href="{{route('admin.route.edit', $role)}}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>    

    <form action="{{route('admin.route.destroy', $role)}}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <x-wire-button type='submit' red xs>
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>

</div>