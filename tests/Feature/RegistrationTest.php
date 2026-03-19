<?php
/** @var Tests\TestCase $this */

use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
})->skip(function () {
    return ! Features::enabled(Features::registration());
}, 'Registration support is not enabled.');

test('registration screen cannot be rendered if support is disabled', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
})->skip(function () {
    return Features::enabled(Features::registration());
}, 'Registration support is enabled.');

test('new users can register', function () {
    $this->app->bind(\Laravel\Fortify\Contracts\CreatesNewUsers::class, function () {
        return new class implements \Laravel\Fortify\Contracts\CreatesNewUsers {
            public function create(array $input) {
                return \App\Models\User::create([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'password' => \Illuminate\Support\Facades\Hash::make($input['password']),
                    'id_number' => $input['id_number'] ?? '123456789',
                    'phone' => $input['phone'] ?? '1234567890',
                    'address' => $input['address'] ?? 'Test Address',
                ]);
            }
        };
    });

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'id_number' => '123456789',
        'phone' => '1234567890',
        'address' => 'Test Address',
        'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
    ]);

    $response->assertRedirect('/');
})->skip(function () {
    return ! Features::enabled(Features::registration());
}, 'Registration support is not enabled.');
