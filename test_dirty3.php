<?php
$user = App\Models\User::first();
$user->address = $user->address ?? 'N/A';
echo "is dirty: " . ($user->isDirty() ? 'yes' : 'no') . "\n";
print_r($user->getDirty());
