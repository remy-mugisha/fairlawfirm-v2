<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "No property ID specified.";
    echo "<script>window.location.href = 'display_properties.php';</script>";
    exit();
}

$id = $_GET['id'];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
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

<div class="row column1">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Edit Property</h2>
                </div>
            </div>
            <div class="full progress_bar_inner">
                <div class="row">
                    <div class="col-md-12">
                        <div class="full padding_infor_info">
                            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <label class="control-label col-sm-3">Current Image</label>
                                    <div class="col-sm-9">
                                        <img src="propertyMgt/proImg/<?php echo htmlspecialchars($property['image']); ?>" alt="Current Property Image" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                </div>                             
                                <div class="form-group row">
                                    <label class="control-label col-sm-3">Update Image</label>
                                    <div class="col-sm-9">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="image" id="imageUpload" accept="image/*">
                                            <label class="custom-file-label" for="imageUpload">Choose new file (leave empty to keep current)</label>
                                        </div>
                                        <div class="mt-3" id="imagePreview" style="display: none;">
                                            <img src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-sm-3">Location</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="location" id="location" value="<?php echo htmlspecialchars($property['location']); ?>" required>
                                    </div>
                                </div>                             
                                <div class="form-group row">
                                    <label class="control-label col-sm-3" for="title">Title</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" name="title" id="title" rows="4" required><?php echo htmlspecialchars($property['title']); ?></textarea>
                                    </div>
                                </div>                               
                                <div class="form-group row">
                                    <div class="col-sm-9 offset-sm-3">
                                        <button type="submit" name="update" class="btn btn-info">Update Property</button>
                                        <a href="display_properties.php" class="btn btn-secondary ml-2">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageUpload = document.getElementById('imageUpload');
    const imagePreview = document.getElementById('imagePreview');
    
    imageUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.style.display = 'block';
                imagePreview.querySelector('img').src = e.target.result;
            }
            reader.readAsDataURL(file);          
            const fileName = file.name;
            const label = this.nextElementSibling;
            label.textContent = fileName;
        }
    });
});
</script>

<?php
require_once 'include/footer.php';
?>