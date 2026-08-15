<?php
session_start();
require_once 'propertyMgt/config.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $video_link = $_POST['video_link'];
    $status = $_POST['status'];

    try {
        // If the video is being set to active, set all other videos to pending
        if ($status == 'active') {
            $stmt = $conn->prepare("UPDATE videos SET status = 'pending' WHERE id != :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }

        // Update video link and status
        $stmt = $conn->prepare("UPDATE videos SET video_link = :video_link, status = :status WHERE id = :id");
        $stmt->bindParam(':video_link', $video_link);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $_SESSION['success_message'] = "Video link updated successfully!";
        header("Location: dashboard.php");
        exit();
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Error updating video link: " . $e->getMessage();
        header("Location: edit_video.php?id=" . $id);
        exit();
    }
}

try {
    $stmt = $conn->prepare("SELECT * FROM videos WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $video = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching video link: " . $e->getMessage();
}
?>

<!-- Display Success/Error Messages -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<!-- Edit Video Link Form -->
<form method="POST" action="edit_video.php?id=<?php echo $id; ?>">
    <div class="form-group">
        <label for="video_link">Video Link</label>
        <input type="text" class="form-control" id="video_link" name="video_link" value="<?php echo htmlspecialchars($video['video_link']); ?>" required>
    </div>
    <div class="form-group">
        <label for="status">Status</label>
        <select class="form-control" id="status" name="status">
            <option value="active" <?php echo ($video['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
            <option value="pending" <?php echo ($video['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Update Video Link</button>
</form>