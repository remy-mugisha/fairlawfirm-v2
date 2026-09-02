<?php
// edit_background.php
require_once 'propertyMgt/config.php';
require_once __DIR__ . '/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfPost();
    $id = $_POST['id'];
    $image_path = $_POST['image_path'];
    $status = $_POST['status'];

    // Update the background in the database
    $sql = "UPDATE home_backgrounds SET image_path = :image_path, status = :status WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':image_path', $image_path, PDO::PARAM_STR);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Background updated successfully!";
    } else {
        echo "An error occurred. Please try again.";
    }
}

// Fetch the background details for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM home_backgrounds WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        die("Background not found.");
    }
} else {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Edit Background | Fair Law Firm LTD</title>
   <link rel="shortcut icon" href="images/logo/logo_icon.png" type="image/x-icon">
   <link rel="stylesheet" href="css/bootstrap5.min.css">
   <link rel="stylesheet" href="css/theme.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body style="padding: var(--sp-6); max-width:600px; margin:0 auto;">
<div style="background: var(--fl-surface); border: 1px solid var(--fl-line); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); padding: var(--sp-6);">
    <h3 style="font-family: var(--font-heading); color: var(--fl-primary); margin:0 0 var(--sp-4);">Edit Background</h3>
    <form method="POST" action="">
        <?php echo csrfHiddenField(); ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
        <div class="mb-3">
            <label for="image_path" class="form-label">Image Path</label>
            <input type="text" class="form-control" name="image_path" id="image_path" value="<?= htmlspecialchars($row['image_path']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" name="status" id="status" required>
                <option value="active" <?= $row['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Background</button>
    </form>
</div>
</body>
</html>