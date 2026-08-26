<?php
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $conn->prepare("DELETE FROM videos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $_SESSION['success_message'] = "Video link deleted successfully!";
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Error deleting video link: " . $e->getMessage();
    }
    session_write_close();
    header("Location: add_video.php");
    exit();
}

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    try {
        $stmt = $conn->prepare("SELECT * FROM videos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $edit_video = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Error fetching video link: " . $e->getMessage();
        session_write_close();
        header("Location: add_video.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $video_link = $_POST['video_link'];
    $status = $_POST['status'] ?? 'pending';
    $id = $_POST['id'] ?? null;

    try {
        if ($id) {
            if ($status == 'active') {
                $stmt = $conn->prepare("UPDATE videos SET status = 'pending' WHERE id != :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            }

            $stmt = $conn->prepare("UPDATE videos SET video_link = :video_link, status = :status WHERE id = :id");
            $stmt->bindParam(':video_link', $video_link);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $_SESSION['success_message'] = "Video link updated successfully!";
        } else {
            $stmt = $conn->prepare("INSERT INTO videos (video_link, status) VALUES (:video_link, :status)");
            $stmt->bindParam(':video_link', $video_link);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            
            $_SESSION['success_message'] = "Video link added successfully!";
        }
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }

    session_write_close();
    header("Location: add_video.php");
    exit();
}

try {
    $stmt = $conn->query("SELECT * FROM videos ORDER BY created_at DESC");
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching video links: " . $e->getMessage();
}
?>

<style>
.flf-video-form {
    max-width: 600px;
}
.flf-field { margin-bottom: 20px; }
.flf-field:last-child { margin-bottom: 0; }
.flf-field label {
    display: block; font-family: var(--flf-font-body); font-weight: 600;
    font-size: 13.5px; color: var(--flf-slate); margin-bottom: 7px;
}
.flf-field label i { color: var(--flf-gold); margin-right: 5px; width: 16px; text-align: center; }
.flf-field .form-control {
    height: 46px; border: 1px solid #d9dee9; border-radius: var(--flf-radius-sm);
    font-family: var(--flf-font-body); font-size: 14px; color: var(--flf-charcoal);
    padding: 0 14px; transition: border-color 0.25s ease, box-shadow 0.25s ease;
    background: var(--flf-white);
}
.flf-field .form-control:focus { border-color: var(--flf-royal); box-shadow: 0 0 0 3px rgba(24, 53, 143, 0.12); outline: none; }
.flf-radio-group { display: flex; gap: 16px; margin-top: 4px; }
.flf-radio-card {
    display: flex; align-items: center; gap: 10px; padding: 12px 20px;
    border: 2px solid #d9dee9; border-radius: var(--flf-radius-sm); background: var(--flf-white);
    cursor: pointer; transition: all 0.2s ease;
    font-family: var(--flf-font-body); font-size: 14px; font-weight: 500; color: var(--flf-charcoal);
}
.flf-radio-card:hover { border-color: var(--flf-royal); }
.flf-radio-card input[type="radio"] { accent-color: var(--flf-navy); width: 18px; height: 18px; }
.flf-radio-card:has(input:checked) { border-color: var(--flf-navy); background: rgba(233, 238, 250, 0.5); color: var(--flf-navy); font-weight: 600; }
.flf-active-note {
    display: inline-flex; align-items: center; gap: 6px; margin-top: 10px;
    padding: 8px 14px; border-radius: var(--flf-radius-sm); font-size: 12.5px;
    font-family: var(--flf-font-body); font-weight: 500;
    background: rgba(24, 53, 143, 0.06); color: var(--flf-royal); border: 1px solid rgba(24, 53, 143, 0.12);
}
.flf-active-note i { font-size: 12px; }
.flf-table {
    width: 100%; border-collapse: separate; border-spacing: 0;
}
.flf-table th {
    font-family: var(--flf-font-body); font-size: 12px; font-weight: 700;
    letter-spacing: 0.8px; text-transform: uppercase; color: var(--flf-white);
    background: var(--flf-midnight); padding: 14px 18px;
    border-bottom: 2px solid var(--flf-gold); white-space: nowrap;
}
.flf-table th:first-child { border-radius: 10px 0 0 0; }
.flf-table th:last-child  { border-radius: 0 10px 0 0; }
.flf-table td {
    font-family: var(--flf-font-body); font-size: 14px; color: var(--flf-charcoal);
    padding: 16px 18px; border-bottom: 1px solid var(--flf-blue); vertical-align: middle;
}
.flf-table tbody tr { transition: background 0.2s ease; }
.flf-table tbody tr:hover { background: rgba(233, 238, 250, 0.45); }
.flf-table tbody tr:last-child td { border-bottom: none; }
.flf-video-link {
    font-family: var(--flf-font-body); font-size: 13px; color: var(--flf-charcoal);
    max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    display: block;
}
.flf-video-link-full {
    color: var(--flf-navy); text-decoration: none; font-weight: 500;
}
.flf-video-link-full:hover { color: var(--flf-gold); text-decoration: underline; }
.flf-status-pill {
    display: inline-block; padding: 4px 12px; border-radius: 20px;
    font-family: var(--flf-font-body); font-size: 11px; font-weight: 700;
    letter-spacing: 0.5px; text-transform: uppercase;
}
.flf-status-active  { background: rgba(26, 122, 76, 0.1);  color: var(--flf-success); }
.flf-status-pending { background: rgba(107, 118, 153, 0.1); color: var(--flf-muted); }
.flf-date-cell { font-size: 13px; color: var(--flf-muted); white-space: nowrap; }
.flf-action-group { display: flex; gap: 5px; }
.flf-action-group .btn-sm {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; padding: 0; border-radius: 7px; font-size: 13px;
}
.flf-empty-state { text-align: center; padding: 48px 20px; }
.flf-empty-state i { font-size: 42px; color: var(--flf-blue); margin-bottom: 14px; display: block; }
.flf-empty-state p { font-family: var(--flf-font-body); font-size: 14px; color: var(--flf-muted); margin: 0; }
@media (max-width: 576px) { .flf-radio-group { flex-direction: column; gap: 10px; } }
</style>

<div class="midde_cont">
    <div class="container-fluid">

        <?php if (!empty($_SESSION['success_message'])): ?>
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

        <?php if (!empty($_SESSION['error_message'])): ?>
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
                        <div class="heading1 margin_0">
                            <h2><i class="fa fa-plus-circle" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i><?php echo isset($edit_video) ? 'Edit Video' : 'Add Video'; ?></h2>
                        </div>
                    </div>

                    <div class="full padding_infor_info">
                        <form method="POST" action="add_video.php" class="flf-video-form">

                            <div class="flf-field">
                                <label><i class="fa fa-link"></i>Video Link</label>
                                <input type="text" class="form-control" name="video_link"
                                       value="<?php echo isset($edit_video) ? htmlspecialchars($edit_video['video_link']) : ''; ?>"
                                       placeholder="e.g. https://www.youtube.com/watch?v=..." required>
                            </div>

                            <div class="flf-field">
                                <label><i class="fa fa-toggle-on"></i>Status</label>
                                <div class="flf-radio-group">
                                    <label class="flf-radio-card">
                                        <input type="radio" name="status" value="active"
                                            <?php echo (isset($edit_video) && $edit_video['status'] == 'active') ? 'checked' : ''; ?>>
                                        <i class="fa fa-check-circle" style="color:var(--flf-success);"></i>
                                        Active
                                    </label>
                                    <label class="flf-radio-card">
                                        <input type="radio" name="status" value="pending"
                                            <?php echo (!isset($edit_video) || $edit_video['status'] == 'pending') ? 'checked' : ''; ?>>
                                        <i class="fa fa-clock-o" style="color:var(--flf-muted);"></i>
                                        Pending
                                    </label>
                                </div>
                                <div class="flf-active-note">
                                    <i class="fa fa-info-circle"></i>Only one video can be active at a time. Setting this as active will deactivate others.
                                </div>
                            </div>

                            <?php if (isset($edit_video)): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_video['id']; ?>">
                            <?php endif; ?>

                            <div style="padding-top:8px;">
                                <button type="submit" class="btn btn-info">
                                    <i class="fa <?php echo isset($edit_video) ? 'fa-save' : 'fa-plus'; ?>" style="margin-right:5px;"></i>
                                    <?php echo isset($edit_video) ? 'Update Video' : 'Add Video'; ?>
                                </button>
                                <?php if (isset($edit_video)): ?>
                                    <a href="add_video.php" class="btn btn-secondary" style="margin-left:10px;">Cancel</a>
                                <?php endif; ?>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0">
                            <h2><i class="fa fa-film" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>All Videos</h2>
                        </div>
                    </div>

                    <div class="full padding_infor_info" style="padding:0;">
                        <?php if (empty($videos)): ?>
                            <div class="flf-empty-state">
                                <i class="fa fa-video-camera"></i>
                                <p>No videos added yet. Use the form above to add your first video.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table flf-table" style="margin-bottom:0;">
                                    <thead>
                                        <tr>
                                            <th style="width:60px;">ID</th>
                                            <th>Video Link</th>
                                            <th style="width:100px;">Status</th>
                                            <th style="width:150px;">Created</th>
                                            <th style="width:100px;text-align:center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($videos as $video): ?>
                                        <tr>
                                            <td><span style="font-weight:700;color:var(--flf-navy);">#<?php echo $video['id']; ?></span></td>
                                            <td>
                                                <span class="flf-video-link" title="<?php echo htmlspecialchars($video['video_link']); ?>">
                                                    <a href="<?php echo htmlspecialchars($video['video_link']); ?>" target="_blank" rel="noopener" class="flf-video-link-full">
                                                        <i class="fa fa-external-link" style="margin-right:5px;font-size:11px;"></i><?php echo htmlspecialchars($video['video_link']); ?>
                                                    </a>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($video['status'] == 'active'): ?>
                                                    <span class="flf-status-pill flf-status-active">Active</span>
                                                <?php else: ?>
                                                    <span class="flf-status-pill flf-status-pending">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="flf-date-cell"><?php echo htmlspecialchars($video['created_at']); ?></span></td>
                                            <td>
                                                <div class="flf-action-group" style="justify-content:center;">
                                                    <a href="add_video.php?edit=<?php echo $video['id']; ?>" class="btn btn-info btn-sm" title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-danger btn-sm" title="Delete"
                                                       onclick="document.getElementById('deleteId').value='<?php echo $video['id']; ?>';document.getElementById('deleteModal').style.display='flex';return false;">
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
            <h4 style="font-family:var(--flf-font-head);color:var(--flf-navy);margin:0 0 8px;font-size:22px;">Delete Video</h4>
            <p style="font-family:var(--flf-font-body);color:var(--flf-muted);font-size:14px;margin:0;">
                Are you sure you want to delete this video link? This action cannot be undone.
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
    window.location.href = 'add_video.php?delete=' + id;
});
</script>

<input type="hidden" id="deleteId" value="">

<?php
require_once 'include/footer.php';
ob_end_flush();
?>