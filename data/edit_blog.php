<?php
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

function getFileIconClass($filename) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    switch(strtolower($ext)) {
        case 'pdf': return 'fa fa-file-pdf-o';
        case 'doc':
        case 'docx': return 'fa fa-file-word-o';
        case 'xls':
        case 'xlsx': return 'fa fa-file-excel-o';
        case 'ppt':
        case 'pptx': return 'fa fa-file-powerpoint-o';
        case 'zip':
        case 'rar': return 'fa fa-file-archive-o';
        case 'txt': return 'fa fa-file-text-o';
        default: return 'fa fa-file-o';
    }
}

function getFileIconColor($filename) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    switch(strtolower($ext)) {
        case 'pdf': return '#a12734';
        case 'doc':
        case 'docx': return '#18358F';
        case 'xls':
        case 'xlsx': return '#1a7a4c';
        case 'ppt':
        case 'pptx': return '#947a2e';
        case 'zip':
        case 'rar': return '#536174';
        default: return '#536174';
    }
}

function formatFileSize($bytes) {
    if ($bytes == 0) return '0 B';
    $k = 1024;
    $sizes = ['B', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 1) . ' ' . $sizes[$i];
}

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

$query = "SELECT * FROM blog_attachments WHERE blog_id = :blog_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':blog_id', $id, PDO::PARAM_INT);
$stmt->execute();
$attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.flf-form-section {
    margin-bottom: 32px;
    padding-bottom: 28px;
    border-bottom: 1px solid var(--flf-blue);
}
.flf-form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.flf-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: var(--flf-font-head);
    font-size: 20px;
    font-weight: 700;
    color: var(--flf-navy);
    margin: 0 0 22px;
}
.flf-section-title i { color: var(--flf-gold); font-size: 16px; }
.flf-field { margin-bottom: 20px; }
.flf-field:last-child { margin-bottom: 0; }
.flf-field label {
    display: block;
    font-family: var(--flf-font-body);
    font-weight: 600;
    font-size: 13.5px;
    color: var(--flf-slate);
    margin-bottom: 7px;
}
.flf-field label i { color: var(--flf-gold); margin-right: 5px; width: 16px; text-align: center; }
.flf-field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.flf-upload-area {
    position: relative;
    width: 100%;
    border: 2px dashed #d9dee9;
    border-radius: var(--flf-radius);
    background: #fafbfe;
    text-align: center;
    padding: 36px 20px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.flf-upload-area:hover, .flf-upload-area.flf-dragover { border-color: var(--flf-royal); background: rgba(24, 53, 143, 0.04); }
.flf-upload-area input[type="file"] { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
.flf-upload-icon { font-size: 36px; color: var(--flf-blue); margin-bottom: 10px; }
.flf-upload-area:hover .flf-upload-icon, .flf-upload-area.flf-dragover .flf-upload-icon { color: var(--flf-royal); }
.flf-upload-text { font-family: var(--flf-font-body); font-size: 14px; color: var(--flf-slate); margin: 0 0 2px; }
.flf-upload-hint { font-family: var(--flf-font-body); font-size: 12px; color: var(--flf-muted); margin: 0; }
.flf-upload-preview { display: block; margin-top: 14px; position: relative; max-width: 400px; }
.flf-upload-preview img { width: 100%; max-height: 220px; object-fit: cover; border-radius: var(--flf-radius-sm); border: 2px solid var(--flf-blue); }
.flf-upload-preview .flf-remove-img {
    position: absolute; top: 8px; right: 8px; width: 28px; height: 28px; border-radius: 50%;
    background: var(--flf-danger); color: var(--flf-white); border: none; font-size: 12px;
    display: flex; align-items: center; justify-content: center; cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2); display: none;
}
.flf-img-current-wrap { display: flex; align-items: flex-start; gap: 24px; flex-wrap: wrap; }
.flf-img-current-wrap .flf-upload-side { flex: 1; min-width: 200px; }
.flf-current-img { width: 100%; max-width: 360px; border-radius: var(--flf-radius); border: 2px solid var(--flf-blue); }
.flf-attach-list { margin-top: 14px; display: flex; flex-direction: column; gap: 8px; }
.flf-attach-item {
    display: flex; align-items: center; gap: 12px; padding: 12px 16px;
    background: var(--flf-white); border: 1px solid var(--flf-blue); border-radius: var(--flf-radius-sm);
    font-family: var(--flf-font-body); font-size: 13px; color: var(--flf-charcoal);
    transition: background 0.2s ease;
}
.flf-attach-item:hover { background: rgba(233, 238, 250, 0.3); }
.flf-attach-item i { font-size: 20px; flex-shrink: 0; }
.flf-attach-item .flf-attach-name { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500; }
.flf-attach-item .flf-attach-size { color: var(--flf-muted); font-size: 12px; flex-shrink: 0; }
.flf-attach-item .flf-attach-actions { display: flex; gap: 4px; flex-shrink: 0; }
.flf-attach-item .flf-attach-actions .btn-sm { width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; font-size: 12px; }
.flf-new-file-list { margin-top: 12px; display: flex; flex-direction: column; gap: 8px; }
.flf-new-file-item {
    display: flex; align-items: center; gap: 10px; padding: 10px 14px;
    background: var(--flf-white); border: 1px solid var(--flf-blue); border-radius: var(--flf-radius-sm);
    font-family: var(--flf-font-body); font-size: 13px; color: var(--flf-charcoal);
}
.flf-new-file-item i { font-size: 18px; flex-shrink: 0; }
.flf-new-file-item .flf-file-name { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.flf-new-file-item .flf-file-size { color: var(--flf-muted); font-size: 12px; flex-shrink: 0; }
.flf-radio-group { display: flex; gap: 24px; margin-top: 6px; }
.flf-radio-card {
    display: flex; align-items: center; gap: 10px; padding: 12px 20px;
    border: 2px solid #d9dee9; border-radius: var(--flf-radius-sm); background: var(--flf-white);
    cursor: pointer; transition: all 0.2s ease;
    font-family: var(--flf-font-body); font-size: 14px; font-weight: 500; color: var(--flf-charcoal);
}
.flf-radio-card:hover { border-color: var(--flf-royal); }
.flf-radio-card input[type="radio"] { accent-color: var(--flf-navy); width: 18px; height: 18px; }
.flf-radio-card:has(input:checked) { border-color: var(--flf-navy); background: rgba(233, 238, 250, 0.5); color: var(--flf-navy); font-weight: 600; }
@media (max-width: 767px) { .flf-field-row { grid-template-columns: 1fr; } .flf-radio-group { flex-direction: column; gap: 10px; } .flf-img-current-wrap { flex-direction: column; } }
</style>

<div class="midde_cont">
    <div class="container-fluid">

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:var(--flf-radius-sm);margin:0;">
                        <i class="fa fa-check-circle" style="margin-right:6px;"></i>
                        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
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
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
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
                            <h2><i class="fa fa-pencil-square-o" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>Edit Blog Post</h2>
                            <a href="display_blog.php" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left" style="margin-right:5px;"></i>Back to Blogs
                            </a>
                        </div>
                    </div>

                    <div class="full padding_infor_info">
                        <form action="edit_blog.php?id=<?php echo htmlspecialchars($id); ?>" method="post" enctype="multipart/form-data" style="max-width:820px;">

                            <!-- Section 1: Content -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-edit"></i>Content</h3>

                                <div class="flf-field">
                                    <label><i class="fa fa-heading"></i>Title</label>
                                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-align-left"></i>Description</label>
                                    <textarea class="form-control" name="description_blog" rows="4" required><?php echo htmlspecialchars($blog['description_blog']); ?></textarea>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-file-text-o"></i>Details</label>
                                    <textarea class="form-control" name="blog_description_details" rows="6" required><?php echo htmlspecialchars($blog['blog_description_details']); ?></textarea>
                                </div>
                            </div>

                            <!-- Section 2: Media & Attachments -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-paperclip"></i>Media & Attachments</h3>

                                <div class="flf-field">
                                    <label><i class="fa fa-camera"></i>Featured Image</label>
                                    <div class="flf-img-current-wrap">
                                        <div style="flex-shrink:0;">
                                            <?php if (!empty($blog['image'])): ?>
                                                <img src="propertyMgt/blogImg/<?php echo htmlspecialchars($blog['image']); ?>" alt="Current Image" class="flf-current-img" id="currentImage">
                                            <?php else: ?>
                                                <div style="width:100%;max-width:360px;height:200px;background:var(--flf-blue);border-radius:var(--flf-radius);display:flex;align-items:center;justify-content:center;border:2px solid var(--flf-blue);">
                                                    <i class="fa fa-image" style="font-size:40px;color:var(--flf-muted);"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flf-upload-side">
                                            <div class="flf-upload-area" id="imageUploadArea">
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

                                <div class="flf-field">
                                    <label><i class="fa fa-files-o"></i>Existing Attachments</label>
                                    <?php if (!empty($attachments)): ?>
                                        <div class="flf-attach-list">
                                            <?php foreach ($attachments as $attachment): ?>
                                                <div class="flf-attach-item">
                                                    <i class="<?php echo getFileIconClass($attachment['file_name']); ?>" style="color:<?php echo getFileIconColor($attachment['file_name']); ?>;"></i>
                                                    <span class="flf-attach-name"><?php echo htmlspecialchars($attachment['file_name']); ?></span>
                                                    <span class="flf-attach-size"><?php echo formatFileSize($attachment['file_size']); ?></span>
                                                    <div class="flf-attach-actions">
                                                        <a href="propertyMgt/blogFiles/<?php echo htmlspecialchars($attachment['file_path']); ?>" class="btn btn-info btn-sm" title="Download" download>
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                        <a href="delete_attachment.php?id=<?php echo $attachment['id']; ?>&blog_id=<?php echo $id; ?>" class="btn btn-danger btn-sm" title="Delete"
                                                           onclick="return confirm('Delete this attachment?')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p style="color:var(--flf-muted);font-size:13px;margin:0;">No attachments yet.</p>
                                    <?php endif; ?>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-plus-circle"></i>Add New Attachments</label>
                                    <div class="flf-upload-area" id="fileUploadArea" style="padding:28px 20px;">
                                        <input type="file" name="attachments[]" id="fileUpload" multiple
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                                        <div class="flf-upload-icon" style="font-size:24px;"><i class="fa fa-files-o"></i></div>
                                        <p class="flf-upload-text"><strong>Click to upload files</strong></p>
                                        <p class="flf-upload-hint">PDF, DOC, XLS, PPT, TXT, ZIP, RAR</p>
                                    </div>
                                    <div id="filePreview" class="flf-new-file-list"></div>
                                </div>
                            </div>

                            <!-- Section 3: Publishing -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-cog"></i>Publishing</h3>

                                <div class="flf-field">
                                    <label><i class="fa fa-folder-open"></i>Category</label>
                                    <input type="text" class="form-control" name="category_blog" value="<?php echo htmlspecialchars($blog['category_blog']); ?>" required>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-toggle-on"></i>Status</label>
                                    <div class="flf-radio-group">
                                        <label class="flf-radio-card">
                                            <input type="radio" name="status" value="active" <?php echo ($blog['status'] == 'active') ? 'checked' : ''; ?>>
                                            <i class="fa fa-check-circle" style="color:var(--flf-success);"></i>
                                            Active
                                        </label>
                                        <label class="flf-radio-card">
                                            <input type="radio" name="status" value="pending" <?php echo ($blog['status'] == 'pending') ? 'checked' : ''; ?>>
                                            <i class="fa fa-clock-o" style="color:var(--flf-gold);"></i>
                                            Pending
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div style="padding-top:8px;">
                                <button type="submit" name="update_blog" class="btn btn-info">
                                    <i class="fa fa-save" style="margin-right:5px;"></i>Update Blog Post
                                </button>
                                <a href="display_blog.php" class="btn btn-secondary" style="margin-left:10px;">Cancel</a>
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
        for (var i = 0; i < files.length; i++) {
            var item = document.createElement('div');
            item.className = 'flf-new-file-item';
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