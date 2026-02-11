<?php
/**
 * Admin Patient Data Management
 * Hospital Information System
 */

session_start();

// Check admin authentication
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../auth/login_admin.php');
    exit;
}

require_once '../config/database.php';

$success_message = '';
$error_message = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    // Delete patient and associated user
    $patient = getRow($pdo, "SELECT user_id FROM patients WHERE id = :id", ['id' => $id]);
    if ($patient) {
        $sql = "DELETE FROM users WHERE id = :user_id";
        if (executeQuery($pdo, $sql, ['user_id' => $patient['user_id']])) {
            $success_message = 'Patient deleted successfully';
        } else {
            $error_message = 'Failed to delete patient';
        }
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $medical_record_number = trim($_POST['medical_record_number'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $blood_type = $_POST['blood_type'] ?? null;
    $allergies = trim($_POST['allergies'] ?? '');
    $medical_history = trim($_POST['medical_history'] ?? '');
    $emergency_contact_name = trim($_POST['emergency_contact_name'] ?? '');
    $emergency_contact_phone = trim($_POST['emergency_contact_phone'] ?? '');

    if (empty($username) || empty($email) || empty($full_name) || empty($medical_record_number)) {
        $error_message = 'Required fields cannot be empty';
    } else {
        if ($id) {
            // Update patient
            $patient = getRow($pdo, "SELECT user_id FROM patients WHERE id = :id", ['id' => $id]);

            // Update user
            $sql_user = "UPDATE users SET username = :username, email = :email WHERE id = :user_id";
            executeQuery($pdo, $sql_user, [
                'username' => $username,
                'email' => $email,
                'user_id' => $patient['user_id']
            ]);

            // Update patient
            $sql_patient = "UPDATE patients SET 
                            full_name = :full_name,
                            medical_record_number = :mrn,
                            date_of_birth = :dob,
                            gender = :gender,
                            phone = :phone,
                            address = :address,
                            blood_type = :blood_type,
                            allergies = :allergies,
                            medical_history = :medical_history,
                            emergency_contact_name = :ec_name,
                            emergency_contact_phone = :ec_phone
                            WHERE id = :id";
            if (
                executeQuery($pdo, $sql_patient, [
                    'id' => $id,
                    'full_name' => $full_name,
                    'mrn' => $medical_record_number,
                    'dob' => $date_of_birth,
                    'gender' => $gender,
                    'phone' => $phone,
                    'address' => $address,
                    'blood_type' => $blood_type,
                    'allergies' => $allergies,
                    'medical_history' => $medical_history,
                    'ec_name' => $emergency_contact_name,
                    'ec_phone' => $emergency_contact_phone
                ])
            ) {
                $success_message = 'Patient updated successfully';
            }
        } else {
            // Add new patient
            $password = $_POST['password'] ?? 'patient123';
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user first
            $sql_user = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
            $user_id = insertAndGetId($pdo, $sql_user, [
                'username' => $username,
                'password' => $hashed_password,
                'email' => $email
            ]);

            if ($user_id) {
                // Insert patient
                $sql_patient = "INSERT INTO patients (user_id, full_name, medical_record_number, date_of_birth, gender, phone, address, blood_type, allergies, medical_history, emergency_contact_name, emergency_contact_phone) 
                                VALUES (:user_id, :full_name, :mrn, :dob, :gender, :phone, :address, :blood_type, :allergies, :medical_history, :ec_name, :ec_phone)";
                if (
                    insertAndGetId($pdo, $sql_patient, [
                        'user_id' => $user_id,
                        'full_name' => $full_name,
                        'mrn' => $medical_record_number,
                        'dob' => $date_of_birth,
                        'gender' => $gender,
                        'phone' => $phone,
                        'address' => $address,
                        'blood_type' => $blood_type,
                        'allergies' => $allergies,
                        'medical_history' => $medical_history,
                        'ec_name' => $emergency_contact_name,
                        'ec_phone' => $emergency_contact_phone
                    ])
                ) {
                    $success_message = 'Patient added successfully';
                }
            }
        }
    }
}

// Get all patients
$patients = getRows($pdo, "SELECT p.*, u.username, u.email FROM patients p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");

// Get patient for editing
$edit_patient = null;
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $edit_patient = getRow($pdo, "SELECT p.*, u.username, u.email FROM patients p LEFT JOIN users u ON p.user_id = u.id WHERE p.id = :id", ['id' => $edit_id]);
}

$page_title = "Patient Data Management";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $page_title; ?> - Hospital Information System
    </title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <div class="sidebar-brand-icon">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <span>HIS Admin</span>
                </div>
            </div>

            <nav>
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a href="dashboard.php" class="sidebar-menu-link">
                            <i class="sidebar-menu-icon fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="pengumuman.php" class="sidebar-menu-link">
                            <i class="sidebar-menu-icon fas fa-bullhorn"></i>
                            <span>Announcements</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="data_pasien.php" class="sidebar-menu-link active">
                            <i class="sidebar-menu-icon fas fa-users"></i>
                            <span>Patient Data</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="jadwal_konsultasi.php" class="sidebar-menu-link">
                            <i class="sidebar-menu-icon fas fa-calendar-alt"></i>
                            <span>Consultations</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item"
                        style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
                        <a href="../auth/logout.php" class="sidebar-menu-link">
                            <i class="sidebar-menu-icon fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <div class="topbar">
                <div class="topbar-left">
                    <button class="btn btn-outline sidebar-toggle" style="display: none;">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="topbar-title">Patient Data Management</h1>
                </div>
                <div class="topbar-right">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['admin_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="user-name">
                                <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                            </div>
                            <div class="user-role">Administrator</div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>
                        <?php echo htmlspecialchars($success_message); ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>
                        <?php echo htmlspecialchars($error_message); ?>
                    </span>
                </div>
            <?php endif; ?>

            <!-- Add/Edit Button -->
            <div class="mb-4">
                <button class="btn btn-primary" data-modal-target="patientModal">
                    <i class="fas fa-user-plus"></i> Add New Patient
                </button>
            </div>

            <!-- Patients List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> All Patients
                    </h3>
                    <div>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search patients..."
                            style="max-width: 300px;">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="patientTable">
                            <thead>
                                <tr>
                                    <th>MRN</th>
                                    <th>Full Name</th>
                                    <th>Gender</th>
                                    <th>Birth Date</th>
                                    <th>Phone</th>
                                    <th>Blood Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($patients): ?>
                                    <?php foreach ($patients as $patient): ?>
                                        <tr>
                                            <td><strong>
                                                    <?php echo htmlspecialchars($patient['medical_record_number']); ?>
                                                </strong></td>
                                            <td>
                                                <?php echo htmlspecialchars($patient['full_name']); ?><br>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($patient['email']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-<?php echo $patient['gender'] === 'male' ? 'primary' : 'danger'; ?>">
                                                    <?php echo ucfirst($patient['gender']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo date('M d, Y', strtotime($patient['date_of_birth'])); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($patient['phone']); ?>
                                            </td>
                                            <td>
                                                <?php if ($patient['blood_type']): ?>
                                                    <span class="badge badge-danger">
                                                        <?php echo htmlspecialchars($patient['blood_type']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="?edit=<?php echo $patient['id']; ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete=<?php echo $patient['id']; ?>"
                                                        class="btn btn-sm btn-danger"
                                                        data-delete-confirm="Are you sure you want to delete this patient?">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No patients found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Patient Modal -->
    <div class="modal-overlay" id="patientModal">
        <div class="modal" style="max-width: 800px;">
            <div class="modal-header">
                <h3 class="modal-title">
                    <?php echo $edit_patient ? 'Edit Patient' : 'Add New Patient'; ?>
                </h3>
                <button class="modal-close" data-modal-close>×</button>
            </div>
            <form method="POST" data-validate>
                <div class="modal-body">
                    <?php if ($edit_patient): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_patient['id']; ?>">
                    <?php endif; ?>

                    <h4 class="mb-3" style="border-bottom: 2px solid var(--primary-color); padding-bottom: 0.5rem;">
                        Account Information</h4>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" id="username" name="username" class="form-control"
                                    value="<?php echo htmlspecialchars($edit_patient['username'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="<?php echo htmlspecialchars($edit_patient['email'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <?php if (!$edit_patient): ?>
                        <div class="form-group">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" id="password" name="password" class="form-control" value="patient123"
                                required>
                            <small class="form-text">Default: patient123</small>
                        </div>
                    <?php endif; ?>

                    <h4 class="mb-3 mt-4"
                        style="border-bottom: 2px solid var(--secondary-color); padding-bottom: 0.5rem;">Personal
                        Information</h4>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" id="full_name" name="full_name" class="form-control"
                                    value="<?php echo htmlspecialchars($edit_patient['full_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="medical_record_number" class="form-label">Medical Record Number *</label>
                                <input type="text" id="medical_record_number" name="medical_record_number"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($edit_patient['medical_record_number'] ?? ''); ?>"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control"
                                    value="<?php echo htmlspecialchars($edit_patient['date_of_birth'] ?? ''); ?>"
                                    required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="gender" class="form-label">Gender *</label>
                                <select id="gender" name="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" <?php echo ($edit_patient['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo ($edit_patient['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="blood_type" class="form-label">Blood Type</label>
                                <select id="blood_type" name="blood_type" class="form-control">
                                    <option value="">Select Blood Type</option>
                                    <?php
                                    $blood_types = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                    foreach ($blood_types as $type) {
                                        $selected = ($edit_patient['blood_type'] ?? '') === $type ? 'selected' : '';
                                        echo "<option value=\"$type\" $selected>$type</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone *</label>
                                <input type="tel" id="phone" name="phone" class="form-control"
                                    value="<?php echo htmlspecialchars($edit_patient['phone'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="address" class="form-label">Address *</label>
                                <input type="text" id="address" name="address" class="form-control"
                                    value="<?php echo htmlspecialchars($edit_patient['address'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <h4 class="mb-3 mt-4"
                        style="border-bottom: 2px solid var(--warning-color); padding-bottom: 0.5rem;">Medical &
                        Emergency Information</h4>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="allergies" class="form-label">Allergies</label>
                                <textarea id="allergies" name="allergies" class="form-control"
                                    rows="2"><?php echo htmlspecialchars($edit_patient['allergies'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="medical_history" class="form-label">Medical History</label>
                                <textarea id="medical_history" name="medical_history" class="form-control"
                                    rows="2"><?php echo htmlspecialchars($edit_patient['medical_history'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                                <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($edit_patient['emergency_contact_name'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                                <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($edit_patient['emergency_contact_phone'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <?php echo $edit_patient ? 'Update' : 'Add'; ?> Patient
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        // Make sidebar toggle visible on mobile
        if (window.innerWidth <= 768) {
            document.querySelector('.sidebar-toggle').style.display = 'inline-flex';
        }

        // Initialize table search
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('patientTable');
        if (searchInput && table) {
            initTableSearch(searchInput, table);
        }

        // Open modal if editing
        <?php if ($edit_patient): ?>
                document.addEventListener('DOMContentLoaded', function () {
                    openModal('patientModal');
                });
        <?php endif; ?>
    </script>
</body>

</html>