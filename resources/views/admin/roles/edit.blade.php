<x-admin-layout 
    title="Roles | MediCitas"
    :breadcrumbs="[
        [     
            'name' => 'Dashboard',
            'href' => route ('admin.dashboard'),
        ],  

        [     
            'name' => 'Roles',
            'href' => route ('admin.roles.index')
        ],
        [
            'name' => 'Editar'
        ]  
    ]">
</x-admin-layout>
