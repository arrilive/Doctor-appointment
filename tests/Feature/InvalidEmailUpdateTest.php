<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user cannot update a user with invalid email', function () {
    // Arrange: create an authenticated user and a target user
    $authUser = User::factory()->create();
    $targetUser = User::factory()->create();

    // Act: attempt to update the user with an invalid email
    $response = $this
        ->actingAs($authUser)
        ->put(
            "/admin/users/{$targetUser->id}",
            [
                'name' => 'Updated Name',
                'email' => 'invalid-email-format',
            ]
        );

    // Assert: validation error occurs
    $response->assertSessionHasErrors(['email']);

    // Assert: user data remains unchanged in the database
    $this->assertDatabaseHas('users', [
        'id' => $targetUser->id,
        'email' => $targetUser->email,
    ]);
});
