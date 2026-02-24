<?php
$user = App\Models\User::first();
$original = $user->getAttributes();
$user->name = $original['name'];
$user->email = $original['email'];
$user->id_number = $original['id_number'];
$user->phone = (string)$original['phone'];
$user->address = $original['address'];
echo json_encode($user->getDirty());
