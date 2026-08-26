<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    try {
        $image_query = "SELECT image_path FROM property_images WHERE property_id = :id";
        $image_stmt = $conn->prepare($image_query);
        $image_stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
        $image_stmt->execute();
        $images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($images as $image) {
            if (file_exists($image['image_path'])) {
                unlink($image['image_path']);
            }
        }
        
        $delete_images = $conn->prepare("DELETE FROM property_images WHERE property_id = :id");
        $delete_images->bindParam(':id', $delete_id, PDO::PARAM_INT);
        $delete_images->execute();
        
        $delete_query = "DELETE FROM properties WHERE id = :id";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
        
        if ($delete_stmt->execute()) {
            $_SESSION['success_message'] = "Property and all associated images deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to delete property.";
        }
    } catch (PDOException $e) {
        error_log("Display rental error: " . $e->getMessage()); $_SESSION['error_message'] = "An error occurred. Please try again.";
    }
    echo "<script>window.location.href = 'display_rental.php';</script>";
    exit();
}

$query = "SELECT * FROM properties ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

function formatDisplayPrice($price) {
    if (preg_match('/(\d+)\s*-\s*(\d+)/', $price, $matches)) {
        return number_format($matches[1], 0, '', ',') . 'Rwf - ' . number_format($matches[2], 0, '', ',') . 'Rwf';
    }
    $cleanPrice = preg_replace('/[^0-9]/', '', $price);
    return number_format($cleanPrice, 0, '', ',') . 'Rwf';
}
?>

<style>
.flf-rental-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.flf-rental-table th {
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
.flf-rental-table th:first-child { border-radius: 10px 0 0 0; }
.flf-rental-table th:last-child  { border-radius: 0 10px 0 0; }
.flf-rental-table td {
    font-family: var(--flf-font-body);
    font-size: 14px;
    color: var(--flf-charcoal);
    padding: 16px 18px;
    border-bottom: 1px solid var(--flf-blue);
    vertical-align: middle;
}
.flf-rental-table tbody tr {
    transition: background 0.2s ease;
}
.flf-rental-table tbody tr:hover {
    background: rgba(233, 238, 250, 0.45);
}
.flf-rental-table tbody tr:last-child td {
    border-bottom: none;
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
.flf-status-rent  { background: rgba(26, 122, 76, 0.1);  color: var(--flf-success); }
.flf-status-sale  { background: rgba(200, 169, 81, 0.15); color: #947a2e; }
.flf-status-na    { background: rgba(107, 118, 153, 0.1); color: var(--flf-muted); }
.flf-price-cell {
    font-weight: 600;
    color: var(--flf-navy);
    white-space: nowrap;
}
.flf-bed-bath {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: var(--flf-font-body);
    font-size: 13px;
    color: var(--flf-slate);
}
.flf-bed-bath i { color: var(--flf-gold); font-size: 12px; }
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
                        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
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
                        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
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
                            <h2><i class="fa fa-building" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>Rental Properties</h2>
                            <a href="add_rental_property.php" class="btn btn-info btn-sm">
                                <i class="fa fa-plus" style="margin-right:5px;"></i>Add New Property
                            </a>
                        </div>
                    </div>

                    <div class="full padding_infor_info" style="padding:0;">
                        <?php if (empty($properties)): ?>
                            <div class="flf-empty-state">
                                <i class="fa fa-home"></i>
                                <p>No rental properties found. Add one to get started.</p>
                                <a href="add_rental_property.php" class="btn btn-info btn-sm">
                                    <i class="fa fa-plus" style="margin-right:5px;"></i>Add Property
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table flf-rental-table" style="margin-bottom:0;">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Price</th>
                                            <th>Bed/Bath</th>
                                            <th style="width:140px;text-align:center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($properties as $row): ?>
                                        <tr>
                                            <td>
                                                <span style="font-weight:600;color:var(--flf-navy);"><?php echo htmlspecialchars($row['title']); ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['property_type']); ?></td>
                                            <td>
                                                <?php if ($row['property_status'] == 'For Rent'): ?>
                                                    <span class="flf-status-pill flf-status-rent"><?php echo htmlspecialchars($row['property_status']); ?></span>
                                                <?php elseif ($row['property_status'] == 'For Sale'): ?>
                                                    <span class="flf-status-pill flf-status-sale"><?php echo htmlspecialchars($row['property_status']); ?></span>
                                                <?php else: ?>
                                                    <span class="flf-status-pill flf-status-na"><?php echo htmlspecialchars($row['property_status']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="flf-price-cell"><?php echo formatDisplayPrice($row['price']); ?></span></td>
                                            <td>
                                                <?php if ($row['property_type'] !== 'Commercial Building'): ?>
                                                    <span class="flf-bed-bath">
                                                        <i class="fa fa-bed"></i><?php echo htmlspecialchars($row['bedroom']); ?>
                                                        <span style="color:#d9dee9;">/</span>
                                                        <i class="fa fa-bath"></i><?php echo htmlspecialchars($row['bathroom']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="flf-bed-bath">
                                                        <i class="fa fa-building"></i><?php echo htmlspecialchars($row['floor']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="flf-action-group" style="justify-content:center;">
                                                    <a href="property_details.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="edit_rental.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a href="property_images.php?property_id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm" title="Images">
                                                        <i class="fa fa-image"></i>
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
            <h4 style="font-family:var(--flf-font-head);color:var(--flf-navy);margin:0 0 8px;font-size:22px;">Delete Property</h4>
            <p style="font-family:var(--flf-font-body);color:var(--flf-muted);font-size:14px;margin:0;">
                Are you sure you want to delete "<strong id="deleteTitle" style="color:var(--flf-charcoal);"></strong>"? This will also remove all associated images. This action cannot be undone.
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
    window.location.href = 'display_rental.php?delete_id=' + id;
});
</script>

<input type="hidden" id="deleteId" value="">

<?php require_once 'include/footer.php'; ?>