<?php
/**
 * User/Patient Personal Data View
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

$page_title = "Personal Data";
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
                        <a href="data_pribadi.php" class="sidebar-menu-link active">
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
                    <h1 class="topbar-title">Personal Data</h1>
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

            <!-- Patient Profile -->
            <div class="row">
                <!-- Personal Information -->
                <div class="col-6">
                    <div class="card">
                        <div class="card-header"
                            style="background: linear-gradient(135deg, #3b82f6, #60a5fa); color: white;">
                            <h3 class="card-title" style="color: white; margin: 0;">
                                <i class="fas fa-user-circle"></i> Personal Information
                            </h3>
                        </div>
                        <div class="card-body" style="padding: 1.5rem;">
                            <div style="margin-bottom: 1.5rem;">
                                <label
                                    style="color: var(--gray-600); font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Full
                                    Name</label>
                                <div style="font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">
                                    <?php echo htmlspecialchars($patient['full_name']); ?>
                                </div>
                            </div>

                            <div style="margin-bottom: 1.5rem;">
                                <label
                                    style="color: var(--gray-600); font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Medical
                                    Record Number</label>
                                <div style="font-size: 1.125rem; font-weight: 600; color: var(--primary-color);">
                                    <?php echo htmlspecialchars($patient['medical_record_number']); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6" style="margin-bottom: 1.5rem;">
                                    <label
                                        style="color: var(--gray-600); font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Date
                                        of Birth</label>
                                    <div style="font-weight: 600; color: var(--gray-900);">
                                        <?php echo date('F d, Y', strtotime($patient['date_of_birth'])); ?>
                                    </div>
                                    <small class="text-muted">Age:
                                        <?php echo date_diff(date_create($patient['date_of_birth']), date_create('now'))->y; ?>
                                        years
                                    </small>
                                </div>

                                <div class="col-6" style="margin-bottom: 1.5rem;">
                                    <label
                                        style="color: var(--gray-600); font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Gender</label>
                                    <div style="font-weight: 600;">
                                        <span
                                            class="badge badge-<?php echo $patient['gender'] === 'male' ? 'primary' : 'danger'; ?>"
                                            style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">
                                            <?php echo ucfirst($patient['gender']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-bottom: 1.5rem;">
                                <label
                                    style="color: var(--gray-600); font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">
                                    <i class="fas fa-tint text-danger"></i> Blood Type
                                </label>
                                <div style="font-size: 1.25rem; font-weight: 700; color: var(--danger-color);">
                                    <?php echo htmlspecialchars($patient['blood_type'] ?? 'Not Specified'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-6">
                    <div class="card">
                        <div class="card-header"
                            style="background: linear-gradient(135deg, #10b981, #34d399); color: white;">
                            <h3 class="card-title" style="color: white; margin: 0;">
                                <i class="fas fa-address-book"></i> Contact Information
                            </h3>
                        </div>
                        <div class="card-body" style="padding: 1.5rem;">
                            <div style="margin-bottom: 1.5rem;">
                                <label
                                    style="color: var(--gray-600); font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">
                                    <i class="fas fa-phone"></i> Phone Number
                                </label>
                                <div style="font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">
                                    <?php echo htmlspecialchars($patient['phone']); ?>
                                </div>
                            </div>

                            <div style="margin-bottom: 1.5rem;">
                                <label
                                    style="color: var(--gray-600); font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">
                                    <i class="fas fa-envelope"></i> Email Address
                                </label>
                                <div style="font-size: 1.125rem; font-weight: 600; color: var(--gray-900);">
                                    <?php echo htmlspecialchars($patient['email']); ?>
                                </div>
                            </div>

                            <div style="margin-bottom: 1.5rem;">
                                <label
                                    style="color: var(--gray-600); font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">
                                    <i class="fas fa-map-marker-alt"></i> Address
                                </label>
                                <div style="font-weight: 600; color: var(--gray-900); line-height: 1.6;">
                                    <?php echo htmlspecialchars($patient['address']); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="card" style="margin-top: 1.5rem;">
                        <div class="card-header"
                            style="background: linear-gradient(135deg, #f59e0b, #fbbf24); color: white;">
                            <h3 class="card-title" style="color: white; margin: 0;">
                                <i class="fas fa-exclamation-triangle"></i> Emergency Contact
                            </h3>
                        </div>
                        <div class="card-body" style="padding: 1.5rem;">
                            <?php if ($patient['emergency_contact_name']): ?>
                                <div style="margin-bottom: 1rem;">
                                    <label
                                        style="color: var(--gray-600); font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Name</label>
                                    <div style="font-weight: 600; color: var(--gray-900);">
                                        <?php echo htmlspecialchars($patient['emergency_contact_name']); ?>
                                    </div>
                                </div>
                                <div>
                                    <label
                                        style="color: var(--gray-600); font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Phone</label>
                                    <div style="font-weight: 600; color: var(--gray-900);">
                                        <?php echo htmlspecialchars($patient['emergency_contact_phone']); ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No emergency contact specified</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Information -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header"
                            style="background: linear-gradient(135deg, #8b5cf6, #a78bfa); color: white;">
                            <h3 class="card-title" style="color: white; margin: 0;">
                                <i class="fas fa-notes-medical"></i> Medical Information
                            </h3>
                        </div>
                        <div class="card-body" style="padding: 1.5rem;">
                            <div class="row">
                                <div class="col-6">
                                    <label
                                        style="color: var(--gray-600); font-size: 0.875rem; font-weight: 600; display: block; margin-bottom: 0.5rem;">
                                        <i class="fas fa-allergies text-warning"></i> Allergies
                                    </label>
                                    <div
                                        style="background: var(--gray-50); padding: 1rem; border-radius: var(--radius); border-left: 4px solid var(--warning-color);">
                                        <?php if ($patient['allergies']): ?>
                                            <p style="margin: 0; white-space: pre-wrap; color: var(--gray-700);">
                                                <?php echo htmlspecialchars($patient['allergies']); ?>
                                            </p>
                                        <?php else: ?>
                                            <p style="margin: 0; color: var(--gray-500); font-style: italic;">No known
                                                allergies</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <label
                                        style="color: var(--gray-600); font-size: 0.875rem; font-weight: 600; display: block; margin-bottom: 0.5rem;">
                                        <i class="fas fa-history text-info"></i> Medical History
                                    </label>
                                    <div
                                        style="background: var(--gray-50); padding: 1rem; border-radius: var(--radius); border-left: 4px solid var(--info-color);">
                                        <?php if ($patient['medical_history']): ?>
                                            <p style="margin: 0; white-space: pre-wrap; color: var(--gray-700);">
                                                <?php echo htmlspecialchars($patient['medical_history']); ?>
                                            </p>
                                        <?php else: ?>
                                            <p style="margin: 0; color: var(--gray-500); font-style: italic;">No medical
                                                history recorded</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Note -->
            <div class="alert alert-info" style="margin-top: 1.5rem;">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>Need to update your information?</strong><br>
                    Please contact the hospital administration at the front desk or call us at <strong>(021)
                        123-4567</strong> to request changes to your personal data.
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