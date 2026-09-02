<?php
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';
require_once __DIR__ . '/csrf.php';

if (isset($_POST['add_blog'])) {
    requireCsrfPost();
    $title = $_POST['title'];
    $description = $_POST['description_blog'];
    $details = $_POST['blog_description_details'];
    $status = $_POST['status'];
    $category = $_POST['category_blog'];

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = uniqid() . '.' . $filetype;
            $upload_dir = 'propertyMgt/blogImg/';

            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
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
    
    if (isset($_FILES['attachments'])) {
        $allowed_files = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar');
        $upload_dir = 'propertyMgt/blogFiles/';
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
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
?>

<div class="padding_infor_info">

   <?php if (isset($_SESSION['success_message'])): ?>
   <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa fa-check-circle me-2"></i>
      <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
      <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   </div>
   <?php endif; ?>

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
            <h2><i class="fa fa-plus-circle" style="color:var(--fl-accent);margin-right:10px;font-size:20px;"></i>Add Blog Post</h2>
            <a href="display_blog.php" class="btn btn-secondary btn-sm">
               <i class="fa fa-th-list" style="margin-right:5px;"></i>View All Blogs
            </a>
         </div>
      </div>

      <div class="full padding_infor_info">
         <form action="add_blog.php" method="post" enctype="multipart/form-data" style="max-width:820px;">
                            <?php echo csrfHiddenField(); ?>

                            <!-- Section 1: Content -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-edit"></i>Content</h3>

                                <div class="flf-field">
                                    <label><i class="fa fa-heading"></i>Title</label>
                                    <input type="text" class="form-control" name="title" placeholder="Enter blog post title" required>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-align-left"></i>Description</label>
                                    <textarea class="form-control" name="description_blog" rows="4" placeholder="Brief summary of the blog post..." required></textarea>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-file-text-o"></i>Details</label>
                                    <textarea class="form-control" name="blog_description_details" rows="6" placeholder="Full blog post content..." required></textarea>
                                </div>
                            </div>

                            <!-- Section 2: Media & Attachments -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-paperclip"></i>Media & Attachments</h3>

                                <div class="flf-field">
                                    <label><i class="fa fa-camera"></i>Featured Image</label>
                                    <div class="flf-upload-area" id="imageUploadArea">
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

                                <div class="flf-field">
                                    <label><i class="fa fa-files-o"></i>Attachments <span style="font-weight:400;color:var(--flf-muted);font-size:12px;">(Max 10 files)</span></label>
                                    <div class="flf-upload-area" id="fileUploadArea" style="padding:28px 20px;">
                                        <input type="file" name="attachments[]" id="fileUpload" multiple
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                                        <div class="flf-upload-icon" style="font-size:28px;"><i class="fa fa-files-o"></i></div>
                                        <p class="flf-upload-text"><strong>Click to upload files</strong></p>
                                        <p class="flf-upload-hint">PDF, DOC, XLS, PPT, TXT, ZIP, RAR</p>
                                    </div>
                                    <div id="filePreview" class="flf-file-list"></div>
                                </div>
                            </div>

                            <!-- Section 3: Publishing -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-cog"></i>Publishing</h3>

                                <div class="flf-field">
                                    <label><i class="fa fa-folder-open"></i>Category</label>
                                    <input type="text" class="form-control" name="category_blog" placeholder="e.g. Law, Real Estate, Legal Tips" required>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-toggle-on"></i>Status</label>
                                    <div class="flf-radio-group">
                                        <label class="flf-radio-card">
                                            <input type="radio" name="status" value="active" checked>
                                            <i class="fa fa-check-circle" style="color:var(--flf-success);"></i>
                                            Active
                                        </label>
                                        <label class="flf-radio-card">
                                            <input type="radio" name="status" value="pending">
                                            <i class="fa fa-clock-o" style="color:var(--flf-gold);"></i>
                                            Pending
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div style="padding-top:8px;">
                                <button type="submit" name="add_blog" class="btn btn-info">
                                    <i class="fa fa-plus" style="margin-right:5px;"></i>Add Blog Post
                                </button>
                                <a href="display_blog.php" class="btn btn-secondary" style="margin-left:10px;">Cancel</a>
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
    var imageUploadArea = document.getElementById('imageUploadArea');
    var fileUpload = document.getElementById('fileUpload');
    var filePreview = document.getElementById('filePreview');
    var fileUploadArea = document.getElementById('fileUploadArea');

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
        imageUploadArea.addEventListener(evt, function() { imageUploadArea.classList.add('flf-dragover'); });
        fileUploadArea.addEventListener(evt, function() { fileUploadArea.classList.add('flf-dragover'); });
    });
    ['dragleave', 'drop'].forEach(function(evt) {
        imageUploadArea.addEventListener(evt, function() { imageUploadArea.classList.remove('flf-dragover'); });
        fileUploadArea.addEventListener(evt, function() { fileUploadArea.classList.remove('flf-dragover'); });
    });

    fileUpload.addEventListener('change', function(e) {
        filePreview.innerHTML = '';
        var files = e.target.files;
        if (files.length > 10) {
            alert('Maximum 10 files allowed.');
            fileUpload.value = '';
            return;
        }
        for (var i = 0; i < files.length; i++) {
            var item = document.createElement('div');
            item.className = 'flf-file-item';
            item.innerHTML = '<i class="' + getFileIcon(files[i].name) + '"></i>' +
                '<span class="flf-file-name">' + escapeHtml(files[i].name) + '</span>' +
                '<span class="flf-file-size">' + formatSize(files[i].size) + '</span>';
            filePreview.appendChild(item);
        }
    });

    function getFileIcon(name) {
        var ext = name.split('.').pop().toLowerCase();
        var icons = {
            pdf: 'fa-file-pdf-o', doc: 'fa-file-word-o', docx: 'fa-file-word-o',
            xls: 'fa-file-excel-o', xlsx: 'fa-file-excel-o',
            ppt: 'fa-file-powerpoint-o', pptx: 'fa-file-powerpoint-o',
            txt: 'fa-file-text-o', zip: 'fa-file-archive-o', rar: 'fa-file-archive-o'
        };
        var colors = {
            pdf: 'color:#a12734', doc: 'color:#18358F', docx: 'color:#18358F',
            xls: 'color:#1a7a4c', xlsx: 'color:#1a7a4c',
            ppt: 'color:#947a2e', pptx: 'color:#947a2e',
            txt: 'color:#536174', zip: 'color:#536174', rar: 'color:#536174'
        };
        var icon = icons[ext] || 'fa-file-o';
        var color = colors[ext] || 'color:#536174';
        return 'fa ' + icon + ' flf-file-icon" style="' + color;
    }

    function formatSize(bytes) {
        if (bytes === 0) return '0 B';
        var k = 1024, sizes = ['B', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>

<?php
require_once 'include/footer.php';
?>