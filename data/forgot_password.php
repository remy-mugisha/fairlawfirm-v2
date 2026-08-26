<?php
session_start();
require_once 'propertyMgt/config.php';

$phpmailerPath = __DIR__ . '/PHPMailer/src/';
if (!file_exists($phpmailerPath . 'Exception.php')) {
    die("PHPMailer files not found. Please download PHPMailer from https://github.com/PHPMailer/PHPMailer and place the library in a PHPMailer directory.");
}

require $phpmailerPath . 'Exception.php';
require $phpmailerPath . 'PHPMailer.php';
require $phpmailerPath . 'SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

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
    error_log("Database error: " . $e->getMessage());
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    
    try {
        $stmt = $conn->prepare("SELECT * FROM login WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $resetStmt = $conn->prepare("
                INSERT INTO password_reset (email, token, expiry) 
                VALUES (:email, :token, :expiry) 
                ON DUPLICATE KEY UPDATE token = :token, expiry = :expiry
            ");
            $resetStmt->bindParam(':email', $email);
            $resetStmt->bindParam(':token', $token);
            $resetStmt->bindParam(':expiry', $expiry);
            $resetStmt->execute();
            
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $resetLink = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
            
            $mail = new PHPMailer(true);
            
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.hostinger.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'noreply@fairlawfirmltd.com';
                $mail->Password   = 'your-email-password';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;
                
                $mail->setFrom('noreply@fairlawfirmltd.com', 'Fair Law Firm');
                $mail->addAddress($email);
                
                $mail->isHTML(true);
                $mail->Subject = 'Fair Law Firm - Password Reset';
                $mail->Body    = "<p>Hello,</p>
                    <p>You have requested to reset your password. Please click the link below to reset your password:</p>
                    <p><a href='$resetLink'>Reset Password</a></p>
                    <p>This link will expire in 1 hour.</p>
                    <p>If you did not request this password reset, please ignore this email.</p>
                    <p>Regards,<br>Fair Law Firm</p>";
                $mail->AltBody = "Hello,\n\nYou have requested to reset your password. Please use this link:\n$resetLink\n\nThis link expires in 1 hour.";
                
                $mail->send();
                
                $_SESSION['success_message'] = "Password reset instructions have been sent to your email.";
                header("Location: index.php");
                exit();
                
            } catch (Exception $e) {
                $_SESSION['reset_token'] = $token;
                header("Location: reset_password.php?token=" . $token);
                exit();
            }
        } else {
            $error_message = "The email address you entered is not registered. Please check your email or contact support.";
        }
    } catch (PDOException $e) {
        $error_message = "An error occurred. Please try again later.";
        error_log("Database Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Fair Law Firm - Forgot Password</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <style>
        :root {
            --flf-navy: #01166A;
            --flf-midnight: #07143F;
            --flf-royal: #18358F;
            --flf-blue: #E9EEFA;
            --flf-white: #FFFFFF;
            --flf-gold: #C8A951;
            --flf-slate: #536174;
            --flf-charcoal: #172033;
            --flf-muted: #6b7699;
            --flf-danger: #a12734;
            --flf-success: #1a7a4c;
            --flf-font-head: 'Cormorant Garamond', serif;
            --flf-font-body: 'DM Sans', sans-serif;
            --flf-radius: 14px;
            --flf-radius-sm: 8px;
        }
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            font-family: var(--flf-font-body);
            background: #f4f6fa;
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
        .flf-auth-icon i {
            font-size: 22px;
            color: var(--flf-gold);
        }
        .flf-auth-body {
            padding: 32px;
        }
        .flf-auth-info {
            font-family: var(--flf-font-body);
            font-size: 14px;
            color: var(--flf-muted);
            text-align: center;
            margin: 0 0 24px;
            line-height: 1.6;
        }
        .flf-field {
            margin-bottom: 20px;
        }
        .flf-field label {
            display: block;
            font-family: var(--flf-font-body);
            font-size: 13px;
            font-weight: 600;
            color: var(--flf-slate);
            margin-bottom: 7px;
        }
        .flf-field label i {
            color: var(--flf-gold);
            margin-right: 5px;
            width: 16px;
            text-align: center;
        }
        .flf-field .form-control {
            height: 46px;
            border: 1px solid #d9dee9;
            border-radius: var(--flf-radius-sm);
            font-family: var(--flf-font-body);
            font-size: 14px;
            color: var(--flf-charcoal);
            padding: 0 14px;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
            background: var(--flf-white);
        }
        .flf-field .form-control:focus {
            border-color: var(--flf-royal);
            box-shadow: 0 0 0 3px rgba(24, 53, 143, 0.12);
            outline: none;
        }
        .flf-btn-submit {
            width: 100%;
            padding: 12px;
            background: var(--flf-navy);
            border: none;
            border-radius: var(--flf-radius-sm);
            color: var(--flf-white);
            font-family: var(--flf-font-body);
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .flf-btn-submit:hover {
            background: var(--flf-midnight);
            box-shadow: 0 4px 14px rgba(1, 22, 106, 0.3);
            transform: translateY(-1px);
        }
        .flf-btn-submit:active { transform: translateY(0); }
        .flf-auth-footer {
            text-align: center;
            padding: 0 32px 28px;
        }
        .flf-back-link {
            font-family: var(--flf-font-body);
            font-size: 14px;
            color: var(--flf-navy);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease;
        }
        .flf-back-link:hover {
            color: var(--flf-gold);
            text-decoration: none;
        }
        .flf-back-link i { font-size: 12px; }
        .flf-alert {
            border-radius: var(--flf-radius-sm);
            padding: 12px 16px;
            margin-bottom: 20px;
            font-family: var(--flf-font-body);
            font-size: 13.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .flf-alert-danger {
            background: rgba(161, 39, 52, 0.08);
            color: var(--flf-danger);
            border: 1px solid rgba(161, 39, 52, 0.2);
        }
        .flf-alert-success {
            background: rgba(26, 122, 76, 0.08);
            color: var(--flf-success);
            border: 1px solid rgba(26, 122, 76, 0.2);
        }
        .flf-divider {
            border: none;
            border-top: 1px solid var(--flf-blue);
            margin: 0 0 20px;
        }
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
                    <h1 class="flf-auth-title">Forgot Password</h1>
                    <p class="flf-auth-tagline">Trusted by Professionals</p>
                </div>
                <div class="flf-auth-icon">
                    <i class="fa fa-lock"></i>
                </div>
            </div>

            <div class="flf-auth-body">
                <?php if (!empty($error_message)): ?>
                    <div class="flf-alert flf-alert-danger">
                        <i class="fa fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="flf-alert flf-alert-success">
                        <i class="fa fa-check-circle"></i>
                        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>

                <p class="flf-auth-info">Enter your email address and we'll send you instructions to reset your password.</p>

                <form method="POST" action="">
                    <div class="flf-field">
                        <label><i class="fa fa-envelope"></i>Email Address</label>
                        <input type="email" class="form-control" name="email" placeholder="you@example.com" required>
                    </div>

                    <hr class="flf-divider">

                    <button type="submit" class="flf-btn-submit">
                        <i class="fa fa-paper-plane"></i>Send Reset Link
                    </button>
                </form>
            </div>

            <div class="flf-auth-footer">
                <a href="index.php" class="flf-back-link">
                    <i class="fa fa-arrow-left"></i>Back to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>