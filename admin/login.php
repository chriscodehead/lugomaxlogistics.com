<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

init_session();

$error = '';
$db = getDB();
//print $hashed_password = hash('haval160,4', '', FALSE);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $username = sanitize_input($username);
    $password = $_POST['password'] ?? '';
    $password_hashed = passwordHash($password);

    /* ========= VALIDATION ========= */

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } elseif (strlen($username) > 50 || strlen($password) > 100) {
        $error = "Invalid login data.";
    } else {

        /* ========= FETCH ADMIN ========= */
        $stmt = $db->prepare("
            SELECT id, username, password, status
            FROM users
            WHERE username = ?
            LIMIT 1
        ");

        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        /* ========= VERIFY ========= */
        if (
            $admin && $admin['status'] === 'active'
            && $password_hashed === $admin['password']
        ) {

            // Prevent session fixation
            session_regenerate_id(true);

            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_user'] = $admin['username'];
            $_SESSION['login_time'] = time();

            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $username = sanitize_input($_POST['username'] ?? '');
//     $password = $_POST['password'] ?? '';

//     // Simple hardcoded login for demo (you can change this)
//     if ($username === 'admin' && $password === 'admin') {
//         $_SESSION['admin_logged_in'] = true;
//         $_SESSION['admin_user'] = $username;
//         header("Location: index.php");
//         exit;
//     } else {
//         $error = 'Invalid username or password';
//     }
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Lugomax Logistics</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0A1F44 0%, #1a3a6b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            padding: 50px 40px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
        }

        .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo h1 {
            font-size: 2rem;
            color: #0A1F44;
            margin-bottom: 8px;
        }

        .logo p {
            color: #64748B;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #0A1F44;
            font-weight: 600;
            font-size: 0.95rem;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: inherit;
        }

        input:focus {
            outline: none;
            border-color: #FF6B2C;
            box-shadow: 0 0 0 3px rgba(255, 107, 44, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #FF6B2C;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: #e55a1f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 44, 0.3);
        }

        .error {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 0.9rem;
        }

        .back-link {
            text-align: center;
            margin-top: 24px;
        }

        .back-link a {
            color: #64748B;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link a:hover {
            color: #FF6B2C;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <h1>🚚 Lugomax Admin</h1>
            <p>Login to your dashboard</p>
        </div>

        <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-login">Login to Dashboard</button>
        </form>

        <div class="back-link">
            <a href="<?= SITE_URL ?>">← Back to Website</a>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; text-align: center;">
            <p style="color: #94a3b8; font-size: 0.85rem;">
                <strong>Demo Credentials:</strong><br>
                Username: admin<br>
                Password: admin123
            </p>
        </div>
    </div>
</body>

</html>