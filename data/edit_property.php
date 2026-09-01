<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';
require_once __DIR__ . '/csrf.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "No property ID specified.";
    echo "<script>window.location.href = 'display_properties.php';</script>";
    exit();
}

$id = $_GET['id'];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    requireCsrfPost();
    $location = $_POST['location'];
    $title = $_POST['title'];
    
    try {
        if (!empty($_FILES['image']['name'])) {
            $stmt = $conn->prepare("SELECT image FROM add_property WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $property = $stmt->fetch(PDO::FETCH_ASSOC);
            $oldImagePath = $property['image'];            
            $targetDir = "propertyMgt/proImg/";
            
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            $fileName = basename($_FILES["image"]["name"]);
            $targetFilePath = $targetDir . time() . '_' . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            
            $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array(strtolower($fileType), $allowTypes)) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                    $imageDbPath = time() . '_' . $fileName;
                    
                    $stmt = $conn->prepare("UPDATE add_property SET image = :image, location = :location, title = :title WHERE id = :id");
                    $stmt->bindParam(':image', $imageDbPath);
                    $stmt->bindParam(':location', $location);
                    $stmt->bindParam(':title', $title);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    
                    if (!empty($oldImagePath) && file_exists("propertyMgt/proImg/" . $oldImagePath)) {
                        unlink("propertyMgt/proImg/" . $oldImagePath);
                    }
                    
                    $_SESSION['success_message'] = "Property updated successfully!";
                } else {
                    $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
                }
            } else {
                $_SESSION['error_message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            }
        } else {
            $stmt = $conn->prepare("UPDATE add_property SET location = :location, title = :title WHERE id = :id");
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $_SESSION['success_message'] = "Property updated successfully!";
        }
        
        echo "<script>window.location.href = 'display_properties.php';</script>";
        exit();
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Error updating property: " . $e->getMessage();
    }
}

try {
    $stmt = $conn->prepare("SELECT * FROM add_property WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $property = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$property) {
        $_SESSION['error_message'] = "Property not found.";
        echo "<script>window.location.href = 'display_properties.php';</script>";
        exit();
    }
} catch(PDOException $e) {
    $_SESSION['error_message'] = "Error fetching property: " . $e->getMessage();
    echo "<script>window.location.href = 'display_properties.php';</script>";
    exit();
}
?>

