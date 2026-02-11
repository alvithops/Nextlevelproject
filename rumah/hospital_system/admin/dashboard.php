<?php
/**
 * Admin Dashboard
 * Hospital Information System
 */

session_start();

// Check admin authentication
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../auth/login_admin.php');
    exit;
}

require_once '../config/database.php';

// Get statistics
$total_patients = getRow($pdo, "SELECT COUNT(*) as count FROM patients")['count'];
$total_announcements = getRow($pdo, "SELECT COUNT(*) as count FROM announcements WHERE is_active = 1")['count'];
$total_consultations = getRow($pdo, "SELECT COUNT(*) as count FROM consultations WHERE status = 'scheduled'")['count'];
$total_completed = getRow($pdo, "SELECT COUNT(*) as count FROM consultations WHERE status = 'completed'")['count'];

// Get recent patients
$recent_patients = getRows($pdo, "SELECT * FROM patients ORDER BY created_at DESC LIMIT 5");

// Get upcoming consultations
$upcoming_consultations = getRows($pdo, "
    SELECT c.*, p.full_name as patient_name, p.medical_record_number 
    FROM consultations c 
    JOIN patients p ON c.patient_id = p.id 
    WHERE c.status = 'scheduled' AND c.consultation_date >= CURDATE()
    ORDER BY c.consultation_date, c.consultation_time 
    LIMIT 5
");

$page_title = "Admin Dashboard";
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
                        <a href="dashboard.php" class="sidebar-menu-link active">
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
                    <h1 class="topbar-title">Dashboard</h1>
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

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-3">
                    <div class="stat-card">
                        <i class="stat-icon fas fa-users"></i>
                        <div class="stat-value">
                            <?php echo $total_patients; ?>
                        </div>
                        <div class="stat-label">Total Patients</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-card success">
                        <i class="stat-icon fas fa-bullhorn"></i>
                        <div class="stat-value">
                            <?php echo $total_announcements; ?>
                        </div>
                        <div class="stat-label">Active Announcements</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-card warning">
                        <i class="stat-icon fas fa-calendar-check"></i>
                        <div class="stat-value">
                            <?php echo $total_consultations; ?>
                        </div>
                        <div class="stat-label">Scheduled Consultations</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-card info">
                        <i class="stat-icon fas fa-check-circle"></i>
                        <div class="stat-value">
                            <?php echo $total_completed; ?>
                        </div>
                        <div class="stat-label">Completed Today</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-plus text-primary"></i> Recent Patients
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>MRN</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Registered</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($recent_patients): ?>
                                            <?php foreach ($recent_patients as $patient): ?>
                                                <tr>
                                                    <td><strong>
                                                            <?php echo htmlspecialchars($patient['medical_record_number']); ?>
                                                        </strong></td>
                                                    <td>
                                                        <?php echo htmlspecialchars($patient['full_name']); ?>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge badge-<?php echo $patient['gender'] === 'male' ? 'primary' : 'danger'; ?>">
                                                            <?php echo ucfirst($patient['gender']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php echo date('M d, Y', strtotime($patient['created_at'])); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No patients found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="data_pasien.php" class="btn btn-primary btn-sm">
                                View All Patients <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-alt text-success"></i> Upcoming Consultations
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Date & Time</th>
                                            <th>Room</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($upcoming_consultations): ?>
                                            <?php foreach ($upcoming_consultations as $consultation): ?>
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
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No upcoming consultations
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="jadwal_konsultasi.php" class="btn btn-secondary btn-sm">
                                View All Consultations <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt text-warning"></i> Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-3">
                                <a href="pengumuman.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> New Announcement
                                </a>
                                <a href="data_pasien.php" class="btn btn-success">
                                    <i class="fas fa-user-plus"></i> Add Patient
                                </a>
                                <a href="jadwal_konsultasi.php" class="btn btn-info">
                                    <i class="fas fa-calendar-plus"></i> Schedule Consultation
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        // Make sidebar toggle visible on mobile
        if (window.innerWidth <= 768) {
            document.querySelector('.sidebar-toggle').style.display = 'inline-flex';
        }
    </script>
</body>

</html>