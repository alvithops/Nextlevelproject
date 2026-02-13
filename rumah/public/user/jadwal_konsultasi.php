<?php
/**
 * User/Patient Consultation Schedule View
 * Hospital Information System
 */

session_start();

// Check user authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'user') {
    header('Location: ../auth/login_user.php');
    exit;
}

require_once '../config/database.php';

// Get all consultations for this patient
$consultations = getRows($pdo, "
    SELECT * FROM consultations 
    WHERE patient_id = :patient_id 
    ORDER BY consultation_date DESC, consultation_time DESC
", ['patient_id' => $_SESSION['patient_id']]);

$page_title = "My Consultations";
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
                        <a href="data_pribadi.php" class="sidebar-menu-link">
                            <i class="sidebar-menu-icon fas fa-user"></i>
                            <span>Personal Data</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="jadwal_konsultasi.php" class="sidebar-menu-link active">
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
                    <h1 class="topbar-title">My Consultations</h1>
                </div>
                <div class="topbar-right">
                    <div class="user-info">
                        <div class="user-avatar" style="background: linear-gradient(135deg, #10b981, #34d399);">
                            <?php echo strtoupper(substr($_SESSION['patient_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="user-name">
                                <?php echo htmlspecialchars($_SESSION['patient_name']); ?>
                            </div>
                            <div class="user-role">Patient</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div style="margin-bottom: 1.5rem; border-bottom: 2px solid var(--gray-200); padding-bottom: 0;">
                <div style="display: flex; gap: 1rem;">
                    <button class="filter-tab active" data-filter="all"
                        style="padding: 0.75rem 1.5rem; border: none; background: none; cursor: pointer; position: relative; font-weight: 600; color: var(--gray-600); transition: var(--transition-fast);">
                        All Consultations
                    </button>
                    <button class="filter-tab" data-filter="scheduled"
                        style="padding: 0.75rem 1.5rem; border: none; background: none; cursor: pointer; position: relative; font-weight: 600; color: var(--gray-600); transition: var(--transition-fast);">
                        Upcoming
                    </button>
                    <button class="filter-tab" data-filter="completed"
                        style="padding: 0.75rem 1.5rem; border: none; background: none; cursor: pointer; position: relative; font-weight: 600; color: var(--gray-600); transition: var(--transition-fast);">
                        Completed
                    </button>
                    <button class="filter-tab" data-filter="cancelled"
                        style="padding: 0.75rem 1.5rem; border: none; background: none; cursor: pointer; position: relative; font-weight: 600; color: var(--gray-600); transition: var(--transition-fast);">
                        Cancelled
                    </button>
                </div>
            </div>

            <!-- Consultations List -->
            <div id="consultationsList">
                <?php if ($consultations): ?>
                    <?php foreach ($consultations as $consultation): ?>
                        <div class="consultation-card" data-status="<?php echo $consultation['status']; ?>" style="background: white; border-radius: var(--radius-lg); box-shadow: var(--shadow); padding: 1.5rem; margin-bottom: 1rem; border-left: 5px solid <?php
                           echo $consultation['status'] === 'completed' ? 'var(--success-color)' :
                               ($consultation['status'] === 'scheduled' ? 'var(--info-color)' :
                                   ($consultation['status'] === 'cancelled' ? 'var(--danger-color)' : 'var(--warning-color)'));
                           ?>;">
                            <div
                                style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                <div>
                                    <h3
                                        style="font-size: 1.25rem; font-weight: 700; color: var(--gray-900); margin-bottom: 0.25rem;">
                                        <?php echo htmlspecialchars($consultation['doctor_name']); ?>
                                    </h3>
                                    <p style="color: var(--gray-600); margin: 0;">
                                        <i class="fas fa-stethoscope"></i>
                                        <?php echo htmlspecialchars($consultation['doctor_specialty']); ?>
                                    </p>
                                </div>
                                <span class="badge badge-<?php
                                echo $consultation['status'] === 'completed' ? 'success' :
                                    ($consultation['status'] === 'scheduled' ? 'info' :
                                        ($consultation['status'] === 'cancelled' ? 'danger' : 'warning'));
                                ?>" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                    <?php echo ucfirst($consultation['status']); ?>
                                </span>
                            </div>

                            <div
                                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; padding: 1rem; background: var(--gray-50); border-radius: var(--radius); margin-bottom: 1rem;">
                                <div>
                                    <div style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 0.25rem;">
                                        <i class="fas fa-calendar"></i> Date
                                    </div>
                                    <div style="font-weight: 600; color: var(--gray-900);">
                                        <?php echo date('l, F d, Y', strtotime($consultation['consultation_date'])); ?>
                                    </div>
                                </div>

                                <div>
                                    <div style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 0.25rem;">
                                        <i class="fas fa-clock"></i> Time
                                    </div>
                                    <div style="font-weight: 600; color: var(--gray-900);">
                                        <?php echo date('H:i', strtotime($consultation['consultation_time'])); ?>
                                    </div>
                                </div>

                                <div>
                                    <div style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 0.25rem;">
                                        <i class="fas fa-door-open"></i> Room
                                    </div>
                                    <div style="font-weight: 600; color: var(--gray-900);">
                                        <?php echo htmlspecialchars($consultation['room_number']); ?>
                                    </div>
                                </div>
                            </div>

                            <?php if ($consultation['notes']): ?>
                                <div
                                    style="padding: 1rem; background: rgba(59, 130, 246, 0.05); border-left: 3px solid var(--info-color); border-radius: var(--radius);">
                                    <div
                                        style="color: var(--gray-600); font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                                        <i class="fas fa-sticky-note"></i> Notes:
                                    </div>
                                    <p style="color: var(--gray-700); margin: 0; white-space: pre-wrap;">
                                        <?php echo htmlspecialchars($consultation['notes']); ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php if ($consultation['status'] === 'scheduled' && strtotime($consultation['consultation_date']) >= strtotime(date('Y-m-d'))): ?>
                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--gray-200);">
                                    <div class="alert alert-info" style="margin: 0; padding: 0.75rem 1rem;">
                                        <i class="fas fa-info-circle"></i>
                                        Please arrive 15 minutes before your scheduled time. Bring your medical record card and ID.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card">
                        <div class="card-body" style="padding: 4rem; text-align: center;">
                            <i class="fas fa-calendar-times"
                                style="font-size: 5rem; color: var(--gray-300); margin-bottom: 1rem;"></i>
                            <h3 style="color: var(--gray-600);">No Consultations Found</h3>
                            <p class="text-muted">You don't have any consultation appointments yet.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Help Section -->
            <div class="card" style="margin-top: 2rem; background: linear-gradient(135deg, #e0f2fe, #dbeafe);">
                <div class="card-body" style="padding: 1.5rem;">
                    <h4 style="color: var(--gray-900); margin-bottom: 1rem;">
                        <i class="fas fa-question-circle text-primary"></i> Need Help?
                    </h4>
                    <p style="color: var(--gray-700); margin-bottom: 1rem;">
                        For consultation scheduling or rescheduling, please contact our front desk or call us at:
                    </p>
                    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                        <div>
                            <i class="fas fa-phone text-success"></i>
                            <strong>(021) 123-4567</strong>
                        </div>
                        <div>
                            <i class="fas fa-envelope text-primary"></i>
                            <strong>info@hospital.com</strong>
                        </div>
                        <div>
                            <i class="fas fa-clock text-warning"></i>
                            <strong>Mon-Fri: 08:00 - 20:00</strong>
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

        // Filter functionality
        const filterTabs = document.querySelectorAll('.filter-tab');
        const consultationCards = document.querySelectorAll('.consultation-card');

        // Style for active tab
        const activeTabStyle = 'color: var(--primary-color); border-bottom: 3px solid var(--primary-color);';
        const inactiveTabStyle = '';

        filterTabs.forEach(tab => {
            tab.addEventListener('click', function () {
                // Remove active class from all tabs
                filterTabs.forEach(t => {
                    t.classList.remove('active');
                    t.setAttribute('style', t.getAttribute('style').replace(activeTabStyle, '') + inactiveTabStyle);
                });

                // Add active class to clicked tab
                this.classList.add('active');
                this.setAttribute('style', this.getAttribute('style') + activeTabStyle);

                const filter = this.getAttribute('data-filter');

                // Filter consultations
                consultationCards.forEach(card => {
                    if (filter === 'all' || card.getAttribute('data-status') === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Set initial active tab style
        document.querySelector('.filter-tab.active').setAttribute('style',
            document.querySelector('.filter-tab.active').getAttribute('style') + activeTabStyle);
    </script>
</body>

</html>