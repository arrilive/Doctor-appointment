<?php

namespace App\Livewire\Admin\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Appointment;

class AppointmentTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Appointment::query()->with(['patient.user', 'doctor.user']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable(),
            Column::make('Paciente', 'patient.user.name')
                ->sortable()
                ->format(fn($value) => $value ?: 'N/A'),
            Column::make('Doctor', 'doctor.user.name')
                ->sortable()
                ->format(fn($value) => $value ?: 'N/A'),
            Column::make('Fecha', 'appointment_date')
                ->sortable()
                ->format(fn($value) => \Carbon\Carbon::parse($value)->format('d/m/Y')),
            Column::make('Hora', 'start_time')->sortable(),
            Column::make('Hora Fin', 'end_time')->sortable(),
            Column::make('Estado', 'status')
                ->sortable()
                ->html()
                ->format(fn($value) => match($value) {
                    'programado' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Programado</span>',
                    'completado' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Completado</span>',
                    'cancelado'  => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Cancelado</span>',
                    default      => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">' . ucfirst((string)$value) . '</span>',
                }),
            Column::make('Acciones')
                ->label(fn($row) => view('admin.appointments.actions', ['appointment' => $row])),
        ];
    }
}
