<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'propertyMgt/config.php';

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
    echo "Table creation error: " . $e->getMessage();
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
        $error_message = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Fair Law Firm - Reset Password</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: rgb(247, 247, 247);
            --secondary-color: #3498db;
            --light-color: #ecf0f1;
        }
        
        body {
            background-color: var(--primary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .auth-wrapper {
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .auth-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .auth-logo img {
            height: 60px;
            width: auto;
        }
        
        .auth-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
        }
        
        .auth-title {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
            font-weight: 600;
        }
        
        .form-control {
            height: 45px;
            border-radius: 5px;
            border: 1px solid #ddd;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: none;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border: none;
            color: white;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 20px;
        }
        
        .auth-link {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .auth-link:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 576px) {
            .auth-wrapper {
                padding: 15px;
            }
            
            .auth-card {
                padding: 20px;
            }
            
            .auth-logo img {
                height: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-logo">
            <img src="propertyMgt/logoImg/logo-0-0-0.png" alt="Fair Law Firm">
        </div>
        
        <div class="auth-card">
            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($valid_token) : ?>
                <!-- <h3 class="auth-title">Reset Your Password</h3> -->
                
                <form method="POST" action="">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" 
                               placeholder="Enter new password" required minlength="8">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               placeholder="Confirm new password" required minlength="8">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </form>
            <?php else : ?>
                <div class="alert alert-danger">
                    Invalid or expired password reset link. Please request a new password reset.
                </div>
                
                <a href="forgot_password.php" class="btn btn-primary">Request New Reset Link</a>
            <?php endif; ?>
            
            <div class="auth-footer">
                <a href="index.php" class="auth-link">Back to Login</a>
            </div>
        </div>
    </div>
    
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>