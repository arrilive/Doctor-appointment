@if ($status === 'programado')
    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Programado</span>
@elseif ($status === 'completado')
    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Completado</span>
@elseif ($status === 'cancelado')
    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Cancelado</span>
@else
    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ ucfirst($status) }}</span>
@endif
