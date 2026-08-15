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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Blog</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .container { max-width: 800px; margin-top: 30px; }
        .card { margin-bottom: 30px; }
        .card img { max-height: 400px; object-fit: cover; }
        .attachment-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #eee; }
        .attachment-item:last-child { border-bottom: none; }
        .file-icon { margin-right: 10px; }
        .attachments-container { margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shadow">
            <img src="propertyMgt/blogImg/<?php echo htmlspecialchars($blog['image']); ?>" class="card-img-top" alt="Blog Image">
            <div class="card-body">
                <h1 class="card-title"><?php echo htmlspecialchars($blog['title']); ?></h1>
                <p class="text-muted">
                    <i class="far fa-calendar-alt"></i> <?php echo date('F j, Y', strtotime($blog['date'])); ?> | 
                    <i class="far fa-folder-open"></i> <?php echo htmlspecialchars($blog['category_blog']); ?>
                </p>
                <div class="card-text">
                    <h4>Description</h4>
                    <p><?php echo nl2br(htmlspecialchars($blog['description_blog'])); ?></p>
                    
                    <h4 class="mt-4">Details</h4>
                    <p><?php echo nl2br(htmlspecialchars($blog['blog_description_details'])); ?></p>
                </div>
                
                <?php if (!empty($attachments)): ?>
                <div class="attachments-container">
                    <h4><i class="far fa-paperclip"></i> Attachments</h4>
                    <div class="list-group">
                        <?php foreach ($attachments as $attachment): ?>
                            <div class="list-group-item">
                                <div class="attachment-item">
                                    <div>
                                        <i class="<?php echo getFileIconClass($attachment['file_name']); ?> file-icon"></i>
                                        <?php echo htmlspecialchars($attachment['file_name']); ?>
                                        <small class="text-muted ml-2">(<?php echo formatFileSize($attachment['file_size']); ?>)</small>
                                    </div>
                                    <a href="propertyMgt/blogFiles/<?php echo htmlspecialchars($attachment['file_path']); ?>" 
                                       class="btn btn-sm btn-primary" download>
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <a href="display_blog.php" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Blog List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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

require_once 'include/footer.php';
?>