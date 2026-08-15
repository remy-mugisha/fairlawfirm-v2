<?php
ob_start();

require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if ($_SESSION['user_type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $conn->beginTransaction();
        
        // Fetch the email before deleting the user
        $stmt = $conn->prepare("SELECT email FROM users WHERE id = :id");
        $stmt->bindParam(':id', $delete_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $email = $user['email'];

            // Delete user first
            $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $delete_id);
            $stmt->execute();

            // Delete associated login record
            $stmt = $conn->prepare("DELETE FROM login WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
        }
        
        $conn->commit();
        
        $_SESSION['success_message'] = "User deleted successfully!";
        header("Location: manage_users.php");
        exit();
    } catch(PDOException $e) {
        $conn->rollback();
        
        $_SESSION['error_message'] = "Error deleting user: " . $e->getMessage();
        header("Location: manage_users.php");
        exit();
    }
}


try {
    $stmt = $conn->query("SELECT u.*, r.role_name 
                          FROM users u 
                          JOIN roles r ON u.role_id = r.role_id 
                          ORDER BY u.id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching users: " . $e->getMessage();
}
?>
<style>
.table .thead-dark th {
    color: #fff;
    background-color: #15283c;
    border-color: #32383e;
}
</style>

<div class="row column1">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                    <h2>Manage Users</h2>
                    <a href="register.php" class="btn btn-info btn-sm">Add New User</a>
                </div>
            </div>          
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <div class="full padding_infor_info">
                <div class="table-responsive">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php elseif (empty($users)): ?>
                        <div class="alert alert-info">No users found. Add a new user to get started.</div>
                    <?php else: ?>
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Profile Image</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td>
                                        <?php if (!empty($user['profile_image']) && file_exists($user['profile_image'])): ?>
                                            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image" class="img-thumbnail" style="max-height: 50px;">
                                        <?php else: ?>
                                            <img src="images/default-avatar.png" alt="Default Avatar" class="img-thumbnail" style="max-height: 50px;">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($user['status'] == 'Active') ? 'badge-success' : (($user['status'] == 'Pending') ? 'badge-warning' : 'badge-danger'); ?>">
                                            <?php echo htmlspecialchars($user['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#viewUserModal<?php echo $user['id']; ?>">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-info btn-sm">
                                            <i class="fa fa-edit"></i> 
                                        </a>
                                        <a href="manage_users.php?delete=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="fa fa-trash"></i> 
                                        </a>
                                    </td>
                                </tr>

                                <div class="modal fade" id="viewUserModal<?php echo $user['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewUserModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="viewUserModalLabel<?php echo $user['id']; ?>">User Details</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4 text-center">
                                                        <?php if (!empty($user['profile_image']) && file_exists($user['profile_image'])): ?>
                                                            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image" class="img-fluid rounded-circle" style="max-height: 150px;">
                                                        <?php else: ?>
                                                            <img src="images/default-avatar.png" alt="Default Avatar" class="img-fluid rounded-circle" style="max-height: 150px;">
                                                        <?php endif; ?>
                                                        <h5 class="mt-3"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h5>
                                                        <p><?php echo htmlspecialchars($user['role_name']); ?></p>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['first_name']); ?></p>
                                                                <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['last_name']); ?></p>
                                                                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                                                                <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
                                                                <p><strong>Status:</strong> <?php echo htmlspecialchars($user['status']); ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'include/footer.php';
ob_end_flush();
?>