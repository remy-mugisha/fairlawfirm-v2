<?php
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Blog ID is missing.";
    header("Location: display_blog.php");
    exit();
}

$id = $_GET['id'];

$query = "SELECT * FROM blog WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$blog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    $_SESSION['error_message'] = "Blog not found.";
    header("Location: display_blog.php");
    exit();
}

if (isset($_POST['update_blog'])) {
    $title = $_POST['title'];
    $description = $_POST['description_blog'];
    $details = $_POST['blog_description_details'];
    $status = $_POST['status'];
    $category = $_POST['category_blog'];

    $image = $blog['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = uniqid() . '.' . $filetype;
            $upload_dir = 'propertyMgt/blogImg/';

            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename)) {
                if ($blog['image'] && file_exists($upload_dir . $blog['image'])) {
                    unlink($upload_dir . $blog['image']);
                }
                $image = $new_filename;
            } else {
                $_SESSION['error_message'] = "Failed to upload image.";
                header("Location: edit_blog.php?id=$id");
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Only JPG, JPEG, PNG & GIF files are allowed.";
            header("Location: edit_blog.php?id=$id");
            exit();
        }
    }

    // Handle new file attachments
    if (isset($_FILES['attachments'])) {
        $allowed_files = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar');
        $upload_dir = 'propertyMgt/blogFiles/';
        
        foreach ($_FILES['attachments']['name'] as $key => $name) {
            if ($_FILES['attachments']['error'][$key] == 0) {
                $filetype = pathinfo($name, PATHINFO_EXTENSION);
                
                if (in_array(strtolower($filetype), $allowed_files)) {
                    $new_filename = uniqid() . '.' . $filetype;
                    $file_tmp = $_FILES['attachments']['tmp_name'][$key];
                    $file_size = $_FILES['attachments']['size'][$key];
                    
                    if (move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {
                        $query = "INSERT INTO blog_attachments 
                                 (blog_id, file_name, file_path, file_type, file_size) 
                                 VALUES (:blog_id, :file_name, :file_path, :file_type, :file_size)";
                        $stmt = $conn->prepare($query);
                        $stmt->execute([
                            ':blog_id' => $id,
                            ':file_name' => $name,
                            ':file_path' => $new_filename,
                            ':file_type' => $filetype,
                            ':file_size' => $file_size
                        ]);
                    }
                }
            }
        }
    }

    $query = "UPDATE blog SET 
              title = :title,
              description_blog = :description,
              blog_description_details = :details,
              image = :image,
              status = :status,
              category_blog = :category
              WHERE id = :id";

    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':details' => $details,
        ':image' => $image,
        ':status' => $status,
        ':category' => $category,
        ':id' => $id
    ]);

    $_SESSION['success_message'] = "Blog updated successfully!";
    header("Location: display_blog.php");
    exit();
}

