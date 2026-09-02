<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';
require_once __DIR__ . '/csrf.php';
?>

<div class="padding_infor_info">

   <?php if (isset($_SESSION['success_message'])): ?>
   <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa fa-check-circle me-2"></i>
      <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
      <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span>
      </button>
   </div>
   <?php endif; ?>

   <?php if (isset($_SESSION['error_message'])): ?>
   <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fa fa-exclamation-circle me-2"></i>
      <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
      <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span>
      </button>
   </div>
   <?php endif; ?>

   <!-- Form panel -->
   <div class="white_shd full margin_bottom_30">
      <div class="full graph_head">
         <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
            <h2><i class="fa fa-plus-circle" style="color:var(--fl-accent);margin-right:10px;font-size:20px;"></i>Add Property</h2>
            <a href="display_properties.php" class="btn btn-secondary btn-sm">
               <i class="fa fa-th-list" style="margin-right:5px;"></i>View All Properties
            </a>
         </div>
      </div>

      <div class="full padding_infor_info">
         <form action="add_property.php" method="post" enctype="multipart/form-data" style="max-width:680px;"><?php echo csrfHiddenField(); ?>

            <div class="flf-form-section">
               <h3 class="flf-section-title"><i class="fa fa-picture-o"></i>Property Image</h3>

               <div class="flf-field">
                  <label><i class="fa fa-camera"></i>Upload Image</label>
                  <div class="flf-upload-area" id="uploadArea">
                     <input type="file" name="image" id="imageUpload" accept="image/*" required>
                     <div class="flf-upload-icon"><i class="fa fa-cloud-upload"></i></div>
                     <p class="flf-upload-text"><strong>Click to upload</strong> or drag and drop</p>
                     <p class="flf-upload-hint">JPG, PNG, or GIF (Max 5MB)</p>
                  </div>
                  <div class="flf-upload-preview" id="imagePreview">
                     <img src="" alt="Preview">
                     <button type="button" class="flf-remove-img" id="removePreview" title="Remove image">
                        <i class="fa fa-times"></i>
                     </button>
                  </div>
               </div>
            </div>

            <div class="flf-form-section">
               <h3 class="flf-section-title"><i class="fa fa-building"></i>Listing Details</h3>

               <div class="flf-field">
                  <label for="location"><i class="fa fa-map-marker-alt"></i>Location</label>
                  <input type="text" class="form-control" name="location" id="location" placeholder="Enter property location" required>
               </div>

               <div class="flf-field">
                  <label for="title"><i class="fa fa-tag"></i>Title</label>
                  <input type="text" class="form-control" name="title" id="title" placeholder="Enter property title" required>
               </div>

               <div style="padding-top:8px;">
                  <button type="submit" name="submit" class="btn btn-info">
                     <i class="fa fa-plus" style="margin-right:5px;"></i>Add Property
                  </button>
                  <a href="display_properties.php" class="btn btn-secondary" style="margin-left:10px;">Cancel</a>
               </div>
            </div>

         </form>
      </div>
   </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var imageUpload = document.getElementById('imageUpload');
    var imagePreview = document.getElementById('imagePreview');
    var removePreview = document.getElementById('removePreview');
    var uploadArea = document.getElementById('uploadArea');

    imageUpload.addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                imagePreview.style.display = 'block';
                imagePreview.querySelector('img').src = ev.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    removePreview.addEventListener('click', function() {
        imageUpload.value = '';
        imagePreview.style.display = 'none';
        imagePreview.querySelector('img').src = '';
    });

    ['dragenter', 'dragover'].forEach(function(evt) {
        uploadArea.addEventListener(evt, function() {
            uploadArea.classList.add('flf-dragover');
        });
    });
    ['dragleave', 'drop'].forEach(function(evt) {
        uploadArea.addEventListener(evt, function() {
            uploadArea.classList.remove('flf-dragover');
        });
    });
});
</script>

<?php
require_once 'include/footer.php';
?>
