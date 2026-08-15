<?php
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

// Handle image uploads
if (isset($_POST['submit'])) {
    $property_id = $_POST['property_id'];
    
    // Check if property exists
    $check_property = $conn->prepare("SELECT id FROM properties WHERE id = ?");
    $check_property->execute([$property_id]);
    
    if ($check_property->rowCount() == 0) {
        $_SESSION['error_message'] = "Property not found!";
        header("Location: property_images.php");
        exit();
    }
    
    // Handle file uploads
    if (!empty($_FILES['images']['name'][0])) {
        $upload_dir = 'propertyMgt/rentalImg/';
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $uploaded_files = [];
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['images']['name'][$key];
            $file_size = $_FILES['images']['size'][$key];
            $file_tmp = $_FILES['images']['tmp_name'][$key];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validate file
            if (in_array($file_ext, $allowed_ext)) {
                if ($file_size <= 5000000) { // 5MB max
                    $new_file_name = uniqid() . '.' . $file_ext;
                    $destination = $upload_dir . $new_file_name;
                    
                    if (move_uploaded_file($file_tmp, $destination)) {
                        // Insert into database
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

// Handle setting featured image
if (isset($_GET['set_featured'])) {
    $image_id = $_GET['set_featured'];
    $property_id = $_GET['property_id'];
    
    // First reset all featured images for this property
    $reset_stmt = $conn->prepare("UPDATE property_images SET is_featured = 0 WHERE property_id = ?");
    $reset_stmt->execute([$property_id]);
    
    // Then set the selected image as featured
    $feature_stmt = $conn->prepare("UPDATE property_images SET is_featured = 1 WHERE id = ?");
    $feature_stmt->execute([$image_id]);
    
    $_SESSION['success_message'] = "Featured image updated successfully!";
    header("Location: property_images.php?property_id=$property_id");
    exit();
}

// Handle image deletion
if (isset($_GET['delete_image'])) {
    $image_id = $_GET['delete_image'];
    $property_id = $_GET['property_id'];
    
    // Get image info before deleting
    $get_stmt = $conn->prepare("SELECT image_path FROM property_images WHERE id = ?");
    $get_stmt->execute([$image_id]);
    $image = $get_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($image) {
        // Delete from database
        $del_stmt = $conn->prepare("DELETE FROM property_images WHERE id = ?");
        $del_stmt->execute([$image_id]);
        
        // Delete file
        if (file_exists($image['image_path'])) {
            unlink($image['image_path']);
        }
        
        $_SESSION['success_message'] = "Image deleted successfully!";
        header("Location: property_images.php?property_id=$property_id");
        exit();
    }
}

// Get all properties for dropdown
$properties = $conn->query("SELECT id, title FROM properties ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);

// Get images for selected property
$property_images = [];
$current_property = null;
if (isset($_GET['property_id'])) {
    $property_id = $_GET['property_id'];
    
    // Fixed the query execution - separate prepare and execute
    $stmt = $conn->prepare("SELECT id, title FROM properties WHERE id = ?");
    $stmt->execute([$property_id]);
    $current_property = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fixed the images query execution
    $stmt = $conn->prepare("SELECT * FROM property_images WHERE property_id = ? ORDER BY is_featured DESC, id DESC");
    $stmt->execute([$property_id]);
    $property_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Images Management</title>
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> -->
    <style>
        .image-thumbnail {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            margin: 5px;
            transition: all 0.3s ease;
        }
        .image-thumbnail:hover {
            transform: scale(1.05);
            border-color: #007bff;
        }
        .featured-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }
        .image-container {
            position: relative;
            display: inline-block;
            margin: 10px;
        }
        .image-actions {
            position: absolute;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
        }
        .preview-image {
            max-width: 100%;
            max-height: 300px;
            margin-bottom: 15px;
        }
        #imagePreviewContainer {
            display: flex;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        .preview-thumbnail {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="row column1">
        <div class="col-md-12">
            <div class="white_shd full margin_bottom_30">
                <div class="full graph_head">
                    <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                        <h2>Property Images Management</h2>
                        <a href="display_rental.php" class="btn btn-info btn-sm">View All Properties</a>
                    </div>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="full progress_bar_inner">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="full padding_infor_info">
                                <!-- Property Selection Form -->
                                <form method="get" action="property_images.php" class="mb-4">
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Select Property</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="property_id" required>
                                                <option value="">-- Select Property --</option>
                                                <?php foreach ($properties as $property): ?>
                                                    <option value="<?php echo $property['id']; ?>" <?php echo isset($_GET['property_id']) && $_GET['property_id'] == $property['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($property['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-info">Load Images</button>
                                        </div>
                                    </div>
                                </form>

                                <?php if (isset($_GET['property_id']) && $current_property): ?>
                                    <h4>Images for: <?php echo htmlspecialchars($current_property['title']); ?></h4>
                                    
                                    <!-- Image Upload Form -->
                                    <form method="post" action="property_images.php" enctype="multipart/form-data" class="mb-5">
                                        <input type="hidden" name="property_id" value="<?php echo $current_property['id']; ?>">
                                        
                                        <div class="form-group">
                                            <label>Upload Images (Multiple allowed)</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="images[]" id="imageUpload" multiple accept="image/*" required>
                                                <label class="custom-file-label" for="imageUpload">Choose files</label>
                                            </div>
                                            <small class="form-text text-muted">You can select multiple images (Max 5MB each)</small>
                                        </div>
                                        
                                        <div id="imagePreviewContainer"></div>
                                        
                                        <div class="form-group">
                                            <button type="submit" name="submit" class="btn btn-success">
                                                <i class="fa fa-upload"></i> Upload Images
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Existing Images Gallery -->
                                    <div class="mt-4">
                                        <h5>Existing Images</h5>
                                        <?php if (count($property_images) > 0): ?>
                                            <div class="d-flex flex-wrap">
                                                <?php foreach ($property_images as $image): ?>
                                                    <div class="image-container">
                                                        <img src="<?php echo $image['image_path']; ?>" alt="Property Image" class="image-thumbnail">
                                                        
                                                        <?php if ($image['is_featured']): ?>
                                                            <span class="featured-badge">Featured</span>
                                                        <?php endif; ?>
                                                        
                                                        <div class="image-actions">
                                                            <?php if (!$image['is_featured']): ?>
                                                                <a href="property_images.php?set_featured=<?php echo $image['id']; ?>&property_id=<?php echo $current_property['id']; ?>" 
                                                                   class="btn btn-sm btn-info" title="Set as Featured">
                                                                    <i class="fa fa-star"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                            
                                                            <a href="property_images.php?delete_image=<?php echo $image['id']; ?>&property_id=<?php echo $current_property['id']; ?>" 
                                                               class="btn btn-sm btn-danger" 
                                                               onclick="return confirm('Are you sure you want to delete this image?')" 
                                                               title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info">No images found for this property.</div>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif (isset($_GET['property_id'])): ?>
                                    <div class="alert alert-danger">Property not found!</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
    // Show selected file names
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var files = e.target.files;
        var label = this.nextElementSibling;
        var previewContainer = document.getElementById('imagePreviewContainer');
        
        if (files.length > 0) {
            if (files.length === 1) {
                label.textContent = files[0].name;
            } else {
                label.textContent = files.length + ' files selected';
            }
            
            // Clear previous previews
            previewContainer.innerHTML = '';
            
            // Create previews for new files
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                if (file.type.match('image.*')) {
                    var reader = new FileReader();
                    
                    reader.onload = (function(file) {
                        return function(e) {
                            var img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'preview-thumbnail';
                            img.title = file.name;
                            previewContainer.appendChild(img);
                        };
                    })(file);
                    
                    reader.readAsDataURL(file);
                }
            }
        } else {
            label.textContent = 'Choose files';
            previewContainer.innerHTML = '';
        }
    });
    
    // Initialize Bootstrap tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    </script>
</body>
</html>