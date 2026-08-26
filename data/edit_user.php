<?php
/* ================================================================
   Fair Law Firm LTD - Edit User
   Admin-only form to update an existing user's details.
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
   Fetch user by ID
   ---------------------------------------------------------------- */
$user = null;

if (!isset($_GET['id']) || empty($_GET['id'])) {
   $_SESSION['error_message'] = 'User ID is missing.';
   header('Location: manage_users.php');
   exit();
}

$id = (int) $_GET['id'];

try {
   $user = $conn->prepare(
      "SELECT u.*, r.role_name
       FROM users u
       JOIN roles r ON u.role_id = r.role_id
       WHERE u.id = :id"
   );
   $user->execute([':id' => $id]);
   $user = $user->fetch(PDO::FETCH_ASSOC);

   if (!$user) {
      $_SESSION['error_message'] = 'User not found.';
      header('Location: manage_users.php');
      exit();
   }
} catch (PDOException $e) {
   $_SESSION['error_message'] = 'Failed to load user.';
   header('Location: manage_users.php');
   exit();
}

/* ----------------------------------------------------------------
   Handle form submission
   ---------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $first_name = trim($_POST['first_name'] ?? '');
   $last_name  = trim($_POST['last_name'] ?? '');
   $email      = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
   $phone      = trim($_POST['phone'] ?? '');
   $gender     = $_POST['gender'] ?? '';
   $role_id    = (int) ($_POST['role_id'] ?? 0);
   $status     = $_POST['status'] ?? '';

   try {
      $conn->beginTransaction();
      $conn->exec('SET FOREIGN_KEY_CHECKS=0');

      // If email changed, check uniqueness and update login table
      if ($email !== $user['email']) {
         $check = $conn->prepare(
            'SELECT 1 FROM login WHERE email = :email AND email != :current'
         );
         $check->execute([':email' => $email, ':current' => $user['email']]);

         if ($check->fetch()) {
            throw new Exception('Email already exists.');
         }

         $conn->prepare('UPDATE login SET email = :new_email WHERE email = :old_email')
              ->execute([':new_email' => $email, ':old_email' => $user['email']]);
      }

      // Update usertype in login table
      $new_usertype = ($role_id === 1) ? 'admin' : 'user';
      $conn->prepare('UPDATE login SET usertype = :ut WHERE email = :email')
           ->execute([':ut' => $new_usertype, ':email' => $email]);

      // Update user record
      $conn->prepare(
         "UPDATE users SET
            first_name = :first_name,
            last_name  = :last_name,
            email      = :email,
            phone      = :phone,
            gender     = :gender,
            role_id    = :role_id,
            status     = :status
          WHERE id = :id"
      )->execute([
         ':first_name' => $first_name,
         ':last_name'  => $last_name,
         ':email'      => $email,
         ':phone'      => $phone,
         ':gender'     => $gender,
         ':role_id'    => $role_id,
         ':status'     => $status,
         ':id'         => $id,
      ]);

      // Handle profile image upload
      if (!empty($_FILES['profile_image']['error']) === UPLOAD_ERR_OK) {
         $upload_dir = 'propertyMgt/userImg/';
         if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
         }

         $ext  = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
         $file = uniqid() . '.' . $ext;
         $path = $upload_dir . $file;

         if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])) {
            throw new Exception('Only JPG, JPEG, PNG & GIF files are allowed.');
         }
         if ($_FILES['profile_image']['size'] > 5000000) {
            throw new Exception('File is too large (max 5 MB).');
         }
         if (getimagesize($_FILES['profile_image']['tmp_name']) === false) {
            throw new Exception('File is not a valid image.');
         }

         if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $path)) {
            // Delete old image
            if (!empty($user['profile_image']) && file_exists($user['profile_image'])) {
               unlink($user['profile_image']);
            }

            $conn->prepare('UPDATE users SET profile_image = :img WHERE id = :id')
                 ->execute([':img' => $path, ':id' => $id]);
         } else {
            throw new Exception('Failed to upload file.');
         }
      }

      $conn->exec('SET FOREIGN_KEY_CHECKS=1');
      $conn->commit();

      $_SESSION['success_message'] = 'User updated successfully.';
      header('Location: manage_users.php');
      exit();
   } catch (Exception $e) {
      $conn->rollback();
      $conn->exec('SET FOREIGN_KEY_CHECKS=1');
      $_SESSION['error_message'] = $e->getMessage();
      header("Location: edit_user.php?id=$id");
      exit();
   }
}

/* ----------------------------------------------------------------
   Fetch roles for dropdown
   ---------------------------------------------------------------- */
$roles = [];
try {
   $roles = $conn->query('SELECT * FROM roles ORDER BY role_id')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   $roles = [];
}
?>

