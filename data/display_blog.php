<?php
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

// Handle blog deletion
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    try {
        // Fetch the blog data before deletion
        $check_query = "SELECT image FROM blog WHERE id = :id";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
        $check_stmt->execute();
        $blog = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($blog) {
            // First delete all attachments
            $query = "SELECT file_path FROM blog_attachments WHERE blog_id = :blog_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':blog_id', $delete_id, PDO::PARAM_INT);
            $stmt->execute();
            $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($attachments as $attachment) {
                $file_path = 'propertyMgt/blogFiles/' . $attachment['file_path'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            // Then delete the blog post
            $delete_query = "DELETE FROM blog WHERE id = :id";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
            
            if ($delete_stmt->execute()) {
                // Delete the associated image file
                if (!empty($blog['image'])) {
                    $image_path = 'propertyMgt/blogImg/' . $blog['image'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
                
                $_SESSION['success_message'] = "Blog and all associated files deleted successfully!";
            } else {
                $_SESSION['error_message'] = "Failed to delete blog.";
            }
        } else {
            $_SESSION['error_message'] = "Blog not found.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
    header("Location: display_blog.php");
    exit();
}

// Fetch all blog posts
$query = "SELECT * FROM blog ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Blogs</title>
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> -->
    <style>
        .table .thead-dark th {
            color: #fff;
            background-color: #15283c;
            border-color: #32383e;
        }
        .table-responsive { overflow-x: auto; }
        .table tbody tr:hover { background-color: #f8f9fa; }
        .badge { font-size: 14px; padding: 6px 12px; }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; }
        .badge-secondary { background-color: #6c757d; }
        .badge-info { background-color: #17a2b8; }
        .action-btns a { margin-right: 5px; }
    </style>
</head>
<body>
    <div class="row column1">
        <div class="col-md-12">
            <div class="white_shd full margin_bottom_30">
                <div class="full graph_head">
                    <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                        <h2>All Blogs</h2>
                        <a href="add_blog.php" class="btn btn-info btn-sm">Add New Blog</a>
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
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Image</th>
                                                <th>Title</th>
                                                <th>Category</th>
                                                <th>Attachments</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($blogs) > 0): ?>
                                                <?php foreach ($blogs as $row): ?>
                                                    <tr>
                                                        <td>
                                                            <img src="propertyMgt/blogImg/<?php echo htmlspecialchars($row['image']); ?>" alt="Blog Image" class="img-thumbnail" style="max-width: 100px;">
                                                        </td>
                                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['category_blog']); ?></td>
                                                        <td>
                                                            <?php 
                                                            $query = "SELECT COUNT(*) as count FROM blog_attachments WHERE blog_id = :blog_id";
                                                            $stmt = $conn->prepare($query);
                                                            $stmt->bindParam(':blog_id', $row['id'], PDO::PARAM_INT);
                                                            $stmt->execute();
                                                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                                            echo $result['count'] > 0 ? 
                                                                '<span class="badge badge-info">' . $result['count'] . '</span>' : 
                                                                '<span class="badge badge-secondary">0</span>';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?php echo ($row['status'] == 'active') ? 'badge-success' : 'badge-warning'; ?>">
                                                                <?php echo htmlspecialchars($row['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                                                        <td class="action-btns">
                                                            <a href="view_blog.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm" title="View">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <a href="edit_blog.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" title="Edit">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            <a href="display_blog.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" 
                                                               onclick="return confirm('Are you sure you want to delete this blog and all its attachments?')" title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No blogs found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
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
</body>
</html>

<?php require_once 'include/footer.php'; ?>