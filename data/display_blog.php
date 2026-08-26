<?php
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    try {
        $check_query = "SELECT image FROM blog WHERE id = :id";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
        $check_stmt->execute();
        $blog = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($blog) {
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
            
            $delete_query = "DELETE FROM blog WHERE id = :id";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
            
            if ($delete_stmt->execute()) {
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

$query = "SELECT * FROM blog ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$attachCounts = [];
if (!empty($blogs)) {
    $ids = array_column($blogs, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $cntQuery = $conn->prepare("SELECT blog_id, COUNT(*) as cnt FROM blog_attachments WHERE blog_id IN ($placeholders) GROUP BY blog_id");
    $cntQuery->execute($ids);
    while ($row = $cntQuery->fetch(PDO::FETCH_ASSOC)) {
        $attachCounts[$row['blog_id']] = $row['cnt'];
    }
}
?>

<style>
.flf-blog-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.flf-blog-table th {
    font-family: var(--flf-font-body);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: var(--flf-white);
    background: var(--flf-midnight);
    padding: 14px 18px;
    border-bottom: 2px solid var(--flf-gold);
    white-space: nowrap;
}
.flf-blog-table th:first-child { border-radius: 10px 0 0 0; }
.flf-blog-table th:last-child  { border-radius: 0 10px 0 0; }
.flf-blog-table td {
    font-family: var(--flf-font-body);
    font-size: 14px;
    color: var(--flf-charcoal);
    padding: 16px 18px;
    border-bottom: 1px solid var(--flf-blue);
    vertical-align: middle;
}
.flf-blog-table tbody tr {
    transition: background 0.2s ease;
}
.flf-blog-table tbody tr:hover {
    background: rgba(233, 238, 250, 0.45);
}
.flf-blog-table tbody tr:last-child td {
    border-bottom: none;
}
.flf-blog-thumb {
    width: 64px;
    height: 48px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid var(--flf-blue);
}
.flf-blog-title {
    font-weight: 600;
    color: var(--flf-navy);
}
.flf-category-pill {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-family: var(--flf-font-body);
    font-size: 12px;
    font-weight: 600;
    background: var(--flf-blue);
    color: var(--flf-navy);
}
.flf-attach-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 26px;
    height: 26px;
    border-radius: 20px;
    font-family: var(--flf-font-body);
    font-size: 12px;
    font-weight: 700;
    padding: 0 8px;
}
.flf-attach-none {
    background: rgba(107, 118, 153, 0.1);
    color: var(--flf-muted);
}
.flf-attach-some {
    background: rgba(24, 53, 143, 0.1);
    color: var(--flf-royal);
}
.flf-status-pill {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-family: var(--flf-font-body);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.flf-status-active  { background: rgba(26, 122, 76, 0.1); color: var(--flf-success); }
.flf-status-pending { background: rgba(200, 169, 81, 0.15); color: #947a2e; }
.flf-date-cell {
    font-size: 13px;
    color: var(--flf-muted);
    white-space: nowrap;
}
.flf-action-group {
    display: flex;
    gap: 5px;
    flex-wrap: nowrap;
}
.flf-action-group .btn-sm {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
    border-radius: 7px;
    font-size: 13px;
}
.flf-empty-state {
    text-align: center;
    padding: 60px 20px;
}
.flf-empty-state i {
    font-size: 48px;
    color: var(--flf-blue);
    margin-bottom: 16px;
    display: block;
}
.flf-empty-state p {
    font-family: var(--flf-font-body);
    font-size: 15px;
    color: var(--flf-muted);
    margin: 0;
}
.flf-empty-state a {
    display: inline-block;
    margin-top: 16px;
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
                            <h2><i class="fa fa-newspaper-o" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>Blog Posts</h2>
                            <a href="add_blog.php" class="btn btn-info btn-sm">
                                <i class="fa fa-plus" style="margin-right:5px;"></i>Add New Blog
                            </a>
                        </div>
                    </div>

                    <div class="full padding_infor_info" style="padding:0;">
                        <?php if (empty($blogs)): ?>
                            <div class="flf-empty-state">
                                <i class="fa fa-file-text-o"></i>
                                <p>No blog posts found. Create your first post to get started.</p>
                                <a href="add_blog.php" class="btn btn-info btn-sm">
                                    <i class="fa fa-plus" style="margin-right:5px;"></i>Add Blog
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table flf-blog-table" style="margin-bottom:0;">
                                    <thead>
                                        <tr>
                                            <th style="width:84px;">Image</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th style="width:80px;text-align:center;">Files</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th style="width:110px;text-align:center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($blogs as $row): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($row['image'])): ?>
                                                    <img src="propertyMgt/blogImg/<?php echo htmlspecialchars($row['image']); ?>" alt="Blog" class="flf-blog-thumb">
                                                <?php else: ?>
                                                    <div class="flf-blog-thumb" style="background:var(--flf-blue);display:flex;align-items:center;justify-content:center;">
                                                        <i class="fa fa-image" style="color:var(--flf-muted);font-size:16px;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="flf-blog-title"><?php echo htmlspecialchars($row['title']); ?></span></td>
                                            <td>
                                                <?php if (!empty($row['category_blog'])): ?>
                                                    <span class="flf-category-pill"><?php echo htmlspecialchars($row['category_blog']); ?></span>
                                                <?php else: ?>
                                                    <span style="color:var(--flf-muted);font-size:13px;">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align:center;">
                                                <?php
                                                $count = isset($attachCounts[$row['id']]) ? intval($attachCounts[$row['id']]) : 0;
                                                if ($count > 0): ?>
                                                    <span class="flf-attach-badge flf-attach-some" title="<?php echo $count; ?> file(s)">
                                                        <i class="fa fa-paperclip" style="font-size:11px;margin-right:3px;"></i><?php echo $count; ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="flf-attach-badge flf-attach-none">0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($row['status'] == 'active'): ?>
                                                    <span class="flf-status-pill flf-status-active">Active</span>
                                                <?php else: ?>
                                                    <span class="flf-status-pill flf-status-pending">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="flf-date-cell"><?php echo htmlspecialchars($row['date']); ?></span></td>
                                            <td>
                                                <div class="flf-action-group" style="justify-content:center;">
                                                    <a href="view_blog.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="edit_blog.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-danger btn-sm" title="Delete"
                                                       onclick="document.getElementById('deleteId').value='<?php echo $row['id']; ?>';document.getElementById('deleteTitle').textContent='<?php echo htmlspecialchars(addslashes($row['title'])); ?>';document.getElementById('deleteModal').style.display='flex';return false;">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div id="deleteModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:var(--flf-white);border-radius:var(--flf-radius);box-shadow:0 20px 60px rgba(0,0,0,0.3);max-width:420px;width:90%;padding:0;overflow:hidden;">
        <div style="padding:28px 28px 0;text-align:center;">
            <div style="width:60px;height:60px;border-radius:50%;background:rgba(161,39,52,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <i class="fa fa-trash" style="font-size:24px;color:var(--flf-danger);"></i>
            </div>
            <h4 style="font-family:var(--flf-font-head);color:var(--flf-navy);margin:0 0 8px;font-size:22px;">Delete Blog Post</h4>
            <p style="font-family:var(--flf-font-body);color:var(--flf-muted);font-size:14px;margin:0;">
                Are you sure you want to delete "<strong id="deleteTitle" style="color:var(--flf-charcoal);"></strong>"? This will also remove all attachments and the featured image. This action cannot be undone.
            </p>
        </div>
        <div style="padding:20px 28px 28px;display:flex;gap:12px;justify-content:center;">
            <a href="#" class="btn btn-secondary" onclick="document.getElementById('deleteModal').style.display='none';return false;" style="min-width:110px;">Cancel</a>
            <a id="deleteConfirmBtn" href="#" class="btn btn-danger" style="min-width:110px;">
                <i class="fa fa-trash" style="margin-right:5px;"></i>Delete
            </a>
        </div>
    </div>
</div>

<script>
document.getElementById('deleteConfirmBtn').addEventListener('click', function(e) {
    e.preventDefault();
    var id = document.getElementById('deleteId').value;
    window.location.href = 'display_blog.php?delete_id=' + id;
});
</script>

<input type="hidden" id="deleteId" value="">

<?php require_once 'include/footer.php'; ?>