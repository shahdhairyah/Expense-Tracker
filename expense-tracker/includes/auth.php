<?php
// includes/auth.php

// FIX 1: Check if session is already active before starting to avoid warnings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// FIX 2: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    
    // FIX 3: Smart Redirect
    // If we are in the admin folder, redirect to admin login logic (or root login)
    // For this app, we redirect to the root login.php
    if (basename(dirname($_SERVER['PHP_SELF'])) == 'admin') {
        header("Location: ../login.php");
    } else {
        header("Location: login.php");
    }
    exit;
}
?>