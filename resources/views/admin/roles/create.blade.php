<x-admin-layout 
    title="Roles | MediCitas"
    :breadcrumbs="[
        [     
            'name' => 'Dashboard',
            'route' => route ('admin.dashboard'),
        ],  

        [     
            'name' => 'Roles',
            'route' => route ('admin.roles.index')
        ],
        [
            'name' => 'Nuevo'
        ]  
    ]">
</x-admin-layout>
