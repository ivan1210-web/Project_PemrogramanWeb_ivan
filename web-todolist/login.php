<?php
/**
 * Halaman Login - To-Do List App
 * Form login dengan validasi dan session management
 */

// Include file yang diperlukan
require_once 'includes/db.php';
require_once 'includes/session.php';

// Redirect jika sudah login
redirect_if_logged_in();

// Variabel untuk pesan
$message = '';
$message_type = '';

// Proses login ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil dan sanitasi data input
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    
    // Validasi input
    $errors = [];
    
    // Validasi email
    if (empty($email)) {
        $errors[] = "Email harus diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    // Validasi password
    if (empty($password)) {
        $errors[] = "Password harus diisi";
    }
    
    // Jika tidak ada error, cek kredensial
    if (empty($errors)) {
        try {
            // Cari user berdasarkan email
            $stmt = $pdo->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verifikasi password
            if ($user && password_verify($password, $user['password'])) {
                // Login berhasil - buat session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                // Redirect ke dashboard
                redirect('dashboard.php');
                
            } else {
                $errors[] = "Email atau password salah";
            }
            
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan saat login. Silakan coba lagi.";
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
    <title>Masuk - To-Do List App</title>
    
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
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Masuk ke Akun
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
                            
                            <!-- Form Login -->
                            <form method="POST" id="loginForm" novalidate>
                                
                                <!-- Email Field -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope me-1"></i>
                                        Email
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                           placeholder="nama@email.com" required autofocus>
                                </div>
                                
                                <!-- Password Field -->
                                <div class="mb-4">
                                    <label for="password" class="form-label">
                                        <i class="bi bi-lock me-1"></i>
                                        Password
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Masukkan password" required>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        Masuk
                                    </button>
                                </div>
                                
                            </form>
                            
                            <!-- Register Link -->
                            <div class="text-center">
                                <p class="mb-0">
                                    Belum punya akun? 
                                    <a href="register.php" class="text-decoration-none fw-bold">
                                        Daftar di sini
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

    <!-- Demo Credentials Info -->
    <div class="position-fixed bottom-0 start-0 m-3">
        <div class="card bg-dark text-white" style="max-width: 300px; opacity: 0.8;">
            <div class="card-body p-3">
                <h6 class="card-title mb-2">
                    <i class="bi bi-info-circle me-1"></i>
                    Demo Account
                </h6>
                <small>
                    Email: demo@todolist.com<br>
                    Password: demo123<br>
                    <em class="text-muted">Atau buat akun baru</em>
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html> 