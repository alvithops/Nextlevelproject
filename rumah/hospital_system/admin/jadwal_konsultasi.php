<?php
/**
 * Admin Consultation Schedule Management
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
    $sql = "DELETE FROM consultations WHERE id = :id";
    if (executeQuery($pdo, $sql, ['id' => $id])) {
        $success_message = 'Consultation deleted successfully';
    } else {
        $error_message = 'Failed to delete consultation';
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $patient_id = $_POST['patient_id'] ?? '';
    $doctor_name = trim($_POST['doctor_name'] ?? '');
    $doctor_specialty = trim($_POST['doctor_specialty'] ?? '');
    $consultation_date = $_POST['consultation_date'] ?? '';
    $consultation_time = $_POST['consultation_time'] ?? '';
    $room_number = trim($_POST['room_number'] ?? '');
    $status = $_POST['status'] ?? 'scheduled';
    $notes = trim($_POST['notes'] ?? '');

    if (empty($patient_id) || empty($doctor_name) || empty($doctor_specialty) || empty($consultation_date) || empty($consultation_time)) {
        $error_message = 'Required fields cannot be empty';
    } else {
        if ($id) {
            // Update
            $sql = "UPDATE consultations SET 
                    patient_id = :patient_id,
                    doctor_name = :doctor_name,
                    doctor_specialty = :doctor_specialty,
                    consultation_date = :consultation_date,
                    consultation_time = :consultation_time,
                    room_number = :room_number,
                    status = :status,
                    notes = :notes
                    WHERE id = :id";
            $params = [
                'id' => $id,
                'patient_id' => $patient_id,
                'doctor_name' => $doctor_name,
                'doctor_specialty' => $doctor_specialty,
                'consultation_date' => $consultation_date,
                'consultation_time' => $consultation_time,
                'room_number' => $room_number,
                'status' => $status,
                'notes' => $notes
            ];
            if (executeQuery($pdo, $sql, $params)) {
                $success_message = 'Consultation updated successfully';
            }
        } else {
            // Insert
            $sql = "INSERT INTO consultations (patient_id, doctor_name, doctor_specialty, consultation_date, consultation_time, room_number, status, notes, created_by) 
                    VALUES (:patient_id, :doctor_name, :doctor_specialty, :consultation_date, :consultation_time, :room_number, :status, :notes, :created_by)";
            $params = [
                'patient_id' => $patient_id,
                'doctor_name' => $doctor_name,
                'doctor_specialty' => $doctor_specialty,
                'consultation_date' => $consultation_date,
                'consultation_time' => $consultation_time,
                'room_number' => $room_number,
                'status' => $status,
                'notes' => $notes,
                'created_by' => $_SESSION['admin_id']
            ];
            if (insertAndGetId($pdo, $sql, $params)) {
                $success_message = 'Consultation scheduled successfully';
            }
        }
    }
}

// Get all consultations
$consultations = getRows($pdo, "
    SELECT c.*, p.full_name as patient_name, p.medical_record_number 
    FROM consultations c 
    JOIN patients p ON c.patient_id = p.id 
    ORDER BY c.consultation_date DESC, c.consultation_time DESC
");

// Get all patients for dropdown
$patients = getRows($pdo, "SELECT id, full_name, medical_record_number FROM patients ORDER BY full_name");

// Get consultation for editing
$edit_consultation = null;
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $edit_consultation = getRow($pdo, "SELECT * FROM consultations WHERE id = :id", ['id' => $edit_id]);
}

$page_title = "Consultation Schedule Management";
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
                        <a href="data_pasien.php" class="sidebar-menu-link">
                            <i class="sidebar-menu-icon fas fa-users"></i>
                            <span>Patient Data</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="jadwal_konsultasi.php" class="sidebar-menu-link active">
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
                    <h1 class="topbar-title">Consultation Schedule</h1>
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

            <!-- Add Button -->
            <div class="mb-4">
                <button class="btn btn-primary" data-modal-target="consultationModal">
                    <i class="fas fa-calendar-plus"></i> Schedule New Consultation
                </button>
            </div>

            <!-- Consultations List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> All Consultations
                    </h3>
                    <div>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search consultations..."
                            style="max-width: 300px;">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="consultationTable">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Date & Time</th>
                                    <th>Room</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($consultations): ?>
                                    <?php foreach ($consultations as $consultation): ?>
                                        <tr>
                                            <td>
                                                <strong>
                                                    <?php echo htmlspecialchars($consultation['patient_name']); ?>
                                                </strong><br>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($consultation['medical_record_number']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($consultation['doctor_name']); ?><br>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($consultation['doctor_specialty']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php echo date('M d, Y', strtotime($consultation['consultation_date'])); ?><br>
                                                <small class="text-muted">
                                                    <?php echo date('H:i', strtotime($consultation['consultation_time'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($consultation['room_number']); ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php
                                                echo $consultation['status'] === 'completed' ? 'success' :
                                                    ($consultation['status'] === 'scheduled' ? 'info' :
                                                        ($consultation['status'] === 'cancelled' ? 'danger' : 'warning'));
                                                ?>">
                                                    <?php echo ucfirst($consultation['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="?edit=<?php echo $consultation['id']; ?>"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete=<?php echo $consultation['id']; ?>"
                                                        class="btn btn-sm btn-danger"
                                                        data-delete-confirm="Are you sure you want to delete this consultation?">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No consultations found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Consultation Modal -->
    <div class="modal-overlay" id="consultationModal">
        <div class="modal" style="max-width: 700px;">
            <div class="modal-header">
                <h3 class="modal-title">
                    <?php echo $edit_consultation ? 'Edit Consultation' : 'Schedule New Consultation'; ?>
                </h3>
                <button class="modal-close" data-modal-close>×</button>
            </div>
            <form method="POST" data-validate>
                <div class="modal-body">
                    <?php if ($edit_consultation): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_consultation['id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="patient_id" class="form-label">Patient *</label>
                        <select id="patient_id" name="patient_id" class="form-control" required>
                            <option value="">Select Patient</option>
                            <?php foreach ($patients as $patient): ?>
                                <option value="<?php echo $patient['id']; ?>" <?php echo ($edit_consultation['patient_id'] ?? '') == $patient['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($patient['full_name'] . ' - ' . $patient['medical_record_number']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="doctor_name" class="form-label">Doctor Name *</label>
                                <input type="text" id="doctor_name" name="doctor_name" class="form-control"
                                    value="<?php echo htmlspecialchars($edit_consultation['doctor_name'] ?? ''); ?>"
                                    required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="doctor_specialty" class="form-label">Specialty *</label>
                                <input type="text" id="doctor_specialty" name="doctor_specialty" class="form-control"
                                    value="<?php echo htmlspecialchars($edit_consultation['doctor_specialty'] ?? ''); ?>"
                                    placeholder="e.g., Cardiology, Pediatrics" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="consultation_date" class="form-label">Date *</label>
                                <input type="date" id="consultation_date" name="consultation_date" class="form-control"
                                    value="<?php echo htmlspecialchars($edit_consultation['consultation_date'] ?? ''); ?>"
                                    required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="consultation_time" class="form-label">Time *</label>
                                <input type="time" id="consultation_time" name="consultation_time" class="form-control"
                                    value="<?php echo htmlspecialchars($edit_consultation['consultation_time'] ?? ''); ?>"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="room_number" class="form-label">Room Number *</label>
                                <input type="text" id="room_number" name="room_number" class="form-control"
                                    value="<?php echo htmlspecialchars($edit_consultation['room_number'] ?? ''); ?>"
                                    placeholder="e.g., Room 301" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="status" class="form-label">Status *</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="scheduled" <?php echo ($edit_consultation['status'] ?? '') === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                    <option value="completed" <?php echo ($edit_consultation['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo ($edit_consultation['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    <option value="rescheduled" <?php echo ($edit_consultation['status'] ?? '') === 'rescheduled' ? 'selected' : ''; ?>>Rescheduled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3"
                            placeholder="Additional notes about the consultation..."><?php echo htmlspecialchars($edit_consultation['notes'] ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <?php echo $edit_consultation ? 'Update' : 'Schedule'; ?> Consultation
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
        const table = document.getElementById('consultationTable');
        if (searchInput && table) {
            initTableSearch(searchInput, table);
        }

        // Open modal if editing
        <?php if ($edit_consultation): ?>
                document.addEventListener('DOMContentLoaded', function () {
                    openModal('consultationModal');
                });
        <?php endif; ?>
    </script>
</body>

</html>