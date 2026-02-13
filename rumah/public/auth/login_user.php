<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Login - Hospital Information System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="login-container user-login">
        <div class="login-box">
            <div class="login-header">
                <div class="logo" style="background: linear-gradient(135deg, #10b981, #34d399);">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <h1>Patient Portal</h1>
                <p>Access Your Medical Information</p>
            </div>

            <?php
            session_start();

            // Check if already logged in
            if (isset($_SESSION['user_id'])) {
                header('Location: ../user/dashboard.php');
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
                    $sql = "SELECT u.*, p.* FROM users u 
                            LEFT JOIN patients p ON u.id = p.user_id 
                            WHERE u.username = :username LIMIT 1";
                    $user = getRow($pdo, $sql, ['username' => $username]);

                    if ($user && password_verify($password, $user['password'])) {
                        // Login successful
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['patient_id'] = $user['patient_id'] ?? $user['id'];
                        $_SESSION['patient_name'] = $user['full_name'] ?? 'Patient';
                        $_SESSION['user_type'] = 'user';

                        // Redirect to user dashboard
                        header('Location: ../user/dashboard.php');
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

                <button type="submit" class="btn btn-secondary btn-block btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Login as Patient
                </button>
            </form>

            <div
                style="text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--gray-200);">
                <p class="text-muted" style="font-size: 0.875rem;">
                    Are you an admin?
                    <a href="login_admin.php" style="color: var(--secondary-color); font-weight: 600;">Login as
                        Admin</a>
                </p>
                <p class="text-muted" style="font-size: 0.75rem; margin-top: 1rem;">
                    Demo: Username: <strong>john.doe</strong> | Password: <strong>patient123</strong>
                </p>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>

</html>