<?php
require_once 'propertyMgt/config.php';

try {
    // Fetch the active video link from the database
    $stmt = $conn->query("SELECT * FROM videos WHERE status = 'active' ORDER BY created_at DESC LIMIT 1");
    $active_video = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch all video links for the table
    $stmt = $conn->query("SELECT * FROM videos ORDER BY created_at DESC");
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Handle database errors
    $error = "Error fetching video links: " . $e->getMessage();
    echo "<p style='color: red;'>$error</p>";
}
?>

<div class="cta-one__left">
    <div class="cta-one__video">
        <?php
        if (!empty($active_video)) {
            echo '<a href="' . htmlspecialchars($active_video['video_link']) . '" class="cta-one__video__icon video_play video-popup">';
            echo '<i class="icon-polygon"></i>';
            echo '</a>';
        } else {
            // Display a message if no active video is available
            echo '<p>No active video available.</p>';
        }
        ?>
    </div>
</div>

<!-- Add Video Link Form -->
<form method="POST" action="add_video.php">
    <div class="form-group">
        <label for="video_link">Video Link</label>
        <input type="text" class="form-control" id="video_link" name="video_link" placeholder="Enter video link" required>
    </div>
    <button type="submit" class="btn btn-info">Add Video Link</button>
</form>

<!-- Video Links Table -->
<table class="table table-bordered">
    <thead>
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
                    <a href="edit_video.php?id=<?php echo $video['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                    <a href="delete_video.php?id=<?php echo $video['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this video link?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>