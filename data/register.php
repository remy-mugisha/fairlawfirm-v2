<?php
/* ================================================================
   Fair Law Firm LTD - Add User (Register)
   Form to create a new user account.
   ================================================================ */
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

$roles = [];
try {
   $roles = $conn->query('SELECT * FROM roles ORDER BY role_id')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   $roles = [];
}
?>

<div class="padding_infor_info">

   <!-- Page header -->
   <div class="flf-page-head">
      <h1 class="flf-title">Add User</h1>
      <p class="flf-subtitle">Create a new user account for the admin portal.</p>
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

   <?php if (!empty($_SESSION['success_message'])): ?>
   <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa fa-check-circle mr-2"></i>
      <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span>
      </button>
   </div>
   <?php endif; ?>

   <!-- Form card -->
   <div class="white_shd full">
      <div class="full padding_infor_info">

         <form method="POST" action="register_process.php" enctype="multipart/form-data" id="flfRegisterForm">

            <!-- Profile Image -->
            <div class="field">
               <label class="label_field">Profile Image</label>
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

            <hr style="border-color: var(--flf-blue); margin: 24px 0;">

            <!-- First / Last Name -->
            <div class="row">
               <div class="col-md-6">
                  <div class="field">
                     <label class="label_field">First Name</label>
                     <input type="text"
                            name="first_name"
                            class="form-control"
                            placeholder="First name"
                            required>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="field">
                     <label class="label_field">Last Name</label>
                     <input type="text"
                            name="last_name"
                            class="form-control"
                            placeholder="Last name"
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
                      placeholder="user@fairlawfirmltd.com"
                      required>
            </div>

            <!-- Phone -->
            <div class="field">
               <label class="label_field">Phone Number</label>
               <input type="tel"
                      name="phone"
                      class="form-control"
                      placeholder="+250 7XX XXX XXX"
                      required>
            </div>

            <!-- Gender -->
            <div class="field">
               <label class="label_field">Gender</label>
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

            <hr style="border-color: var(--flf-blue); margin: 24px 0;">

            <!-- Password -->
            <div class="row">
               <div class="col-md-6">
                  <div class="field">
                     <label class="label_field">Password</label>
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
               </div>
               <div class="col-md-6">
                  <div class="field">
                     <label class="label_field">Confirm Password</label>
                     <input type="password"
                            name="confirm_password"
                            id="flfRegPassConfirm"
                            class="form-control"
                            placeholder="Repeat password"
                            required>
                  </div>
               </div>
            </div>

            <!-- Role -->
            <div class="field">
               <label class="label_field">Role</label>
               <select name="role_id" class="form-control" required>
                  <option value="" disabled selected>Select a role</option>
                  <?php foreach ($roles as $role): ?>
                     <option value="<?php echo (int) $role['role_id']; ?>">
                        <?php echo htmlspecialchars($role['role_name']); ?>
                     </option>
                  <?php endforeach; ?>
               </select>
            </div>

            <hr style="border-color: var(--flf-blue); margin: 24px 0;">

            <!-- Submit -->
            <div class="field mb-0">
               <button type="submit" class="btn btn-info btn-block">
                  <i class="fa fa-user-plus mr-2"></i> Register User
               </button>
            </div>

            <p class="text-center mt-3" style="font-size: 14px; color: var(--flf-muted);">
               Already have an account? <a href="index.php" style="font-weight: 700;">Sign in</a>
            </p>

         </form>

      </div>
   </div>

</div>

<!-- ================================================================
     SCRIPTS — avatar preview + password toggle
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
      border: 3px solid var(--flf-blue);
      flex-shrink: 0;
      background: var(--flf-blue);
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
      color: var(--flf-muted-light);
      font-size: 15px;
      transition: all 0.25s ease;
      display: flex;
      align-items: center;
      justify-content: center;
   }
   .flf-toggle-pass:hover {
      color: var(--flf-navy);
      background: var(--flf-blue);
   }
</style>

<?php require_once 'include/footer.php'; ?>
