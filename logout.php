<?php
/**
 * Smart Logout - Destroys both admin and member sessions
 * This works because we check which session exists and destroy it
 */

// Try to destroy admin session
session_name('chitfund_admin');
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    session_destroy();
}

// Try to destroy member session
session_name('chitfund_member');
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] === 'member') {
    session_destroy();
}

header("Location: index.php");
exit;
?>