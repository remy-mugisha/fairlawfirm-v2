<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid request. No ID provided.";
    echo "<script>window.location.href = 'display_about.php';</script>";
    exit();
}

$id = $_GET['id'];

// Fetch existing data
try {
    $stmt = $conn->prepare("SELECT * FROM about_content WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $about = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$about) {
        $_SESSION['error_message'] = "About content not found.";
        echo "<script>window.location.href = 'display_about.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error fetching about content: " . $e->getMessage();
    echo "<script>window.location.href = 'display_about.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $more_description = $_POST['more_description'];
    $client = $_POST['client'];
    $cases_won = $_POST['cases_won'];
    $achievements = $_POST['achievements'];
    $our_team = $_POST['our_team'];
    $status = $_POST['status'];

    // Handle image upload
    $image = $about['image']; // Keep the existing image by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Delete the old image if it exists
        if (!empty($about['image']) && file_exists("propertyMgt/aboutImg/" . $about['image'])) {
            unlink("propertyMgt/aboutImg/" . $about['image']);
        }
        // Upload the new image
        $image = basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "propertyMgt/aboutImg/" . $image);
    }

    try {
        $stmt = $conn->prepare("UPDATE about_content SET image = :image, title = :title, description = :description, more_description = :more_description, client = :client, cases_won = :cases_won, achievements = :achievements, our_team = :our_team, status = :status WHERE id = :id");
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':more_description', $more_description);
        $stmt->bindParam(':client', $client);
        $stmt->bindParam(':cases_won', $cases_won);
        $stmt->bindParam(':achievements', $achievements);
        $stmt->bindParam(':our_team', $our_team);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $_SESSION['success_message'] = "About content updated successfully!";
        echo "<script>window.location.href = 'display_about.php';</script>";
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating about content: " . $e->getMessage();
    }
}
?>

<div class="row column1">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Edit About Content</h2>
                </div>
            </div>
            <div class="full padding_infor_info">
                <form action="edit_about.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($about['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($about['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="more_description">More Description</label>
                        <textarea class="form-control" id="more_description" name="more_description" rows="3"><?php echo htmlspecialchars($about['more_description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="client">Client</label>
                        <input type="text" class="form-control" id="client" name="client" value="<?php echo htmlspecialchars($about['client']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="cases_won">Cases Won</label>
                        <input type="text" class="form-control" id="cases_won" name="cases_won" value="<?php echo htmlspecialchars($about['cases_won']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="achievements">Achievements</label>
                        <textarea class="form-control" id="achievements" name="achievements" rows="3"><?php echo htmlspecialchars($about['achievements']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="our_team">Our Team</label>
                        <textarea class="form-control" id="our_team" name="our_team" rows="3"><?php echo htmlspecialchars($about['our_team']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" class="form-control-file" id="image" name="image">
                        <?php if (!empty($about['image'])): ?>
                            <img src="propertyMgt/aboutImg/<?php echo htmlspecialchars($about['image']); ?>" alt="Current Image" class="img-thumbnail" style="max-height: 100px; margin-top: 10px;">
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="Active" <?php echo ($about['status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                            <option value="Pending" <?php echo ($about['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-info">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'include/footer.php';
?>