<?php
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM blog WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute([':id' => $id]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$blog) {
        echo "Blog not found!";
        exit;
    }
} else {
    echo "Invalid request!";
    exit;
}

// Get attachments
$query = "SELECT * FROM blog_attachments WHERE blog_id = :blog_id";
$stmt = $conn->prepare($query);
$stmt->execute([':blog_id' => $id]);
$attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .flf-viewblog { max-width: 860px; margin: 0 auto; }
    .flf-viewblog .flf-viewblog-img { max-height: 420px; width: 100%; object-fit: cover; border-radius: var(--radius-md) var(--radius-md) 0 0; }
    .flf-viewblog-date { color: var(--fl-ink-muted); font-size: var(--fs-small); }
    .flf-viewblog-detail h4 { font-family: var(--font-heading); color: var(--fl-primary); margin: var(--sp-6) 0 var(--sp-2); }
    .flf-viewblog-detail p { color: var(--fl-ink-secondary); line-height: var(--lh-body); white-space: pre-line; }
    .flf-attachment-item { display: flex; justify-content: space-between; align-items: center; padding: var(--sp-3) var(--sp-4); border-bottom: 1px solid var(--fl-line); }
    .flf-attachment-item:last-child { border-bottom: none; }
    .flf-attachment-item .fa { color: var(--fl-accent); margin-right: var(--sp-2); }
</style>

<div class="midde_cont">
    <div class="container-fluid">

        <div class="white_shd full">
            <div class="padding_infor_info">
                <div class="flf-viewblog">
                    <img src="propertyMgt/blogImg/<?php echo htmlspecialchars($blog['image']); ?>" class="flf-viewblog-img" alt="Blog Image">
                    <div class="p-4">
                        <h3 style="font-family:var(--font-heading);color:var(--fl-primary);font-weight:var(--fw-bold);"><?php echo htmlspecialchars($blog['title']); ?></h3>
                        <p class="flf-viewblog-date">
                            <i class="fa fa-calendar"></i> <?php echo date('F j, Y', strtotime($blog['date'])); ?> |
                            <i class="fa fa-folder-open"></i> <?php echo htmlspecialchars($blog['category_blog']); ?>
                        </p>

                        <div class="flf-viewblog-detail">
                            <h4>Description</h4>
                            <p><?php echo nl2br(htmlspecialchars($blog['description_blog'])); ?></p>

                            <h4>Details</h4>
                            <p><?php echo nl2br(htmlspecialchars($blog['blog_description_details'])); ?></p>
                        </div>

                        <?php if (!empty($attachments)): ?>
                        <div style="margin-top: var(--sp-6);">
                            <h4><i class="fa fa-paperclip"></i> Attachments</h4>
                            <div class="list-group" style="border:1px solid var(--fl-line);border-radius:var(--radius-md);overflow:hidden;">
                                <?php foreach ($attachments as $attachment): ?>
                                    <div class="flf-attachment-item">
                                        <div>
                                            <i class="<?php echo getFileIconClass($attachment['file_name']); ?>"></i>
                                            <?php echo htmlspecialchars($attachment['file_name']); ?>
                                            <small class="text-muted ms-2">(<?php echo formatFileSize($attachment['file_size']); ?>)</small>
                                        </div>
                                        <a href="propertyMgt/blogFiles/<?php echo htmlspecialchars($attachment['file_path']); ?>"
                                           class="btn btn-sm btn-primary" download>
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div style="margin-top: var(--sp-6);">
                            <a href="display_blog.php" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back to Blog List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
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
        case 'txt': return 'fa fa-file-o';
        default: return 'fa fa-file-o';
    }
}

function formatFileSize($bytes) {
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

require_once 'include/footer.php';
?>