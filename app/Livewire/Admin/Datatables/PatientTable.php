<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;

class PatientTable extends DataTableComponent
{
    //Define el modelo y su consulta
    public function builder(): Builder
    {
        return Patient::query()->with(['user']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Paciente", "user.name")
                ->sortable(),
            Column::make("Correo", "user.email")
                ->sortable(),
            Column::make("Número de id", "user.id_number")
                ->sortable(),
            Column::make("Teléfono", "user.phone")
                ->sortable(),
            Column::make("Acciones")
                ->label(function($row){
                    return view('admin.patients.actions', ['patient' => $row]);
                })
        ];
    }
}