// Get existing attachments
$query = "SELECT * FROM blog_attachments WHERE blog_id = :blog_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':blog_id', $id, PDO::PARAM_INT);
$stmt->execute();
$attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .custom-file-label::after { content: "Browse"; }
        .form-group { margin-bottom: 1.5rem; }
        .control-label { font-weight: 500; padding-top: 7px; }
        .padding_infor_info { padding: 30px; }
        #imagePreview img, #filePreview img { border: 1px solid #ddd; border-radius: 4px; padding: 5px; }
        .file-preview-item { display: flex; align-items: center; margin-bottom: 5px; }
        .file-icon { margin-right: 8px; font-size: 1.2rem; }
        .attachment-item { display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
    <div class="row column1">
        <div class="col-md-12">
            <div class="white_shd full margin_bottom_30">
                <div class="full graph_head">
                    <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                        <h2>Edit Blog</h2>
                        <a href="display_blog.php" class="btn btn-info btn-sm">View All Blogs</a>
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
                                <form class="form-horizontal" action="edit_blog.php?id=<?php echo htmlspecialchars($id); ?>" method="post" enctype="multipart/form-data">
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Title</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Description</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="description_blog" rows="4" required><?php echo htmlspecialchars($blog['description_blog']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Details</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="blog_description_details" rows="4" required><?php echo htmlspecialchars($blog['blog_description_details']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Featured Image</label>
                                        <div class="col-sm-9">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="image" id="imageUpload" accept="image/*">
                                                <label class="custom-file-label" for="imageUpload">Choose new image</label>
                                            </div>
                                            <div class="mt-3" id="imagePreview">
                                                <img src="propertyMgt/blogImg/<?php echo htmlspecialchars($blog['image']); ?>" alt="Current Image" class="img-fluid" style="max-height: 200px;">
                                                <p class="mt-2 text-muted">Current image shown. Upload a new one to replace it.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Add Attachments</label>
                                        <div class="col-sm-9">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="attachments[]" id="fileUpload" multiple 
                                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                                                <label class="custom-file-label" for="fileUpload">Choose additional files</label>
                                            </div>
                                            <small class="form-text text-muted">
                                                You can upload multiple additional files
                                            </small>
                                            <div id="filePreview" class="mt-2"></div>
                                            
                                            <?php if (!empty($attachments)): ?>
                                                <div class="mt-3">
                                                    <h6>Current Attachments:</h6>
                                                    <ul class="list-group">
                                                        <?php foreach ($attachments as $attachment): ?>
                                                            <li class="list-group-item">
                                                                <div class="attachment-item">
                                                                    <div>
                                                                        <i class="<?php echo getFileIconClass($attachment['file_name']); ?> file-icon"></i>
                                                                        <?php echo htmlspecialchars($attachment['file_name']); ?>
                                                                        <small class="text-muted ml-2">(<?php echo formatFileSize($attachment['file_size']); ?>)</small>
                                                                    </div>
                                                                    <div>
                                                                        <a href="propertyMgt/blogFiles/<?php echo htmlspecialchars($attachment['file_path']); ?>" 
                                                                           class="btn btn-sm btn-info" download>
                                                                            <i class="fa fa-download"></i>
                                                                        </a>
                                                                        <a href="delete_attachment.php?id=<?php echo $attachment['id']; ?>&blog_id=<?php echo $id; ?>" 
                                                                           class="btn btn-sm btn-danger" 
                                                                           onclick="return confirm('Are you sure you want to delete this attachment?')">
                                                                            <i class="fa fa-trash"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Category</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="category_blog" value="<?php echo htmlspecialchars($blog['category_blog']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Status</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="status" required>
                                                <option value="active" <?php echo ($blog['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                                <option value="pending" <?php echo ($blog['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-9 offset-sm-3">
                                            <button type="submit" name="update_blog" class="btn btn-info">Update Blog</button>
                                            <a href="view_blog.php?id=<?php echo htmlspecialchars($id); ?>" class="btn btn-secondary ml-2">Cancel</a>
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image preview
        const imageUpload = document.getElementById('imageUpload');
        const imagePreview = document.getElementById('imagePreview');
        
        imageUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.querySelector('img').src = e.target.result;
                    imagePreview.querySelector('p').textContent = "New image selected.";
                }
                reader.readAsDataURL(file);
            }
        });
        
        // File attachments preview
        const fileUpload = document.getElementById('fileUpload');
        const filePreview = document.getElementById('filePreview');
        
        fileUpload.addEventListener('change', function(e) {
            filePreview.innerHTML = '';
            
            if (this.files.length > 0) {
                const fileList = document.createElement('div');
                fileList.className = 'list-group';
                
                for (let i = 0; i < this.files.length; i++) {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'list-group-item';
                    
                    const fileIcon = document.createElement('i');
                    fileIcon.className = getFileIconClass(this.files[i].name);
                    
                    const fileName = document.createElement('span');
                    fileName.textContent = `${this.files[i].name} (${formatFileSize(this.files[i].size)})`;
                    
                    fileItem.appendChild(fileIcon);
                    fileItem.appendChild(fileName);
                    fileList.appendChild(fileItem);
                }
                
                filePreview.appendChild(fileList);
            }
        });
        
        function getFileIconClass(filename) {
            const ext = filename.split('.').pop().toLowerCase();
            let iconClass = 'far fa-file-alt file-icon';
            
            switch(ext) {
                case 'pdf': iconClass = 'far fa-file-pdf file-icon text-danger'; break;
                case 'doc':
                case 'docx': iconClass = 'far fa-file-word file-icon text-primary'; break;
                case 'xls':
                case 'xlsx': iconClass = 'far fa-file-excel file-icon text-success'; break;
                case 'ppt':
                case 'pptx': iconClass = 'far fa-file-powerpoint file-icon text-warning'; break;
                case 'zip':
                case 'rar': iconClass = 'far fa-file-archive file-icon text-secondary'; break;
                case 'txt': iconClass = 'far fa-file-alt file-icon text-info'; break;
            }
            
            return iconClass;
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    });
    </script>
</body>
</html>

<?php
function getFileIconClass($filename) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    switch(strtolower($ext)) {
        case 'pdf': return 'far fa-file-pdf text-danger';
        case 'doc':
        case 'docx': return 'far fa-file-word text-primary';
        case 'xls':
        case 'xlsx': return 'far fa-file-excel text-success';
        case 'ppt':
        case 'pptx': return 'far fa-file-powerpoint text-warning';
        case 'zip':
        case 'rar': return 'far fa-file-archive text-secondary';
        case 'txt': return 'far fa-file-alt text-info';
        default: return 'far fa-file-alt';
    }
}

function formatFileSize($bytes) {
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
?>