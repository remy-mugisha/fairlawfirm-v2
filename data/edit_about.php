<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';
require_once __DIR__ . '/csrf.php';

if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid request. No ID provided.";
    echo "<script>window.location.href = 'display_about.php';</script>";
    exit();
}

$id = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM about_content WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $about = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$about) {
        $_SESSION['error_message'] = "About content not found.";
        echo "<script>window.location.href = 'display_about.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    error_log("Fetch about error: " . $e->getMessage()); $_SESSION['error_message'] = "An error occurred. Please try again.";
    echo "<script>window.location.href = 'display_about.php';</script>";
    exit();
}

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

    $image = $about['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        if (!empty($about['image']) && file_exists("propertyMgt/aboutImg/" . $about['image'])) {
            unlink("propertyMgt/aboutImg/" . $about['image']);
        }
        $image = basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "propertyMgt/aboutImg/" . $image);
    }

    try {
        $stmt = $conn->prepare("UPDATE about_content SET image = :image, title = :title, description = :description, more_description = :more_description, client = :client, cases_won = :cases_won, achievements = :achievements, our_team = :our_team, status = :status WHERE id = :id");
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':more_description', $more_description);
        $stmt->bindParam(':client', $client);
        $stmt->bindParam(':cases_won', $cases_won);
        $stmt->bindParam(':achievements', $achievements);
        $stmt->bindParam(':our_team', $our_team);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $_SESSION['success_message'] = "About content updated successfully!";
        echo "<script>window.location.href = 'display_about.php';</script>";
        exit();
    } catch (PDOException $e) {
        error_log("Update about error: " . $e->getMessage()); $_SESSION['error_message'] = "An error occurred. Please try again.";
    }
}
?>

