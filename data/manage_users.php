<?php
/* ================================================================
   Fair Law Firm LTD - Manage Users
   Admin-only page: list, view (modal), edit, delete users.
   ================================================================ */
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

/* ----------------------------------------------------------------
   Admin-only access
   ---------------------------------------------------------------- */
if ($_SESSION['user_type'] !== 'admin') {
   header('Location: index.php');
   exit();
}

/* ----------------------------------------------------------------
   Delete user (with transaction)
   ---------------------------------------------------------------- */
if (isset($_GET['delete'])) {
   $delete_id = (int) $_GET['delete'];

   try {
      $conn->beginTransaction();

      $stmt = $conn->prepare('SELECT email FROM users WHERE id = :id');
      $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user) {
         $conn->prepare('DELETE FROM users WHERE id = :id')
              ->execute([':id' => $delete_id]);

         $conn->prepare('DELETE FROM login WHERE email = :email')
              ->execute([':email' => $user['email']]);
      }

      $conn->commit();
      $_SESSION['success_message'] = 'User deleted successfully.';
   } catch (PDOException $e) {
      $conn->rollback();
      $_SESSION['error_message'] = 'Error deleting user.';
   }

   header('Location: manage_users.php');
   exit();
}

/* ----------------------------------------------------------------
   Fetch all users with roles
   ---------------------------------------------------------------- */
$users  = [];
$userError = '';

try {
   $users = $conn->query(
      "SELECT u.*, r.role_name
       FROM users u
       JOIN roles r ON u.role_id = r.role_id
       ORDER BY u.id DESC"
   )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   $userError = 'Failed to load users.';
}

$userCount = count($users);
?>

<!-- ================================================================
     PAGE CONTENT
     ================================================================ -->
<div class="padding_infor_info">

   <!-- Page header -->
   <div class="flf-page-head d-flex justify-content-between align-items-center">
      <div>
         <h1 class="flf-title">Manage Users</h1>
         <p class="flf-subtitle">
            <?php echo $userCount; ?> registered user<?php echo $userCount !== 1 ? 's' : ''; ?> in the system.
         </p>
      </div>
      <a href="register.php" class="btn btn-info">
         <i class="fa fa-plus"></i> Add New User
      </a>
   </div>

   <!-- Flash messages -->
   <?php if (!empty($_SESSION['success_message'])): ?>
   <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa fa-check-circle mr-2"></i>
      <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span>
      </button>
   </div>
   <?php endif; ?>

   <?php if (!empty($_SESSION['error_message'])): ?>
   <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fa fa-exclamation-circle mr-2"></i>
      <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span>
      </button>
   </div>
   <?php endif; ?>

   <?php if (!empty($userError)): ?>
   <div class="alert alert-danger">
      <i class="fa fa-exclamation-circle mr-2"></i>
      <?php echo htmlspecialchars($userError); ?>
   </div>
   <?php endif; ?>

   <!-- User table -->
   <div class="white_shd full">
      <div class="full padding_infor_info">
         <div class="table-responsive">

            <?php if (empty($users) && empty($userError)): ?>
            <div class="alert alert-info">
               <i class="fa fa-info-circle mr-2"></i>
               No users found. <a href="register.php">Add a new user</a> to get started.
            </div>

            <?php else: ?>
            <table class="table table-bordered table-striped">
               <thead class="thead-dark">
                  <tr>
                     <th>ID</th>
                     <th>Profile</th>
                     <th>Name</th>
                     <th>Email</th>
                     <th>Role</th>
                     <th>Status</th>
                     <th style="width: 140px;">Actions</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($users as $u): ?>
                  <tr>
                     <td><?php echo (int) $u['id']; ?></td>

                     <td>
                        <?php if (!empty($u['profile_image']) && file_exists($u['profile_image'])): ?>
                           <img src="<?php echo htmlspecialchars($u['profile_image']); ?>"
                                alt="" class="img-thumbnail" style="max-height: 44px;">
                        <?php else: ?>
                           <img src="images/default-avatar.png"
                                alt="" class="img-thumbnail" style="max-height: 44px;">
                        <?php endif; ?>
                     </td>

                     <td>
                        <strong><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></strong>
                     </td>

                     <td><?php echo htmlspecialchars($u['email']); ?></td>

                     <td><?php echo htmlspecialchars($u['role_name']); ?></td>

                     <td>
                        <?php if ($u['status'] === 'Active'): ?>
                           <span class="badge badge-success">Active</span>
                        <?php elseif ($u['status'] === 'Pending'): ?>
                           <span class="badge badge-warning">Pending</span>
                        <?php else: ?>
                           <span class="badge badge-danger"><?php echo htmlspecialchars($u['status']); ?></span>
                        <?php endif; ?>
                     </td>

                     <td>
                        <button type="button"
                                class="btn btn-primary btn-sm"
                                data-toggle="modal"
                                data-target="#viewUser<?php echo (int) $u['id']; ?>"
                                title="View user">
                           <i class="fa fa-eye"></i>
                        </button>
                        <a href="edit_user.php?id=<?php echo (int) $u['id']; ?>"
                           class="btn btn-info btn-sm"
                           title="Edit user">
                           <i class="fa fa-edit"></i>
                        </a>
                        <a href="manage_users.php?delete=<?php echo (int) $u['id']; ?>"
                           class="btn btn-danger btn-sm"
                           title="Delete user"
                           onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                           <i class="fa fa-trash"></i>
                        </a>
                     </td>
                  </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
            <?php endif; ?>

         </div>
      </div>
   </div>

