<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Hospital Information System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h1>Admin Portal</h1>
                <p>Hospital Information System</p>
            </div>

            <?php
            session_start();

            // Check if already logged in
            if (isset($_SESSION['admin_id'])) {
                header('Location: ../admin/dashboard.php');
                exit;
            }

            // Handle login
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                require_once '../config/database.php';

                $username = trim($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';

                if (empty($username) || empty($password)) {
                    $error = 'Please enter both username and password';
                } else {
                    $sql = "SELECT * FROM admins WHERE username = :username LIMIT 1";
                    $admin = getRow($pdo, $sql, ['username' => $username]);

                    if ($admin && password_verify($password, $admin['password'])) {
                        // Login successful
                        $_SESSION['admin_id'] = $admin['id'];
                        $_SESSION['admin_username'] = $admin['username'];
                        $_SESSION['admin_name'] = $admin['full_name'];
                        $_SESSION['admin_email'] = $admin['email'];
                        $_SESSION['user_type'] = 'admin';

                        // Redirect to admin dashboard
                        header('Location: ../admin/dashboard.php');
                        exit;
                    } else {
                        $error = 'Invalid username or password';
                    }
                }
            }
            ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>
                        <?php echo htmlspecialchars($error); ?>
                    </span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" data-validate>
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input type="text" id="username" name="username" class="form-control"
                        placeholder="Enter your username"
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                        required autofocus>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Login as Admin
                </button>
            </form>

            <div
                style="text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--gray-200);">
                <p class="text-muted" style="font-size: 0.875rem;">
                    Not an admin?
                    <a href="login_user.php" style="color: var(--primary-color); font-weight: 600;">Login as Patient</a>
                </p>
                <p class="text-muted" style="font-size: 0.75rem; margin-top: 1rem;">
                    Demo: Username: <strong>admin</strong> | Password: <strong>admin123</strong>
                </p>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>

</html>