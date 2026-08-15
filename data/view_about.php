<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid request. No ID provided.";
    echo "<script>window.location.href = 'display_about.php';</script>";
    exit();
}

$id = $_GET['id'];

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
?>

<div class="row column1">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>View About Content</h2>
                </div>
            </div>
            <div class="full padding_infor_info">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <p><?php echo htmlspecialchars($about['title']); ?></p>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <p><?php echo htmlspecialchars($about['description']); ?></p>
                        </div>
                        <div class="form-group">
                            <label for="more_description">More Description</label>
                            <p><?php echo htmlspecialchars($about['more_description']); ?></p>
                        </div>
                        <div class="form-group">
                            <label for="client">Client</label>
                            <p><?php echo htmlspecialchars($about['client']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cases_won">Cases Won</label>
                            <p><?php echo htmlspecialchars($about['cases_won']); ?></p>
                        </div>
                        <div class="form-group">
                            <label for="achievements">Achievements</label>
                            <p><?php echo htmlspecialchars($about['achievements']); ?></p>
                        </div>
                        <div class="form-group">
                            <label for="our_team">Our Team</label>
                            <p><?php echo htmlspecialchars($about['our_team']); ?></p>
                        </div>
                        <div class="form-group">
                            <label for="image">Image</label>
                            <img src="propertyMgt/aboutImg/<?php echo htmlspecialchars($about['image']); ?>" alt="About Image" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    </div>
                </div>
                <a href="display_about.php" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'include/footer.php';
?>