<style>
.flf-current-img {
    width: 100%;
    max-width: 420px;
    border-radius: var(--flf-radius);
    border: 2px solid var(--flf-blue);
    box-shadow: 0 2px 10px rgba(1, 22, 106, 0.08);
}
.flf-img-current-wrap {
    display: flex;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
}
.flf-img-current-wrap .flf-upload-side {
    flex: 1;
    min-width: 200px;
}
.flf-upload-area {
    position: relative;
    width: 100%;
    border: 2px dashed #d9dee9;
    border-radius: var(--flf-radius);
    background: #fafbfe;
    text-align: center;
    padding: 32px 16px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.flf-upload-area:hover,
.flf-upload-area.flf-dragover {
    border-color: var(--flf-royal);
    background: rgba(24, 53, 143, 0.04);
}
.flf-upload-area input[type="file"] {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}
.flf-upload-icon { font-size: 32px; color: var(--flf-blue); margin-bottom: 8px; }
.flf-upload-area:hover .flf-upload-icon,
.flf-upload-area.flf-dragover .flf-upload-icon { color: var(--flf-royal); }
.flf-upload-text { font-family: var(--flf-font-body); font-size: 14px; color: var(--flf-slate); margin: 0 0 2px; }
.flf-upload-hint { font-family: var(--flf-font-body); font-size: 12px; color: var(--flf-muted); margin: 0; }
.flf-upload-preview {
    display: none;
    margin-top: 14px;
    position: relative;
    max-width: 320px;
    margin-left: auto;
    margin-right: auto;
}
.flf-upload-preview img {
    width: 100%;
    max-height: 180px;
    object-fit: cover;
    border-radius: var(--flf-radius-sm);
    border: 2px solid var(--flf-blue);
}
.flf-upload-preview .flf-remove-img {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: var(--flf-danger);
    color: var(--flf-white);
    border: none;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
.flf-field {
    margin-bottom: 22px;
}
.flf-field label {
    display: block;
    font-family: var(--flf-font-body);
    font-weight: 600;
    font-size: 13.5px;
    color: var(--flf-slate);
    margin-bottom: 8px;
}
.flf-img-label {
    font-family: var(--flf-font-body);
    font-weight: 600;
    font-size: 13.5px;
    color: var(--flf-slate);
    display: block;
    margin-bottom: 8px;
}
</style>

<div class="midde_cont">
    <div class="container-fluid">

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:var(--flf-radius-sm);margin:0;">
                        <i class="fa fa-check-circle" style="margin-right:6px;"></i>
                        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:var(--flf-radius-sm);margin:0;">
                        <i class="fa fa-exclamation-triangle" style="margin-right:6px;"></i>
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                            <h2><i class="fa fa-pencil-square-o" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>Edit Property</h2>
                            <a href="display_properties.php" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left" style="margin-right:5px;"></i>Back to Listings
                            </a>
                        </div>
                    </div>

                    <div class="full padding_infor_info">
                        <form action="" method="post" enctype="multipart/form-data" style="max-width:820px;"><?php echo csrfHiddenField(); ?>

                            <div class="flf-field">
                                <span class="flf-img-label"><i class="fa fa-image" style="color:var(--flf-gold);margin-right:5px;"></i>Property Image</span>
                                <div class="flf-img-current-wrap">
                                    <div style="flex-shrink:0;">
                                        <?php if (!empty($property['image'])): ?>
                                            <img src="propertyMgt/proImg/<?php echo htmlspecialchars($property['image']); ?>" alt="Current Property Image" class="flf-current-img" id="currentImage">
                                        <?php else: ?>
                                            <div style="width:100%;max-width:420px;height:220px;background:var(--flf-blue);border-radius:var(--flf-radius);display:flex;align-items:center;justify-content:center;border:2px solid var(--flf-blue);">
                                                <i class="fa fa-image" style="font-size:40px;color:var(--flf-muted);"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flf-upload-side">
                                        <div class="flf-upload-area" id="uploadArea">
                                            <input type="file" name="image" id="imageUpload" accept="image/*">
                                            <div class="flf-upload-icon"><i class="fa fa-cloud-upload"></i></div>
                                            <p class="flf-upload-text"><strong>Replace image</strong></p>
                                            <p class="flf-upload-hint">Leave empty to keep current image</p>
                                        </div>
                                        <div class="flf-upload-preview" id="imagePreview">
                                            <img src="" alt="New Preview">
                                            <button type="button" class="flf-remove-img" id="removePreview" title="Remove selection">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flf-field">
                                <label for="location"><i class="fa fa-map-marker-alt" style="color:var(--flf-gold);margin-right:5px;"></i>Location</label>
                                <input type="text" class="form-control" name="location" id="location" value="<?php echo htmlspecialchars($property['location']); ?>" placeholder="Enter property location" required>
                            </div>

                            <div class="flf-field">
                                <label for="title"><i class="fa fa-tag" style="color:var(--flf-gold);margin-right:5px;"></i>Title</label>
                                <input type="text" class="form-control" name="title" id="title" value="<?php echo htmlspecialchars($property['title']); ?>" placeholder="Enter property title" required>
                            </div>

                            <div style="padding-top:8px;">
                                <button type="submit" name="update" class="btn btn-info">
                                    <i class="fa fa-save" style="margin-right:5px;"></i>Update Property
                                </button>
                                <a href="display_properties.php" class="btn btn-secondary" style="margin-left:10px;">Cancel</a>
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
    var removePreview = document.getElementById('removePreview');
    var uploadArea = document.getElementById('uploadArea');
    var currentImage = document.getElementById('currentImage');

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