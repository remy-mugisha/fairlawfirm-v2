<?php
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';
require_once __DIR__ . '/csrf.php';

if (isset($_POST['submit'])) {
    requireCsrfPost();
    $property_id = $_POST['property_id'];
    
    $check_property = $conn->prepare("SELECT id FROM properties WHERE id = ?");
    $check_property->execute([$property_id]);
    
    if ($check_property->rowCount() == 0) {
        $_SESSION['error_message'] = "Property not found!";
        header("Location: property_images.php");
        exit();
    }
    
    if (!empty($_FILES['images']['name'][0])) {
        $upload_dir = 'propertyMgt/rentalImg/';
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $uploaded_files = [];
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['images']['name'][$key];
            $file_size = $_FILES['images']['size'][$key];
            $file_tmp = $_FILES['images']['tmp_name'][$key];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (in_array($file_ext, $allowed_ext)) {
                if ($file_size <= 5000000) {
                    $new_file_name = uniqid() . '.' . $file_ext;
                    $destination = $upload_dir . $new_file_name;
                    
                    if (move_uploaded_file($file_tmp, $destination)) {
                        $is_featured = ($key == 0 && !isset($_POST['featured'])) ? 1 : 0;
                        
                        $stmt = $conn->prepare("INSERT INTO property_images (property_id, image_name, image_path, is_featured) 
                                              VALUES (?, ?, ?, ?)");
                        $stmt->execute([$property_id, $new_file_name, $destination, $is_featured]);
                        
                        $uploaded_files[] = $file_name;
                    } else {
                        $_SESSION['error_message'] = "Failed to upload $file_name";
                        header("Location: property_images.php");
                        exit();
                    }
                } else {
                    $_SESSION['error_message'] = "File $file_name is too large (max 5MB)";
                    header("Location: property_images.php");
                    exit();
                }
            } else {
                $_SESSION['error_message'] = "Invalid file type for $file_name (only JPG, JPEG, PNG, GIF allowed)";
                header("Location: property_images.php");
                exit();
            }
        }
        
        if (!empty($uploaded_files)) {
            $_SESSION['success_message'] = "Successfully uploaded " . count($uploaded_files) . " images!";
            header("Location: property_images.php?property_id=$property_id");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Please select at least one image to upload";
        header("Location: property_images.php");
        exit();
    }
}

if (isset($_GET['set_featured'])) {
    $image_id = $_GET['set_featured'];
    $property_id = $_GET['property_id'];
    
    $reset_stmt = $conn->prepare("UPDATE property_images SET is_featured = 0 WHERE property_id = ?");
    $reset_stmt->execute([$property_id]);
    
    $feature_stmt = $conn->prepare("UPDATE property_images SET is_featured = 1 WHERE id = ?");
    $feature_stmt->execute([$image_id]);
    
    $_SESSION['success_message'] = "Featured image updated successfully!";
    header("Location: property_images.php?property_id=$property_id");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_image'])) {
    requireCsrfPost();
    $image_id = intval($_POST['delete_image']);
    $property_id = intval($_POST['property_id']);
    
    $get_stmt = $conn->prepare("SELECT image_path FROM property_images WHERE id = ?");
    $get_stmt->execute([$image_id]);
    $image = $get_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($image) {
        $del_stmt = $conn->prepare("DELETE FROM property_images WHERE id = ?");
        $del_stmt->execute([$image_id]);
        
        if (file_exists($image['image_path'])) {
            unlink($image['image_path']);
        }
        
        $_SESSION['success_message'] = "Image deleted successfully!";
        header("Location: property_images.php?property_id=$property_id");
        exit();
    }
}

$properties = $conn->query("SELECT id, title FROM properties ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);

$property_images = [];
$current_property = null;
if (isset($_GET['property_id'])) {
    $property_id = $_GET['property_id'];
    
    $stmt = $conn->prepare("SELECT id, title FROM properties WHERE id = ?");
    $stmt->execute([$property_id]);
    $current_property = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $conn->prepare("SELECT * FROM property_images WHERE property_id = ? ORDER BY is_featured DESC, id DESC");
    $stmt->execute([$property_id]);
    $property_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<style>
.flf-field { margin-bottom: 20px; }
.flf-field:last-child { margin-bottom: 0; }
.flf-field label {
    display: block; font-family: var(--fl-font-body); font-weight: 600;
    font-size: 13.5px; color: var(--fl-ink-500); margin-bottom: 7px;
}
.flf-field label i { color: var(--fl-seal-600); margin-right: 5px; width: 16px; text-align: center; }
.flf-field .form-control, .flf-field select {
    height: 46px; border: 1px solid #d9dee9; border-radius: var(--fl-r-sm);
    font-family: var(--fl-font-body); font-size: 14px; color: var(--fl-chambers-900);
    padding: 0 14px; transition: border-color 0.25s ease, box-shadow 0.25s ease;
    background: var(--fl-surface);
}
.flf-field select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B7899' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; padding-right: 36px; cursor: pointer; }
.flf-field .form-control:focus, .flf-field select:focus { border-color: var(--fl-chambers-600); box-shadow: 0 0 0 3px rgba(24, 53, 143, 0.12); outline: none; }
.flf-section-title {
    display: flex; align-items: center; gap: 10px;
    font-family: var(--fl-font-display); font-size: 18px; font-weight: 600;
    color: var(--fl-chambers-600); margin-bottom: 18px; padding-bottom: 10px;
    border-bottom: 2px solid var(--fl-chambers-100);
}
.flf-section-title i { color: var(--fl-seal-600); font-size: 16px; }
.flf-upload-zone {
    border: 2px dashed #d0d7e8; border-radius: var(--fl-r-sm); padding: 30px 20px;
    text-align: center; cursor: pointer; transition: all 0.25s ease; background: var(--fl-surface);
}
.flf-upload-zone:hover { border-color: var(--fl-chambers-600); background: rgba(233, 238, 250, 0.3); }
.flf-upload-zone.dragover { border-color: var(--fl-chambers-600); background: rgba(233, 238, 250, 0.5); }
.flf-upload-zone i { font-size: 36px; color: var(--fl-chambers-600); margin-bottom: 10px; display: block; }
.flf-upload-zone p { font-family: var(--fl-font-body); font-size: 14px; color: var(--fl-chambers-900); margin: 0 0 6px; }
.flf-upload-zone small { font-size: 12px; color: var(--fl-ink-400); }
.flf-upload-zone input[type="file"] { display: none; }
.flf-upload-label {
    display: inline-block; padding: 8px 18px; border-radius: var(--fl-r-sm);
    background: var(--fl-chambers-600); color: var(--fl-surface); font-family: var(--fl-font-body);
    font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.2s;
}
.flf-upload-label:hover { background: var(--fl-chambers-600); }
.flf-upload-count {
    display: inline-block; margin-top: 10px; padding: 4px 12px; border-radius: 20px;
    background: rgba(24, 53, 143, 0.08); color: var(--fl-chambers-600);
    font-family: var(--fl-font-body); font-size: 12px; font-weight: 600;
}
.flf-preview-grid { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 16px; }
.flf-preview-item {
    position: relative; width: 120px; height: 120px; border-radius: 8px; overflow: hidden;
    border: 2px solid var(--fl-chambers-100);
}
.flf-preview-item img { width: 100%; height: 100%; object-fit: cover; }
.flf-preview-item .flf-preview-remove {
    position: absolute; top: 4px; right: 4px; width: 22px; height: 22px; border-radius: 50%;
    background: rgba(161, 39, 52, 0.9); color: white; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center; font-size: 10px;
}
.flf-gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
.flf-gallery-card {
    position: relative; border-radius: var(--fl-r-md); overflow: hidden;
    border: 2px solid var(--fl-chambers-100); background: var(--fl-surface);
    transition: border-color 0.25s, box-shadow 0.25s;
}
.flf-gallery-card:hover { border-color: var(--fl-chambers-600); box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
.flf-gallery-card.featured { border-color: var(--fl-seal-600); box-shadow: 0 2px 12px rgba(200, 169, 81, 0.15); }
.flf-gallery-img {
    width: 100%; height: 180px; object-fit: cover; display: block;
    background: var(--flf-ice);
}
.flf-gallery-info { padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.flf-gallery-name {
    font-family: var(--fl-font-body); font-size: 12px; color: var(--fl-ink-400);
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 100px;
}
.flf-featured-badge {
    position: absolute; top: 10px; left: 10px; padding: 4px 10px; border-radius: 20px;
    background: var(--fl-seal-600); color: var(--fl-chambers-600);
    font-family: var(--fl-font-body); font-size: 10px; font-weight: 700;
    letter-spacing: 0.5px; text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.flf-gallery-actions { display: flex; gap: 5px; }
.flf-gallery-actions .btn-sm {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; padding: 0; border-radius: 7px; font-size: 12px;
}
.flf-empty-state { text-align: center; padding: 48px 20px; }
.flf-empty-state i { font-size: 42px; color: var(--fl-chambers-100); margin-bottom: 14px; display: block; }
.flf-empty-state p { font-family: var(--fl-font-body); font-size: 14px; color: var(--fl-ink-400); margin: 0; }
</style>

<div class="midde_cont">
    <div class="container-fluid">

        <?php if (!empty($_SESSION['success_message'])): ?>
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

        <?php if (!empty($_SESSION['error_message'])): ?>
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
                        <div class="heading1 margin_0" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                            <h2 style="margin:0;"><i class="fa fa-images" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>Property Images</h2>
                            <a href="display_properties.php" class="btn btn-info btn-sm"><i class="fa fa-arrow-left" style="margin-right:5px;"></i>All Properties</a>
                        </div>
                    </div>

                    <div class="full padding_infor_info">
                        <form method="GET" action="property_images.php">
                            <div class="flf-field">
                                <label><i class="fa fa-home"></i>Select Property</label>
                                <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                                    <select name="property_id" required style="flex:1;min-width:250px;height:46px;border:1px solid #d9dee9;border-radius:var(--flf-radius-sm);font-family:var(--flf-font-body);font-size:14px;color:var(--flf-charcoal);padding:0 36px 0 14px;background:var(--flf-white) url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2712%27 height=%2712%27 viewBox=%270 0 12 12%27%3E%3Cpath fill=%27%236B7899%27 d=%27M6 8L1 3h10z%27/%3E%3C/svg%3E') no-repeat right 14px center;appearance:none;cursor:pointer;">
                                        <option value="">-- Select Property --</option>
                                        <?php foreach ($properties as $property): ?>
                                            <option value="<?php echo $property['id']; ?>" <?php echo isset($_GET['property_id']) && $_GET['property_id'] == $property['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($property['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-info" style="white-space:nowrap;">
                                        <i class="fa fa-refresh" style="margin-right:5px;"></i>Load Images
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['property_id']) && $current_property): ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="white_shd full margin_bottom_30">
                        <div class="full graph_head">
                            <div class="heading1 margin_0">
                                <h2><i class="fa fa-upload" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>Upload Images</h2>
                            </div>
                        </div>

                        <div class="full padding_infor_info">
                            <div class="flf-section-title">
                                <i class="fa fa-info-circle"></i>
                                Images for: <strong><?php echo htmlspecialchars($current_property['title']); ?></strong>
                            </div>

                            <form method="POST" action="property_images.php" enctype="multipart/form-data" id="uploadForm"><?php echo csrfHiddenField(); ?>
                                <input type="hidden" name="property_id" value="<?php echo $current_property['id']; ?>">

                                <div class="flf-field">
                                    <label><i class="fa fa-camera"></i>Select Images</label>
                                    <div class="flf-upload-zone" id="dropZone">
                                        <i class="fa fa-cloud-upload"></i>
                                        <p>Drag & drop images here or <label for="imageUpload" class="flf-upload-label" style="margin-left:4px;">Browse Files</label></p>
                                        <small>JPG, JPEG, PNG, GIF &middot; Max 5MB each</small>
                                        <input type="file" name="images[]" id="imageUpload" multiple accept="image/*" required>
                                    </div>
                                    <div id="fileCount" class="flf-upload-count" style="display:none;"></div>
                                </div>

                                <div id="imagePreviewContainer" class="flf-preview-grid"></div>

                                <div style="padding-top:12px;">
                                    <button type="submit" name="submit" class="btn btn-info">
                                        <i class="fa fa-upload" style="margin-right:5px;"></i>Upload Images
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="white_shd full margin_bottom_30">
                        <div class="full graph_head">
                            <div class="heading1 margin_0">
                                <h2><i class="fa fa-th-large" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>Existing Images</h2>
                            </div>
                        </div>

                        <div class="full padding_infor_info">
                            <?php if (empty($property_images)): ?>
                                <div class="flf-empty-state">
                                    <i class="fa fa-image"></i>
                                    <p>No images uploaded for this property yet.</p>
                                </div>
                            <?php else: ?>
                                <div class="flf-gallery-grid">
                                    <?php foreach ($property_images as $image): ?>
                                        <div class="flf-gallery-card <?php echo $image['is_featured'] ? 'featured' : ''; ?>">
                                            <?php if ($image['is_featured']): ?>
                                                <span class="flf-featured-badge"><i class="fa fa-star" style="margin-right:3px;"></i>Featured</span>
                                            <?php endif; ?>
                                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Property Image" class="flf-gallery-img"
                                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                            <div style="display:none;width:100%;height:180px;align-items:center;justify-content:center;background:var(--flf-ice);color:var(--flf-muted);font-size:36px;"><i class="fa fa-image"></i></div>
                                            <div class="flf-gallery-info">
                                                <span class="flf-gallery-name" title="<?php echo htmlspecialchars($image['image_name']); ?>">
                                                    <?php echo htmlspecialchars($image['image_name']); ?>
                                                </span>
                                                <div class="flf-gallery-actions">
                                                    <?php if (!$image['is_featured']): ?>
                                                        <a href="property_images.php?set_featured=<?php echo $image['id']; ?>&property_id=<?php echo $current_property['id']; ?>"
                                                           class="btn btn-info btn-sm" title="Set as Featured">
                                                            <i class="fa fa-star"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <form method="POST" action="property_images.php" style="display:inline;" onsubmit="return confirm('Delete this image?');">
                                                        <?php echo csrfHiddenField(); ?>
                                                        <input type="hidden" name="delete_image" value="<?php echo $image['id']; ?>">
                                                        <input type="hidden" name="property_id" value="<?php echo $current_property['id']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif (isset($_GET['property_id'])): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger" style="border-radius:var(--flf-radius-sm);">
                        <i class="fa fa-exclamation-triangle" style="margin-right:6px;"></i>Property not found!
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<div id="deleteModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:var(--flf-white);border-radius:var(--flf-radius);box-shadow:0 20px 60px rgba(0,0,0,0.3);max-width:420px;width:90%;padding:0;overflow:hidden;">
        <div style="padding:28px 28px 0;text-align:center;">
            <div style="width:60px;height:60px;border-radius:50%;background:rgba(161,39,52,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <i class="fa fa-trash" style="font-size:24px;color:var(--flf-danger);"></i>
            </div>
            <h4 style="font-family:var(--flf-font-head);color:var(--flf-navy);margin:0 0 8px;font-size:22px;">Delete Image</h4>
            <p style="font-family:var(--flf-font-body);color:var(--flf-muted);font-size:14px;margin:0;">
                Are you sure you want to delete this image? The file will be permanently removed.
            </p>
        </div>
        <div style="padding:20px 28px 28px;display:flex;gap:12px;justify-content:center;">
            <a href="#" class="btn btn-secondary" onclick="document.getElementById('deleteModal').style.display='none';return false;" style="min-width:110px;">Cancel</a>
            <a id="deleteConfirmBtn" href="#" class="btn btn-danger" style="min-width:110px;">
                <i class="fa fa-trash" style="margin-right:5px;"></i>Delete
            </a>
        </div>
    </div>
</div>

<script>
(function() {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('imageUpload');
    var previewContainer = document.getElementById('imagePreviewContainer');
    var fileCountEl = document.getElementById('fileCount');
    var deleteConfirmBtn = document.getElementById('deleteConfirmBtn');

    dropZone.addEventListener('click', function() { fileInput.click(); });

    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault(); dropZone.classList.add('dragover');
    });
    dropZone.addEventListener('dragleave', function() {
        dropZone.classList.remove('dragover');
    });
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault(); dropZone.classList.remove('dragover');
        fileInput.files = e.dataTransfer.files;
        fileInput.dispatchEvent(new Event('change'));
    });

    fileInput.addEventListener('change', function(e) {
        var files = e.target.files;
        previewContainer.innerHTML = '';

        if (files.length > 0) {
            fileCountEl.style.display = 'inline-block';
            fileCountEl.textContent = files.length + ' file' + (files.length > 1 ? 's' : '') + ' selected';

            for (var i = 0; i < files.length; i++) {
                if (files[i].type.match('image.*')) {
                    (function(file) {
                        var reader = new FileReader();
                        reader.onload = function(ev) {
                            var item = document.createElement('div');
                            item.className = 'flf-preview-item';
                            item.innerHTML = '<img src="' + ev.target.result + '" title="' + file.name + '">';
                            previewContainer.appendChild(item);
                        };
                        reader.readAsDataURL(file);
                    })(files[i]);
                }
            }
        } else {
            fileCountEl.style.display = 'none';
        }
    });

    deleteConfirmBtn.addEventListener('click', function(e) {
        e.preventDefault();
        var id = document.getElementById('deleteId').value;
        var pid = '<?php echo isset($_GET["property_id"]) ? $_GET["property_id"] : ""; ?>';
        window.location.href = 'property_images.php?delete_image=' + id + '&property_id=' + pid;
    });
})();
</script>

<input type="hidden" id="deleteId" value="">

<?php
require_once 'include/footer.php';
ob_end_flush();
?>