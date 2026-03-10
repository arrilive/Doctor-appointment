<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')
                ->constrained('doctors')
                ->onDelete('cascade');
            $table->tinyInteger('day_of_week'); // 0=Lunes, 1=Martes ... 6=Domingo
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            // Un doctor no puede tener dos bloques idénticos el mismo día
            $table->unique(['doctor_id', 'day_of_week', 'start_time'], 'unique_doctor_schedule');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};
