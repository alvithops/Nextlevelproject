-- Create database
CREATE DATABASE IF NOT EXISTS hospital_system;

USE hospital_system;

-- Drop existing tables if they exist (for clean reinstall)
DROP TABLE IF EXISTS consultations;

DROP TABLE IF EXISTS announcements;

DROP TABLE IF EXISTS patients;

DROP TABLE IF EXISTS users;

DROP TABLE IF EXISTS admins;

-- Table: admins
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: users (for patient authentication)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    user_type ENUM('patient', 'admin') DEFAULT 'patient',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: patients (patient information)
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    patient_id VARCHAR(20) UNIQUE NOT NULL,
    nik VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100),
    blood_type ENUM(
        'A',
        'B',
        'AB',
        'O',
        'A+',
        'B+',
        'AB+',
        'O+',
        'A-',
        'B-',
        'AB-',
        'O-'
    ),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

-- Table: announcements
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    announcement_date DATE NOT NULL,
    admin_id INT NOT NULL,
    is_important TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins (id) ON DELETE CASCADE
);

-- Table: consultations (jadwal konsultasi)
CREATE TABLE consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_name VARCHAR(100) NOT NULL,
    department VARCHAR(50) NOT NULL,
    consultation_date DATE NOT NULL,
    consultation_time TIME NOT NULL,
    complaint TEXT,
    diagnosis TEXT,
    prescription TEXT,
    status ENUM(
        'Scheduled',
        'Completed',
        'Cancelled'
    ) DEFAULT 'Scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients (id) ON DELETE CASCADE
);

-- Insert default admin user
-- Username: admin
-- Password: admin123 (hashed with bcrypt)
INSERT INTO
    admins (
        username,
        password,
        full_name,
        email
    )
VALUES (
        'admin',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'Administrator',
        'admin@hospital.com'
    ),
    (
        'superadmin',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'Super Administrator',
        'superadmin@hospital.com'
    );

-- Insert sample users
-- Password: patient123 (hashed with bcrypt)
INSERT INTO
    users (
        username,
        password,
        email,
        user_type
    )
VALUES (
        'john.doe',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'john.doe@email.com',
        'patient'
    ),
    (
        'jane.smith',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'jane.smith@email.com',
        'patient'
    ),
    (
        'bob.wilson',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'bob.wilson@email.com',
        'patient'
    );

-- Insert sample patients
INSERT INTO
    patients (
        user_id,
        patient_id,
        nik,
        full_name,
        date_of_birth,
        gender,
        address,
        phone,
        email,
        blood_type
    )
VALUES (
        1,
        'P001',
        '3273010101010001',
        'John Doe',
        '1990-05-15',
        'Male',
        '123 Main Street, Jakarta',
        '081234567890',
        'john.doe@email.com',
        'A+'
    ),
    (
        2,
        'P002',
        '3273020202020002',
        'Jane Smith',
        '1985-08-22',
        'Female',
        '45 Sudirman Avenue, Bandung',
        '081298765432',
        'jane.smith@email.com',
        'B+'
    ),
    (
        3,
        'P003',
        '3273030303030003',
        'Bob Wilson',
        '1992-03-10',
        'Male',
        '78 Gatot Subroto Street, Surabaya',
        '081345678901',
        'bob.wilson@email.com',
        'O+'
    );

-- Insert sample announcements
INSERT INTO
    announcements (
        title,
        content,
        announcement_date,
        admin_id,
        is_important
    )
VALUES (
        'Extended Operating Hours',
        'Starting from January 1, 2024, the hospital will extend its operating hours until 9:00 PM to improve patient access to healthcare services.',
        '2024-01-10',
        1,
        1
    ),
    (
        'National Holiday Notice',
        'The hospital will be closed on August 17, 2024 in observance of Indonesia Independence Day. Emergency services will remain available 24/7.',
        '2024-01-05',
        1,
        0
    ),
    (
        'New COVID-19 Testing Facility',
        'We are pleased to announce the opening of our new COVID-19 testing facility. Walk-in appointments are welcome from Monday to Saturday, 8:00 AM - 4:00 PM.',
        '2024-01-15',
        2,
        1
    ),
    (
        'Health Insurance Update',
        'We now accept additional health insurance providers. Please contact our billing department for more information.',
        '2024-01-20',
        1,
        0
    );

-- Insert sample consultations
INSERT INTO
    consultations (
        patient_id,
        doctor_name,
        department,
        consultation_date,
        consultation_time,
        complaint,
        diagnosis,
        prescription,
        status,
        notes
    )
VALUES (
        1,
        'Dr. Sarah Johnson',
        'General Practice',
        '2024-02-20',
        '09:00:00',
        'Fever and cough for 3 days',
        'Upper respiratory tract infection',
        'Paracetamol 500mg, Amoxicillin 500mg',
        'Scheduled',
        'Patient advised to rest and drink plenty of fluids'
    ),
    (
        2,
        'Dr. Michael Chen',
        'Dentistry',
        '2024-02-22',
        '10:30:00',
        'Toothache on lower right molar',
        'Dental cavity',
        'Ibuprofen 400mg, Dental filling scheduled',
        'Scheduled',
        'Follow-up appointment in 1 week'
    ),
    (
        3,
        'Dr. Emily Davis',
        'Cardiology',
        '2024-02-25',
        '14:00:00',
        'Chest pain and shortness of breath',
        'Angina pectoris',
        'Aspirin 100mg, Nitroglycerin as needed',
        'Scheduled',
        'ECG and stress test recommended'
    ),
    (
        1,
        'Dr. Sarah Johnson',
        'General Practice',
        '2024-01-15',
        '09:00:00',
        'Annual check-up',
        'Healthy',
        'Multivitamin',
        'Completed',
        'All vital signs normal'
    ),
    (
        2,
        'Dr. Robert Lee',
        'Dermatology',
        '2024-01-18',
        '11:00:00',
        'Skin rash on arms',
        'Allergic dermatitis',
        'Hydrocortisone cream',
        'Completed',
        'Avoid known allergens'
    );

-- Create indexes for better performance
CREATE INDEX idx_username ON users (username);

CREATE INDEX idx_patient_id ON patients (patient_id);

CREATE INDEX idx_nik ON patients (nik);

CREATE INDEX idx_consultation_date ON consultations (consultation_date);

CREATE INDEX idx_announcement_date ON announcements (announcement_date);