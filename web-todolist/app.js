// Firebase variables (using compat SDK)
let app, auth, database, analytics;

// Global variables
let currentUser = null;
let currentFilter = 'all';
let tasksRef = null;
let tasksListener = null;

// DOM elements
const loadingScreen = document.getElementById('loadingScreen');
const welcomeScreen = document.getElementById('welcomeScreen');
const dashboardScreen = document.getElementById('dashboardScreen');
const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));

// Initialize app
document.addEventListener('DOMContentLoaded', function() {
    initializeFirebase();
});

// Initialize Firebase and the application
function initializeFirebase() {
    // Initialize Firebase
    app = firebase.initializeApp(window.firebaseConfig);
    auth = firebase.auth();
    database = firebase.database();
    analytics = firebase.analytics();
    
    // Set up authentication state listener
    auth.onAuthStateChanged((user) => {
        if (user) {
            currentUser = user;
            showDashboard();
            setupTasksListener();
        } else {
            currentUser = null;
            showWelcome();
            cleanupTasksListener();
        }
        hideLoading();
    });
    
    // Set up event listeners
    setupEventListeners();
}

// Set up all event listeners
function setupEventListeners() {
    // Welcome screen buttons
    document.getElementById('loginBtn').addEventListener('click', () => {
        loginModal.show();
    });
    
    document.getElementById('registerBtn').addEventListener('click', () => {
        registerModal.show();
    });
    
    // Login form
    document.getElementById('loginForm').addEventListener('submit', handleLogin);
    
    // Register form
    document.getElementById('registerForm').addEventListener('submit', handleRegister);
    
    // Logout button
    document.getElementById('logoutBtn').addEventListener('click', handleLogout);
    
    // Add task form
    document.getElementById('addTaskForm').addEventListener('submit', handleAddTask);
    
    // Filter buttons
    document.querySelectorAll('[data-filter]').forEach(button => {
        button.addEventListener('click', (e) => {
            const filter = e.target.getAttribute('data-filter');
            setActiveFilter(filter);
        });
    });
}

// Authentication functions
async function handleLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    const alertDiv = document.getElementById('loginAlert');
    
    try {
        showLoading();
        await auth.signInWithEmailAndPassword(email, password);
        loginModal.hide();
        document.getElementById('loginForm').reset();
        hideAlert(alertDiv);
        showMessage('Login berhasil!', 'success');
    } catch (error) {
        hideLoading();
        showAlert(alertDiv, getErrorMessage(error.code), 'danger');
    }
}

async function handleRegister(e) {
    e.preventDefault();
    
    const name = document.getElementById('registerName').value;
    const email = document.getElementById('registerEmail').value;
    const password = document.getElementById('registerPassword').value;
    const confirmPassword = document.getElementById('registerConfirmPassword').value;
    const alertDiv = document.getElementById('registerAlert');
    
    // Validate passwords match
    if (password !== confirmPassword) {
        showAlert(alertDiv, 'Password tidak cocok!', 'danger');
        return;
    }
    
    // Validate password length
    if (password.length < 6) {
        showAlert(alertDiv, 'Password minimal 6 karakter!', 'danger');
        return;
    }
    
    try {
        showLoading();
        const userCredential = await auth.createUserWithEmailAndPassword(email, password);
        
        // Update user profile with display name
        await userCredential.user.updateProfile({
            displayName: name
        });
        
        registerModal.hide();
        document.getElementById('registerForm').reset();
        hideAlert(alertDiv);
        showMessage('Registrasi berhasil!', 'success');
    } catch (error) {
        hideLoading();
        showAlert(alertDiv, getErrorMessage(error.code), 'danger');
    }
}

async function handleLogout() {
    try {
        await auth.signOut();
        showMessage('Logout berhasil!', 'success');
    } catch (error) {
        showMessage('Terjadi kesalahan saat logout', 'danger');
    }
}

// Task management functions
async function handleAddTask(e) {
    e.preventDefault();
    
    const taskName = document.getElementById('taskName').value.trim();
    
    if (!taskName) {
        showMessage('Nama tugas harus diisi!', 'danger');
        return;
    }
    
    try {
        const taskData = {
            name: taskName,
            completed: false,
            createdAt: firebase.database.ServerValue.TIMESTAMP,
            userId: currentUser.uid
        };
        
        await tasksRef.push(taskData);
        document.getElementById('taskName').value = '';
        showMessage('Tugas berhasil ditambahkan!', 'success');
    } catch (error) {
        showMessage('Terjadi kesalahan saat menambah tugas', 'danger');
    }
}

async function toggleTask(taskId, completed) {
    try {
        const taskRef = database.ref(`tasks/${currentUser.uid}/${taskId}`);
        await taskRef.update({
            completed: !completed,
            updatedAt: firebase.database.ServerValue.TIMESTAMP
        });
    } catch (error) {
        showMessage('Terjadi kesalahan saat mengubah status tugas', 'danger');
    }
}

async function deleteTask(taskId) {
    if (!confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
        return;
    }
    
    try {
        const taskRef = database.ref(`tasks/${currentUser.uid}/${taskId}`);
        await taskRef.remove();
        showMessage('Tugas berhasil dihapus!', 'success');
    } catch (error) {
        showMessage('Terjadi kesalahan saat menghapus tugas', 'danger');
    }
}

// Database listener functions
let tasksData = {};

function setupTasksListener() {
    if (!currentUser) return;
    
    tasksRef = database.ref(`tasks/${currentUser.uid}`);
    
    tasksListener = tasksRef.on('value', (snapshot) => {
        tasksData = snapshot.val() || {};
        renderTasks();
        updateStatistics();
    });
}

