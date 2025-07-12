/**
 * JavaScript untuk To-Do List App
 * Validasi form dan interaksi dinamis
 */

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi form validation
    initFormValidation();
    
    // Inisialisasi task interactions
    initTaskInteractions();
    
    // Inisialisasi filter functionality
    initFilterFunctionality();
    
    // Add fade-in animation
    document.body.classList.add('fade-in');
});

/**
 * Validasi Form (Register & Login)
 */
function initFormValidation() {
    // Validasi form register
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            if (!validateRegisterForm()) {
                e.preventDefault();
            }
        });
        
        // Real-time validation untuk password
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        
        if (password && confirmPassword) {
            confirmPassword.addEventListener('input', function() {
                validatePasswordMatch();
            });
        }
    }
    
    // Validasi form login
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (!validateLoginForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Validasi form add task
    const addTaskForm = document.getElementById('addTaskForm');
    if (addTaskForm) {
        addTaskForm.addEventListener('submit', function(e) {
            if (!validateTaskForm()) {
                e.preventDefault();
            }
        });
    }
}

/**
 * Validasi form registrasi
 */
function validateRegisterForm() {
    let isValid = true;
    
    // Validate username
    const username = document.getElementById('username');
    if (username.value.trim().length < 3) {
        showFieldError(username, 'Username minimal 3 karakter');
        isValid = false;
    } else {
        clearFieldError(username);
    }
    
    // Validate email
    const email = document.getElementById('email');
    if (!isValidEmail(email.value)) {
        showFieldError(email, 'Format email tidak valid');
        isValid = false;
    } else {
        clearFieldError(email);
    }
    
    // Validate password
    const password = document.getElementById('password');
    if (password.value.length < 6) {
        showFieldError(password, 'Password minimal 6 karakter');
        isValid = false;
    } else {
        clearFieldError(password);
    }
    
    // Validate confirm password
    const confirmPassword = document.getElementById('confirm_password');
    if (password.value !== confirmPassword.value) {
        showFieldError(confirmPassword, 'Konfirmasi password tidak cocok');
        isValid = false;
    } else {
        clearFieldError(confirmPassword);
    }
    
    return isValid;
}

/**
 * Validasi form login
 */
function validateLoginForm() {
    let isValid = true;
    
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    
    if (!isValidEmail(email.value)) {
        showFieldError(email, 'Format email tidak valid');
        isValid = false;
    } else {
        clearFieldError(email);
    }
    
    if (password.value.trim() === '') {
        showFieldError(password, 'Password harus diisi');
        isValid = false;
    } else {
        clearFieldError(password);
    }
    
    return isValid;
}

/**
 * Validasi form tambah task
 */
function validateTaskForm() {
    const taskName = document.getElementById('task_name');
    
    if (taskName.value.trim() === '') {
        showFieldError(taskName, 'Nama task harus diisi');
        return false;
    } else if (taskName.value.trim().length < 3) {
        showFieldError(taskName, 'Nama task minimal 3 karakter');
        return false;
    } else {
        clearFieldError(taskName);
        return true;
    }
}

/**
 * Validasi real-time untuk password match
 */
function validatePasswordMatch() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    if (confirmPassword.value !== '' && password.value !== confirmPassword.value) {
        showFieldError(confirmPassword, 'Konfirmasi password tidak cocok');
    } else {
        clearFieldError(confirmPassword);
    }
}

/**
 * Cek apakah email valid
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Tampilkan error pada field
 */
function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('is-invalid');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

/**
 * Hapus error pada field
 */
function clearFieldError(field) {
    field.classList.remove('is-invalid');
    
    const errorDiv = field.parentNode.querySelector('.invalid-feedback');
    if (errorDiv) {
        errorDiv.remove();
    }
}

/**
 * Inisialisasi interaksi task
 */
function initTaskInteractions() {
    // Handle checkbox untuk mark task as completed
    const taskCheckboxes = document.querySelectorAll('.task-checkbox');
    taskCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateTaskStatus(this.dataset.taskId, this.checked);
        });
    });
    
    // Handle delete task buttons
    const deleteButtons = document.querySelectorAll('.delete-task');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Apakah Anda yakin ingin menghapus task ini?')) {
                deleteTask(this.dataset.taskId);
            }
        });
    });
}

/**
 * Update status task (completed/not completed)
 */
