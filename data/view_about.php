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

<style>
.flf-about-hero {
    position: relative;
    width: 100%;
    border-radius: var(--flf-radius);
    overflow: hidden;
    margin-bottom: 28px;
}
.flf-about-hero img {
    width: 100%;
    max-height: 380px;
    object-fit: cover;
    display: block;
}
.flf-about-hero-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(1, 22, 106, 0.85));
    padding: 40px 32px 24px;
}
.flf-about-hero-overlay h2 {
    font-family: var(--flf-font-head);
    font-size: 30px;
    font-weight: 700;
    color: var(--flf-white);
    margin: 0;
}
.flf-about-hero-overlay .flf-status-pill {
    margin-top: 8px;
}
.flf-about-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 32px;
}
.flf-about-block {
    padding: 0;
}
.flf-about-block-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: var(--flf-font-head);
    font-size: 18px;
    font-weight: 700;
    color: var(--flf-navy);
    margin: 0 0 12px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--flf-blue);
}
.flf-about-block-title i {
    color: var(--flf-gold);
    font-size: 14px;
}
.flf-about-text {
    font-family: var(--flf-font-body);
    font-size: 14px;
    line-height: 1.7;
    color: var(--flf-charcoal);
    margin: 0;
    white-space: pre-wrap;
}
.flf-stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-top: 4px;
}
.flf-stat-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px 20px;
    background: var(--flf-white);
    border: 1px solid var(--flf-blue);
    border-radius: var(--flf-radius-sm);
    transition: all 0.2s ease;
}
.flf-stat-card:hover {
    box-shadow: 0 4px 14px rgba(1, 22, 106, 0.08);
}
.flf-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.flf-stat-icon i { color: var(--flf-white); }
.flf-stat-icon-stat1 { background: linear-gradient(135deg, var(--flf-navy), var(--flf-royal)); }
.flf-stat-icon-stat2 { background: linear-gradient(135deg, var(--flf-success), #1a9960); }
.flf-stat-icon-stat3 { background: linear-gradient(135deg, var(--flf-gold), #b8983e); }
.flf-stat-icon-stat4 { background: linear-gradient(135deg, var(--flf-royal), #2a4ec0); }
.flf-stat-info { display: flex; flex-direction: column; }
.flf-stat-value {
    font-family: var(--flf-font-head);
    font-size: 22px;
    font-weight: 700;
    color: var(--flf-navy);
    line-height: 1.2;
}
.flf-stat-label {
    font-family: var(--flf-font-body);
    font-size: 12px;
    font-weight: 600;
    color: var(--flf-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.flf-status-pill {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-family: var(--flf-font-body);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.flf-status-active  { background: rgba(26, 122, 76, 0.15); color: #1a7a4c; }
.flf-status-pending { background: rgba(200, 169, 81, 0.2); color: #947a2e; }
@media (max-width: 767px) {
    .flf-about-body { grid-template-columns: 1fr; }
    .flf-stats-grid { grid-template-columns: 1fr; }
}
</style>

<div class="midde_cont">
    <div class="container-fluid">

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:var(--flf-radius-sm);margin:0;">
                        <i class="fa fa-check-circle" style="margin-right:6px;"></i>
                        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                            <h2><i class="fa fa-info-circle" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>About Content</h2>
                            <div>
                                <a href="edit_about.php?id=<?php echo $about['id']; ?>" class="btn btn-info btn-sm">
                                    <i class="fa fa-pencil" style="margin-right:5px;"></i>Edit
                                </a>
                                <a href="display_about.php" class="btn btn-secondary btn-sm" style="margin-left:6px;">
                                    <i class="fa fa-arrow-left" style="margin-right:5px;"></i>Back
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="full padding_infor_info">

                        <!-- Hero Image -->
                        <?php if (!empty($about['image'])): ?>
                            <div class="flf-about-hero">
                                <img src="propertyMgt/aboutImg/<?php echo htmlspecialchars($about['image']); ?>" alt="<?php echo htmlspecialchars($about['title']); ?>">
                                <div class="flf-about-hero-overlay">
                                    <h2><?php echo htmlspecialchars($about['title']); ?></h2>
                                    <?php if ($about['status'] == 'Active'): ?>
                                        <span class="flf-status-pill flf-status-active">Active</span>
                                    <?php else: ?>
                                        <span class="flf-status-pill flf-status-pending">Pending</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Content Grid -->
                        <div class="flf-about-body" style="margin-bottom:32px;">

                            <div class="flf-about-block">
                                <h3 class="flf-about-block-title"><i class="fa fa-align-left"></i>Description</h3>
                                <p class="flf-about-text"><?php echo htmlspecialchars($about['description']); ?></p>
                            </div>

                            <?php if (!empty($about['more_description'])): ?>
                            <div class="flf-about-block">
                                <h3 class="flf-about-block-title"><i class="fa fa-paragraph"></i>More Description</h3>
                                <p class="flf-about-text"><?php echo htmlspecialchars($about['more_description']); ?></p>
                            </div>
                            <?php endif; ?>

                        </div>

                        <!-- Statistics -->
                        <div style="margin-top:8px;">
                            <h3 class="flf-about-block-title" style="margin-bottom:18px;"><i class="fa fa-bar-chart"></i>Statistics</h3>

                            <div class="flf-stats-grid">
                                <div class="flf-stat-card">
                                    <div class="flf-stat-icon flf-stat-icon-stat1"><i class="fa fa-users"></i></div>
                                    <div class="flf-stat-info">
                                        <span class="flf-stat-value"><?php echo htmlspecialchars($about['client']); ?></span>
                                        <span class="flf-stat-label">Clients</span>
                                    </div>
                                </div>

                                <div class="flf-stat-card">
                                    <div class="flf-stat-icon flf-stat-icon-stat2"><i class="fa fa-trophy"></i></div>
                                    <div class="flf-stat-info">
                                        <span class="flf-stat-value"><?php echo htmlspecialchars($about['cases_won']); ?></span>
                                        <span class="flf-stat-label">Cases Won</span>
                                    </div>
                                </div>

                                <div class="flf-stat-card">
                                    <div class="flf-stat-icon flf-stat-icon-stat3"><i class="fa fa-star"></i></div>
                                    <div class="flf-stat-info">
                                        <span class="flf-stat-value"><?php echo htmlspecialchars($about['achievements']); ?></span>
                                        <span class="flf-stat-label">Achievements</span>
                                    </div>
                                </div>

                                <div class="flf-stat-card">
                                    <div class="flf-stat-icon flf-stat-icon-stat4"><i class="fa fa-user-md"></i></div>
                                    <div class="flf-stat-info">
                                        <span class="flf-stat-value"><?php echo htmlspecialchars($about['our_team']); ?></span>
                                        <span class="flf-stat-label">Our Team</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once 'include/footer.php'; ?>