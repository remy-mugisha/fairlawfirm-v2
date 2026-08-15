<?php
// home_background.php
require_once 'propertyMgt/config.php';

// Handle form submission to add a new background
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_background'])) {
    $image_path = $_POST['image_path'];
    $status = $_POST['status'];

    // Insert new background into the database
    $sql = "INSERT INTO home_backgrounds (image_path, status) VALUES (:image_path, :status)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':image_path', $image_path, PDO::PARAM_STR);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo "Background added successfully!";
    } else {
        echo "Error adding background.";
    }
}

// Handle background deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the background from the database
    $sql = "DELETE FROM home_backgrounds WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $delete_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Background deleted successfully!";
    } else {
        echo "Error deleting background.";
    }
}

// Fetch all backgrounds from the database
$sql = "SELECT * FROM home_backgrounds";
$stmt = $conn->query($sql);
$backgrounds = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Home Backgrounds</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Manage Home Backgrounds</h1>

    <!-- Form to add a new background -->
    <h2>Add New Background</h2>
    <form method="POST" action="">
        <label for="image_path">Image Path:</label>
        <input type="text" name="image_path" id="image_path" required>
        <br>
        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="active">Active</option>
            <option value="pending">Pending</option>
        </select>
        <br>
        <button type="submit" name="add_background">Add Background</button>
    </form>

    <!-- Display existing backgrounds -->
    <h2>Existing Backgrounds</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Image Path</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($backgrounds as $background): ?>
        <tr>
            <td><?= htmlspecialchars($background['id']) ?></td>
            <td><?= htmlspecialchars($background['image_path']) ?></td>
            <td><?= htmlspecialchars($background['status']) ?></td>
            <td>
                <a href="edit_background.php?id=<?= $background['id'] ?>">Edit</a> |
                <a href="home_background.php?delete_id=<?= $background['id'] ?>" onclick="return confirm('Are you sure you want to delete this background?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>