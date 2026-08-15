<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

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
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "propertyMgt/aboutImg/" . $image);
    }

    try {
        $stmt = $conn->prepare("INSERT INTO about_content (image, title, description, more_description, client, cases_won, achievements, our_team, status) VALUES (:image, :title, :description, :more_description, :client, :cases_won, :achievements, :our_team, :status)");
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':more_description', $more_description);
        $stmt->bindParam(':client', $client);
        $stmt->bindParam(':cases_won', $cases_won);
        $stmt->bindParam(':achievements', $achievements);
        $stmt->bindParam(':our_team', $our_team);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        $_SESSION['success_message'] = "About content added successfully!";
        echo "<script>window.location.href = 'display_about.php';</script>";
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error adding about content: " . $e->getMessage();
    }
}
?>

<div class="row column1">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Add About Content</h2>
                </div>
            </div>
            <div class="full padding_infor_info">
                <form action="add_about.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="more_description">More Description</label>
                        <textarea class="form-control" id="more_description" name="more_description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="client">Client</label>
                        <input type="text" class="form-control" id="client" name="client">
                    </div>
                    <div class="form-group">
                        <label for="cases_won">Cases Won</label>
                        <input type="text" class="form-control" id="cases_won" name="cases_won" required>
                    </div>
                    <div class="form-group">
                        <label for="achievements">Achievements</label>
                        <input type="text" class="form-control" id="achievements" name="achievements" required>
                    </div>
                    <div class="form-group">
                        <label for="our_team">Our Team</label>
                        <input type="text" class="form-control" id="our_team" name="our_team" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" class="form-control-file" id="image" name="image" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="Active">Active</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'include/footer.php';
?>