<?php
// edit_background.php
require_once 'propertyMgt/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        echo "Error updating background.";
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
    <meta charset="UTF-8">
    <title>Edit Background</title>
</head>
<body>
    <h1>Edit Background</h1>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
        <label for="image_path">Image Path:</label>
        <input type="text" name="image_path" id="image_path" value="<?= htmlspecialchars($row['image_path']) ?>" required>
        <br>
        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="active" <?= $row['status'] === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
        </select>
        <br>
        <button type="submit">Update Background</button>
    </form>
</body>
</html>