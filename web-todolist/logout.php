<?php
/**
 * Halaman Logout - To-Do List App
 * Menghancurkan session dan redirect ke welcome page
 */

// Include session management
require_once 'includes/session.php';

// Hancurkan session dan redirect
logout_user();
?> 