<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Usamos la cualidad para refrescar la base de datos en cada test
uses(RefreshDatabase::class);

test('Un usuario no puede eliminarse a sí mismo', function () {
    
    //1) Creamos un usuario
    $user = User::factory()->create();

    //2) Simulamos que ya inició sesión
    $this->actingAs($user, 'web');

    //3) Simulamos una petición HTTP DELETE al endpoint de eliminación de usuarios
    $response = $this->delete(route('admin.users.destroy', $user));

    //4) Esperamos que el servidor prohíba la acción
    $response->assertStatus(403);

    //5) Verificar que el usuario siga existiendo en DB
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);
});