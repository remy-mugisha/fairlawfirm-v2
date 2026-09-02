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
    <link rel="shortcut icon" href="images/logo/logo_icon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap5.min.css">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--fl-page);
        }
        .flf-dev-card {
            width: 100%;
            max-width: 560px;
            background: var(--fl-surface);
            border: 1px solid var(--fl-line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }
        .flf-dev-head {
            background: linear-gradient(135deg, var(--fl-primary) 0%, var(--fl-primary-dark) 100%);
            padding: var(--sp-6);
            text-align: center;
        }
        .flf-dev-head h1 {
            font-family: var(--font-heading);
            font-weight: var(--fw-bold);
            font-size: var(--fs-h2);
            color: var(--fl-surface);
            margin: 0;
        }
        .flf-dev-head p {
            color: var(--fl-accent);
            letter-spacing: var(--ls-caps);
            text-transform: uppercase;
            font-size: var(--fs-caption);
            font-weight: var(--fw-semibold);
            margin: var(--sp-2) 0 0;
        }
        .flf-dev-body { padding: var(--sp-6); }
        .flf-dev-link {
            font-family: var(--font-mono);
            font-size: var(--fs-small);
            word-break: break-all;
            padding: var(--sp-4);
            background: var(--fl-surface-muted);
            border: 1px solid var(--fl-line);
            border-radius: var(--radius-sm);
            display: block;
        }
        .flf-dev-body .btn { min-width: 220px; }
        .flf-dev-back { color: var(--fl-ink-muted); font-size: var(--fs-body); }
    </style>
</head>
<body>
    <div class="p-3" style="width:100%;">
        <div class="flf-dev-card mx-auto">
            <div class="flf-dev-head">
                <h1>Development Mode</h1>
                <p>Reset link</p>
            </div>
            <div class="flf-dev-body">
                <div class="alert alert-info" role="alert">
                    <i class="fa fa-info-circle"></i>
                    This page is only displayed in the development environment. In production, the reset link would be sent to <strong><?php echo htmlspecialchars($email); ?></strong>.
                </div>

                <label class="form-label" for="reset-link">Password Reset Link</label>
                <a href="<?php echo htmlspecialchars($resetLink); ?>" id="reset-link" class="flf-dev-link mb-4" target="_blank"><?php echo htmlspecialchars($resetLink); ?></a>

                <div class="d-flex flex-wrap gap-2 justify-content-start mb-4">
                    <a href="<?php echo htmlspecialchars($resetLink); ?>" class="btn btn-primary">
                        <i class="fa fa-key"></i> Go to Reset Password Page
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>