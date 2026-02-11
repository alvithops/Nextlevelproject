<?php
/**
 * User/Patient Dashboard
 * Hospital Information System
 */

session_start();


// Check user authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'user') {
    header('Location: ../auth/login_user.php');
    exit;
}


require_once '../config/database.php';

// Get patient data
$patient = getRow($pdo, "
    SELECT p.*, u.username, u.email 
    FROM patients p 
    LEFT JOIN users u ON p.user_id = u.id 
    WHERE p.id = :patient_id
", ['patient_id' => $_SESSION['patient_id']]);

// Get upcoming consultations
$upcoming_consultations = getRows($pdo, "
    SELECT * FROM consultations 
    WHERE patient_id = :patient_id 
    AND status = 'scheduled' 
    AND consultation_date >= CURDATE()
    ORDER BY consultation_date, consultation_time 
    LIMIT 3
", ['patient_id' => $_SESSION['patient_id']]);

// Get recent announcements
$recent_announcements = getRows($pdo, "
    SELECT * FROM announcements 
    WHERE is_active = 1 
    ORDER BY created_at DESC 
    LIMIT 3
");

// Get consultation stats
$total_consultations = getRow($pdo, "SELECT COUNT(*) as count FROM consultations WHERE patient_id = :patient_id", ['patient_id' => $_SESSION['patient_id']])['count'];
$completed_consultations = getRow($pdo, "SELECT COUNT(*) as count FROM consultations WHERE patient_id = :patient_id AND status = 'completed'", ['patient_id' => $_SESSION['patient_id']])['count'];

$page_title = "Patient Dashboard";
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
        <aside class="sidebar" style="background: linear-gradient(180deg, #047857 0%, #065f46 100%);">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <div class="sidebar-brand-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                        <i class="fas fa-hospital-user"></i>
                    </div>
                    <span>Patient Portal</span>
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
                        <a href="data_pribadi.php" class="sidebar-menu-link">
                            <i class="sidebar-menu-icon fas fa-user"></i>
                            <span>Personal Data</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="jadwal_konsultasi.php" class="sidebar-menu-link">
                            <i class="sidebar-menu-icon fas fa-calendar-alt"></i>
                            <span>My Consultations</span>
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
                    <h1 class="topbar-title">Welcome,
                        <?php echo htmlspecialchars($patient['full_name']); ?>!
                    </h1>
                </div>
                <div class="topbar-right">
                    <div class="user-info">
                        <div class="user-avatar" style="background: linear-gradient(135deg, #10b981, #34d399);">
                            <?php echo strtoupper(substr($patient['full_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="user-name">
                                <?php echo htmlspecialchars($patient['full_name']); ?>
                            </div>
                            <div class="user-role">Patient</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-4">
                    <div class="stat-card success">
                        <i class="stat-icon fas fa-id-card"></i>
                        <div class="stat-value" style="font-size: 1.5rem;">
                            <?php echo htmlspecialchars($patient['medical_record_number']); ?>
                        </div>
                        <div class="stat-label">Medical Record Number</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-card info">
                        <i class="stat-icon fas fa-calendar-check"></i>
                        <div class="stat-value">
                            <?php echo $total_consultations; ?>
                        </div>
                        <div class="stat-label">Total Consultations</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-card warning">
                        <i class="stat-icon fas fa-check-circle"></i>
                        <div class="stat-value">
                            <?php echo $completed_consultations; ?>
                        </div>
                        <div class="stat-label">Completed Visits</div>
                    </div>
                </div>
            </div>

            <!-- Content Row -->
            <div class="row">
                <!-- Upcoming Consultations -->
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-alt text-success"></i> Upcoming Consultations
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if ($upcoming_consultations): ?>
                                <?php foreach ($upcoming_consultations as $consultation): ?>
                                    <div
                                        style="padding: 1rem; background: var(--gray-50); border-radius: var(--radius); margin-bottom: 1rem; border-left: 4px solid var(--success-color);">
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                            <div>
                                                <strong style="color: var(--gray-900); font-size: 1.05rem;">
                                                    <?php echo htmlspecialchars($consultation['doctor_name']); ?>
                                                </strong>
                                                <div style="color: var(--gray-600); font-size: 0.875rem;">
                                                    <?php echo htmlspecialchars($consultation['doctor_specialty']); ?>
                                                </div>
                                            </div>
                                            <span class="badge badge-success">Scheduled</span>
                                        </div>
                                        <div
                                            style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 0.75rem; font-size: 0.875rem; color: var(--gray-600);">
                                            <div>
                                                <i class="fas fa-calendar" style="color: var(--success-color);"></i>
                                                <?php echo date('M d, Y', strtotime($consultation['consultation_date'])); ?>
                                            </div>
                                            <div>
                                                <i class="fas fa-clock" style="color: var(--success-color);"></i>
                                                <?php echo date('H:i', strtotime($consultation['consultation_time'])); ?>
                                            </div>
                                            <div>
                                                <i class="fas fa-door-open" style="color: var(--success-color);"></i>
                                                <?php echo htmlspecialchars($consultation['room_number']); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted" style="padding: 2rem;">
                                    <i class="fas fa-calendar-times"
                                        style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;"></i>
                                    <p>No upcoming consultations</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <a href="jadwal_konsultasi.php" class="btn btn-secondary btn-sm">
                                View All Consultations <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Announcements -->
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bullhorn text-primary"></i> Recent Announcements
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if ($recent_announcements): ?>
                                <?php foreach ($recent_announcements as $announcement): ?>
                                    <div
                                        style="padding: 1rem; background: var(--gray-50); border-radius: var(--radius); margin-bottom: 1rem; border-left: 4px solid var(--primary-color);">
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                            <strong style="color: var(--gray-900); font-size: 1.05rem;">
                                                <?php echo htmlspecialchars($announcement['title']); ?>
                                            </strong>
                                            <span class="badge badge-<?php
                                            echo $announcement['priority'] === 'high' ? 'danger' :
                                                ($announcement['priority'] === 'medium' ? 'warning' : 'secondary');
                                            ?>">
                                                <?php echo ucfirst($announcement['priority']); ?>
                                            </span>
                                        </div>
                                        <p style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 0.5rem;">
                                            <?php echo substr(htmlspecialchars($announcement['content']), 0, 120) . '...'; ?>
                                        </p>
                                        <div style="font-size: 0.75rem; color: var(--gray-500);">
                                            <i class="fas fa-clock"></i>
                                            <?php echo date('M d, Y', strtotime($announcement['created_at'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted" style="padding: 2rem;">
                                    <i class="fas fa-bell-slash"
                                        style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;"></i>
                                    <p>No announcements available</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <a href="pengumuman.php" class="btn btn-primary btn-sm">
                                View All Announcements <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Info Summary -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-circle text-info"></i> Quick Info
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <div style="text-align: center; padding: 1rem;">
                                        <i class="fas fa-birthday-cake"
                                            style="font-size: 2rem; color: var(--primary-color); margin-bottom: 0.5rem;"></i>
                                        <div style="font-size: 0.875rem; color: var(--gray-600);">Birth Date</div>
                                        <div style="font-weight: 600; color: var(--gray-900);">
                                            <?php echo date('M d, Y', strtotime($patient['date_of_birth'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div style="text-align: center; padding: 1rem;">
                                        <i class="fas fa-tint"
                                            style="font-size: 2rem; color: var(--danger-color); margin-bottom: 0.5rem;"></i>
                                        <div style="font-size: 0.875rem; color: var(--gray-600);">Blood Type</div>
                                        <div style="font-weight: 600; color: var(--gray-900);">
                                            <?php echo htmlspecialchars($patient['blood_type'] ?? 'N/A'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div style="text-align: center; padding: 1rem;">
                                        <i class="fas fa-phone"
                                            style="font-size: 2rem; color: var(--success-color); margin-bottom: 0.5rem;"></i>
                                        <div style="font-size: 0.875rem; color: var(--gray-600);">Phone</div>
                                        <div style="font-weight: 600; color: var(--gray-900);">
                                            <?php echo htmlspecialchars($patient['phone']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div style="text-align: center; padding: 1rem;">
                                        <i class="fas fa-envelope"
                                            style="font-size: 2rem; color: var(--info-color); margin-bottom: 0.5rem;"></i>
                                        <div style="font-size: 0.875rem; color: var(--gray-600);">Email</div>
                                        <div style="font-weight: 600; color: var(--gray-900); font-size: 0.875rem;">
                                            <?php echo htmlspecialchars($patient['email']); ?>
                                        </div>
                                    </div>
                                </div>
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