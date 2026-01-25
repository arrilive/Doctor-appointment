<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot update a user record', function () {
    // Arrange: create a user to be updated
    $user = User::factory()->create();

    // Act: attempt to update the user without authentication
    $response = $this->put(
        "/admin/users/{$user->id}",
        [
            'name' => 'Unauthorized Update',
            'email' => 'unauthorized@example.com',
        ]
    );

    // Assert: user is redirected (authentication required)
    $response->assertRedirect();

    // Assert: user data remains unchanged
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
    ]);
});
