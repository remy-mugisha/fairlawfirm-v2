<?php
/* ================================================================
   Fair Law Firm LTD - Account Settings (Profile + Change Password)
   ================================================================ */
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';
require_once __DIR__ . '/csrf.php';

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

/* ----------------------------------------------------------------
   Handle form submissions (Profile / Password) — PRG pattern
   ---------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfPost();
    $form_action = $_POST['form_action'] ?? '';

    if ($form_action === 'update_profile') {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name  = trim($_POST['last_name'] ?? '');
        $email      = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $phone      = trim($_POST['phone'] ?? '');

        try {
            $stmt = $conn->prepare('SELECT id, profile_image FROM users WHERE email = :email');
            $stmt->execute([':email' => $_SESSION['email']]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$existing) {
                throw new Exception('Your profile record could not be found.');
            }

            $conn->beginTransaction();

            if ($email !== $_SESSION['email']) {
                $check = $conn->prepare('SELECT 1 FROM login WHERE email = :email AND email != :current');
                $check->execute([':email' => $email, ':current' => $_SESSION['email']]);
                if ($check->fetch()) {
                    throw new Exception('That email address is already in use.');
                }
                $conn->prepare('UPDATE login SET email = :new_email WHERE email = :old_email')
                     ->execute([':new_email' => $email, ':old_email' => $_SESSION['email']]);
            }

            $conn->prepare(
                'UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone
                 WHERE id = :id'
            )->execute([
                ':first_name' => $first_name,
                ':last_name'  => $last_name,
                ':email'      => $email,
                ':phone'      => $phone,
                ':id'         => $existing['id'],
            ]);

            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'propertyMgt/userImg/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])) {
                    throw new Exception('Only JPG, JPEG, PNG & GIF files are allowed.');
                }
                if ($_FILES['profile_image']['size'] > 5000000) {
                    throw new Exception('File is too large (max 5 MB).');
                }
                if (getimagesize($_FILES['profile_image']['tmp_name']) === false) {
                    throw new Exception('File is not a valid image.');
                }

                $path = $upload_dir . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $path)) {
                    if (!empty($existing['profile_image']) && file_exists($existing['profile_image'])) {
                        unlink($existing['profile_image']);
                    }
                    $conn->prepare('UPDATE users SET profile_image = :img WHERE id = :id')
                         ->execute([':img' => $path, ':id' => $existing['id']]);
                    $_SESSION['profile_image'] = $path;
                } else {
                    throw new Exception('Failed to upload the image.');
                }
            }

            $conn->commit();

            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name']  = $last_name;
            $_SESSION['email']      = $email;
            $_SESSION['success_message'] = 'Profile updated successfully.';
        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $_SESSION['error_message'] = $e->getMessage();
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log('Profile update error: ' . $e->getMessage());
            $_SESSION['error_message'] = 'A database error occurred while updating your profile.';
        }

        header('Location: profile.php');
        exit();
    }

    if ($form_action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password     = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        try {
            $stmt = $conn->prepare('SELECT password FROM login WHERE email = :email');
            $stmt->execute([':email' => $_SESSION['email']]);
            $login_row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$login_row || !password_verify($current_password, $login_row['password'])) {
                throw new Exception('Current password is incorrect.');
            }
            if ($new_password !== $confirm_password) {
                throw new Exception('New passwords do not match.');
            }
            if (strlen($new_password) < 8) {
                throw new Exception('New password must be at least 8 characters long.');
            }

            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $conn->prepare('UPDATE login SET password = :password WHERE email = :email')
                 ->execute([':password' => $hashed, ':email' => $_SESSION['email']]);

            $_SESSION['success_message'] = 'Password changed successfully.';
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        } catch (PDOException $e) {
            error_log('Password change error: ' . $e->getMessage());
            $_SESSION['error_message'] = 'A database error occurred while changing your password.';
        }

        header('Location: profile.php');
        exit();
    }
}

/* ----------------------------------------------------------------
   Fetch current user for display
   ---------------------------------------------------------------- */