function updateTaskStatus(taskId, isCompleted) {
    // Show loading
    const taskItem = document.querySelector(`[data-task-id="${taskId}"]`).closest('.task-item');
    const originalContent = taskItem.innerHTML;
    
    // Create FormData
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('task_id', taskId);
    formData.append('status', isCompleted ? 1 : 0);
    
    // Send AJAX request
    fetch('dashboard.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            if (isCompleted) {
                taskItem.classList.add('completed');
            } else {
                taskItem.classList.remove('completed');
            }
            
            // Update statistik real-time
            updateStatistics();
            
            // Show success message
            showNotification('Status task berhasil diupdate', 'success');
        } else {
            // Revert checkbox state
            const checkbox = taskItem.querySelector('.task-checkbox');
            checkbox.checked = !isCompleted;
            
            showNotification('Gagal mengupdate status task', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert checkbox state
        const checkbox = taskItem.querySelector('.task-checkbox');
        checkbox.checked = !isCompleted;
        
        showNotification('Terjadi kesalahan', 'error');
    });
}

/**
 * Hapus task
 */
function deleteTask(taskId) {
    const taskItem = document.querySelector(`[data-task-id="${taskId}"]`).closest('.task-item');
    
    // Create FormData
    const formData = new FormData();
    formData.append('action', 'delete_task');
    formData.append('task_id', taskId);
    
    // Send AJAX request
    fetch('dashboard.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove task from UI with animation
            taskItem.style.transition = 'all 0.3s ease';
            taskItem.style.transform = 'translateX(-100%)';
            taskItem.style.opacity = '0';
            
            setTimeout(() => {
                taskItem.remove();
                // Update statistik setelah task dihapus dari DOM
                updateStatistics();
            }, 300);
            
            showNotification('Task berhasil dihapus', 'success');
        } else {
            showNotification('Gagal menghapus task', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan', 'error');
    });
}

/**
 * Update statistik real-time
 */
function updateStatistics() {
    // Hitung ulang berdasarkan task yang ada di DOM
    const allTasks = document.querySelectorAll('.task-item');
    const completedTasks = document.querySelectorAll('.task-item.completed');
    
    const totalCount = allTasks.length;
    const completedCount = completedTasks.length;
    const pendingCount = totalCount - completedCount;
    
    // Update tampilan statistik menggunakan ID yang spesifik
    const totalElement = document.getElementById('total-count');
    const completedElement = document.getElementById('completed-count');
    const pendingElement = document.getElementById('pending-count');
    
    // Update nilai dengan animasi
    if (totalElement) {
        updateCounterWithAnimation(totalElement, totalCount);
    }
    
    if (completedElement) {
        updateCounterWithAnimation(completedElement, completedCount);
    }
    
    if (pendingElement) {
        updateCounterWithAnimation(pendingElement, pendingCount);
    }
    
    // Debug log untuk memastikan update berjalan
    console.log(`Statistik diupdate: Total=${totalCount}, Selesai=${completedCount}, Menunggu=${pendingCount}`);
}

/**
 * Update counter dengan animasi
 */
function updateCounterWithAnimation(element, newValue) {
    if (!element) return;
    
    // Animasi scale
    element.style.transform = 'scale(1.15)';
    element.style.transition = 'all 0.3s ease';
    
    // Ubah nilai
    element.textContent = newValue;
    
    // Highlight warna
    const originalColor = element.style.color;
    element.style.color = '#28a745';
    
    // Kembalikan ke normal
    setTimeout(() => {
        element.style.transform = 'scale(1)';
        element.style.color = originalColor;
    }, 300);
}

/**
 * Inisialisasi filter functionality
 */
function initFilterFunctionality() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter tasks
            filterTasks(filter);
        });
    });
}

/**
 * Filter tasks berdasarkan status
 */
function filterTasks(filter) {
    const taskItems = document.querySelectorAll('.task-item');
    
    taskItems.forEach(item => {
        const isCompleted = item.classList.contains('completed');
        
        switch(filter) {
            case 'all':
                item.style.display = 'block';
                break;
            case 'completed':
                item.style.display = isCompleted ? 'block' : 'none';
                break;
            case 'pending':
                item.style.display = !isCompleted ? 'block' : 'none';
                break;
        }
    });
}

/**
 * Tampilkan notifikasi
 */
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

/**
 * Utility function untuk loading state
 */
function showLoading(button) {
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="loading"></span> Loading...';
    button.disabled = true;
    
    return function() {
        button.innerHTML = originalText;
        button.disabled = false;
    };
}

/**
 * Auto-hide alerts after 5 seconds
 */
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.parentNode) {
                alert.style.transition = 'all 0.3s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }
        }, 5000);
    });
}); 