<div class="padding_infor_info">

   <!-- Page header -->
   <div class="flf-page-head d-flex justify-content-between align-items-center">
      <div>
         <h1 class="flf-title">Edit User</h1>
         <p class="flf-subtitle">
            Editing: <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
         </p>
      </div>
      <a href="manage_users.php" class="btn btn-secondary">
         <i class="fa fa-arrow-left mr-1"></i> Back to Users
      </a>
   </div>

   <!-- Flash messages -->
   <?php if (!empty($_SESSION['error_message'])): ?>
   <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fa fa-exclamation-circle mr-2"></i>
      <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span>
      </button>
   </div>
   <?php endif; ?>

   <!-- Form card -->
   <div class="white_shd full">
      <div class="full padding_infor_info">

         <form method="POST" action="" enctype="multipart/form-data" id="flfEditUserForm">

            <!-- Profile Image -->
            <div class="field">
               <label class="label_field">Profile Image</label>
               <div class="d-flex align-items-center gap-4">
                  <div class="flf-avatar-preview" id="avatarPreview">
                     <?php if (!empty($user['profile_image']) && file_exists($user['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="" id="avatarImg">
                     <?php else: ?>
                        <img src="images/default-avatar.png" alt="" id="avatarImg">
                     <?php endif; ?>
                  </div>
                  <div class="flex-grow-1">
                     <input type="file"
                            name="profile_image"
                            id="profileImage"
                            class="form-control"
                            accept="image/*">
                     <small class="text-muted">
                        Leave empty to keep the current image.
                        JPG, PNG or GIF. Max 5 MB.
                     </small>
                  </div>
               </div>
            </div>

            <hr style="border-color: var(--flf-blue); margin: 24px 0;">

            <!-- First / Last Name -->
            <div class="row">
               <div class="col-md-6">
                  <div class="field">
                     <label class="label_field">First Name</label>
                     <input type="text"
                            name="first_name"
                            class="form-control"
                            value="<?php echo htmlspecialchars($user['first_name']); ?>"
                            required>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="field">
                     <label class="label_field">Last Name</label>
                     <input type="text"
                            name="last_name"
                            class="form-control"
                            value="<?php echo htmlspecialchars($user['last_name']); ?>"
                            required>
                  </div>
               </div>
            </div>

            <!-- Email -->
            <div class="field">
               <label class="label_field">Email Address</label>
               <input type="email"
                      name="email"
                      class="form-control"
                      value="<?php echo htmlspecialchars($user['email']); ?>"
                      required>
            </div>

            <!-- Phone -->
            <div class="field">
               <label class="label_field">Phone Number</label>
               <input type="tel"
                      name="phone"
                      class="form-control"
                      value="<?php echo htmlspecialchars($user['phone']); ?>"
                      required>
            </div>

            <!-- Gender -->
            <div class="field">
               <label class="label_field">Gender</label>
               <div class="radio_group">
                  <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                  <div class="radio_option">
                     <input type="radio"
                            name="gender"
                            id="flf_<?php echo strtolower($g); ?>"
                            value="<?php echo $g; ?>"
                            <?php echo ($user['gender'] === $g) ? 'checked' : ''; ?>
                            class="radio_input">
                     <label for="flf_<?php echo strtolower($g); ?>" class="radio_label">
                        <span class="radio_custom"></span> <?php echo $g; ?>
                     </label>
                  </div>
                  <?php endforeach; ?>
               </div>
            </div>

            <hr style="border-color: var(--flf-blue); margin: 24px 0;">

            <!-- Role + Status -->
            <div class="row">
               <div class="col-md-6">
                  <div class="field">
                     <label class="label_field">Role</label>
                     <select name="role_id" class="form-control" required>
                        <?php foreach ($roles as $role): ?>
                           <option value="<?php echo (int) $role['role_id']; ?>"
                              <?php echo ($role['role_id'] == $user['role_id']) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($role['role_name']); ?>
                           </option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="field">
                     <label class="label_field">Status</label>
                     <select name="status" class="form-control" required>
                        <?php foreach (['Active', 'Pending', 'Inactive'] as $s): ?>
                           <option value="<?php echo $s; ?>"
                              <?php echo ($user['status'] === $s) ? 'selected' : ''; ?>>
                              <?php echo $s; ?>
                           </option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
            </div>

            <hr style="border-color: var(--flf-blue); margin: 24px 0;">

            <!-- Submit -->
            <div class="field mb-0">
               <button type="submit" class="btn btn-info btn-block">
                  <i class="fa fa-save mr-2"></i> Update User
               </button>
            </div>

         </form>

      </div>
   </div>

</div>

<!-- ================================================================
     SCRIPTS — avatar preview
     ================================================================ -->
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
            reader.onload = function (e) {
               avatarImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
         }
      });
   }
})();
</script>

<style>
   .flf-avatar-preview {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      overflow: hidden;
      border: 3px solid var(--flf-blue);
      flex-shrink: 0;
      background: var(--flf-blue);
   }
   .flf-avatar-preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
   }
</style>

<?php
require_once 'include/footer.php';
ob_end_flush();
?>
