<?php
$user = App\Models\User::first();
if (!$user) { echo "No user found\n"; exit; }

$validated = [
    'name' => $user->name,
    'email' => $user->email,
    'id_number' => $user->id_number,
    'phone' => $user->phone,
    'address' => $user->address,
];

// simulate ConvertEmptyStringsToNull
if ($validated['address'] === '') $validated['address'] = null;

$user->name = $validated['name'];
$user->email = $validated['email'];
$user->id_number = $validated['id_number'];
$user->phone = $validated['phone'];
$user->address = $validated['address'] ?? $user->address;

echo "Dirty fields: " . json_encode($user->getDirty()) . "\n";
