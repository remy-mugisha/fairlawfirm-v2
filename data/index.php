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
    <title>Sign In | Fair Law Firm LTD</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --flf-navy: #01166A;
            --flf-navy-dark: #010F47;
            --flf-blue: #E9EEFA;
            --flf-gold: #C8A951;
            --flf-muted: #6b7699;
            --flf-muted-light: #7c88ab;
        }

        * { box-sizing: border-box; }

        body.flf-login-body {
            margin: 0;
            min-height: 100vh;
            font-family: 'DM Sans', sans-serif;
            background: var(--flf-blue);
        }

        .flf-split {
            display: grid;
            grid-template-columns: minmax(420px, 46%) 1fr;
            min-height: 100vh;
        }

        /* ================= LEFT — BRAND PANE ================= */
        .flf-brand-pane {
            position: relative;
            overflow: hidden;
            background: linear-gradient(160deg, var(--flf-navy) 0%, var(--flf-navy-dark) 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px clamp(36px, 6vw, 90px);
        }

        .flf-brand-pane::after {
            content: "\f0a9";
            font-family: 'FontAwesome';
            position: absolute;
            right: -70px;
            bottom: -50px;
            font-size: 340px;
            color: rgba(200, 169, 81, 0.07);
            transform: rotate(-18deg);
            pointer-events: none;
        }

        .flf-logo-tile {
            align-self: flex-start;
            background: #fff;
            border-radius: 16px;
            padding: 14px 22px;
            margin-bottom: 38px;
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.28);
        }
        .flf-logo-tile img {
            height: 52px;
            width: auto;
            display: block;
        }

        .flf-brand-headline {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 700;
            font-size: clamp(32px, 3.4vw, 44px);
            line-height: 1.15;
            margin: 0 0 16px;
            position: relative;
            z-index: 1;
        }
        .flf-brand-headline em {
            font-style: normal;
            color: var(--flf-gold);
        }

        .flf-brand-copy {
            font-size: 15.5px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.78);
            max-width: 430px;
            margin: 0 0 26px;
            position: relative;
            z-index: 1;
        }

        .flf-brand-rule {
            width: 70px;
            height: 3px;
            border-radius: 2px;
            background: var(--flf-gold);
            border: none;
            margin: 0 0 28px;
        }

        .flf-feature-list {
            list-style: none;
            margin: 0;
            padding: 0;
            position: relative;
            z-index: 1;
        }
        .flf-feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 13px;
            margin-bottom: 17px;
            font-size: 14.5px;
            color: rgba(255, 255, 255, 0.88);
        }
        .flf-feature-list i {
            color: var(--flf-gold);
            font-size: 17px;
            margin-top: 1px;
        }

        .flf-brand-foot {
            position: absolute;
            left: clamp(36px, 6vw, 90px);
            bottom: 26px;
            font-size: 12px;
            letter-spacing: 0.4px;
            color: rgba(255, 255, 255, 0.45);
        }

        /* ================= RIGHT — FORM PANE ================= */
        .flf-form-pane {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
            background:
                radial-gradient(circle at 85% 12%, rgba(200, 169, 81, 0.10) 0%, rgba(200, 169, 81, 0) 42%),
                #f7f9fd;
        }

        .flf-form-card {
            width: 100%;
            max-width: 420px;
        }

        .flf-form-mark {
            display: none;
            margin-bottom: 30px;
        }

        .flf-form-title {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 700;
            font-size: 34px;
            color: var(--flf-navy);
            margin: 0 0 6px;
        }
        .flf-form-title::after {
            content: "";
            display: block;
            width: 54px;
            height: 3px;
            background: var(--flf-gold);
            margin-top: 12px;
            border-radius: 2px;
        }
        .flf-form-sub {
            font-size: 14.5px;
            color: var(--flf-muted);
            margin: 14px 0 30px;
        }

        .flf-alert {
            border-radius: 10px;
            font-size: 13.5px;
            padding: 12px 16px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }
        .flf-alert i { margin-right: 8px; }
        .flf-alert-danger {
            background: #fdf0f1;
            border-color: #f3d2d5;
            color: #a12734;
        }
        .flf-alert-success {
            background: #eefaf3;
            border-color: #cdeedd;
            color: #1c7c4d;
        }

        .flf-field {
            margin-bottom: 20px;
        }
        .flf-field label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.1px;
            text-transform: uppercase;
            color: var(--flf-navy);
            margin-bottom: 8px;
        }

        .flf-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .flf-input-wrap > i {
            position: absolute;
            left: 15px;
            color: var(--flf-muted-light);
            font-size: 15px;
            transition: color 0.25s ease;
            z-index: 2;
        }
        .flf-input {
            width: 100%;
            height: 48px;
            border: 1px solid #dbe3f2;
            border-radius: 10px;
            background: #fff;
            padding: 0 44px 0 42px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14.5px;
            color: var(--flf-navy);
            outline: none;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }
        .flf-input::placeholder { color: #a5aec7; }
        .flf-input:focus {
            border-color: var(--flf-gold);
            box-shadow: 0 0 0 3px rgba(200, 169, 81, 0.18);
        }
        .flf-input:focus + i,
        .flf-input:focus ~ i { color: var(--flf-navy); }

        .flf-toggle-pass {
            position: absolute;
            right: 6px;
            border: none;
            background: transparent;
            cursor: pointer;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            color: var(--flf-muted-light);
            font-size: 15px;
            transition: all 0.25s ease;
        }
        .flf-toggle-pass:hover {
            color: var(--flf-navy);
            background: var(--flf-blue);
        }

        .flf-form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 4px 0 24px;
        }

        .flf-remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            color: var(--flf-muted);
            cursor: pointer;
            margin: 0;
        }
        .flf-remember input {
            width: 16px;
            height: 16px;
            accent-color: var(--flf-navy);
            cursor: pointer;
        }

        .flf-forgot {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--flf-navy);
            text-decoration: none;
            border-bottom: 2px solid transparent;
            transition: all 0.25s ease;
            white-space: nowrap;
        }
        .flf-forgot:hover {
            color: var(--flf-gold);
            border-bottom-color: var(--flf-gold);
            text-decoration: none;
        }

        .flf-signin-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            height: 50px;
            background: var(--flf-navy);
            border: none;
            border-bottom: 3px solid var(--flf-gold);
            border-radius: 10px;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.6px;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .flf-signin-btn:hover {
            background: var(--flf-navy-dark);
            color: var(--flf-gold);
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(1, 22, 106, 0.28);
        }
        .flf-signin-btn:focus { outline: none; }
        .flf-signin-btn[disabled] {
            opacity: 0.75;
            cursor: default;
            transform: none;
        }

        .flf-secure-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 22px;
            font-size: 12.5px;
            color: var(--flf-muted-light);
        }
        .flf-secure-note i { color: var(--flf-gold); }

        .flf-form-foot {
            margin-top: 26px;
            padding-top: 18px;
            border-top: 1px solid #e4eaf6;
            text-align: center;
            font-size: 12.5px;
            color: var(--flf-muted-light);
        }
        .flf-form-foot a {
            color: var(--flf-navy);
            font-weight: 700;
            text-decoration: none;
        }
        .flf-form-foot a:hover { color: var(--flf-gold); }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 991px) {
            .flf-split { grid-template-columns: 1fr; }

            .flf-brand-pane {
                padding: 34px 24px 40px;
                justify-content: flex-start;
            }
            .flf-logo-tile {
                margin-bottom: 22px;
                padding: 10px 16px;
            }
            .flf-logo-tile img { height: 42px; }
            .flf-brand-headline { font-size: 27px; margin-bottom: 8px; }
            .flf-brand-rule { margin-bottom: 0; }
            .flf-hide-sm { display: none; }
            .flf-brand-foot { position: static; margin-top: 24px; }

            .flf-form-pane { padding: 40px 24px 56px; }
            .flf-form-card { max-width: 460px; }
            .flf-form-mark { display: block; }
            .flf-form-mark img { height: 40px; }
        }

        @media (max-width: 400px) {
            .flf-form-row { flex-direction: column; align-items: flex-start; gap: 14px; }
        }
    </style>