<style>
.flf-form-section {
    margin-bottom: 32px;
    padding-bottom: 28px;
    border-bottom: 1px solid var(--fl-chambers-100);
}
.flf-form-section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
.flf-section-title {
    display: flex; align-items: center; gap: 10px;
    font-family: var(--fl-font-display); font-size: 20px; font-weight: 700;
    color: var(--fl-chambers-600); margin: 0 0 22px;
}
.flf-section-title i { color: var(--fl-seal-600); font-size: 16px; }
.flf-field { margin-bottom: 20px; }
.flf-field:last-child { margin-bottom: 0; }
.flf-field label {
    display: block; font-family: var(--fl-font-body); font-weight: 600;
    font-size: 13.5px; color: var(--fl-ink-500); margin-bottom: 7px;
}
.flf-field label i { color: var(--fl-seal-600); margin-right: 5px; width: 16px; text-align: center; }
.flf-field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.flf-upload-area {
    position: relative; width: 100%; border: 2px dashed #d9dee9;
    border-radius: var(--fl-r-md); background: #fafbfe; text-align: center;
    padding: 36px 20px; cursor: pointer; transition: all 0.3s ease;
}
.flf-upload-area:hover, .flf-upload-area.flf-dragover { border-color: var(--fl-chambers-600); background: rgba(24, 53, 143, 0.04); }
.flf-upload-area input[type="file"] { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
.flf-upload-icon { font-size: 36px; color: var(--fl-chambers-100); margin-bottom: 10px; }
.flf-upload-area:hover .flf-upload-icon, .flf-upload-area.flf-dragover .flf-upload-icon { color: var(--fl-chambers-600); }
.flf-upload-text { font-family: var(--fl-font-body); font-size: 14px; color: var(--fl-ink-500); margin: 0 0 2px; }
.flf-upload-hint { font-family: var(--fl-font-body); font-size: 12px; color: var(--fl-ink-400); margin: 0; }
.flf-img-current-wrap { display: flex; align-items: flex-start; gap: 24px; flex-wrap: wrap; }
.flf-img-current-wrap .flf-upload-side { flex: 1; min-width: 200px; }
.flf-current-img { width: 100%; max-width: 360px; border-radius: var(--fl-r-md); border: 2px solid var(--fl-chambers-100); }
.flf-upload-preview { display: block; margin-top: 14px; position: relative; max-width: 360px; }
.flf-upload-preview img { width: 100%; max-height: 200px; object-fit: cover; border-radius: var(--fl-r-sm); border: 2px solid var(--fl-chambers-100); }
.flf-upload-preview .flf-remove-img {
    position: absolute; top: 8px; right: 8px; width: 28px; height: 28px; border-radius: 50%;
    background: var(--flf-danger); color: var(--fl-surface); border: none; font-size: 12px;
    display: none; align-items: center; justify-content: center; cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
.flf-radio-group { display: flex; gap: 24px; margin-top: 6px; }
.flf-radio-card {
    display: flex; align-items: center; gap: 10px; padding: 12px 20px;
    border: 2px solid #d9dee9; border-radius: var(--fl-r-sm); background: var(--fl-surface);
    cursor: pointer; transition: all 0.2s ease;
    font-family: var(--fl-font-body); font-size: 14px; font-weight: 500; color: var(--fl-chambers-900);
}
.flf-radio-card:hover { border-color: var(--fl-chambers-600); }
.flf-radio-card input[type="radio"] { accent-color: var(--fl-chambers-600); width: 18px; height: 18px; }
.flf-radio-card:has(input:checked) { border-color: var(--fl-chambers-600); background: rgba(233, 238, 250, 0.5); color: var(--fl-chambers-600); font-weight: 600; }
@media (max-width: 767px) { .flf-field-row { grid-template-columns: 1fr; } .flf-radio-group { flex-direction: column; gap: 10px; } .flf-img-current-wrap { flex-direction: column; } }
</style>

<div class="midde_cont">
    <div class="container-fluid">

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:var(--flf-radius-sm);margin:0;">
                        <i class="fa fa-check-circle" style="margin-right:6px;"></i>
                        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:var(--flf-radius-sm);margin:0;">
                        <i class="fa fa-exclamation-triangle" style="margin-right:6px;"></i>
                        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                            <h2><i class="fa fa-pencil-square-o" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>Edit About Content</h2>
                            <a href="display_about.php" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left" style="margin-right:5px;"></i>Back to About
                            </a>
                        </div>
                    </div>

                    <div class="full padding_infor_info">
                        <form action="edit_about.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data" style="max-width:820px;">
                            <?php echo csrfHiddenField(); ?>

                            <!-- Section 1: Image -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-camera"></i>Featured Image</h3>

                                <div class="flf-field">
                                    <div class="flf-img-current-wrap">
                                        <div style="flex-shrink:0;">
                                            <?php if (!empty($about['image'])): ?>
                                                <img src="propertyMgt/aboutImg/<?php echo htmlspecialchars($about['image']); ?>" alt="Current Image" class="flf-current-img" id="currentImage">
                                            <?php else: ?>
                                                <div style="width:100%;max-width:360px;height:200px;background:var(--flf-blue);border-radius:var(--flf-radius);display:flex;align-items:center;justify-content:center;border:2px solid var(--flf-blue);">
                                                    <i class="fa fa-image" style="font-size:40px;color:var(--flf-muted);"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flf-upload-side">
                                            <div class="flf-upload-area" id="uploadArea">
                                                <input type="file" name="image" id="imageUpload" accept="image/*">
                                                <div class="flf-upload-icon" style="font-size:28px;"><i class="fa fa-cloud-upload"></i></div>
                                                <p class="flf-upload-text"><strong>Replace image</strong></p>
                                                <p class="flf-upload-hint">Leave empty to keep current image</p>
                                            </div>
                                            <div class="flf-upload-preview" id="imagePreview">
                                                <img src="" alt="New Preview">
                                                <button type="button" class="flf-remove-img" id="removeImage" title="Remove selection"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Content -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-edit"></i>Content</h3>

                                <div class="flf-field">
                                    <label><i class="fa fa-heading"></i>Title</label>
                                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($about['title']); ?>" required>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-align-left"></i>Description</label>
                                    <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($about['description']); ?></textarea>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-paragraph"></i>More Description</label>
                                    <textarea class="form-control" name="more_description" rows="4"><?php echo htmlspecialchars($about['more_description']); ?></textarea>
                                </div>
                            </div>

                            <!-- Section 3: Statistics -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-bar-chart"></i>Statistics</h3>

                                <div class="flf-field-row">
                                    <div class="flf-field">
                                        <label><i class="fa fa-users"></i>Client</label>
                                        <input type="text" class="form-control" name="client" value="<?php echo htmlspecialchars($about['client']); ?>">
                                    </div>

                                    <div class="flf-field">
                                        <label><i class="fa fa-trophy"></i>Cases Won</label>
                                        <input type="text" class="form-control" name="cases_won" value="<?php echo htmlspecialchars($about['cases_won']); ?>" required>
                                    </div>
                                </div>

                                <div class="flf-field-row">
                                    <div class="flf-field">
                                        <label><i class="fa fa-star"></i>Achievements</label>
                                        <textarea class="form-control" name="achievements" rows="3"><?php echo htmlspecialchars($about['achievements']); ?></textarea>
                                    </div>

                                    <div class="flf-field">
                                        <label><i class="fa fa-user-md"></i>Our Team</label>
                                        <textarea class="form-control" name="our_team" rows="3"><?php echo htmlspecialchars($about['our_team']); ?></textarea>
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
                                            <input type="radio" name="status" value="Active" <?php echo ($about['status'] === 'Active') ? 'checked' : ''; ?>>
                                            <i class="fa fa-check-circle" style="color:var(--flf-success);"></i>
                                            Active
                                        </label>
                                        <label class="flf-radio-card">
                                            <input type="radio" name="status" value="Pending" <?php echo ($about['status'] === 'Pending') ? 'checked' : ''; ?>>
                                            <i class="fa fa-clock-o" style="color:var(--flf-gold);"></i>
                                            Pending
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div style="padding-top:8px;">
                                <button type="submit" class="btn btn-info">
                                    <i class="fa fa-save" style="margin-right:5px;"></i>Update About Content
                                </button>
                                <a href="display_about.php" class="btn btn-secondary" style="margin-left:10px;">Cancel</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
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
                removeImage.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }
    });

    removeImage.addEventListener('click', function() {
        imageUpload.value = '';
        imagePreview.style.display = 'none';
        imagePreview.querySelector('img').src = '';
        removeImage.style.display = 'none';
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