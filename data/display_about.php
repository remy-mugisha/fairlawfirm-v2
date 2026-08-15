<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $conn->prepare("SELECT image FROM about_content WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $about = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($about && !empty($about['image']) && file_exists("propertyMgt/aboutImg/" . $about['image'])) {
            unlink("propertyMgt/aboutImg/" . $about['image']);
        }
        
        $stmt = $conn->prepare("DELETE FROM about_content WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $_SESSION['success_message'] = "About content deleted successfully!";
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Error deleting about content: " . $e->getMessage();
    }
    echo "<script>window.location.href = 'display_about.php';</script>";
    exit();
}

try {
    $stmt = $conn->query("SELECT * FROM about_content WHERE status='Active' ORDER BY id DESC");
    $aboutContents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching about content: " . $e->getMessage();
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
                    <h2>About Content List</h2>
                    <a href="add_about.php" class="btn btn-info btn-sm">Add New About Content</a>
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
                    <?php elseif (empty($aboutContents)): ?>
                        <div class="alert alert-info">No about content found. Add new content to get started.</div>
                    <?php else: ?>
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Cases Won</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($aboutContents as $about): ?>
                                <tr>
                                    <td><?php echo $about['id']; ?></td>
                                    <td>
                                        <img src="propertyMgt/aboutImg/<?php echo htmlspecialchars($about['image']); ?>" alt="About Image" class="img-thumbnail" style="max-height: 100px;">
                                    </td>
                                    <td><?php echo htmlspecialchars($about['title']); ?></td>
                                    <td><?php echo htmlspecialchars($about['description']); ?></td>
                                    <td><?php echo htmlspecialchars($about['cases_won']); ?></td>
                                    <td>
                                        <a href="view_about.php?id=<?php echo $about['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fa fa-eye"></i> 
                                        </a>
                                        <a href="edit_about.php?id=<?php echo $about['id']; ?>" class="btn btn-info btn-sm">
                                            <i class="fa fa-edit"></i> 
                                        </a>
                                        <a href="display_about.php?delete=<?php echo $about['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this about content?')">
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