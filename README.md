# IPRHub - Sistem Verifikasi Paten dan Hak Cipta

Sistem berbasis web untuk pengelolaan, pengajuan, dan verifikasi dokumen paten serta hak cipta secara digital.

---

## Tujuan Sistem
- Mempermudah proses pengajuan paten dan hak cipta
- Mengurangi kesalahan administrasi dalam verifikasi dokumen
- Meningkatkan efisiensi dan transparansi proses validasi

---

## Fitur Utama
- Generate dokumen otomatis  
- Upload file/berkas  
- Verifikasi dokumen oleh Admin  
- Revisi berkas  
- Monitoring status pengajuan  

---

## Cara Menjalankan Project

### 1. Clone Repository
```bash
git clone https://github.com/Website-IPRHub/Website-Hak-Paten-dan-Hak-Cipta.git
cd Website-Hak-Paten-dan-Hak-Cipta
```

### 2. Jalankan XAMPP
- Aktifkan Apache
- Aktifkan MySQL

### 3. Jalankan Backend
```bash
php artisan serve
```

### 4. Jalankan Frontend
```bash
npm install
npm run dev
```

### 5. Akses Aplikasi
```
http://localhost:8000
```

---

## Konfigurasi Email
Sistem menggunakan SMTP Gmail untuk pengiriman email.

Langkah:
1. Aktifkan 2-Step Verification di akun Google  
2. Generate App Password  
3. Masukkan ke file `.env`  

Contoh konfigurasi:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="IPRHub"
```

---

## Teknologi yang Digunakan
- PHP (Laravel / Blade)  
- JavaScript  
- CSS  
- MySQL  
- LibreOffice (untuk generate dokumen)  

---

## Struktur Project
```
IPRHub/
│── app/
│── public/
│── resources/
│── routes/
│── database/
│── README.md
```

---

## Alur Sistem
1. Pengguna input data  
2. Sistem mengenerate dokumen  
3. Pengguna upload dokumen  
4. Sistem menyimpan dan mengecek berkas  
5. Admin melakukan verifikasi  
6. Jika terdapat kesalahan, pengguna melakukan revisi  
7. Dokumen disetujui  

---

## Author
- Aisha  
- Athiqotuz Zulaiva  

---

IPRHub - Sistem Verifikasi Paten dan Hak Cipta
