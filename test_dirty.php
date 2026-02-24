<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
echo "Original phone: "; var_dump($user->phone);

$user->name = $user->name;
$user->email = $user->email;
$user->id_number = $user->id_number;
$user->phone = $user->phone;
$user->address = $user->address;

echo "Is dirty? " . ($user->isDirty() ? 'yes' : 'no') . "\n";
if ($user->isDirty()) {
    print_r($user->getDirty());
}
