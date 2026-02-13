-- Buat database
CREATE DATABASE IF NOT EXISTS hospital_db;
USE hospital_db;

-- Tabel Admin
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pasien (User)
CREATE TABLE pasien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(20) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
    alamat TEXT NOT NULL,
    no_telepon VARCHAR(15) NOT NULL,
    email VARCHAR(100),
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pengumuman
CREATE TABLE pengumuman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    isi TEXT NOT NULL,
    tanggal DATE NOT NULL,
    admin_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE CASCADE
);

-- Tabel Jadwal Konsultasi
CREATE TABLE jadwal_konsultasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pasien_id INT NOT NULL,
    dokter VARCHAR(100) NOT NULL,
    poli VARCHAR(50) NOT NULL,
    tanggal DATE NOT NULL,
    jam TIME NOT NULL,
    keluhan TEXT,
    status ENUM('Terjadwal', 'Selesai', 'Dibatalkan') DEFAULT 'Terjadwal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pasien_id) REFERENCES pasien(id) ON DELETE CASCADE
);

-- Insert data admin default (password: admin123)
INSERT INTO admin (username, password, nama_lengkap, email) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Utama', 'admin@rs.com');

-- Insert data pasien contoh (password: pasien123)
INSERT INTO pasien (nik, nama_lengkap, tanggal_lahir, jenis_kelamin, alamat, no_telepon, email, username, password) 
VALUES 
('3273010101010001', 'Budi Santoso', '1990-05-15', 'Laki-laki', 'Jl. Merdeka No. 123, Jakarta', '081234567890', 'budi@email.com', 'budi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('3273010202020002', 'Siti Aisyah', '1985-08-22', 'Perempuan', 'Jl. Sudirman No. 45, Bandung', '081298765432', 'siti@email.com', 'siti', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert data pengumuman contoh
INSERT INTO pengumuman (judul, isi, tanggal, admin_id) 
VALUES 
('Peningkatan Pelayanan Kesehatan', 'Mulai tanggal 1 Januari 2024, rumah sakit akan menambah jam layanan hingga pukul 21.00 untuk meningkatkan akses pasien.', '2024-01-10', 1),
('Libur Nasional', 'Berdasarkan kalender nasional, rumah sakit akan tutup pada tanggal 17 Agustus 2024 untuk memperingati HUT RI ke-79.', '2024-01-05', 1);

-- Insert data jadwal konsultasi contoh
INSERT INTO jadwal_konsultasi (pasien_id, dokter, poli, tanggal, jam, keluhan, status) 
VALUES 
(1, 'dr. Rina Wijaya', 'Poli Umum', '2024-01-20', '09:00:00', 'Demam dan batuk sudah 3 hari', 'Terjadwal'),
(2, 'dr. Ahmad Fauzi', 'Poli Gigi', '2024-01-22', '10:30:00', 'Sakit gigi geraham', 'Terjadwal');