<x-admin-layout 
  title="Pacientes | MediCitas"
  :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],

    ['name' => 'Pacientes'],
    ['href' => route('admin.patients.index'), 'name' => 'Pacientes'],
    ['name' => 'Crear Paciente'],
  ]"
>

</x-admin-layout>