</div>

<!-- ================================================================
     VIEW USER MODALS (one per user, rendered outside the table)
     ================================================================ -->
<?php foreach ($users as $u): ?>
<div class="modal fade" id="viewUser<?php echo (int) $u['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

         <div class="modal-header">
            <h5 class="modal-title">User Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>

         <div class="modal-body">
            <div class="row">

               <!-- Avatar + name -->
               <div class="col-md-4 text-center mb-3 mb-md-0">
                  <?php if (!empty($u['profile_image']) && file_exists($u['profile_image'])): ?>
                     <img src="<?php echo htmlspecialchars($u['profile_image']); ?>"
                          alt="" class="rounded-circle mb-3" style="max-height: 140px;">
                  <?php else: ?>
                     <img src="images/default-avatar.png"
                          alt="" class="rounded-circle mb-3" style="max-height: 140px;">
                  <?php endif; ?>
                  <h5 style="color: var(--flf-navy);"><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></h5>
                  <p class="text-muted mb-0"><?php echo htmlspecialchars($u['role_name']); ?></p>
                  <span class="badge badge-<?php echo $u['status'] === 'Active' ? 'success' : ($u['status'] === 'Pending' ? 'warning' : 'danger'); ?> mt-2">
                     <?php echo htmlspecialchars($u['status']); ?>
                  </span>
               </div>

               <!-- Details -->
               <div class="col-md-8">
                  <div class="row">
                     <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block mb-1">First Name</small>
                        <strong><?php echo htmlspecialchars($u['first_name']); ?></strong>
                     </div>
                     <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block mb-1">Last Name</small>
                        <strong><?php echo htmlspecialchars($u['last_name']); ?></strong>
                     </div>
                     <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block mb-1">Email</small>
                        <strong><?php echo htmlspecialchars($u['email']); ?></strong>
                     </div>
                     <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block mb-1">Phone</small>
                        <strong><?php echo htmlspecialchars($u['phone']); ?></strong>
                     </div>
                     <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block mb-1">Gender</small>
                        <strong><?php echo htmlspecialchars($u['gender']); ?></strong>
                     </div>
                     <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block mb-1">Role</small>
                        <strong><?php echo htmlspecialchars($u['role_name']); ?></strong>
                     </div>
                  </div>
               </div>

            </div>
         </div>

         <div class="modal-footer">
            <a href="edit_user.php?id=<?php echo (int) $u['id']; ?>" class="btn btn-info btn-sm">
               <i class="fa fa-edit"></i> Edit User
            </a>
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
         </div>

      </div>
   </div>
</div>
<?php endforeach; ?>

<?php
require_once 'include/footer.php';
ob_end_flush();
?>
