<?php
/**
 * Logout Script
 * Destroys session and redirects to homepage
 */

session_start();

// Get user type before destroying session
$user_type = $_SESSION['user_type'] ?? 'user';

// Destroy all session data
session_unset();
session_destroy();

// Redirect based on user type
if ($user_type === 'admin') {
    header('Location: login_admin.php');
} else {
    header('Location: login_user.php');
}
exit;
