# Firebase To-Do List App

Aplikasi To-Do List modern menggunakan Firebase Realtime Database dengan fitur authentication dan real-time updates.

## 🚀 Fitur

- ✅ **Authentication**: Registrasi dan login menggunakan Firebase Auth
- ✅ **Real-time Updates**: Perubahan data langsung tersinkronisasi
- ✅ **CRUD Operations**: Tambah, ubah, hapus tugas
- ✅ **Filter Tasks**: Filter berdasarkan status (semua, menunggu, selesai)
- ✅ **Statistics**: Statistik real-time jumlah tugas
- ✅ **Responsive Design**: Tampilan optimal di semua perangkat
- ✅ **Modern UI**: Desain modern dengan Bootstrap 5 dan animasi

## 🛠️ Teknologi

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: Firebase Realtime Database
- **Authentication**: Firebase Auth
- **Hosting**: Firebase Hosting
- **Framework CSS**: Bootstrap 5
- **Icons**: Bootstrap Icons

## 📦 Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd firebase-todo-list
```

### 2. Install Dependencies
```bash
npm install
```

### 3. Setup Firebase
1. Buat project baru di [Firebase Console](https://console.firebase.google.com/)
2. Aktifkan Authentication (Email/Password)
3. Aktifkan Realtime Database
4. Salin konfigurasi Firebase ke `firebase-config.js`

### 4. Konfigurasi Firebase
Edit file `firebase-config.js` dengan konfigurasi Firebase Anda:

```javascript
const firebaseConfig = {
  apiKey: "your-api-key",
  authDomain: "your-auth-domain",
  databaseURL: "your-database-url",
  projectId: "your-project-id",
  storageBucket: "your-storage-bucket",
  messagingSenderId: "your-messaging-sender-id",
  appId: "your-app-id",
  measurementId: "your-measurement-id"
};
```

### 5. Setup Database Rules
Deploy database rules untuk keamanan:
```bash
firebase deploy --only database
```

## 🚀 Deployment

### 1. Login ke Firebase
```bash
firebase login
```

### 2. Inisialisasi Project
```bash
firebase init
```
Pilih:
- Hosting
- Realtime Database
- Pilih project Firebase yang sudah dibuat
- Gunakan `public` sebagai folder hosting (atau `.` untuk root)

### 3. Deploy ke Firebase Hosting
```bash
firebase deploy
```

### 4. Akses Aplikasi
Setelah deployment berhasil, aplikasi dapat diakses di:
```
https://your-project-id.web.app
```

## 📱 Penggunaan

1. **Registrasi**: Buat akun baru dengan email dan password
2. **Login**: Masuk dengan akun yang sudah dibuat
3. **Tambah Tugas**: Masukkan nama tugas dan klik "Tambah Tugas"
4. **Kelola Tugas**: 
   - Klik "Selesai" untuk menandai tugas sebagai selesai
   - Klik "Batal" untuk mengembalikan status tugas
   - Klik "Hapus" untuk menghapus tugas
5. **Filter**: Gunakan filter untuk melihat tugas berdasarkan status
6. **Statistik**: Lihat statistik real-time di bagian atas dashboard

## 🔧 Development

### Menjalankan Lokal
```bash
npm start
# atau
firebase serve
```

### Struktur Project
```
firebase-todo-list/
├── index.html              # Halaman utama
├── styles.css              # Styling aplikasi
├── app.js                  # Logic aplikasi utama
├── firebase-config.js      # Konfigurasi Firebase
├── firebase.json           # Konfigurasi Firebase Hosting
├── database.rules.json     # Rules Firebase Database
├── package.json            # Dependencies dan scripts
└── README.md              # Dokumentasi
```

## 🔒 Keamanan

- Database rules memastikan user hanya dapat mengakses data mereka sendiri
- Authentication wajib untuk mengakses aplikasi
- Validasi data di sisi client dan server
- HTTPS enforced untuk semua komunikasi

## 🎨 Kustomisasi

### Mengubah Tema
Edit variabel CSS di `styles.css`:
```css
:root {
    --primary-color: #6f42c1;
    --primary-hover: #5a359a;
    /* ... */
}
```

### Menambah Fitur
1. Edit `app.js` untuk logic baru
2. Update `index.html` untuk UI baru
3. Tambahkan styling di `styles.css`

## 🐛 Troubleshooting

### Error: Firebase not initialized
- Pastikan `firebase-config.js` sudah dikonfigurasi dengan benar
- Periksa console browser untuk error detail

### Error: Permission denied
- Periksa database rules di Firebase Console
- Pastikan user sudah login

### Error: Module not found
- Pastikan menggunakan server lokal (tidak bisa dibuka langsung dari file)
- Gunakan `firebase serve` atau server HTTP lainnya

## 📄 Lisensi

MIT License - Lihat file LICENSE untuk detail lengkap.

## 🤝 Kontribusi

1. Fork repository
2. Buat branch fitur baru
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

## 📞 Support

Jika ada pertanyaan atau masalah, silakan buat issue di repository ini.

---

**Dibuat dengan ❤️ menggunakan Firebase dan Bootstrap** 