<?php
ob_start();
require_once 'include/header.php'; // session_start() is inside this
require_once 'propertyMgt/config.php';

// Handle Delete Action
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
    session_write_close(); // Ensure session data is saved
    header("Location: add_video.php");
    exit();
}

// Handle Edit Action
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

// Handle Form Submission (Add or Update)
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

    session_write_close(); // Ensure session data is saved
    header("Location: add_video.php");
    exit();
}

// Fetch all video links
try {
    $stmt = $conn->query("SELECT * FROM videos ORDER BY created_at DESC");
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching video links: " . $e->getMessage();
    echo "<p style='color: red;'>$error</p>";
}
?>

<!-- Display Success/Error Messages -->
<?php if (!empty($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>


<!-- Add/Edit Video Link Form -->
<form method="POST" action="add_video.php">
    <div class="form-group">
        <label for="video_link">Video Link</label>
        <input type="text" class="form-control" id="video_link" name="video_link" 
               value="<?php echo isset($edit_video) ? htmlspecialchars($edit_video['video_link']) : ''; ?>" 
               placeholder="Enter video link" required>
    </div>
    <div class="form-group">
        <label for="status">Status</label>
        <select class="form-control" id="status" name="status">
            <option value="active" <?php echo (isset($edit_video) && $edit_video['status'] == 'active' ? 'selected' : ''); ?>>Active</option>
            <option value="pending" <?php echo (isset($edit_video) && $edit_video['status'] == 'pending' ? 'selected' : ''); ?>>Pending</option>
        </select>
    </div>
    <?php if (isset($edit_video)): ?>
        <input type="hidden" name="id" value="<?php echo $edit_video['id']; ?>">
    <?php endif; ?>
    <button type="submit" class="btn btn-info">
        <?php echo isset($edit_video) ? 'Update Video Link' : 'Add Video Link'; ?>
    </button>
</form>
<br>
<style>
.table .thead-dark th {
    color: #fff;
    background-color: #15283c;
    border-color: #32383e;
}
</style>
<!-- Video Links Table -->
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Video Link</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($videos as $video): ?>
                <tr>
                    <td><?php echo $video['id']; ?></td>
                    <td><?php echo htmlspecialchars($video['video_link']); ?></td>
                    <td><?php echo $video['status']; ?></td>
                    <td><?php echo $video['created_at']; ?></td>
                    <td>
                        <a href="add_video.php?edit=<?php echo $video['id']; ?>" class="btn btn-info btn-sm">
                            <i class="fa fa-edit"></i> 
                        </a>
                        <a href="add_video.php?delete=<?php echo $video['id']; ?>" class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure you want to delete this video link?')">
                            <i class="fa fa-trash"></i> 
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once 'include/footer.php';
ob_end_flush(); // End output buffering and send output to the browser
?>