<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';
require_once __DIR__ . '/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfPost();
    $title = $_POST['title'];
    $description = $_POST['description'];
    $more_description = $_POST['more_description'];
    $client = $_POST['client'];
    $cases_won = $_POST['cases_won'];
    $achievements = $_POST['achievements'];
    $our_team = $_POST['our_team'];
    $status = $_POST['status'];

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "propertyMgt/aboutImg/" . $image);
    }

    try {
        $stmt = $conn->prepare("INSERT INTO about_content (image, title, description, more_description, client, cases_won, achievements, our_team, status) VALUES (:image, :title, :description, :more_description, :client, :cases_won, :achievements, :our_team, :status)");
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':more_description', $more_description);
        $stmt->bindParam(':client', $client);
        $stmt->bindParam(':cases_won', $cases_won);
        $stmt->bindParam(':achievements', $achievements);
        $stmt->bindParam(':our_team', $our_team);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        $_SESSION['success_message'] = "About content added successfully!";
        echo "<script>window.location.href = 'display_about.php';</script>";
        exit();
    } catch (PDOException $e) {
        error_log("Add about error: " . $e->getMessage()); $_SESSION['error_message'] = "An error occurred. Please try again.";
    }
}
?>

<div class="padding_infor_info">

   <?php if (isset($_SESSION['error_message'])): ?>
   <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fa fa-exclamation-circle me-2"></i>
      <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
      <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   </div>
   <?php endif; ?>

   <!-- Form panel -->
   <div class="white_shd full margin_bottom_30">
      <div class="full graph_head">
         <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
            <h2><i class="fa fa-plus-circle" style="color:var(--fl-accent);margin-right:10px;font-size:20px;"></i>Add About Content</h2>
            <a href="display_about.php" class="btn btn-secondary btn-sm">
               <i class="fa fa-th-list" style="margin-right:5px;"></i>View All About
            </a>
         </div>
      </div>

      <div class="full padding_infor_info">
         <form action="add_about.php" method="POST" enctype="multipart/form-data" style="max-width:820px;">
                            <?php echo csrfHiddenField(); ?>

                            <!-- Section 1: Image -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-camera"></i>Featured Image</h3>

                                <div class="flf-field">
                                    <div class="flf-upload-area" id="uploadArea">
                                        <input type="file" name="image" id="imageUpload" accept="image/*" required>
                                        <div class="flf-upload-icon"><i class="fa fa-cloud-upload"></i></div>
                                        <p class="flf-upload-text"><strong>Click to upload</strong> or drag and drop</p>
                                        <p class="flf-upload-hint">JPG, PNG, or GIF (Max 5MB)</p>
                                    </div>
                                    <div class="flf-upload-preview" id="imagePreview">
                                        <img src="" alt="Preview">
                                        <button type="button" class="flf-remove-img" id="removeImage" title="Remove image">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Content -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-edit"></i>Content</h3>

                                <div class="flf-field">
                                    <label><i class="fa fa-heading"></i>Title</label>
                                    <input type="text" class="form-control" name="title" placeholder="e.g. About Fair Law Firm" required>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-align-left"></i>Description</label>
                                    <textarea class="form-control" name="description" rows="4" placeholder="Main description of the about content..." required></textarea>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-paragraph"></i>More Description</label>
                                    <textarea class="form-control" name="more_description" rows="4" placeholder="Additional details or extended description..."></textarea>
                                </div>
                            </div>

                            <!-- Section 3: Statistics -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-bar-chart"></i>Statistics</h3>

                                <div class="flf-field-row">
                                    <div class="flf-field">
                                        <label><i class="fa fa-users"></i>Client</label>
                                        <input type="text" class="form-control" name="client" placeholder="e.g. 500+">
                                    </div>

                                    <div class="flf-field">
                                        <label><i class="fa fa-trophy"></i>Cases Won</label>
                                        <input type="text" class="form-control" name="cases_won" placeholder="e.g. 1200+" required>
                                    </div>
                                </div>

                                <div class="flf-field-row">
                                    <div class="flf-field">
                                        <label><i class="fa fa-star"></i>Achievements</label>
                                        <input type="text" class="form-control" name="achievements" placeholder="e.g. 50+ Awards">
                                    </div>

                                    <div class="flf-field">
                                        <label><i class="fa fa-user-md"></i>Our Team</label>
                                        <input type="text" class="form-control" name="our_team" placeholder="e.g. 30+ Lawyers">
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Status -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-toggle-on"></i>Publishing</h3>

                                <div class="flf-field">
                                    <label><i class="fa fa-eye"></i>Status</label>
                                    <div class="flf-radio-group">
                                        <label class="flf-radio-card">
                                            <input type="radio" name="status" value="Active" checked>
                                            <i class="fa fa-check-circle" style="color:var(--flf-success);"></i>
                                            Active
                                        </label>
                                        <label class="flf-radio-card">
                                            <input type="radio" name="status" value="Pending">
                                            <i class="fa fa-clock-o" style="color:var(--flf-gold);"></i>
                                            Pending
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div style="padding-top:8px;">
                                <button type="submit" class="btn btn-info">
                                    <i class="fa fa-save" style="margin-right:5px;"></i>Add About Content
                                </button>
                                <a href="display_about.php" class="btn btn-secondary" style="margin-left:10px;">Cancel</a>
                            </div>

                        </form>
                    </div>
                </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var imageUpload = document.getElementById('imageUpload');
    var imagePreview = document.getElementById('imagePreview');
    var removeImage = document.getElementById('removeImage');
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

    removeImage.addEventListener('click', function() {
        imageUpload.value = '';
        imagePreview.style.display = 'none';
        imagePreview.querySelector('img').src = '';
    });

    ['dragenter', 'dragover'].forEach(function(evt) {
        uploadArea.addEventListener(evt, function() { uploadArea.classList.add('flf-dragover'); });
    });
    ['dragleave', 'drop'].forEach(function(evt) {
        uploadArea.addEventListener(evt, function() { uploadArea.classList.remove('flf-dragover'); });
    });
});
</script>

<?php require_once 'include/footer.php'; ?>