</head>
<body class="flf-login-body">
    <div class="flf-split">

        <!-- Left: brand pane -->
        <aside class="flf-brand-pane">
            <div class="flf-logo-tile">
                <img src="propertyMgt/logoImg/logo-0-0-0.png" alt="Fair Law Firm LTD Logo">
            </div>

            <h1 class="flf-brand-headline">Counsel built on <em>trust</em>,<br>accessed with confidence.</h1>
            <p class="flf-brand-copy flf-hide-sm">
                The Fair Law Firm LTD administration portal brings property management, rental listings
                and firm publications together in one secure workspace.
            </p>

            <hr class="flf-brand-rule flf-hide-sm">

            <ul class="flf-feature-list flf-hide-sm">
                <li><i class="fa fa-shield"></i><span>Secure, permission-controlled client portal</span></li>
                <li><i class="fa fa-building"></i><span>Centralised property &amp; rental management</span></li>
                <li><i class="fa fa-newspaper-o"></i><span>Publish firm news, rulings and insights</span></li>
            </ul>

            <p class="flf-brand-foot">&copy; <?php echo date('Y'); ?> Fair Law Firm LTD &middot; Kigali, Rwanda</p>
        </aside>

        <!-- Right: login form -->
        <main class="flf-form-pane">
            <div class="flf-form-card">

                <div class="flf-form-mark">
                    <img src="propertyMgt/logoImg/logo-0-0-0.png" alt="Fair Law Firm LTD">
                </div>

                <h2 class="flf-form-title">Welcome back</h2>
                <p class="flf-form-sub">Sign in to your account to continue to the admin portal.</p>

                <?php if (!empty($error_message)) : ?>
                    <div class="flf-alert flf-alert-danger">
                        <i class="fa fa-exclamation-circle"></i><?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($_SESSION['success_message'])) : ?>
                    <div class="flf-alert flf-alert-success">
                        <i class="fa fa-check-circle"></i><?php
                        echo htmlspecialchars($_SESSION['success_message']);
                        unset($_SESSION['success_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="flfLoginForm" autocomplete="on">
                    <div class="flf-field">
                        <label for="email">Email Address</label>
                        <div class="flf-input-wrap">
                            <input type="email" class="flf-input" id="email" name="email"
                                   placeholder="you@fairlawfirmltd.com" required autofocus>
                            <i class="fa fa-envelope-o"></i>
                        </div>
                    </div>

                    <div class="flf-field">
                        <label for="password">Password</label>
                        <div class="flf-input-wrap">
                            <input type="password" class="flf-input" id="password" name="password"
                                   placeholder="Enter your password" required>
                            <button type="button" class="flf-toggle-pass" id="togglePass"
                                    aria-label="Show or hide password"><i class="fa fa-eye"></i></button>
                            <i class="fa fa-key" style="right:44px;left:auto;"></i>
                        </div>
                    </div>

                    <div class="flf-form-row">
                        <label class="flf-remember" for="remember">
                            <input type="checkbox" id="remember" name="remember"> Remember me
                        </label>
                        <a href="forgot_password.php" class="flf-forgot">Forgot password?</a>
                    </div>

                    <button type="submit" class="flf-signin-btn" id="signinBtn">
                        <span>Sign In</span> <i class="fa fa-arrow-right"></i>
                    </button>
                </form>

                <div class="flf-secure-note">
                    <i class="fa fa-lock"></i> Authorized personnel only &mdash; all activity is monitored
                </div>

                <p class="flf-form-foot">
                    &copy; <?php echo date('Y'); ?> <a href="https://fairlawfirmltd.com/" target="_blank" rel="noopener">Fair Law Firm LTD</a> &middot; All rights reserved
                </p>

            </div>
        </main>
    </div>

    <script>
    (function () {
        'use strict';

        var passInput = document.getElementById('password');
        var toggleBtn = document.getElementById('togglePass');
        if (passInput && toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                var showing = passInput.type === 'text';
                passInput.type = showing ? 'password' : 'text';
                toggleBtn.innerHTML = showing ? '<i class="fa fa-eye"></i>' : '<i class="fa fa-eye-slash"></i>';
                passInput.focus();
            });
        }

        var form = document.getElementById('flfLoginForm');
        if (form) {
            form.addEventListener('submit', function () {
                var btn = document.getElementById('signinBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa fa-circle-o-notch fa-spin"></i> <span>Verifying credentials&hellip;</span>';
            });
        }
    })();
    </script>
</body>
</html>
