<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';
require_once __DIR__ . '/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    requireCsrfPost();
    $id = intval($_POST['delete_id']);
    try {
        $stmt = $conn->prepare("SELECT image FROM about_content WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $about = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($about && !empty($about['image']) && file_exists("propertyMgt/aboutImg/" . $about['image'])) {
            unlink("propertyMgt/aboutImg/" . $about['image']);
        }
        
        $stmt = $conn->prepare("DELETE FROM about_content WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $_SESSION['success_message'] = "About content deleted successfully!";
    } catch(PDOException $e) {
        error_log("Delete about error: " . $e->getMessage()); $_SESSION['error_message'] = "An error occurred. Please try again.";
    }
    echo "<script>window.location.href = 'display_about.php';</script>";
    exit();
}

try {
    $stmt = $conn->query("SELECT * FROM about_content ORDER BY id DESC");
    $aboutContents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Fetch about error: " . $e->getMessage()); $error = "An error occurred while loading about content.";
}
?>

<style>
.flf-about-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.flf-about-table th {
    font-family: var(--fl-font-body);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: var(--fl-surface);
    background: var(--flf-midnight);
    padding: 14px 18px;
    border-bottom: 2px solid var(--fl-seal-600);
    white-space: nowrap;
}
.flf-about-table th:first-child { border-radius: 10px 0 0 0; }
.flf-about-table th:last-child  { border-radius: 0 10px 0 0; }
.flf-about-table td {
    font-family: var(--fl-font-body);
    font-size: 14px;
    color: var(--fl-chambers-900);
    padding: 16px 18px;
    border-bottom: 1px solid var(--fl-chambers-100);
    vertical-align: middle;
}
.flf-about-table tbody tr { transition: background 0.2s ease; }
.flf-about-table tbody tr:hover { background: rgba(233, 238, 250, 0.45); }
.flf-about-table tbody tr:last-child td { border-bottom: none; }
.flf-about-thumb {
    width: 72px;
    height: 54px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid var(--fl-chambers-100);
}
.flf-about-id { font-weight: 700; color: var(--fl-chambers-600); }
.flf-about-title {
    font-weight: 600;
    color: var(--fl-chambers-600);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    display: inline-block;
    vertical-align: middle;
}
.flf-about-desc {
    color: var(--fl-ink-400);
    font-size: 13px;
    max-width: 260px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: inline-block;
    vertical-align: middle;
    width: 100%;
}
.flf-cases-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-family: var(--fl-font-body);
    font-size: 13px;
    font-weight: 700;
    background: rgba(26, 122, 76, 0.1);
    color: var(--flf-success);
}
.flf-cases-badge i { font-size: 11px; }
.flf-status-pill {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-family: var(--fl-font-body);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.flf-status-active  { background: rgba(26, 122, 76, 0.1);  color: var(--flf-success); }
.flf-status-pending { background: rgba(200, 169, 81, 0.15); color: #947a2e; }
.flf-action-group { display: flex; gap: 5px; }
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
.flf-empty-state { text-align: center; padding: 60px 20px; }
.flf-empty-state i { font-size: 48px; color: var(--fl-chambers-100); margin-bottom: 16px; display: block; }
.flf-empty-state p { font-family: var(--fl-font-body); font-size: 15px; color: var(--fl-ink-400); margin: 0; }
.flf-empty-state a { display: inline-block; margin-top: 16px; }
</style>

<div class="padding_infor_info flf-wrap">

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:var(--flf-radius-sm);margin:0;">
                        <i class="fa fa-check-circle" style="margin-right:6px;"></i>
                        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:var(--flf-radius-sm);margin:0;">
                        <i class="fa fa-exclamation-triangle" style="margin-right:6px;"></i>
                        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                            <h2><i class="fa fa-building-o" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>About Content</h2>
                            <a href="add_about.php" class="btn btn-info btn-sm">
                                <i class="fa fa-plus" style="margin-right:5px;"></i>Add New About
                            </a>
                        </div>
                    </div>

                    <div class="full padding_infor_info" style="padding:0;">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" style="margin:24px 24px 0;border-radius:var(--flf-radius-sm);">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php elseif (empty($aboutContents)): ?>
                            <div class="flf-empty-state">
                                <i class="fa fa-info-circle"></i>
                                <p>No about content found. Add new content to get started.</p>
                                <a href="add_about.php" class="btn btn-info btn-sm">
                                    <i class="fa fa-plus" style="margin-right:5px;"></i>Add About Content
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table flf-about-table" style="margin-bottom:0;">
                                    <thead>
                                        <tr>
                                            <th style="width:60px;">ID</th>
                                            <th style="width:90px;">Image</th>
                                            <th style="width:20%;">Title</th>
                                            <th style="width:38%;">Description</th>
                                            <th style="width:110px;">Cases Won</th>
                                            <th style="width:80px;">Status</th>
                                            <th style="width:130px;text-align:center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($aboutContents as $about): ?>
                                        <tr>
                                            <td><span class="flf-about-id">#<?php echo $about['id']; ?></span></td>
                                            <td>
                                                <?php if (!empty($about['image'])): ?>
                                                    <img src="propertyMgt/aboutImg/<?php echo htmlspecialchars($about['image']); ?>" alt="About" class="flf-about-thumb">
                                                <?php else: ?>
                                                    <div class="flf-about-thumb" style="background:var(--flf-blue);display:flex;align-items:center;justify-content:center;">
                                                        <i class="fa fa-image" style="color:var(--flf-muted);"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="flf-about-title"><?php echo htmlspecialchars($about['title']); ?></span></td>
                                            <td><span class="flf-about-desc" title="<?php echo htmlspecialchars($about['description']); ?>"><?php echo htmlspecialchars($about['description']); ?></span></td>
                                            <td>
                                                <span class="flf-cases-badge">
                                                    <i class="fa fa-trophy"></i><?php echo htmlspecialchars($about['cases_won']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($about['status'] == 'Active'): ?>
                                                    <span class="flf-status-pill flf-status-active">Active</span>
                                                <?php else: ?>
                                                    <span class="flf-status-pill flf-status-pending">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="flf-action-group" style="justify-content:center;">
                                                    <a href="view_about.php?id=<?php echo $about['id']; ?>" class="btn btn-info btn-sm" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="edit_about.php?id=<?php echo $about['id']; ?>" class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-danger btn-sm" title="Delete"
                                                       onclick="document.getElementById('deleteId').value='<?php echo $about['id']; ?>';document.getElementById('deleteFormId').value='<?php echo $about['id']; ?>';document.getElementById('deleteTitle').textContent='<?php echo htmlspecialchars(addslashes($about['title'])); ?>';document.getElementById('deleteModal').style.display='flex';return false;">
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

<div id="deleteModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:var(--flf-white);border-radius:var(--flf-radius);box-shadow:0 20px 60px rgba(0,0,0,0.3);max-width:420px;width:90%;padding:0;overflow:hidden;">
        <div style="padding:28px 28px 0;text-align:center;">
            <div style="width:60px;height:60px;border-radius:50%;background:rgba(161,39,52,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <i class="fa fa-trash" style="font-size:24px;color:var(--flf-danger);"></i>
            </div>
            <h4 style="font-family:var(--flf-font-head);color:var(--flf-navy);margin:0 0 8px;font-size:22px;">Delete About Content</h4>
            <p style="font-family:var(--flf-font-body);color:var(--flf-muted);font-size:14px;margin:0;">
                Are you sure you want to delete "<strong id="deleteTitle" style="color:var(--flf-charcoal);"></strong>"? This action cannot be undone.
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
    document.getElementById('deleteForm').submit();
});
</script>

<form id="deleteForm" method="POST" action="display_about.php" style="display:none;">
    <?php echo csrfHiddenField(); ?>
    <input type="hidden" name="delete_id" id="deleteFormId" value="">
</form>
<input type="hidden" id="deleteId" value="">

<?php require_once 'include/footer.php'; ?>