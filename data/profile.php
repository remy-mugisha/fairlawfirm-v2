<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

try {
    $stmt = $conn->prepare("SELECT u.*, r.role_name 
                           FROM users u
                           JOIN roles r ON u.role_id = r.role_id
                           WHERE u.email = :email");
    $stmt->bindParam(':email', $_SESSION['email']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $stmt = $conn->prepare("SELECT * FROM login WHERE email = :email");
        $stmt->bindParam(':email', $_SESSION['email']);
        $stmt->execute();
        $login_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($login_user) {
            $user = [
                'email' => $login_user['email'],
                'role_name' => $login_user['usertype'],
                'first_name' => 'User',
                'last_name' => '',
                'profile_image' => '',
                'phone' => '',
                'gender' => '',
                'status' => 'Active'
            ];
        }
    }
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}

$avatar = (!empty($user['profile_image']) && file_exists($user['profile_image']))
    ? htmlspecialchars($user['profile_image'])
    : 'images/default-avatar.png';

$full_name = trim($user['first_name'] . ' ' . $user['last_name']);
if (empty($full_name)) $full_name = 'User';

$initials = '';
$parts = explode(' ', $full_name);
foreach ($parts as $p) { if ($p !== '') $initials .= mb_strtoupper(mb_substr($p, 0, 1)); }
if (strlen($initials) > 2) $initials = mb_substr($initials, 0, 2);
?>

<style>
.flf-profile-card {
    background: var(--flf-white);
    border: 1px solid var(--flf-blue);
    border-radius: var(--flf-radius);
    box-shadow: 0 4px 14px rgba(1, 22, 106, 0.06);
    overflow: hidden;
}
.flf-profile-header {
    background: linear-gradient(135deg, var(--flf-navy) 0%, var(--flf-royal) 100%);
    padding: 28px 32px 22px;
    display: flex;
    align-items: center;
    gap: 20px;
}
.flf-profile-avatar-wrap {
    position: relative;
    flex-shrink: 0;
}
.flf-profile-avatar {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--flf-gold);
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
    background: var(--flf-white);
}
.flf-profile-initials {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    border: 4px solid var(--flf-gold);
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
    background: var(--flf-midnight);
    color: var(--flf-gold);
    font-family: var(--flf-font-head);
    font-size: 38px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}
.flf-profile-header-text h2 {
    margin: 0;
    font-family: var(--flf-font-head);
    font-size: 28px;
    font-weight: 700;
    color: var(--flf-white);
    line-height: 1.2;
}
.flf-profile-header-text .flf-role-badge {
    display: inline-block;
    margin-top: 6px;
    padding: 4px 14px;
    border-radius: var(--flf-radius-pill);
    background: rgba(200, 169, 81, 0.2);
    color: var(--flf-gold);
    font-family: var(--flf-font-body);
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.flf-profile-body {
    padding: 32px;
}
.flf-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
}
.flf-info-item {
    padding: 16px 0;
    border-bottom: 1px solid var(--flf-blue);
}
.flf-info-item:nth-last-child(-n+2) {
    border-bottom: none;
}
.flf-info-item label {
    display: block;
    margin: 0 0 4px;
    font-family: var(--flf-font-body);
    font-size: 12px;
    font-weight: 600;
    color: var(--flf-muted);
    text-transform: uppercase;
    letter-spacing: 0.6px;
}
.flf-info-item p {
    margin: 0;
    font-family: var(--flf-font-body);
    font-size: 15px;
    font-weight: 500;
    color: var(--flf-charcoal);
}
.flf-info-item .flf-status-active {
    display: inline-block;
    padding: 3px 12px;
    border-radius: var(--flf-radius-pill);
    background: rgba(26, 122, 76, 0.1);
    color: var(--flf-success);
    font-size: 13px;
    font-weight: 600;
}
@media (max-width: 767px) {
    .flf-profile-header { flex-direction: column; text-align: center; padding: 28px 20px 20px; }
    .flf-profile-header-text h2 { font-size: 22px; }
    .flf-profile-body { padding: 24px 20px; }
    .flf-info-grid { grid-template-columns: 1fr; }
    .flf-info-item { border-bottom: 1px solid var(--flf-blue); }
    .flf-info-item:last-child { border-bottom: none; }
}
</style>

<div class="midde_cont">
    <div class="container-fluid">

        <?php if (!empty($error_message)): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-danger" style="border-radius: var(--flf-radius-sm); margin: 0;">
                        <i class="fa fa-exclamation-triangle" style="margin-right: 6px;"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="flf-profile-card">

                    <div class="flf-profile-header">
                        <div class="flf-profile-avatar-wrap">
                            <?php if (!empty($user['profile_image']) && file_exists($user['profile_image'])): ?>
                                <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="flf-profile-avatar">
                            <?php else: ?>
                                <div class="flf-profile-initials"><?php echo $initials; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="flf-profile-header-text">
                            <h2><?php echo htmlspecialchars($full_name); ?></h2>
                            <span class="flf-role-badge"><?php echo htmlspecialchars($user['role_name']); ?></span>
                        </div>
                    </div>

                    <div class="flf-profile-body">
                        <div class="flf-info-grid">

                            <div class="flf-info-item">
                                <label>First Name</label>
                                <p><?php echo htmlspecialchars($user['first_name']); ?></p>
                            </div>

                            <div class="flf-info-item">
                                <label>Last Name</label>
                                <p><?php echo htmlspecialchars($user['last_name']); ?></p>
                            </div>

                            <div class="flf-info-item">
                                <label>Email</label>
                                <p><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>

                            <div class="flf-info-item">
                                <label>Phone</label>
                                <p><?php echo htmlspecialchars($user['phone']); ?></p>
                            </div>

                            <div class="flf-info-item">
                                <label>Gender</label>
                                <p><?php echo htmlspecialchars($user['gender']); ?></p>
                            </div>

                            <div class="flf-info-item">
                                <label>Role</label>
                                <p><?php echo htmlspecialchars($user['role_name']); ?></p>
                            </div>

                            <div class="flf-info-item">
                                <label>Status</label>
                                <p><span class="flf-status-active"><?php echo htmlspecialchars($user['status']); ?></span></p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?php
require_once 'include/footer.php';
?>