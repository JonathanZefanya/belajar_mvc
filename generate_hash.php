<?php
// Generate password hash untuk "password"
$password = 'password';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Hash: $hash\n";
echo "\n";
echo "Verification: " . (password_verify($password, $hash) ? "SUCCESS" : "FAILED");
?>
