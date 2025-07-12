<?php
/**
 * Session Management untuk To-Do List App
 * Mengatur session login dan proteksi halaman
 */

// Start session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Function untuk mengecek apakah user sudah login
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Function untuk memproteksi halaman yang membutuhkan login
 * Redirect ke login.php jika belum login
 */
function require_login() {
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

/**
 * Function untuk redirect user yang sudah login
 * Biasanya digunakan di halaman login/register
 */
function redirect_if_logged_in() {
    if (is_logged_in()) {
        redirect('dashboard.php');
    }
}

/**
 * Function untuk logout user
 */
function logout_user() {
    session_destroy();
    redirect('welcome.php');
}

/**
 * Function untuk mendapatkan info user yang sedang login
 */
function get_user_info() {
    if (is_logged_in()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email']
        ];
    }
    return null;
}
?> 