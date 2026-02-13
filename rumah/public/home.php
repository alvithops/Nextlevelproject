<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Information System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,197.3C1248,203,1344,149,1392,122.7L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
            opacity: 0.3;
        }

        .hero-content {
            background: white;
            border-radius: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 3rem;
            max-width: 900px;
            width: 100%;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hospital-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .login-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 3rem;
        }

        .login-card {
            padding: 2rem;
            border-radius: 1rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid var(--gray-200);
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .login-card.admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }

        .login-card.patient {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
            border-color: transparent;
        }

        .login-card-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .login-card h3 {
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }

        .features {
            margin-top: 3rem;
            padding-top: 3rem;
            border-top: 2px solid var(--gray-200);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .feature-item {
            text-align: center;
            padding: 1rem;
        }

        .feature-icon {
            font-size: 2rem;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .hero-content {
                padding: 2rem;
            }

            .login-options {
                grid-template-columns: 1fr;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="hero-section">
        <div class="hero-content">
            <div class="hospital-icon">
                <i class="fas fa-hospital"></i>
            </div>

            <h1 style="text-align: center; font-size: 2.5rem; color: var(--gray-900); margin-bottom: 1rem;">
                Hospital Information System
            </h1>

            <p
                style="text-align: center; font-size: 1.125rem; color: var(--gray-600); max-width: 600px; margin: 0 auto;">
                Welcome to our modern hospital management system. Access your medical records, view announcements, and
                manage appointments with ease.
            </p>

            <div class="login-options">
                <a href="auth/login_admin.php" class="login-card admin">
                    <div class="login-card-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3>Admin Portal</h3>
                    <p>Manage patients, appointments, and announcements</p>
                    <div style="margin-top: 1rem;">
                        <i class="fas fa-arrow-right"></i> Login as Administrator
                    </div>
                </a>

                <a href="auth/login_user.php" class="login-card patient">
                    <div class="login-card-icon">
                        <i class="fas fa-hospital-user"></i>
                    </div>
                    <h3>Patient Portal</h3>
                    <p>View your medical records and appointments</p>
                    <div style="margin-top: 1rem;">
                        <i class="fas fa-arrow-right"></i> Login as Patient
                    </div>
                </a>
            </div>

            <div class="features">
                <h2 style="text-align: center; color: var(--gray-900); margin-bottom: 1rem;">
                    System Features
                </h2>

                <div class="features-grid">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h4 style="color: var(--gray-900); margin-bottom: 0.5rem;">Announcements</h4>
                        <p style="color: var(--gray-600); font-size: 0.875rem;">Stay updated with hospital news and
                            information</p>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h4 style="color: var(--gray-900); margin-bottom: 0.5rem;">Appointments</h4>
                        <p style="color: var(--gray-600); font-size: 0.875rem;">Schedule and manage your consultations
                        </p>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h4 style="color: var(--gray-900); margin-bottom: 0.5rem;">Patient Records</h4>
                        <p style="color: var(--gray-600); font-size: 0.875rem;">Secure access to medical information</p>
                    </div>
                </div>
            </div>

            <div
                style="margin-top: 3rem; padding: 1.5rem; background: var(--gray-50); border-radius: 1rem; text-align: center;">
                <p style="color: var(--gray-700); margin-bottom: 0.5rem;">
                    <i class="fas fa-info-circle text-primary"></i> Demo Credentials
                </p>
                <div
                    style="display: flex; justify-content: center; gap: 3rem; flex-wrap: wrap; margin-top: 1rem; font-size: 0.875rem;">
                    <div>
                        <strong>Admin:</strong> admin / admin123
                    </div>
                    <div>
                        <strong>Patient:</strong> john.doe / patient123
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>