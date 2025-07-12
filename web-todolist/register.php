<?php
/**
 * Halaman Registrasi - To-Do List App
 * Form registrasi user baru dengan validasi
 */

// Include file yang diperlukan
require_once 'includes/db.php';
require_once 'includes/session.php';

// Redirect jika sudah login
redirect_if_logged_in();

// Variabel untuk pesan
$message = '';
$message_type = '';

// Proses registrasi ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil dan sanitasi data input
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi input
    $errors = [];
    
    // Validasi username
    if (empty($username)) {
        $errors[] = "Username harus diisi";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username minimal 3 karakter";
    }
    
    // Validasi email
    if (empty($email)) {
        $errors[] = "Email harus diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    // Validasi password
    if (empty($password)) {
        $errors[] = "Password harus diisi";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    }
    
    // Validasi konfirmasi password
    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok";
    }
    
    // Cek apakah username atau email sudah ada
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Username atau email sudah digunakan";
            }
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan saat memvalidasi data";
        }
    }
    
    // Jika tidak ada error, simpan user baru
    if (empty($errors)) {
        try {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user baru ke database
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password]);
            
            $message = "Registrasi berhasil! Silakan login dengan akun Anda.";
            $message_type = "success";
            
            // Redirect ke login setelah 2 detik
            header("refresh:2;url=login.php");
            
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
        }
    }
    
    // Tampilkan error jika ada
    if (!empty($errors)) {
        $message = implode("<br>", $errors);
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - To-Do List App</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="welcome-bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="card shadow-lg">
                        <div class="card-header text-center">
                            <h3 class="mb-0">
                                <i class="bi bi-person-plus me-2"></i>
                                Daftar Akun Baru
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            
                            <!-- Pesan Alert -->
                            <?php if (!empty($message)): ?>
                                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                                    <?php echo $message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Form Registrasi -->
                            <form method="POST" id="registerForm" novalidate>
                                
                                <!-- Username Field -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">
                                        <i class="bi bi-person me-1"></i>
                                        Username
                                    </label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                           placeholder="Masukkan username" required>
                                </div>
                                
                                <!-- Email Field -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope me-1"></i>
                                        Email
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                           placeholder="nama@email.com" required>
                                </div>
                                
                                <!-- Password Field -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="bi bi-lock me-1"></i>
                                        Password
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Minimal 6 karakter" required>
                                </div>
                                
                                <!-- Confirm Password Field -->
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">
                                        <i class="bi bi-lock-fill me-1"></i>
                                        Konfirmasi Password
                                    </label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           placeholder="Ulangi password" required>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-person-plus me-2"></i>
                                        Daftar Sekarang
                                    </button>
                                </div>
                                
                            </form>
                            
                            <!-- Login Link -->
                            <div class="text-center">
                                <p class="mb-0">
                                    Sudah punya akun? 
                                    <a href="login.php" class="text-decoration-none fw-bold">
                                        Masuk di sini
                                    </a>
                                </p>
                            </div>
                            
                        </div>
                    </div>
                    
                    <!-- Back to Welcome -->
                    <div class="text-center mt-3">
                        <a href="welcome.php" class="text-white text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>
                            Kembali ke Beranda
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html> 