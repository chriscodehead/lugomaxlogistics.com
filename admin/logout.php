<?php
require_once '../includes/functions.php';

init_session();

// Clear all session data
$_SESSION = [];

// Destroy the session
session_destroy();

if (!isset($_SESSION['admin_logged_in'])) {
 header("Location: login.php");
 exit;
}

if (time() - $_SESSION['login_time'] > 3600) {
 session_destroy();
 header("Location: login.php");
 exit;
}

// Redirect to login page
header("Location: login.php");
exit;
