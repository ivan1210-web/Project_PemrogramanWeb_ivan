<?php
/**
 * Halaman Welcome - To-Do List App
 * Halaman selamat datang dengan desain modern
 */

// Include session management
require_once 'includes/session.php';

// Redirect jika sudah login
redirect_if_logged_in();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - To-Do List App</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Welcome Section -->
    <div class="welcome-bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="welcome-section">
                        <!-- Logo/Icon -->
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill" style="font-size: 4rem; color: #fff;"></i>
                        </div>
                        
                        <!-- Welcome Text -->
                        <h1 class="display-3 fw-bold mb-4">
                            To-Do List App
                        </h1>
                        
                        <p class="lead mb-5">
                            Kelola tugas harian Anda dengan mudah dan efisien. 
                            Aplikasi to-do list modern yang membantu Anda tetap produktif 
                            dan terorganisir setiap hari.
                        </p>
                        
                        <!-- Feature Highlights -->
                        <div class="row mb-5">
                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <i class="bi bi-plus-circle-fill mb-2" style="font-size: 2rem;"></i>
                                    <h5>Tambah Tugas</h5>
                                    <p class="small">Buat tugas baru dengan cepat dan mudah</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <i class="bi bi-check-square-fill mb-2" style="font-size: 2rem;"></i>
                                    <h5>Kelola Status</h5>
                                    <p class="small">Tandai tugas sebagai selesai atau belum</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <i class="bi bi-funnel-fill mb-2" style="font-size: 2rem;"></i>
                                    <h5>Filter Tugas</h5>
                                    <p class="small">Lihat tugas berdasarkan status penyelesaian</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                            <a href="login.php" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Masuk
                            </a>
                            <a href="register.php" class="btn btn-outline-light btn-lg px-5">
                                <i class="bi bi-person-plus me-2"></i>
                                Daftar Sekarang
                            </a>
                        </div>
                        
                        <!-- Additional Info -->
                        <div class="mt-5">
                            <small class="opacity-75">
                                <i class="bi bi-shield-check me-1"></i>
                                Aman & Terpercaya | 
                                <i class="bi bi-device-desktop me-1"></i>
                                Responsif di Semua Device |
                                <i class="bi bi-lightning-charge me-1"></i>
                                Cepat & Mudah Digunakan
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container-fluid py-5" style="background: rgba(0,0,0,0.2);">
        <div class="container">
            <div class="row text-center text-white">
                <div class="col-12 mb-4">
                    <h2 class="fw-bold" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Mengapa Memilih To-Do List App?</h2>
                    <p class="lead" style="color: #f8f9fa; text-shadow: 1px 1px 2px rgba(0,0,0,0.4);">Fitur-fitur canggih untuk produktivitas maksimal</p>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="p-4">
                        <i class="bi bi-lock-fill mb-3" style="font-size: 3rem; color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.4);"></i>
                        <h5 style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.4);">Keamanan Terjamin</h5>
                        <p style="color: #f8f9fa; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Data Anda aman dengan enkripsi password dan session management yang tepat</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="p-4">
                        <i class="bi bi-phone-fill mb-3" style="font-size: 3rem; color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.4);"></i>
                        <h5 style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.4);">Responsive Design</h5>
                        <p style="color: #f8f9fa; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Akses dari desktop, tablet, atau smartphone dengan tampilan yang optimal</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="p-4">
                        <i class="bi bi-speedometer2 mb-3" style="font-size: 3rem; color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.4);"></i>
                        <h5 style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.4);">Performa Cepat</h5>
                        <p style="color: #f8f9fa; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Dibuat dengan teknologi modern untuk performa yang cepat dan stabil</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="p-4">
                        <i class="bi bi-person-circle mb-3" style="font-size: 3rem; color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.4);"></i>
                        <h5 style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.4);">User Friendly</h5>
                        <p style="color: #f8f9fa; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Interface yang intuitif dan mudah digunakan untuk semua kalangan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="mb-0">
                &copy; <?php echo date('Y'); ?> To-Do List App. 
                Dibuat dengan <i class="bi bi-heart-fill text-danger"></i> menggunakan PHP & Bootstrap
            </p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html> 