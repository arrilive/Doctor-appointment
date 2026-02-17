<?php

use App\Models\BloodType;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('al actualizar un paciente con tipo de sangre, se persiste en la BD y se ve en el modelo', function () {
    // Crear tipo de sangre
    $bloodType = BloodType::create(['name' => 'A+']);

    // Crear usuario y paciente (sin tipo de sangre)
    $user = User::factory()->create();
    $patient = Patient::create([
        'user_id' => $user->id,
        'blood_type_id' => null,
        'allergies' => null,
        'chronic_diseases' => null,
        'surgery_history' => null,
        'family_history' => null,
        'observations' => null,
        'emergency_contact_name' => null,
        'emergency_contact_phone' => null,
        'emergency_relationship' => null,
    ]);

    expect($patient->blood_type_id)->toBeNull();

    // Simular la petición PUT del formulario de edición
    $response = $this
        ->actingAs($user)
        ->put(route('admin.patients.update', $patient), [
            'blood_type_id' => (string) $bloodType->id,
            'allergies' => null,
            'chronic_diseases' => null,
            'surgery_history' => null,
            'family_history' => null,
            'observations' => null,
            'emergency_contact_name' => null,
            'emergency_contact_phone' => null,
            'emergency_relationship' => null,
        ]);

    $response->assertRedirect(route('admin.patients.edit', $patient));

    // Comprobar directamente en la tabla
    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'blood_type_id' => $bloodType->id,
    ]);

    // Comprobar que al recargar el modelo desde la BD, el tipo de sangre está
    $patient->refresh();
    expect($patient->blood_type_id)->toBe($bloodType->id);

    $patient->load('bloodType');
    expect($patient->bloodType)->not->toBeNull();
    expect($patient->bloodType->name)->toBe('A+');
});
