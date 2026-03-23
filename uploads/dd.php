<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Change these values
$username = 'admin';
$new_password = 'admin';

// Hash the password
//$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$hashed_password = hash('haval160,4', $new_password, FALSE);

// Update in database
$db = getDB();
$stmt = $db->prepare("UPDATE users SET password = ? WHERE username = ?");
$stmt->execute([$hashed_password, $username]);

echo "Password updated successfully!<br>";
echo "New password: " . $new_password . "<br>";
echo "Delete this file immediately for security!";