$user = null;
try {
    $stmt = $conn->prepare(
        "SELECT u.*, r.role_name
         FROM users u
         JOIN roles r ON u.role_id = r.role_id
         WHERE u.email = :email"
    );
    $stmt->execute([':email' => $_SESSION['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $user = [
            'first_name'    => $_SESSION['first_name'] ?? 'User',
            'last_name'     => $_SESSION['last_name'] ?? '',
            'email'         => $_SESSION['email'],
            'phone'         => '',
            'profile_image' => $_SESSION['profile_image'] ?? '',
            'role_name'     => $_SESSION['user_type'] ?? 'User',
        ];
    }
} catch (PDOException $e) {
    error_log('Profile fetch error: ' . $e->getMessage());
    $_SESSION['error_message'] = 'A database error occurred. Please try again later.';
    $user = [
        'first_name' => $_SESSION['first_name'] ?? 'User', 'last_name' => $_SESSION['last_name'] ?? '',
        'email' => $_SESSION['email'], 'phone' => '', 'profile_image' => '', 'role_name' => '',
    ];
}

$has_avatar = !empty($user['profile_image']) && file_exists($user['profile_image']);
?>

<div class="padding_infor_info">

    <!-- Page header -->
    <div class="flf-page-head">
        <h1 class="flf-title">Account Settings</h1>
        <p class="flf-subtitle">Manage your profile and password.</p>
    </div>

    <!-- Flash messages -->
    <?php if (!empty($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-check-circle me-2"></i>
        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-circle me-2"></i>
        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <?php endif; ?>

    <div class="flf-settings-stack">

        <!-- ==================== PROFILE ==================== -->
        <div class="white_shd full flf-settings-card">
            <div class="flf-settings-card-head">
                <div>
                    <span class="fl-kicker">Account</span>
                    <h3>Profile</h3>
                </div>
                <span class="fl-icon-tile fl-icon-tile--chambers"><i class="fa fa-user"></i></span>
            </div>

            <div class="flf-settings-card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <?php echo csrfHiddenField(); ?>
                    <input type="hidden" name="form_action" value="update_profile">

                    <div class="d-flex align-items-center gap-4 flex-wrap" style="margin-bottom: var(--sp-6);">
                        <div class="flf-avatar-preview" id="avatarPreview">
                            <img src="<?php echo $has_avatar ? htmlspecialchars($user['profile_image']) : 'images/default-avatar.png'; ?>" alt="" id="avatarImg">
                        </div>
                        <div class="flex-grow-1">
                            <label class="label_field" for="profileImage">Profile Image</label>
                            <input type="file" name="profile_image" id="profileImage" class="form-control" accept="image/*">
                            <small class="text-muted">Leave empty to keep the current image. JPG, PNG or GIF. Max 5 MB.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="field">
                                <label class="label_field">First Name</label>
                                <input type="text" name="first_name" class="form-control"
                                       value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field">
                                <label class="label_field">Last Name</label>
                                <input type="text" name="last_name" class="form-control"
                                       value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label_field">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="field margin_0">
                        <label class="label_field">Phone Number</label>
                        <input type="tel" name="phone" class="form-control"
                               value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>

                    <div class="flf-settings-card-actions">
                        <button type="submit" class="btn btn-info">
                            <i class="fa fa-save"></i> Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ==================== CHANGE PASSWORD ==================== -->
        <div class="white_shd full flf-settings-card">
            <div class="flf-settings-card-head">
                <div>
                    <span class="fl-kicker">Security</span>
                    <h3>Change Password</h3>
                </div>
                <span class="fl-icon-tile fl-icon-tile--seal"><i class="fa fa-key"></i></span>
            </div>

            <div class="flf-settings-card-body">
                <form method="POST" action="" id="flfPasswordForm">
                    <?php echo csrfHiddenField(); ?>
                    <input type="hidden" name="form_action" value="change_password">

                    <div class="field">
                        <label class="label_field">Current Password</label>
                        <div class="flf-pw-wrap">
                            <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                            <button type="button" class="flf-pw-toggle" data-target-pw title="Show password"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label_field">New Password</label>
                        <div class="flf-pw-wrap">
                            <input type="password" name="new_password" class="form-control" required minlength="8" autocomplete="new-password">
                            <button type="button" class="flf-pw-toggle" data-target-pw title="Show password"><i class="fa fa-eye"></i></button>
                        </div>
                        <small class="text-muted">At least 8 characters.</small>
                    </div>

                    <div class="field margin_0">
                        <label class="label_field">Confirm New Password</label>
                        <div class="flf-pw-wrap">
                            <input type="password" name="confirm_password" class="form-control" required minlength="8" autocomplete="new-password">
                            <button type="button" class="flf-pw-toggle" data-target-pw title="Show password"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>

                    <div class="flf-settings-card-actions">
                        <button type="submit" class="btn btn-info">
                            <i class="fa fa-refresh"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>

<script>
(function () {
    'use strict';

    var fileInput = document.getElementById('profileImage');
    var avatarImg = document.getElementById('avatarImg');
    if (fileInput && avatarImg) {
        fileInput.addEventListener('change', function () {
            var file = this.files[0];
            if (file && file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function (e) { avatarImg.src = e.target.result; };
                reader.readAsDataURL(file);
            }
        });
    }

    document.querySelectorAll('[data-target-pw]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = btn.previousElementSibling;
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
        });
    });

    var pwForm = document.getElementById('flfPasswordForm');
    if (pwForm) {
        pwForm.addEventListener('submit', function (e) {
            var newPw = pwForm.querySelector('[name="new_password"]').value;
            var confirmPw = pwForm.querySelector('[name="confirm_password"]').value;
            if (newPw !== confirmPw) {
                e.preventDefault();
                alert('New passwords do not match.');
            }
        });
    }
})();
</script>

<style>
.flf-settings-stack {
    display: flex;
    flex-direction: column;
    gap: var(--sp-6);
    max-width: 720px;
}
.flf-settings-card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: var(--sp-5) var(--sp-6);
    border-bottom: 1px solid var(--fl-line);
}
.flf-settings-card-head h3 {
    margin: 2px 0 0;
    font-family: var(--font-heading);
    font-size: var(--fs-h3);
    color: var(--fl-primary);
}
.flf-settings-card-body { padding: var(--sp-6); }
.flf-settings-card-actions {
    display: flex;
    justify-content: flex-end;
    padding-top: var(--sp-4);
    margin-top: var(--sp-2);
}
.flf-avatar-preview {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid var(--fl-line);
    flex-shrink: 0;
    background: var(--fl-surface-muted);
}
.flf-avatar-preview img { width: 100%; height: 100%; object-fit: cover; }
.flf-pw-wrap { position: relative; }
.flf-pw-wrap .form-control { padding-right: 44px; }
.flf-pw-toggle {
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    width: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    cursor: pointer;
    color: var(--fl-ink-muted);
    transition: color var(--transition-fast);
}
.flf-pw-toggle:hover { color: var(--fl-primary); }
@media (max-width: 767px) {
    .flf-settings-card-head { padding: var(--sp-4) var(--sp-5); }
    .flf-settings-card-body { padding: var(--sp-5); }
}
</style>

<?php
require_once 'include/footer.php';
ob_end_flush();
?>
