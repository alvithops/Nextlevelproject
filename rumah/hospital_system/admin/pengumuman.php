<?php
/**
 * Admin Announcements Management
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
    $id = (int)$_GET['delete'];
    $sql = "DELETE FROM announcements WHERE id = :id";
    if (executeQuery($pdo, $sql, ['id' => $id])) {
        $success_message = 'Announcement deleted successfully';
    } else {
        $error_message = 'Failed to delete announcement';
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = $_POST['category'] ?? 'general';
    $priority = $_POST['priority'] ?? 'medium';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($title) || empty($content)) {
        $error_message = 'Title and content are required';
    } else {
        if ($id) {
            // Update
            $sql = "UPDATE announcements SET 
                    title = :title, 
                    content = :content, 
                    category = :category, 
                    priority = :priority, 
                    is_active = :is_active,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";
            $params = [
                'id' => $id,
                'title' => $title,
                'content' => $content,
                'category' => $category,
                'priority' => $priority,
                'is_active' => $is_active
            ];
            if (executeQuery($pdo, $sql, $params)) {
                $success_message = 'Announcement updated successfully';
            } else {
                $error_message = 'Failed to update announcement';
            }
        } else {
            // Insert
            $sql = "INSERT INTO announcements (title, content, category, priority, is_active, created_by) 
                    VALUES (:title, :content, :category, :priority, :is_active, :created_by)";
            $params = [
                'title' => $title,
                'content' => $content,
                'category' => $category,
                'priority' => $priority,
                'is_active' => $is_active,
                'created_by' => $_SESSION['admin_id']
            ];
            if (insertAndGetId($pdo, $sql, $params)) {
                $success_message = 'Announcement created successfully';
            } else {
                $error_message = 'Failed to create announcement';
            }
        }
    }
}

// Get all announcements
$announcements = getRows($pdo, "SELECT a.*, ad.full_name as created_by_name 
                                  FROM announcements a 
                                  LEFT JOIN admins ad ON a.created_by = ad.id 
                                  ORDER BY a.created_at DESC");

// Get announcement for editing
$edit_announcement = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_announcement = getRow($pdo, "SELECT * FROM announcements WHERE id = :id", ['id' => $edit_id]);
}

$page_title = "Announcements Management";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Hospital Information System</title>
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
                        <a href="pengumuman.php" class="sidebar-menu-link active">
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
                    <li class="sidebar-menu-item" style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
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
                    <h1 class="topbar-title">Announcements Management</h1>
                </div>
                <div class="topbar-right">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['admin_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="user-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
                            <div class="user-role">Administrator</div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo htmlspecialchars($success_message); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            <?php endif; ?>

            <!-- Add/Edit Form -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-<?php echo $edit_announcement ? 'edit' : 'plus'; ?>"></i>
                                <?php echo $edit_announcement ? 'Edit Announcement' : 'Add New Announcement'; ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" data-validate>
                                <?php if ($edit_announcement): ?>
                                    <input type="hidden" name="id" value="<?php echo $edit_announcement['id']; ?>">
                                <?php endif; ?>
                                
                                <div class="row">
                                    <div class="col-8">
                                        <div class="form-group">
                                            <label for="title" class="form-label">Title *</label>
                                            <input 
                                                type="text" 
                                                id="title" 
                                                name="title" 
                                                class="form-control" 
                                                placeholder="Enter announcement title"
                                                value="<?php echo htmlspecialchars($edit_announcement['title'] ?? ''); ?>"
                                                required
                                            >
                                        </div>
                                    </div>
                                    
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="category" class="form-label">Category *</label>
                                            <select id="category" name="category" class="form-control" required>
                                                <option value="general" <?php echo ($edit_announcement['category'] ?? '') === 'general' ? 'selected' : ''; ?>>General</option>
                                                <option value="emergency" <?php echo ($edit_announcement['category'] ?? '') === 'emergency' ? 'selected' : ''; ?>>Emergency</option>
                                                <option value="schedule" <?php echo ($edit_announcement['category'] ?? '') === 'schedule' ? 'selected' : ''; ?>>Schedule</option>
                                                <option value="facility" <?php echo ($edit_announcement['category'] ?? '') === 'facility' ? 'selected' : ''; ?>>Facility</option>
                                                <option value="health_tips" <?php echo ($edit_announcement['category'] ?? '') === 'health_tips' ? 'selected' : ''; ?>>Health Tips</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="content" class="form-label">Content *</label>
                                    <textarea 
                                        id="content" 
                                        name="content" 
                                        class="form-control" 
                                        rows="5"
                                        placeholder="Enter announcement content"
                                        required
                                    ><?php echo htmlspecialchars($edit_announcement['content'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="priority" class="form-label">Priority *</label>
                                            <select id="priority" name="priority" class="form-control" required>
                                                <option value="low" <?php echo ($edit_announcement['priority'] ?? '') === 'low' ? 'selected' : ''; ?>>Low</option>
                                                <option value="medium" <?php echo ($edit_announcement['priority'] ?? '') === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                                <option value="high" <?php echo ($edit_announcement['priority'] ?? '') === 'high' ? 'selected' : ''; ?>>High</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label">Status</label>
                                            <div style="padding: 0.75rem 0;">
                                                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                                    <input 
                                                        type="checkbox" 
                                                        name="is_active" 
                                                        <?php echo (($edit_announcement['is_active'] ?? 1) == 1) ? 'checked' : ''; ?>
                                                        style="width: 20px; height: 20px;"
                                                    >
                                                    <span>Active (visible to patients)</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> <?php echo $edit_announcement ? 'Update' : 'Create'; ?> Announcement
                                    </button>
                                    <?php if ($edit_announcement): ?>
                                        <a href="pengumuman.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Announcements List -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-list"></i> All Announcements
                            </h3>
                            <div>
                                <input 
                                    type="text" 
                                    id="searchInput" 
                                    class="form-control" 
                                    placeholder="Search announcements..."
                                    style="max-width: 300px;"
                                >
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="announcementTable">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($announcements): ?>
                                            <?php foreach ($announcements as $announcement): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($announcement['title']); ?></strong><br>
                                                        <small class="text-muted"><?php echo substr(htmlspecialchars($announcement['content']), 0, 80) . '...'; ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            <?php echo ucfirst(str_replace('_', ' ', $announcement['category'])); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-<?php 
                                                            echo $announcement['priority'] === 'high' ? 'danger' : 
                                                                ($announcement['priority'] === 'medium' ? 'warning' : 'secondary');
                                                        ?>">
                                                            <?php echo ucfirst($announcement['priority']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-<?php echo $announcement['is_active'] ? 'success' : 'secondary'; ?>">
                                                            <?php echo $announcement['is_active'] ? 'Active' : 'Inactive'; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($announcement['created_by_name']); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($announcement['created_at'])); ?></td>
                                                    <td>
                                                        <div class="d-flex gap-1">
                                                            <a href="?edit=<?php echo $announcement['id']; ?>" class="btn btn-sm btn-info">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a 
                                                                href="?delete=<?php echo $announcement['id']; ?>" 
                                                                class="btn btn-sm btn-danger"
                                                                data-delete-confirm="Are you sure you want to delete this announcement?"
                                                            >
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No announcements found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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
        
        // Initialize table search
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('announcementTable');
        if (searchInput && table) {
            initTableSearch(searchInput, table);
        }
    </script>
</body>
</html>
