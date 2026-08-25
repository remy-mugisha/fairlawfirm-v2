<?php
if (session_status() === PHP_SESSION_NONE) {
}
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

function getCount($conn, $table, $condition = null) {
    try {
        $sql = "SELECT COUNT(*) as count FROM $table";
        if ($condition) {
            $sql .= " WHERE $condition";
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (PDOException $e) {
        return 0;
    }
}

$userCount = getCount($conn, "users", "status = 'Active'");
$totalProperties = getCount($conn, "add_property");
$rentalProperties = getCount($conn, "properties", "property_status = 'For Rent'");
$blogCount = getCount($conn, "blog");

$recentUsers = [];
try {
    $stmt = $conn->prepare("SELECT first_name, last_name, email, profile_image, status, created_at FROM users ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $recentUsers = [];
}

$statusRows = [];
try {
    $stmt = $conn->query("SELECT property_status AS label, COUNT(*) AS cnt FROM properties GROUP BY property_status ORDER BY cnt DESC");
    $statusRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $statusRows = [];
}

$growthMap = [];
try {
    $stmt = $conn->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH) GROUP BY ym ORDER BY ym");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $growthMap[$row['ym']] = (int)$row['c'];
    }
} catch (Throwable $e) {
    $growthMap = [];
}

$growthLabels = [];
$growthCounts = [];
for ($i = 11; $i >= 0; $i--) {
    $ts = strtotime("first day of -$i months");
    $key = date('Y-m', $ts);
    $growthLabels[] = date('M y', $ts);
    $growthCounts[] = isset($growthMap[$key]) ? $growthMap[$key] : 0;
}

$welcomeName = 'Guest';
if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
    $welcomeName = htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']);
}

function timeAgo($datetime) {
    $ts = strtotime($datetime);
    if ($ts === false) return '';
    $diff = time() - $ts;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
    if ($diff < 604800) return floor($diff / 86400) . ' day(s) ago';
    return date('M j, Y', $ts);
}
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

