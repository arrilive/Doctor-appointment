<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('regular authenticated user cannot delete another user', function () {
    // Arrange: create two regular users
    $user = User::factory()->create();
    $targetUser = User::factory()->create();

    // Act: authenticated user attempts to delete another user
    $response = $this
        ->actingAs($user)
        ->delete("/admin/users/{$targetUser->id}");

    // Assert: action is forbidden
    $response->assertStatus(403);

    // Assert: target user still exists
    $this->assertDatabaseHas('users', [
        'id' => $targetUser->id,
    ]);
});
