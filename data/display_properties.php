<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    requireCsrfPost();
    $id = intval($_POST['delete_id']);
    try {
        $stmt = $conn->prepare("SELECT image FROM add_property WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $property = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($property && !empty($property['image']) && file_exists("propertyMgt/proImg/" . $property['image'])) {
            unlink("propertyMgt/proImg/" . $property['image']);
        }
        
        $stmt = $conn->prepare("DELETE FROM add_property WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $_SESSION['success_message'] = "Property deleted successfully!";
    } catch(PDOException $e) {
        error_log("Delete property error: " . $e->getMessage()); $_SESSION['error_message'] = "An error occurred. Please try again.";
    }
    echo "<script>window.location.href = 'display_properties.php';</script>";
    exit();
}

try {
    $stmt = $conn->query("SELECT * FROM add_property where status='Active' ORDER BY id DESC");
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Fetch properties error: " . $e->getMessage()); $error = "An error occurred while loading properties.";
}
?>

<style>
.flf-prop-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.flf-prop-table th {
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
.flf-prop-table th:first-child { border-radius: 10px 0 0 0; }
.flf-prop-table th:last-child  { border-radius: 0 10px 0 0; }
.flf-prop-table td {
    font-family: var(--fl-font-body);
    font-size: 14px;
    color: var(--fl-chambers-900);
    padding: 16px 18px;
    border-bottom: 1px solid var(--fl-chambers-100);
    vertical-align: middle;
}
.flf-prop-table tbody tr {
    transition: background 0.2s ease;
}
.flf-prop-table tbody tr:hover {
    background: rgba(233, 238, 250, 0.45);
}
.flf-prop-table tbody tr:last-child td {
    border-bottom: none;
}
.flf-prop-thumb {
    width: 72px;
    height: 54px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid var(--fl-chambers-100);
}
.flf-prop-id {
    font-weight: 700;
    color: var(--fl-chambers-600);
}
.flf-prop-location,
.flf-prop-title {
    color: var(--fl-chambers-900);
}
.flf-action-group {
    display: flex;
    gap: 6px;
}
.flf-action-group .btn-sm {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    padding: 0;
    border-radius: 8px;
    font-size: 14px;
}
.flf-empty-state {
    text-align: center;
    padding: 60px 20px;
}
.flf-empty-state i {
    font-size: 48px;
    color: var(--fl-chambers-100);
    margin-bottom: 16px;
    display: block;
}
.flf-empty-state p {
    font-family: var(--fl-font-body);
    font-size: 15px;
    color: var(--fl-ink-400);
    margin: 0;
}
.flf-empty-state a {
    display: inline-block;
    margin-top: 16px;
}
@media (max-width: 767px) {
    .flf-prop-table th,
    .flf-prop-table td { padding: 12px 10px; font-size: 13px; }
    .flf-prop-thumb { width: 52px; height: 40px; }
}
</style>

<div class="midde_cont">
    <div class="container-fluid">

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:var(--flf-radius-sm);margin:0;">
                        <i class="fa fa-check-circle" style="margin-right:6px;"></i>
                        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
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
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                            <h2><i class="fa fa-home" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>Property Listings</h2>
                            <a href="manage_property.php" class="btn btn-info btn-sm">
                                <i class="fa fa-plus" style="margin-right:5px;"></i>Add New Property
                            </a>
                        </div>
                    </div>

                    <div class="full padding_infor_info" style="padding:0;">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" style="margin:24px 24px 0;border-radius:var(--flf-radius-sm);">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php elseif (empty($properties)): ?>
                            <div class="flf-empty-state">
                                <i class="fa fa-building"></i>
                                <p>No properties found. Add a new property to get started.</p>
                                <a href="manage_property.php" class="btn btn-info btn-sm">
                                    <i class="fa fa-plus" style="margin-right:5px;"></i>Add Property
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table flf-prop-table" style="margin-bottom:0;">
                                    <thead>
                                        <tr>
                                            <th style="width:60px;">ID</th>
                                            <th style="width:90px;">Image</th>
                                            <th>Location</th>
                                            <th>Title</th>
                                            <th style="width:110px;text-align:center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($properties as $property): ?>
                                        <tr>
                                            <td><span class="flf-prop-id">#<?php echo htmlspecialchars($property['id']); ?></span></td>
                                            <td>
                                                <?php if (!empty($property['image'])): ?>
                                                    <img src="propertyMgt/proImg/<?php echo htmlspecialchars($property['image']); ?>" alt="Property" class="flf-prop-thumb">
                                                <?php else: ?>
                                                    <div class="flf-prop-thumb" style="background:var(--flf-blue);display:flex;align-items:center;justify-content:center;">
                                                        <i class="fa fa-image" style="color:var(--flf-muted);"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="flf-prop-location"><?php echo htmlspecialchars($property['location']); ?></span></td>
                                            <td><span class="flf-prop-title"><?php echo htmlspecialchars($property['title']); ?></span></td>
                                            <td>
                                                <div class="flf-action-group" style="justify-content:center;">
                                                    <a href="edit_property.php?id=<?php echo $property['id']; ?>" class="btn btn-info btn-sm" title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-danger btn-sm" title="Delete"
                                                       onclick="document.getElementById('deleteId').value='<?php echo $property['id']; ?>';document.getElementById('deleteFormId').value='<?php echo $property['id']; ?>';document.getElementById('deleteTitle').textContent='<?php echo htmlspecialchars(addslashes($property['title'])); ?>';document.getElementById('deleteModal').style.display='flex';return false;">
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
            <h4 style="font-family:var(--flf-font-head);color:var(--flf-navy);margin:0 0 8px;font-size:22px;">Delete Property</h4>
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

<form id="deleteForm" method="POST" action="display_properties.php" style="display:none;">
    <?php echo csrfHiddenField(); ?>
    <input type="hidden" name="delete_id" id="deleteFormId" value="">
</form>
<input type="hidden" id="deleteId" value="">

<?php
require_once 'include/footer.php';
?>