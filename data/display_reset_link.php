<?php
session_start();
if (!isset($_SESSION['reset_link']) || ($_SERVER['SERVER_NAME'] != 'localhost' && $_SERVER['SERVER_NAME'] != '127.0.0.1')) {
    header("Location: index.php");
    exit();
}

$resetLink = $_SESSION['reset_link'];
$email = $_SESSION['reset_email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fair Law Firm - Password Reset Link</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="inner_page login">
    <div class="full_container">
        <div class="container">
            <div class="center verticle_center full_height">
                <div class="login_section">
                    <div class="logo_login">
                        <div class="center">
                            <h2 style="color: #fff;">Fair Law Firm</h2>
                        </div>
                    </div>
                    <div class="login_form">
                        <h3>Development Mode</h3>
                        <div class="alert alert-info">
                            <p>This page is only displayed in the development environment.</p>
                            <p>In production, the reset link would be sent to: <strong><?php echo htmlspecialchars($email); ?></strong></p>
                        </div>
                        
                        <div class="field">
                            <label class="label_field">Password Reset Link:</label>
                            <div class="p-3 bg-light border rounded">
                                <a href="<?php echo htmlspecialchars($resetLink); ?>" target="_blank"><?php echo htmlspecialchars($resetLink); ?></a>
                            </div>
                        </div>
                        
                        <div class="field margin_top_15">
                            <a href="<?php echo htmlspecialchars($resetLink); ?>" class="main_bt">Go to Reset Password Page</a>
                        </div>
                        
                        <div class="field margin_top_15">
                            <a href="index.php">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>