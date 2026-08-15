<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $conn->prepare("SELECT image FROM add_property WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $property = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($property && !empty($property['image']) && file_exists("propertyMgt/proImg/" . $property['image'])) {
            unlink("propertyMgt/proImg/" . $property['image']);
        }
        
        $stmt = $conn->prepare("DELETE FROM add_property WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $_SESSION['success_message'] = "Property deleted successfully!";
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Error deleting property: " . $e->getMessage();
    }
    echo "<script>window.location.href = 'display_properties.php';</script>";
    exit();
}

try {
    $stmt = $conn->query("SELECT * FROM add_property where status='Active' ORDER BY id DESC");
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching properties: " . $e->getMessage();
}
?>

<style>
.table .thead-dark th {
    color: #fff;
    background-color: #15283c;
    border-color: #32383e;
}
</style>

<div class="row column1">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                    <h2>Property Listings</h2>
                    <a href="manage_property.php" class="btn btn-info btn-sm">Add New Property</a>
                </div>
            </div>          
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <div class="full padding_infor_info">
                <div class="table-responsive">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php elseif (empty($properties)): ?>
                        <div class="alert alert-info">No properties found. Add a new property to get started.</div>
                    <?php else: ?>
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Location</th>
                                    <th>title</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($properties as $property): ?>
                                <tr>
                                    <td><?php echo $property['id']; ?></td>
                                    <td>
                                        <img src="propertyMgt/proImg/<?php echo htmlspecialchars($property['image']); ?>" alt="Property Image" class="img-thumbnail" style="max-height: 100px;">
                                    </td>
                                    <td><?php echo htmlspecialchars($property['location']); ?></td>
                                    <td><?php echo htmlspecialchars($property['title']); ?></td>
                                    <td>
                                        <a href="edit_property.php?id=<?php echo $property['id']; ?>" class="btn btn-info btn-sm">
                                            <i class="fa fa-edit"></i> 
                                        </a>
                                        <a href="display_properties.php?delete=<?php echo $property['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this property?')">
                                            <i class="fa fa-trash"></i> 
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'include/footer.php';
?>