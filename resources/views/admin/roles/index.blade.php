<x-admin-layout 
  title="Roles | MediCitas"
  :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Roles'],
  ]"
>
  <x-slot name="actions">
    <a href="{{ route('admin.roles.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-white">
      <i class="fa-solid fa-plus"></i>
      Nuevo
    </a>
  </x-slot>

  @livewire('admin.datatables.role-table')
</x-admin-layout>
