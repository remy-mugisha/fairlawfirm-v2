<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
require_once 'propertyMgt/config.php';
require_once __DIR__ . '/csrf.php';

date_default_timezone_set('UTC');

try {
    $conn->exec("
        CREATE TABLE IF NOT EXISTS password_reset (
            email VARCHAR(255) PRIMARY KEY,
            token VARCHAR(255) NOT NULL,
            expiry DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
} catch (PDOException $e) {
    error_log("Table creation error: " . $e->getMessage());
}

$token = isset($_GET['token']) ? $_GET['token'] : null;
$valid_token = false;

if ($token) {
    try {
        $stmt = $conn->prepare("SELECT * FROM password_reset WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $reset_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $expiry = strtotime($reset_data['expiry']);
            $current_time = time();
            
            if ($expiry > $current_time) {
                $valid_token = true;
                $email = $reset_data['email'];
            }
        }
    } catch (PDOException $e) {
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['token'])) {
    requireCsrfPost();
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    try {
        $stmt = $conn->prepare("SELECT * FROM password_reset WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $reset_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $expiry = strtotime($reset_data['expiry']);
            $current_time = time();
            
            if ($expiry > $current_time) {
                $email = $reset_data['email'];
                
                if ($new_password === $confirm_password) {
                    if (strlen($new_password) >= 8) {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        
                        $updateStmt = $conn->prepare("UPDATE login SET password = :password WHERE email = :email");
                        $updateStmt->bindParam(':password', $hashed_password);
                        $updateStmt->bindParam(':email', $email);
                        $updateStmt->execute();
                        
                        $deleteStmt = $conn->prepare("DELETE FROM password_reset WHERE token = :token");
                        $deleteStmt->bindParam(':token', $token);
                        $deleteStmt->execute();
                        
                        $_SESSION['success_message'] = "Your password has been reset successfully. You can now login with your new password.";
                        header("Location: index.php");
                        exit();
                    } else {
                        $error_message = "Password must be at least 8 characters long.";
                    }
                } else {
                    $error_message = "Passwords do not match.";
                }
            } else {
                $error_message = "Invalid or expired token.";
            }
        } else {
            $error_message = "Invalid or expired token.";
        }
    } catch (PDOException $e) {
        error_log("Password reset error: " . $e->getMessage());
        $error_message = "A database error occurred. Please try again later.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Fair Law Firm - Reset Password</title>
    <link rel="shortcut icon" href="images/logo/logo_icon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap5.min.css">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            font-family: var(--flf-font-body);
            background: var(--fl-page);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .flf-auth-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }
        .flf-auth-card {
            background: var(--flf-white);
            border: 1px solid var(--flf-blue);
            border-radius: var(--flf-radius);
            box-shadow: 0 8px 30px rgba(1, 22, 106, 0.08);
            overflow: hidden;
        }
        .flf-auth-header {
            background: linear-gradient(135deg, var(--flf-navy) 0%, var(--flf-midnight) 100%);
            padding: 32px 32px 28px;
            text-align: center;
        }
        .flf-auth-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        .flf-auth-logo {
            height: 52px;
            width: auto;
            filter: brightness(0) invert(1);
        }
        .flf-auth-title {
            font-family: var(--flf-font-head);
            font-size: 24px;
            font-weight: 700;
            color: var(--flf-white);
            margin: 0;
            letter-spacing: 0.5px;
        }
        .flf-auth-tagline {
            font-family: var(--flf-font-body);
            font-size: 12px;
            font-weight: 600;
            color: var(--flf-gold);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 4px 0 0;
        }
        .flf-auth-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: rgba(200, 169, 81, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 16px auto 0;
        }
        .flf-auth-icon i { font-size: 22px; color: var(--flf-gold); }
        .flf-auth-body { padding: 32px; }
        .flf-field { margin-bottom: 20px; }
        .flf-field label {
            display: block; font-family: var(--flf-font-body); font-size: 13px;
            font-weight: 600; color: var(--flf-slate); margin-bottom: 7px;
        }
        .flf-field label i { color: var(--flf-gold); margin-right: 5px; width: 16px; text-align: center; }
        .flf-input-wrap {
            position: relative;
        }
        .flf-field .form-control {
            height: 46px; border: 1px solid var(--fl-line); border-radius: var(--flf-radius-sm);
            font-family: var(--flf-font-body); font-size: 14px; color: var(--flf-charcoal);
            padding: 0 44px 0 14px; transition: border-color 0.25s ease, box-shadow 0.25s ease;
            background: var(--flf-white); width: 100%;
        }
        .flf-field .form-control:focus { border-color: var(--flf-royal); box-shadow: 0 0 0 3px rgba(24, 53, 143, 0.12); outline: none; }
        .flf-pw-toggle {
            position: absolute; right: 0; top: 0; bottom: 0; width: 44px;
            display: flex; align-items: center; justify-content: center;
            background: none; border: none; cursor: pointer; color: var(--flf-muted);
            font-size: 16px; transition: color 0.2s ease;
        }
        .flf-pw-toggle:hover { color: var(--flf-navy); }
        .flf-btn-submit {
            width: 100%; padding: 12px; background: var(--flf-navy); border: none;
            border-radius: var(--flf-radius-sm); color: var(--flf-white);
            font-family: var(--flf-font-body); font-size: 15px; font-weight: 600;
            cursor: pointer; transition: all 0.25s ease; display: flex;
            align-items: center; justify-content: center; gap: 8px;
        }
        .flf-btn-submit:hover { background: var(--flf-midnight); box-shadow: 0 4px 14px rgba(1, 22, 106, 0.3); transform: translateY(-1px); }
        .flf-btn-submit:active { transform: translateY(0); }
        .flf-auth-footer { text-align: center; padding: 0 32px 28px; }
        .flf-back-link {
            font-family: var(--flf-font-body); font-size: 14px; color: var(--flf-navy);
            text-decoration: none; font-weight: 500; display: inline-flex;
            align-items: center; gap: 6px; transition: color 0.2s ease;
        }
        .flf-back-link:hover { color: var(--flf-gold); text-decoration: none; }
        .flf-back-link i { font-size: 12px; }
        .flf-alert {
            border-radius: var(--flf-radius-sm); padding: 12px 16px; margin-bottom: 20px;
            font-family: var(--flf-font-body); font-size: 13.5px; display: flex;
            align-items: center; gap: 8px;
        }
        .flf-alert-danger { background: rgba(161, 39, 52, 0.08); color: var(--flf-danger); border: 1px solid rgba(161, 39, 52, 0.2); }
        .flf-alert-success { background: rgba(26, 122, 76, 0.08); color: var(--flf-success); border: 1px solid rgba(26, 122, 76, 0.2); }
        .flf-alert-info { background: rgba(24, 53, 143, 0.08); color: var(--flf-royal); border: 1px solid rgba(24, 53, 143, 0.2); }
        .flf-divider { border: none; border-top: 1px solid var(--flf-blue); margin: 0 0 20px; }
        .flf-expired-card { text-align: center; padding: 24px 0 4px; }
        .flf-expired-card i { font-size: 48px; color: var(--flf-danger); opacity: 0.6; display: block; margin-bottom: 16px; }
        .flf-expired-card p { font-family: var(--flf-font-body); font-size: 14px; color: var(--flf-muted); margin: 0 0 20px; }
        .flf-btn-outline {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; border: 1px solid var(--flf-navy); border-radius: var(--flf-radius-sm);
            color: var(--flf-navy); font-family: var(--flf-font-body); font-size: 14px;
            font-weight: 600; text-decoration: none; transition: all 0.25s ease;
        }
        .flf-btn-outline:hover { background: var(--flf-navy); color: var(--flf-white); text-decoration: none; }
        @media (max-width: 480px) {
            .flf-auth-wrapper { padding: 12px; }
            .flf-auth-header { padding: 24px 20px 20px; }
            .flf-auth-body { padding: 24px 20px; }
            .flf-auth-footer { padding: 0 20px 24px; }
        }
    </style>
</head>
<body>
    <div class="flf-auth-wrapper">
        <div class="flf-auth-card">
            <div class="flf-auth-header">
                <div class="flf-auth-brand">
                    <img src="propertyMgt/logoImg/logo-0-0-0.png" alt="Fair Law Firm" class="flf-auth-logo">
                    <h1 class="flf-auth-title">Reset Password</h1>
                    <p class="flf-auth-tagline">Trusted by Professionals</p>
                </div>
                <div class="flf-auth-icon">
                    <i class="fa fa-key"></i>
                </div>
            </div>

            <div class="flf-auth-body">
                <?php if (!empty($error_message)): ?>
                    <div class="flf-alert flf-alert-danger">
                        <i class="fa fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($valid_token): ?>
                    <form method="POST" action=""><?php echo csrfHiddenField(); ?>
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                        <div class="flf-field">
                            <label><i class="fa fa-lock"></i>New Password</label>
                            <div class="flf-input-wrap">
                                <input type="password" class="form-control" id="new_password" name="new_password"
                                       placeholder="Enter new password" required minlength="8">
                                <button type="button" class="flf-pw-toggle" onclick="togglePw('new_password', this)" title="Show password">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flf-field">
                            <label><i class="fa fa-lock"></i>Confirm Password</label>
                            <div class="flf-input-wrap">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                       placeholder="Confirm new password" required minlength="8">
                                <button type="button" class="flf-pw-toggle" onclick="togglePw('confirm_password', this)" title="Show password">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flf-field" style="margin-bottom:0;">
                            <div class="flf-alert flf-alert-info" style="margin-bottom:0;font-size:12.5px;">
                                <i class="fa fa-info-circle"></i>Password must be at least 8 characters long.
                            </div>
                        </div>

                        <hr class="flf-divider" style="margin-top:20px;">

                        <button type="submit" class="flf-btn-submit">
                            <i class="fa fa-check-circle"></i>Reset Password
                        </button>
                    </form>

                <?php else: ?>
                    <div class="flf-expired-card">
                        <i class="fa fa-times-circle"></i>
                        <p>Invalid or expired password reset link. Please request a new password reset.</p>
                        <a href="forgot_password.php" class="flf-btn-outline">
                            <i class="fa fa-refresh"></i>Request New Reset Link
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flf-auth-footer">
                <a href="index.php" class="flf-back-link">
                    <i class="fa fa-arrow-left"></i>Back to Login
                </a>
            </div>
        </div>
    </div>

    <script>
    function togglePw(inputId, btn) {
        var input = document.getElementById(inputId);
        var icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa fa-eye-slash';
            btn.title = 'Hide password';
        } else {
            input.type = 'password';
            icon.className = 'fa fa-eye';
            btn.title = 'Show password';
        }
    }
    </script>
</body>
</html>