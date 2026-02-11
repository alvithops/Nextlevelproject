<?php
/**
 * User/Patient Announcements View
 * Hospital Information System
 */

session_start();

// Check user authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'user') {
    header('Location: ../auth/login_user.php');
    exit;
}

require_once '../config/database.php';

// Get all active announcements
$announcements = getRows($pdo, "
    SELECT * FROM announcements 
    WHERE is_active = 1 
    ORDER BY priority DESC, created_at DESC
");

$page_title = "Hospital Announcements";
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
                        <a href="pengumuman.php" class="sidebar-menu-link active">
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
                    <h1 class="topbar-title">Hospital Announcements</h1>
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

            <!-- Search Box -->
            <div class="mb-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Search announcements..."
                    style="max-width: 400px;">
            </div>

            <!-- Announcements Grid -->
            <div class="row" id="announcementGrid">
                <?php if ($announcements): ?>
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="col-6">
                            <div class="card" style="height: 100%;">
                                <div class="card-header" style="background: linear-gradient(135deg, <?php
                                echo $announcement['priority'] === 'high' ? '#ef4444, #f87171' :
                                    ($announcement['priority'] === 'medium' ? '#f59e0b, #fbbf24' : '#6b7280, #9ca3af');
                                ?>); color: white; border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
                                    <div style="display: flex; justify-content: space-between; align-items: start;">
                                        <h3 class="card-title" style="color: white; margin: 0;">
                                            <?php echo htmlspecialchars($announcement['title']); ?>
                                        </h3>
                                        <span class="badge" style="background: rgba(255,255,255,0.3);">
                                            <?php echo ucfirst(str_replace('_', ' ', $announcement['category'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p style="color: var(--gray-700); line-height: 1.7; white-space: pre-wrap;">
                                        <?php echo htmlspecialchars($announcement['content']); ?>
                                    </p>
                                </div>
                                <div class="card-footer" style="background: var(--gray-50);">
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; color: var(--gray-600);">
                                        <div>
                                            <i class="fas fa-clock"></i>
                                            <?php echo date('F d, Y | H:i', strtotime($announcement['created_at'])); ?>
                                        </div>
                                        <span class="badge badge-<?php
                                        echo $announcement['priority'] === 'high' ? 'danger' :
                                            ($announcement['priority'] === 'medium' ? 'warning' : 'secondary');
                                        ?>">
                                            <?php echo ucfirst($announcement['priority']); ?> Priority
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body" style="padding: 4rem; text-align: center;">
                                <i class="fas fa-bell-slash"
                                    style="font-size: 5rem; color: var(--gray-300); margin-bottom: 1rem;"></i>
                                <h3 style="color: var(--gray-600);">No Announcements Available</h3>
                                <p class="text-muted">There are currently no active announcements from the hospital.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        // Make sidebar toggle visible on mobile
        if (window.innerWidth <= 768) {
            document.querySelector('.sidebar-toggle').style.display = 'inline-flex';
        }

        // Search functionality for announcement cards
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function (e) {
                const searchTerm = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('#announcementGrid > div');

                cards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            }, 300));
        }
    </script>
</body>

</html>