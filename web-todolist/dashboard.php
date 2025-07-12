<?php
/**
 * Halaman Dashboard - To-Do List App
 * Halaman utama untuk mengelola tasks
 */

// Include file yang diperlukan
require_once 'includes/db.php';
require_once 'includes/session.php';

// Proteksi halaman - harus login
require_login();

// Ambil info user yang sedang login
$current_user = get_user_info();

// Variabel untuk pesan
$message = '';
$message_type = '';

// Handle AJAX requests untuk update/delete task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        if ($_POST['action'] == 'update_status') {
            // Update status task
            $task_id = (int)$_POST['task_id'];
            $status = (int)$_POST['status'];
            
            $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
            $result = $stmt->execute([$status, $task_id, $current_user['id']]);
            
            echo json_encode(['success' => $result]);
            exit;
            
        } elseif ($_POST['action'] == 'delete_task') {
            // Hapus task
            $task_id = (int)$_POST['task_id'];
            
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
            $result = $stmt->execute([$task_id, $current_user['id']]);
            
            echo json_encode(['success' => $result]);
            exit;
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Handle form tambah task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['action'])) {
    $task_name = sanitize_input($_POST['task_name']);
    
    // Validasi input
    if (empty($task_name)) {
        $message = "Nama task harus diisi";
        $message_type = "danger";
    } elseif (strlen($task_name) < 3) {
        $message = "Nama task minimal 3 karakter";
        $message_type = "danger";
    } else {
        try {
            // Simpan task baru
            $stmt = $pdo->prepare("INSERT INTO tasks (user_id, task_name, status, created_at) VALUES (?, ?, 0, NOW())");
            $stmt->execute([$current_user['id'], $task_name]);
            
            $message = "Task berhasil ditambahkan!";
            $message_type = "success";
            
        } catch(PDOException $e) {
            $message = "Terjadi kesalahan saat menambah task";
            $message_type = "danger";
        }
    }
}

// Ambil semua tasks user
try {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$current_user['id']]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Hitung statistik
    $total_tasks = count($tasks);
    $completed_tasks = count(array_filter($tasks, function($task) { return $task['status'] == 1; }));
    $pending_tasks = $total_tasks - $completed_tasks;
    
} catch(PDOException $e) {
    $tasks = [];
    $total_tasks = $completed_tasks = $pending_tasks = 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - To-Do List App</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body style="background: #f8f9fa;">
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-check-circle-fill me-2"></i>
                To-Do List App
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        <?php echo htmlspecialchars($current_user['username']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <span class="dropdown-item-text">
                                <small class="text-muted"><?php echo htmlspecialchars($current_user['email']); ?></small>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        
        <!-- Welcome Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white p-4">
                        <h2 class="mb-2">
                            <i class="bi bi-sun me-2"></i>
                            Selamat datang, <?php echo htmlspecialchars($current_user['username']); ?>!
                        </h2>
                        <p class="mb-0 opacity-75">Mari kelola tugas harian Anda dengan mudah dan efisien</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-list-task text-primary mb-2" style="font-size: 2rem;"></i>
                        <h3 class="fw-bold text-primary" id="total-count"><?php echo $total_tasks; ?></h3>
                        <p class="mb-0 text-muted">Total Tugas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle-fill text-success mb-2" style="font-size: 2rem;"></i>
                        <h3 class="fw-bold text-success" id="completed-count"><?php echo $completed_tasks; ?></h3>
                        <p class="mb-0 text-muted">Selesai</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-clock text-warning mb-2" style="font-size: 2rem;"></i>
                        <h3 class="fw-bold text-warning" id="pending-count"><?php echo $pending_tasks; ?></h3>
                        <p class="mb-0 text-muted">Menunggu</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Form Tambah Task -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-plus-circle me-2"></i>
                            Tambah Tugas Baru
                        </h5>
                    </div>
                    <div class="card-body">
                        
                        <!-- Pesan Alert -->
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="addTaskForm">
                            <div class="mb-3">
                                <label for="task_name" class="form-label">Nama Tugas</label>
                                <textarea class="form-control" id="task_name" name="task_name" rows="3" 
                                          placeholder="Masukkan nama tugas..." required></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-lg me-2"></i>
                                    Tambah Tugas
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Daftar Tasks -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Daftar Tugas
                        </h5>
                        
                        <!-- Filter Buttons -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm active filter-btn" data-filter="all">
                                Semua
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm filter-btn" data-filter="completed">
                                Selesai
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm filter-btn" data-filter="pending">
                                Menunggu
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <?php if (empty($tasks)): ?>
                            <!-- Empty State -->
                            <div class="text-center py-5">
                                <i class="bi bi-inbox text-muted mb-3" style="font-size: 4rem;"></i>
                                <h5 class="text-muted">Belum ada tugas</h5>
                                <p class="text-muted">Tambahkan tugas pertama Anda untuk memulai!</p>
                            </div>
                        <?php else: ?>
                            <!-- Tasks List -->
                            <div class="tasks-container">
                                <?php foreach ($tasks as $task): ?>
                                    <div class="task-item <?php echo $task['status'] == 1 ? 'completed' : ''; ?>">
                                        <div class="d-flex align-items-start">
                                            <div class="form-check me-3">
                                                <input class="form-check-input task-checkbox" type="checkbox" 
                                                       data-task-id="<?php echo $task['id']; ?>"
                                                       <?php echo $task['status'] == 1 ? 'checked' : ''; ?>>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="task-text">
                                                    <?php echo htmlspecialchars($task['task_name']); ?>
                                                </div>
                                                <div class="task-date">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    <?php echo date('d M Y, H:i', strtotime($task['created_at'])); ?>
                                                </div>
                                            </div>
                                            <div class="task-actions">
                                                <button class="btn btn-outline-danger btn-sm delete-task" 
                                                        data-task-id="<?php echo $task['id']; ?>"
                                                        title="Hapus tugas">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <p class="mb-0">
                &copy; <?php echo date('Y'); ?> To-Do List App. 
            </p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html> 