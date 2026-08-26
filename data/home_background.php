<?php
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_background'])) {
    $image_path = $_POST['image_path'];
    $status = $_POST['status'];

    $sql = "INSERT INTO home_backgrounds (image_path, status) VALUES (:image_path, :status)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':image_path', $image_path, PDO::PARAM_STR);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Background added successfully!";
    } else {
        $_SESSION['error_message'] = "Error adding background.";
    }

    session_write_close();
    header("Location: home_background.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $sql = "DELETE FROM home_backgrounds WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $delete_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Background deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting background.";
    }

    session_write_close();
    header("Location: home_background.php");
    exit();
}

$sql = "SELECT * FROM home_backgrounds";
$stmt = $conn->query($sql);
$backgrounds = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.flf-form-section { margin-bottom: 30px; }
.flf-section-title {
    display: flex; align-items: center; gap: 10px;
    font-family: var(--flf-font-head); font-size: 18px; font-weight: 600;
    color: var(--flf-navy); margin-bottom: 18px; padding-bottom: 10px;
    border-bottom: 2px solid var(--flf-blue);
}
.flf-section-title i { color: var(--flf-gold); font-size: 16px; }
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
.flf-img-preview {
    width: 80px; height: 50px; object-fit: cover; border-radius: 6px;
    border: 1px solid var(--flf-blue); background: var(--flf-ice);
}
.flf-img-placeholder {
    width: 80px; height: 50px; border-radius: 6px; border: 1px dashed var(--flf-blue);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; color: var(--flf-muted); background: var(--flf-ice);
}
.flf-status-pill {
    display: inline-block; padding: 4px 12px; border-radius: 20px;
    font-family: var(--flf-font-body); font-size: 11px; font-weight: 700;
    letter-spacing: 0.5px; text-transform: uppercase;
}
.flf-status-active  { background: rgba(26, 122, 76, 0.1);  color: var(--flf-success); }
.flf-status-pending { background: rgba(107, 118, 153, 0.1); color: var(--flf-muted); }
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
                            <h2><i class="fa fa-plus-circle" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>Add Background</h2>
                        </div>
                    </div>

                    <div class="full padding_infor_info">
                        <form method="POST" action="home_background.php" class="flf-video-form" style="max-width:600px;">

                            <div class="flf-field">
                                <label><i class="fa fa-image"></i>Image Path</label>
                                <input type="text" class="form-control" name="image_path"
                                       placeholder="e.g. images/bg-home.jpg" required>
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
                                        <i class="fa fa-clock-o" style="color:var(--flf-muted);"></i>
                                        Pending
                                    </label>
                                </div>
                            </div>

                            <div style="padding-top:8px;">
                                <button type="submit" name="add_background" class="btn btn-info">
                                    <i class="fa fa-plus" style="margin-right:5px;"></i>Add Background
                                </button>
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
                            <h2><i class="fa fa-image" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>Existing Backgrounds</h2>
                        </div>
                    </div>

                    <div class="full padding_infor_info" style="padding:0;">
                        <?php if (empty($backgrounds)): ?>
                            <div class="flf-empty-state">
                                <i class="fa fa-image"></i>
                                <p>No backgrounds added yet. Use the form above to add your first background.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table flf-table" style="margin-bottom:0;">
                                    <thead>
                                        <tr>
                                            <th style="width:60px;">ID</th>
                                            <th style="width:120px;">Preview</th>
                                            <th>Image Path</th>
                                            <th style="width:100px;">Status</th>
                                            <th style="width:120px;text-align:center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($backgrounds as $background): ?>
                                        <tr>
                                            <td><span style="font-weight:700;color:var(--flf-navy);">#<?php echo htmlspecialchars($background['id']); ?></span></td>
                                            <td>
                                                <?php if (!empty($background['image_path'])): ?>
                                                    <img src="<?php echo htmlspecialchars($background['image_path']); ?>"
                                                         alt="Background" class="flf-img-preview"
                                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                                    <div class="flf-img-placeholder" style="display:none;"><i class="fa fa-image"></i></div>
                                                <?php else: ?>
                                                    <div class="flf-img-placeholder"><i class="fa fa-image"></i></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span style="font-size:13px;color:var(--flf-charcoal);word-break:break-all;">
                                                    <?php echo htmlspecialchars($background['image_path']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (strtolower($background['status']) == 'active'): ?>
                                                    <span class="flf-status-pill flf-status-active">Active</span>
                                                <?php else: ?>
                                                    <span class="flf-status-pill flf-status-pending">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="flf-action-group" style="justify-content:center;">
                                                    <a href="edit_background.php?id=<?php echo $background['id']; ?>" class="btn btn-info btn-sm" title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-danger btn-sm" title="Delete"
                                                       onclick="document.getElementById('deleteId').value='<?php echo $background['id']; ?>';document.getElementById('deleteModal').style.display='flex';return false;">
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
            <h4 style="font-family:var(--flf-font-head);color:var(--flf-navy);margin:0 0 8px;font-size:22px;">Delete Background</h4>
            <p style="font-family:var(--flf-font-body);color:var(--flf-muted);font-size:14px;margin:0;">
                Are you sure you want to delete this background? This action cannot be undone.
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
    window.location.href = 'home_background.php?delete_id=' + id;
});
</script>

<input type="hidden" id="deleteId" value="">

<?php
require_once 'include/footer.php';
ob_end_flush();
?>