function cleanupTasksListener() {
    if (tasksListener && tasksRef) {
        tasksRef.off('value', tasksListener);
        tasksListener = null;
        tasksRef = null;
    }
}

// UI rendering functions
function renderTasks() {
    const tasksList = document.getElementById('tasksList');
    const tasks = Object.entries(tasksData);
    
    if (tasks.length === 0) {
        tasksList.innerHTML = `
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p>Belum ada tugas. Tambahkan tugas pertama Anda!</p>
            </div>
        `;
        return;
    }
    
    // Filter tasks
    const filteredTasks = tasks.filter(([id, task]) => {
        if (currentFilter === 'completed') return task.completed;
        if (currentFilter === 'pending') return !task.completed;
        return true; // 'all'
    });
    
    if (filteredTasks.length === 0) {
        tasksList.innerHTML = `
            <div class="empty-state">
                <i class="bi bi-search"></i>
                <p>Tidak ada tugas yang sesuai dengan filter.</p>
            </div>
        `;
        return;
    }
    
    // Sort tasks by creation date (newest first)
    filteredTasks.sort(([,a], [,b]) => {
        const aTime = a.createdAt || 0;
        const bTime = b.createdAt || 0;
        return bTime - aTime;
    });
    
    tasksList.innerHTML = filteredTasks.map(([id, task]) => `
        <div class="task-item ${task.completed ? 'completed' : ''}" data-task-id="${id}">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="task-text">${escapeHtml(task.name)}</div>
                    <div class="task-date">
                        <i class="bi bi-calendar3 me-1"></i>
                        ${formatDate(task.createdAt)}
                    </div>
                </div>
                <div class="task-actions">
                    <button class="btn btn-sm ${task.completed ? 'btn-warning' : 'btn-success'}" 
                            onclick="toggleTask('${id}', ${task.completed})">
                        <i class="bi ${task.completed ? 'bi-arrow-counterclockwise' : 'bi-check-circle'}"></i>
                        ${task.completed ? 'Batal' : 'Selesai'}
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteTask('${id}')">
                        <i class="bi bi-trash"></i>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

function updateStatistics() {
    const tasks = Object.values(tasksData);
    const totalTasks = tasks.length;
    const completedTasks = tasks.filter(task => task.completed).length;
    const pendingTasks = totalTasks - completedTasks;
    
    // Update with animation
    animateCounter('totalTasks', totalTasks);
    animateCounter('completedTasks', completedTasks);
    animateCounter('pendingTasks', pendingTasks);
}

function animateCounter(elementId, newValue) {
    const element = document.getElementById(elementId);
    const currentValue = parseInt(element.textContent) || 0;
    
    if (currentValue !== newValue) {
        element.classList.add('stat-number');
        element.textContent = newValue;
        
        setTimeout(() => {
            element.classList.remove('stat-number');
        }, 500);
    }
}

function setActiveFilter(filter) {
    currentFilter = filter;
    
    // Update active button
    document.querySelectorAll('[data-filter]').forEach(button => {
        button.classList.remove('active');
        if (button.getAttribute('data-filter') === filter) {
            button.classList.add('active');
        }
    });
    
    // Re-render tasks
    renderTasks();
}

// Screen management functions
function showWelcome() {
    loadingScreen.classList.add('d-none');
    welcomeScreen.classList.remove('d-none');
    dashboardScreen.classList.add('d-none');
}

function showDashboard() {
    loadingScreen.classList.add('d-none');
    welcomeScreen.classList.add('d-none');
    dashboardScreen.classList.remove('d-none');
    
    // Update user info
    const displayName = currentUser.displayName || currentUser.email.split('@')[0];
    document.getElementById('userDisplayName').textContent = displayName;
    document.getElementById('userEmail').textContent = currentUser.email;
    document.getElementById('welcomeUserName').textContent = displayName;
}

function showLoading() {
    loadingScreen.classList.remove('d-none');
}

function hideLoading() {
    loadingScreen.classList.add('d-none');
}

// Utility functions
function showAlert(alertDiv, message, type) {
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.classList.remove('d-none');
}

function hideAlert(alertDiv) {
    alertDiv.classList.add('d-none');
}

function showMessage(message, type) {
    // Create message overlay if it doesn't exist
    let overlay = document.querySelector('.message-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'message-overlay';
        document.body.appendChild(overlay);
    }
    
    // Create alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    overlay.appendChild(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

function getErrorMessage(errorCode) {
    switch (errorCode) {
        case 'auth/email-already-in-use':
            return 'Email sudah digunakan!';
        case 'auth/invalid-email':
            return 'Format email tidak valid!';
        case 'auth/weak-password':
            return 'Password terlalu lemah!';
        case 'auth/user-not-found':
            return 'User tidak ditemukan!';
        case 'auth/wrong-password':
            return 'Password salah!';
        case 'auth/too-many-requests':
            return 'Terlalu banyak percobaan. Coba lagi nanti!';
        default:
            return 'Terjadi kesalahan. Silakan coba lagi!';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(timestamp) {
    if (!timestamp) return 'Baru saja';
    
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;
    
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);
    
    if (minutes < 1) return 'Baru saja';
    if (minutes < 60) return `${minutes} menit yang lalu`;
    if (hours < 24) return `${hours} jam yang lalu`;
    if (days < 7) return `${days} hari yang lalu`;
    
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Make functions globally available for onclick handlers
window.toggleTask = toggleTask;
window.deleteTask = deleteTask; 