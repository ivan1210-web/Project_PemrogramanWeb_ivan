<?php
/**
 * Koneksi Database MySQL untuk To-Do List App
 * Konfigurasi untuk XAMPP (localhost)
 */

// Konfigurasi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "todo_app";

try {
    // Membuat koneksi PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set error mode ke exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

/**
 * Function untuk mengamankan input dari user
 * Mencegah SQL injection dan XSS attacks
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Function untuk redirect halaman
 */
function redirect($url) {
    header("Location: $url");
    exit();
}
?> 