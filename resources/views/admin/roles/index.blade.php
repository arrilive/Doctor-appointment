<x-admin-layout 
    title="Roles | MediCitas"
    :breadcrumbs="[
        [     
            'name' => 'Dashboard',
            'href' => route ('admin.dashboard'),
        ],  

        [     
            'name' => 'Roles',
        ],  
    ]">

    <x-slot name="actions">
        <x-wire-button blue href="{{route('admin.roles.create')}}">
            <i class="fa-solid fa-plus"></i>
            Nuevo
        </x-wire-button>

    </x-slot>

    @livewire('admin.datatables.role-table')
    
</x-admin-layout>
