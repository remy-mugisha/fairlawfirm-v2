<?php
session_start();

require_once 'propertyMgt/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];

    try {
        // Check if user exists in 'users' table
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user_exists = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user_exists) {
            $error_message = "Your account has been deleted. Please contact support.";
        } else {
            // Fetch login details
            $stmt = $conn->prepare("SELECT l.*, u.first_name, u.last_name, u.profile_image, u.status 
                                    FROM login l
                                    LEFT JOIN users u ON l.email = u.email
                                    WHERE l.email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if ($user['status'] !== 'Active') {
                    $error_message = "Your account is not active yet. Please contact the administrator.";
                } elseif (password_verify($password, $user['password'])) {
                    $_SESSION['user_type'] = $user['usertype'];
                    $_SESSION['email'] = $user['email'];
                    
                    if (isset($user['first_name'])) {
                        $_SESSION['first_name'] = $user['first_name'];
                        $_SESSION['last_name'] = $user['last_name'];
                        $_SESSION['profile_image'] = $user['profile_image'];
                    }

                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error_message = "Invalid email or password";
                }
            } else {
                $error_message = "User not found";
            }
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
    <title>Fair Law Firm - Login</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: #f7f7f7;
            --secondary-color: #3498db;
            --light-color: #ecf0f1;
        }
        
        body {
            background-color: var(--primary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        .login-wrapper {
            width: 100%;
            max-width: 450px;
            /* text-align: center; */
        }
        
        .logo-container {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .logo-container img {
            max-width: 200px;
            height: auto;
        }
        
        .login-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
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
        
        .btn-login {
            background-color: var(--secondary-color);
            border: none;
            color: white;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input {
            margin-right: 5px;
        }
        
        .forgot-password {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 576px) {
            .login-card {
                padding: 20px;
            }
            
            .form-footer {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .forgot-password {
                margin-top: 10px;
            }
            
            .logo-container {
                margin-bottom: 20px;
            }
            
            .logo-container img {
                max-width: 180px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="logo-container">
            <img src="propertyMgt/logoImg/logo-0-0-0.png" alt="Fair Law Firm Logo">
        </div>
        
        <div class="login-card">
            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($_SESSION['success_message'])) : ?>
                <div class="alert alert-success">
                    <?php 
                    echo htmlspecialchars($_SESSION['success_message']); 
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <div class="form-footer">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn btn-login">Login</button>
            </form>
        </div>
    </div>
    
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>