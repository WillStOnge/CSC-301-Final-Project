<?php
include_once "../objects/user.php";

// Test C(reate)
$user = User::create("test", "test@test.com", "123", 123);

$id = $user->user_id;

echo json_encode($user) . PHP_EOL;

// Test U(pdate)
$user->email = "ongewill@gmail.com";
$user->update();

// Test (Read)
$user = User::read($id);

echo json_encode($user) . PHP_EOL;

// Test D(elete)
$user->delete();

// Should fail
$user = User::read($id);
?>