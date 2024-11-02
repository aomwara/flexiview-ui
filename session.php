<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_email'])) {
    // If not logged in, redirect to login page
    header("Location: index.php");
    exit;
}
