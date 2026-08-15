<?php
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (isset($_POST['add_blog'])) {
    $title = $_POST['title'];
    $description = $_POST['description_blog'];
    $details = $_POST['blog_description_details'];
    $status = $_POST['status'];
    $category = $_POST['category_blog'];

    // Image upload
    $image = '';
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
                $image = $new_filename;
            } else {
                $_SESSION['error_message'] = "Failed to upload image.";
                header("Location: add_blog.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Only JPG, JPEG, PNG & GIF images are allowed.";
            header("Location: add_blog.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Please select a featured image.";
        header("Location: add_blog.php");
        exit();
    }

    // Insert blog post
    $query = "INSERT INTO blog (title, description_blog, blog_description_details, image, status, category_blog, date) 
              VALUES (:title, :description, :details, :image, :status, :category, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':details' => $details,
        ':image' => $image,
        ':status' => $status,
        ':category' => $category
    ]);
    
    $blog_id = $conn->lastInsertId();
    
    // Handle file attachments
    if (isset($_FILES['attachments'])) {
        $allowed_files = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar');
        $upload_dir = 'propertyMgt/blogFiles/';
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
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
                            ':blog_id' => $blog_id,
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
    
    $_SESSION['success_message'] = "Blog post and attachments added successfully!";
    header("Location: display_blog.php");
    exit();
}

$query = "SELECT * FROM blog WHERE status = 'active'";
$stmt = $conn->prepare($query);
$stmt->execute();
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Blog</title>
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
    </style>
</head>
<body>
    <div class="row column1">
        <div class="col-md-12">
            <div class="white_shd full margin_bottom_30">
                <div class="full graph_head">
                    <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                        <h2>Add Blog</h2>
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
                                <form class="form-horizontal" action="add_blog.php" method="post" enctype="multipart/form-data">
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Title</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="title" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Description</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="description_blog" rows="4" required></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Details</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="blog_description_details" rows="4" required></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Featured Image</label>
                                        <div class="col-sm-9">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="image" id="imageUpload" accept="image/*" required>
                                                <label class="custom-file-label" for="imageUpload">Choose file</label>
                                            </div>
                                            <div class="mt-3" id="imagePreview" style="display: none;">
                                                <img src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Attachments</label>
                                        <div class="col-sm-9">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="attachments[]" id="fileUpload" multiple 
                                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                                                <label class="custom-file-label" for="fileUpload">Choose files (multiple allowed)</label>
                                            </div>
                                            <small class="form-text text-muted">
                                                Max 10 files (PDF, DOC, XLS, PPT, TXT, ZIP, RAR)
                                            </small>
                                            <div id="filePreview" class="mt-2"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Category</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="category_blog" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Status</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="status" required>
                                                <option value="active">Active</option>
                                                <option value="pending">Pending</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-9 offset-sm-3">
                                            <button type="submit" name="add_blog" class="btn btn-info">Add Blog</button>
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
                    imagePreview.style.display = 'block';
                    imagePreview.querySelector('img').src = e.target.result;
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