<div class="padding_infor_info flf-wrap">

    <div class="flf-page-head">
        <h1 class="flf-title">Dashboard</h1>
        <p class="flf-subtitle">Welcome back, <?php echo $welcomeName; ?> &mdash; here is what is happening at Fair Law Firm LTD today.</p>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xl-3">
            <div class="flf-stat-card">
                <span class="flf-accent"></span>
                <div class="flf-icon-box"><i class="fa fa-users"></i></div>
                <div class="flf-stat-info">
                    <h2><?php echo $userCount; ?></h2>
                    <h5>Total Employers</h5>
                    <p>Registered active employers</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="flf-stat-card">
                <span class="flf-accent"></span>
                <div class="flf-icon-box"><i class="fa fa-building"></i></div>
                <div class="flf-stat-info">
                    <h2><?php echo $totalProperties; ?></h2>
                    <h5>Total Properties</h5>
                    <p>All properties in system</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="flf-stat-card">
                <span class="flf-accent"></span>
                <div class="flf-icon-box"><i class="fa fa-home"></i></div>
                <div class="flf-stat-info">
                    <h2><?php echo $rentalProperties; ?></h2>
                    <h5>Rental Properties</h5>
                    <p>Currently listed for rent</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="flf-stat-card">
                <span class="flf-accent"></span>
                <div class="flf-icon-box"><i class="fa fa-newspaper-o"></i></div>
                <div class="flf-stat-info">
                    <h2><?php echo $blogCount; ?></h2>
                    <h5>Blog Posts</h5>
                    <p>Articles published firm-wide</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-6 mb-4">
            <div class="flf-panel">
                <div class="flf-panel-head">
                    <h3><i class="fa fa-pie-chart"></i> Property Status Distribution</h3>
                </div>
                <?php if (empty($statusRows)): ?>
                    <div class="flf-chart-empty">No property listings available yet.</div>
                <?php else: ?>
                    <div class="flf-chart-box">
                        <canvas id="flf_property_chart"></canvas>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="flf-panel">
                <div class="flf-panel-head">
                    <h3><i class="fa fa-line-chart"></i> User Growth &mdash; Last 12 Months</h3>
                </div>
                <div class="flf-chart-box">
                    <canvas id="flf_growth_chart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="flf-panel">
                <div class="flf-panel-head">
                    <h3><i class="fa fa-history"></i> Recent Activity</h3>
                    <a href="manage_users.php" class="flf-panel-link">View All</a>
                </div>
                <ul class="flf-activity-list">
                    <?php if (empty($recentUsers)): ?>
                        <li class="flf-empty">No recent user activity found.</li>
                    <?php else: ?>
                        <?php foreach ($recentUsers as $ru): ?>
                            <li>
                                <?php if (!empty($ru['profile_image'])): ?>
                                    <img class="flf-avatar" src="<?php echo htmlspecialchars($ru['profile_image']); ?>" alt="">
                                <?php else: ?>
                                    <img class="flf-avatar" src="images/default-avatar.png" alt="">
                                <?php endif; ?>
                                <div class="flf-activity-body">
                                    <span class="flf-name"><?php echo htmlspecialchars($ru['first_name'] . ' ' . $ru['last_name']); ?></span>
                                    <span class="flf-email"><?php echo htmlspecialchars($ru['email']); ?></span>
                                </div>
                                <div class="flf-activity-meta">
                                    <span class="flf-badge flf-badge-<?php echo strtolower($ru['status']); ?>"><?php echo htmlspecialchars($ru['status']); ?></span>
                                    <span class="flf-time"><?php echo timeAgo($ru['created_at']); ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="flf-panel">
                <div class="flf-panel-head">
                    <h3><i class="fa fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="flf-actions-grid">
                    <a href="add_blog.php" class="flf-action-tile">
                        <i class="fa fa-pencil-square-o"></i>
                        <span>New Blog</span>
                    </a>
                    <a href="add_property.php" class="flf-action-tile">
                        <i class="fa fa-building"></i>
                        <span>Add Property</span>
                    </a>
                    <a href="add_rental_property.php" class="flf-action-tile">
                        <i class="fa fa-home"></i>
                        <span>Add Rental</span>
                    </a>
                    <a href="register.php" class="flf-action-tile">
                        <i class="fa fa-user-plus"></i>
                        <span>Add User</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    if (typeof Chart === 'undefined' || !window.jQuery) return;

    var NAVY       = '#01166A';
    var NAVY_DARK  = '#010F47';
    var GOLD       = '#C8A951';
    var GOLD_LIGHT = '#E3CE8F';
    var BLUE_SOFT  = '#8FA0CC';
    var MUTED      = '#7c88ab';
    var FONT_BODY  = "'DM Sans', sans-serif";

    var tooltipStyle = {
        backgroundColor: NAVY,
        titleFontColor: GOLD,
        bodyFontColor: '#ffffff',
        borderColor: GOLD,
        borderWidth: 1,
        cornerRadius: 8,
        displayColors: false,
        titleFontFamily: FONT_BODY,
        bodyFontFamily: FONT_BODY
    };

    /* ---- Chart 1: property status distribution (doughnut) ---- */
    var propCanvas = document.getElementById('flf_property_chart');
    if (propCanvas) {
        var propLabels = <?php echo json_encode(array_map(function ($r) { return $r['label']; }, $statusRows)); ?>;
        var propValues = <?php echo json_encode(array_map(function ($r) { return (int)$r['cnt']; }, $statusRows)); ?>;
        var palette = [NAVY, GOLD, BLUE_SOFT, GOLD_LIGHT, NAVY_DARK, '#63719C', '#B49440'];

        new Chart(propCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: propLabels,
                datasets: [{
                    data: propValues,
                    backgroundColor: propLabels.map(function (_, i) { return palette[i % palette.length]; }),
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverBorderWidth: 3,
                    hoverBorderColor: NAVY
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                cutoutPercentage: 62,
                legend: {
                    position: 'bottom',
                    labels: {
                        fontFamily: FONT_BODY,
                        fontColor: NAVY,
                        boxWidth: 12,
                        padding: 18,
                        usePointStyle: true
                    }
                },
                tooltips: tooltipStyle
            },
            plugins: [{
                id: 'flfCenterTotal',
                afterDraw: function (chart) {
                    var ctx = chart.chart.ctx;
                    var meta = chart.getDatasetMeta(0);
                    if (!meta.data.length) return;
                    var x = meta.data[0]._model.x;
                    var y = meta.data[0]._model.y;
                    var total = chart.data.datasets[0].data.reduce(function (a, b) { return a + b; }, 0);

                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.font = "700 34px 'Cormorant Garamond', serif";
                    ctx.fillStyle = NAVY;
                    ctx.fillText(total, x, y - 10);
                    ctx.font = "500 11px " + FONT_BODY;
                    ctx.fillStyle = GOLD;
                    ctx.fillText('TOTAL LISTINGS', x, y + 16);
                    ctx.restore();
                }
            }]
        });
    }

    /* ---- Chart 2: user growth, last 12 months (line) ---- */
    var growthCanvas = document.getElementById('flf_growth_chart');
    if (growthCanvas) {
        var gCtx = growthCanvas.getContext('2d');
        var grad = gCtx.createLinearGradient(0, 0, 0, 320);
        grad.addColorStop(0, 'rgba(200, 169, 81, 0.30)');
        grad.addColorStop(1, 'rgba(200, 169, 81, 0.02)');

        new Chart(gCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($growthLabels); ?>,
                datasets: [{
                    label: 'New Users',
                    data: <?php echo json_encode($growthCounts); ?>,
                    borderColor: NAVY,
                    backgroundColor: grad,
                    borderWidth: 3,
                    pointBackgroundColor: GOLD,
                    pointBorderColor: GOLD,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: NAVY,
                    pointHoverBorderColor: GOLD,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                legend: { display: false },
                scales: {
                    xAxes: [{
                        gridLines: { display: false },
                        ticks: {
                            fontFamily: FONT_BODY,
                            fontColor: MUTED,
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 6
                        }
                    }],
                    yAxes: [{
                        beginAtZero: true,
                        gridLines: { color: '#E9EEFA', drawBorder: false },
                        ticks: {
                            precision: 0,
                            fontFamily: FONT_BODY,
                            fontColor: MUTED,
                            padding: 8
                        }
                    }]
                },
                tooltips: tooltipStyle
            }
        });
    }
});
</script>

<?php
require_once 'include/footer.php';
?>
