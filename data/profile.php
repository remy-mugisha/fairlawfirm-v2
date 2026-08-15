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
?>

<div class="midde_cont">
    <div class="container-fluid">
        <!-- <div class="row column_title">
            <div class="col-md-12">
                <div class="page_title">
                    <h2>User Profile</h2>
                </div>
            </div>
        </div> -->
        
        <div class="row">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0">
                            <h2>Profile Information</h2>
                        </div>
                    </div>
                    <div class="full padding_infor_info">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="profile_img text-center">
                                    <?php if (!empty($user['profile_image']) && file_exists($user['profile_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image" class="img-responsive" style="max-width: 200px; margin: 0 auto;">
                                    <?php else: ?>
                                        <img src="images/default-avatar.png" alt="Default Avatar" class="img-responsive" style="max-width: 200px; margin: 0 auto;">
                                    <?php endif; ?>
                                    <h4 class="mt-3"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                                    <p><?php echo htmlspecialchars($user['role_name']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="field">
                                            <label class="label_field">First Name</label>
                                            <p><?php echo htmlspecialchars($user['first_name']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="field">
                                            <label class="label_field">Last Name</label>
                                            <p><?php echo htmlspecialchars($user['last_name']); ?></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="field">
                                    <label class="label_field">Email</label>
                                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                                
                                <div class="field">
                                    <label class="label_field">Phone</label>
                                    <p><?php echo htmlspecialchars($user['phone']); ?></p>
                                </div>
                                
                                <div class="field">
                                    <label class="label_field">Gender</label>
                                    <p><?php echo htmlspecialchars($user['gender']); ?></p>
                                </div>
                                
                                <div class="field">
                                    <label class="label_field">Role</label>
                                    <p><?php echo htmlspecialchars($user['role_name']); ?></p>
                                </div>
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