-- ============================================
-- Hospital Information System Database
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS hospital_system;

USE hospital_system;

-- ============================================
-- Table: admins
-- ============================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ============================================
-- Table: users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ============================================
-- Table: patients
-- ============================================
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    medical_record_number VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    blood_type ENUM(
        'A+',
        'A-',
        'B+',
        'B-',
        'AB+',
        'AB-',
        'O+',
        'O-'
    ),
    allergies TEXT,
    medical_history TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ============================================
-- Table: announcements
-- ============================================
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    category ENUM(
        'general',
        'emergency',
        'schedule',
        'facility',
        'health_tips'
    ) DEFAULT 'general',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    created_by INT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ============================================
-- Table: consultations
-- ============================================
CREATE TABLE IF NOT EXISTS consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_name VARCHAR(100) NOT NULL,
    doctor_specialty VARCHAR(100) NOT NULL,
    consultation_date DATE NOT NULL,
    consultation_time TIME NOT NULL,
    room_number VARCHAR(20),
    status ENUM(
        'scheduled',
        'completed',
        'cancelled',
        'rescheduled'
    ) DEFAULT 'scheduled',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients (id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES admins (id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ============================================
-- Insert Sample Data
-- ============================================

-- Insert Admin
-- Password: admin123
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
        'Dr. Sarah Administrator',
        'admin@hospital.com'
    );

-- Insert Users (Patients)
-- Password: patient123
INSERT INTO
    users (username, password, email)
VALUES (
        'john.doe',
        '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
        'john.doe@email.com'
    ),
    (
        'jane.smith',
        '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
        'jane.smith@email.com'
    );

-- Insert Patient Data
INSERT INTO
    patients (
        user_id,
        medical_record_number,
        full_name,
        date_of_birth,
        gender,
        phone,
        address,
        emergency_contact_name,
        emergency_contact_phone,
        blood_type,
        allergies,
        medical_history
    )
VALUES (
        1,
        'MR-2024-001',
        'John Doe',
        '1985-05-15',
        'male',
        '081234567890',
        'Jl. Merdeka No. 123, Jakarta',
        'Jane Doe (Wife)',
        '081234567891',
        'O+',
        'Penicillin',
        'Hypertension since 2020'
    ),
    (
        2,
        'MR-2024-002',
        'Jane Smith',
        '1990-08-22',
        'female',
        '082345678901',
        'Jl. Sudirman No. 456, Jakarta',
        'Robert Smith (Husband)',
        '082345678902',
        'A+',
        'None',
        'Diabetes Type 2'
    );

-- Insert Announcements
INSERT INTO
    announcements (
        title,
        content,
        category,
        priority,
        created_by,
        is_active
    )
VALUES (
        'Hospital Operating Hours Update',
        'Starting from February 15th, 2026, our hospital will extend operating hours to 24/7 for emergency services. Regular consultation hours remain 08:00 - 20:00.',
        'schedule',
        'high',
        1,
        1
    ),
    (
        'New Cardiology Specialist Available',
        'We are pleased to announce that Dr. Michael Chen, a renowned cardiology specialist, has joined our medical team. Appointments are now available.',
        'general',
        'medium',
        1,
        1
    ),
    (
        'Health Tips: Preventing Dengue Fever',
        'With the rainy season approaching, please take preventive measures against dengue fever. Eliminate standing water, use mosquito repellent, and seek immediate medical attention if you experience high fever.',
        'health_tips',
        'high',
        1,
        1
    ),
    (
        'New MRI Facility Now Operational',
        'Our state-of-the-art MRI facility is now fully operational. Please contact our radiology department for appointments and pricing information.',
        'facility',
        'medium',
        1,
        1
    );

-- Insert Consultation Schedules
INSERT INTO
    consultations (
        patient_id,
        doctor_name,
        doctor_specialty,
        consultation_date,
        consultation_time,
        room_number,
        status,
        notes,
        created_by
    )
VALUES (
        1,
        'Dr. Michael Chen',
        'Cardiology',
        '2026-02-15',
        '10:00:00',
        'Room 301',
        'scheduled',
        'Routine heart checkup',
        1
    ),
    (
        1,
        'Dr. Lisa Wong',
        'Internal Medicine',
        '2026-02-20',
        '14:00:00',
        'Room 205',
        'scheduled',
        'Follow-up hypertension treatment',
        1
    ),
    (
        2,
        'Dr. Robert Johnson',
        'Endocrinology',
        '2026-02-18',
        '09:30:00',
        'Room 402',
        'scheduled',
        'Diabetes management consultation',
        1
    ),
    (
        2,
        'Dr. Amanda Lee',
        'General Practice',
        '2026-02-12',
        '11:00:00',
        'Room 101',
        'completed',
        'General health checkup completed',
        1
    );

-- ============================================
-- Create Indexes for Performance
-- ============================================
CREATE INDEX idx_announcements_active ON announcements (is_active);

CREATE INDEX idx_announcements_category ON announcements (category);

CREATE INDEX idx_consultations_date ON consultations (consultation_date);

CREATE INDEX idx_consultations_status ON consultations (status);

CREATE INDEX idx_patients_mrn ON patients (medical_record_number);

-- ============================================
-- Success Message
-- ============================================
SELECT 'Database hospital_system created successfully!' AS Message;