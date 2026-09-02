<?php
/* ================================================================
   Fair Law Firm LTD - Add User (Register)
   Form to create a new user account.
   ================================================================ */
require_once 'include/header.php';
require_once 'propertyMgt/config.php';
require_once __DIR__ . '/csrf.php';

$roles = [];
try {
   $roles = $conn->query('SELECT * FROM roles ORDER BY role_id')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   $roles = [];
}
?>

<div class="padding_infor_info">

   <!-- Flash messages -->
   <?php if (!empty($_SESSION['error_message'])): ?>
   <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fa fa-exclamation-circle me-2"></i>
      <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
      <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span>
      </button>
   </div>
   <?php endif; ?>

   <?php if (!empty($_SESSION['success_message'])): ?>
   <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa fa-check-circle me-2"></i>
      <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
      <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span>
      </button>
   </div>
   <?php endif; ?>

   <!-- Form panel -->
   <div class="white_shd full margin_bottom_30">
      <div class="full graph_head">
         <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
            <h2><i class="fa fa-plus-circle" style="color:var(--fl-accent);margin-right:10px;font-size:20px;"></i>Add User</h2>
            <a href="manage_users.php" class="btn btn-secondary btn-sm">
               <i class="fa fa-th-list" style="margin-right:5px;"></i>View All Users
            </a>
         </div>
      </div>

      <div class="full padding_infor_info">
         <form method="POST" action="register_process.php" enctype="multipart/form-data" id="flfRegisterForm" style="max-width:820px;"><?php echo csrfHiddenField(); ?>

            <!-- Profile Image -->
            <div class="flf-form-section">
               <h3 class="flf-section-title"><i class="fa fa-user"></i>Profile</h3>

               <div class="flf-field">
                  <label><i class="fa fa-camera"></i>Profile Image</label>
                  <div class="d-flex align-items-center gap-4">
                     <div class="flf-avatar-preview" id="avatarPreview">
                        <img src="images/default-avatar.png" alt="Preview" id="avatarImg">
                     </div>
                     <div class="flex-grow-1">
                        <input type="file"
                               name="profile_image"
                               id="profileImage"
                               class="form-control"
                               accept="image/*">
                        <small class="text-muted">JPG, PNG or GIF. Max 5 MB.</small>
                     </div>
                  </div>
               </div>

               <div class="flf-field-row">
                  <div class="flf-field">
                     <label><i class="fa fa-user"></i>First Name</label>
                     <input type="text"
                            name="first_name"
                            class="form-control"
                            placeholder="First name"
                            required>
                  </div>
                  <div class="flf-field">
                     <label><i class="fa fa-user-o"></i>Last Name</label>
                     <input type="text"
                            name="last_name"
                            class="form-control"
                            placeholder="Last name"
                            required>
                  </div>
               </div>

               <div class="flf-field">
                  <label><i class="fa fa-envelope"></i>Email Address</label>
                  <input type="email"
                         name="email"
                         class="form-control"
                         placeholder="user@fairlawfirmltd.com"
                         required>
               </div>

               <div class="flf-field">
                  <label><i class="fa fa-phone"></i>Phone Number</label>
                  <input type="tel"
                         name="phone"
                         class="form-control"
                         placeholder="+250 7XX XXX XXX"
                         required>
               </div>

               <div class="flf-field">
                  <label><i class="fa fa-venus-mars"></i>Gender</label>
                  <div class="radio_group">
                     <div class="radio_option">
                        <input type="radio" name="gender" id="male" value="Male" checked class="radio_input">
                        <label for="male" class="radio_label">
                           <span class="radio_custom"></span> Male
                        </label>
                     </div>
                     <div class="radio_option">
                        <input type="radio" name="gender" id="female" value="Female" class="radio_input">
                        <label for="female" class="radio_label">
                           <span class="radio_custom"></span> Female
                        </label>
                     </div>
                     <div class="radio_option">
                        <input type="radio" name="gender" id="other" value="Other" class="radio_input">
                        <label for="other" class="radio_label">
                           <span class="radio_custom"></span> Other
                        </label>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Security -->
            <div class="flf-form-section">
               <h3 class="flf-section-title"><i class="fa fa-lock"></i>Security</h3>

               <div class="flf-field-row">
                  <div class="flf-field">
                     <label><i class="fa fa-key"></i>Password</label>
                     <div class="position-relative">
                        <input type="password"
                               name="password"
                               id="flfRegPass"
                               class="form-control"
                               placeholder="Create a password"
                               required
                               style="padding-right: 44px;">
                        <button type="button"
                                class="flf-toggle-pass"
                                id="toggleRegPass"
                                aria-label="Show or hide password"
                                style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%);">
                           <i class="fa fa-eye"></i>
                        </button>
                     </div>
                  </div>
                  <div class="flf-field">
                     <label><i class="fa fa-check-circle"></i>Confirm Password</label>
                     <input type="password"
                            name="confirm_password"
                            id="flfRegPassConfirm"
                            class="form-control"
                            placeholder="Repeat password"
                            required>
                  </div>
               </div>
            </div>

            <!-- Role & Submit -->
            <div class="flf-form-section">
               <h3 class="flf-section-title"><i class="fa fa-shield"></i>Role</h3>

               <div class="flf-field">
                  <label><i class="fa fa-tags"></i>Role</label>
                  <select name="role_id" class="form-control" required>
                     <option value="" disabled selected>Select a role</option>
                     <?php foreach ($roles as $role): ?>
                        <option value="<?php echo (int) $role['role_id']; ?>">
                           <?php echo htmlspecialchars($role['role_name']); ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>

               <div style="padding-top:8px;">
                  <button type="submit" class="btn btn-info">
                     <i class="fa fa-user-plus" style="margin-right:5px;"></i> Register User
                  </button>
                  <a href="manage_users.php" class="btn btn-secondary" style="margin-left:10px;">Cancel</a>
               </div>
            </div>

         </form>
      </div>
   </div>

</div>

<!-- ================================================================
     SCRIPTS - avatar preview + password toggle
     ================================================================ -->
<script>
(function () {
   'use strict';

   /* Avatar preview */
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

   /* Password visibility toggle */
   var passInput   = document.getElementById('flfRegPass');
   var toggleBtn   = document.getElementById('toggleRegPass');

   if (passInput && toggleBtn) {
      toggleBtn.addEventListener('click', function () {
         var showing = passInput.type === 'text';
         passInput.type = showing ? 'password' : 'text';
         toggleBtn.querySelector('i').className = showing ? 'fa fa-eye' : 'fa fa-eye-slash';
         passInput.focus();
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
      border: 3px solid var(--fl-primary);
      flex-shrink: 0;
      background: var(--fl-primary);
   }
   .flf-avatar-preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
   }
   .flf-toggle-pass {
      border: none;
      background: transparent;
      cursor: pointer;
      width: 32px;
      height: 32px;
      border-radius: 6px;
      color: var(--fl-ink-muted);
      font-size: 15px;
      transition: all 0.25s ease;
      display: flex;
      align-items: center;
      justify-content: center;
   }
   .flf-toggle-pass:hover {
      color: var(--fl-primary);
      background: var(--fl-primary-light);
   }
</style>

<?php require_once 'include/footer.php'; ?>
