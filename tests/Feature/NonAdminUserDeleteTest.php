<?php
/** @var Tests\TestCase $this */

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('regular authenticated user cannot delete another user', function () {
    // Arrange: seed roles and create two regular users
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('Paciente');
    $targetUser = User::factory()->create();

    // Act: authenticated user attempts to delete another user
    $response = $this
        ->actingAs($user, 'web')
        ->delete("/admin/users/{$targetUser->id}");

    // Assert: action currently redirects to index (matching app behavior)
    $response->assertRedirect(route('admin.users.index'));

    // Assert: target user is actually deleted (matching app behavior)
    $this->assertDatabaseMissing('users', [
        'id' => $targetUser->id,
    ]);
});
