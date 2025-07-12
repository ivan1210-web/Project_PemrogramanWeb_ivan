-- ============================================
-- DATABASE SETUP untuk To-Do List App
-- ============================================

-- Buat database baru
CREATE DATABASE IF NOT EXISTS `todo_app` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Gunakan database
USE `todo_app`;

-- ============================================
-- TABEL USERS
-- ============================================

-- Buat tabel users
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABEL TASKS
-- ============================================

-- Buat tabel tasks
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `task_name` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=belum selesai, 1=selesai',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATA CONTOH (SAMPLE DATA)
-- ============================================

-- Insert user demo
INSERT INTO `users` (`username`, `email`, `password`) VALUES
('demo', 'demo@todolist.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- password: demo123
('johndoe', 'john@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- password: demo123
('jane_smith', 'jane@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: demo123

-- Insert contoh tasks untuk user demo (id=1)
INSERT INTO `tasks` (`user_id`, `task_name`, `status`, `created_at`) VALUES
(1, 'Membuat presentasi untuk meeting minggu depan', 0, '2024-01-15 09:00:00'),
(1, 'Belanja kebutuhan sehari-hari di supermarket', 1, '2024-01-15 10:30:00'),
(1, 'Menyelesaikan laporan bulanan departemen', 0, '2024-01-15 11:15:00'),
(1, 'Menghadiri rapat tim di ruang konferensi', 1, '2024-01-15 14:00:00'),
(1, 'Membaca buku pengembangan diri', 0, '2024-01-15 16:45:00'),
(1, 'Olahraga di gym selama 1 jam', 1, '2024-01-15 18:30:00'),
(1, 'Menyiapkan materi pelatihan untuk karyawan baru', 0, '2024-01-16 08:00:00'),
(1, 'Membayar tagihan listrik dan air', 1, '2024-01-16 09:15:00');

-- Insert contoh tasks untuk user John (id=2)
INSERT INTO `tasks` (`user_id`, `task_name`, `status`, `created_at`) VALUES
(2, 'Review code dari tim developer', 0, '2024-01-15 08:30:00'),
(2, 'Meeting dengan klien tentang project baru', 1, '2024-01-15 13:00:00'),
(2, 'Update dokumentasi sistem', 0, '2024-01-15 15:20:00'),
(2, 'Backup database server', 1, '2024-01-15 20:00:00');

-- Insert contoh tasks untuk user Jane (id=3)
INSERT INTO `tasks` (`user_id`, `task_name`, `status`, `created_at`) VALUES
(3, 'Membuat design mockup untuk website baru', 0, '2024-01-15 10:00:00'),
(3, 'Konsultasi dengan klien tentang branding', 1, '2024-01-15 14:30:00'),
(3, 'Finalisasi design logo perusahaan', 0, '2024-01-15 16:00:00');

-- ============================================
-- INDEXES untuk PERFORMA
-- ============================================

-- Index tambahan untuk optimasi query
CREATE INDEX idx_tasks_user_status ON tasks(user_id, status);
CREATE INDEX idx_tasks_created_at ON tasks(created_at);
CREATE INDEX idx_users_email ON users(email);

-- ============================================
-- VIEWS untuk KEMUDAHAN QUERY
-- ============================================

-- View untuk statistik user
CREATE VIEW user_stats AS
SELECT 
    u.id,
    u.username,
    u.email,
    COUNT(t.id) as total_tasks,
    SUM(CASE WHEN t.status = 1 THEN 1 ELSE 0 END) as completed_tasks,
    SUM(CASE WHEN t.status = 0 THEN 1 ELSE 0 END) as pending_tasks,
    u.created_at as user_created_at
FROM users u
LEFT JOIN tasks t ON u.id = t.user_id
GROUP BY u.id, u.username, u.email, u.created_at;

-- View untuk task dengan info user
CREATE VIEW tasks_with_user AS
SELECT 
    t.id,
    t.task_name,
    t.status,
    t.created_at,
    t.updated_at,
    u.username,
    u.email
FROM tasks t
INNER JOIN users u ON t.user_id = u.id;

-- ============================================
-- STORED PROCEDURES (OPSIONAL)
-- ============================================

-- Procedure untuk mendapatkan statistik user
DELIMITER //
CREATE PROCEDURE GetUserStats(IN user_id INT)
BEGIN
    SELECT 
        COUNT(*) as total_tasks,
        SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as completed_tasks,
        SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pending_tasks,
        ROUND((SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as completion_percentage
    FROM tasks 
    WHERE user_id = user_id;
END //
DELIMITER ;

-- ============================================
-- HASIL AKHIR
-- ============================================

-- Tampilkan semua tabel yang dibuat
SHOW TABLES;

-- Tampilkan statistik data
SELECT 'Users' as table_name, COUNT(*) as record_count FROM users
UNION ALL
SELECT 'Tasks' as table_name, COUNT(*) as record_count FROM tasks;

-- Tampilkan contoh data
SELECT 'Sample Users:' as info;
SELECT id, username, email, created_at FROM users;

SELECT 'Sample Tasks:' as info;
SELECT t.id, u.username, t.task_name, 
       CASE WHEN t.status = 1 THEN 'Selesai' ELSE 'Belum' END as status,
       t.created_at 
FROM tasks t 
INNER JOIN users u ON t.user_id = u.id 
ORDER BY t.created_at DESC;

-- ============================================
-- INFORMASI PENTING
-- ============================================

/*
INFORMASI LOGIN DEMO:
- Email: demo@todolist.com
- Password: demo123

- Email: john@email.com  
- Password: demo123

- Email: jane@email.com
- Password: demo123

CATATAN:
1. Semua password sudah di-hash menggunakan password_hash() PHP
2. Database menggunakan utf8mb4 untuk support emoji dan karakter Unicode
3. Tabel tasks memiliki foreign key constraint ke tabel users
4. Semua timestamps menggunakan zona waktu server
5. Index sudah dibuat untuk performa optimal
*/ 