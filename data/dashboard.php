<?php
/* ================================================================
   Fair Law Firm LTD - Admin Dashboard
   Main landing page: stat cards, charts, activity, quick actions.
   ================================================================ */
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

/* ----------------------------------------------------------------
   Helpers
   ---------------------------------------------------------------- */
function getCount($conn, $table, $condition = null) {
   try {
      $sql = "SELECT COUNT(*) AS cnt FROM `$table`";
      if ($condition) {
         $sql .= " WHERE $condition";
      }
      return (int) $conn->query($sql)->fetchColumn();
   } catch (PDOException $e) {
      return 0;
   }
}

function timeAgo($datetime) {
   $ts = strtotime($datetime);
   if ($ts === false) return '';
   $diff = time() - $ts;
   if ($diff < 60)    return 'Just now';
   if ($diff < 3600)  return floor($diff / 60) . ' min ago';
   if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
   if ($diff < 604800) return floor($diff / 86400) . ' day(s) ago';
   return date('M j, Y', $ts);
}

/* ----------------------------------------------------------------
   Stat card counts
   ---------------------------------------------------------------- */
$userCount        = getCount($conn, 'users',     "status = 'Active'");
$totalProperties  = getCount($conn, 'add_property');
$rentalProperties = getCount($conn, 'properties', "property_status = 'For Rent'");
$blogCount        = getCount($conn, 'blog');

/* ----------------------------------------------------------------
   Recent activity (last 5 users)
   ---------------------------------------------------------------- */
$recentUsers = [];
try {
   $recentUsers = $conn->query(
      "SELECT first_name, last_name, email, profile_image, status, created_at
       FROM users ORDER BY created_at DESC LIMIT 5"
   )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   $recentUsers = [];
}

/* ----------------------------------------------------------------
   Property status distribution (for doughnut chart)
   ---------------------------------------------------------------- */
$statusRows = [];
try {
   $statusRows = $conn->query(
      "SELECT property_status AS label, COUNT(*) AS cnt
       FROM properties GROUP BY property_status ORDER BY cnt DESC"
   )->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
   $statusRows = [];
}

/* ----------------------------------------------------------------
   User growth — last 12 months (for line chart)
   ---------------------------------------------------------------- */
$growthMap = [];
try {
   $stmt = $conn->query(
      "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c
       FROM users
       WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
       GROUP BY ym ORDER BY ym"
   );
   foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $growthMap[$row['ym']] = (int) $row['c'];
   }
} catch (Throwable $e) {
   $growthMap = [];
}

$growthLabels = [];
$growthCounts = [];
for ($i = 11; $i >= 0; $i--) {
   $ts  = strtotime("first day of -$i months");
   $key = date('Y-m', $ts);
   $growthLabels[] = date('M y', $ts);
   $growthCounts[] = isset($growthMap[$key]) ? $growthMap[$key] : 0;
}

/* ----------------------------------------------------------------
   Welcome name
   ---------------------------------------------------------------- */
$welcomeName = 'Guest';
if (isset($_SESSION['first_name'], $_SESSION['last_name'])) {
   $welcomeName = htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']);
}
?>

<!-- ================================================================
     PAGE CONTENT
     ================================================================ -->
<div class="padding_infor_info flf-wrap">

   <!-- Page header -->
   <div class="flf-page-head">
      <h1 class="flf-title">Dashboard</h1>
      <p class="flf-subtitle">Welcome back, <?php echo $welcomeName; ?> &mdash; here is what is happening at Fair Law Firm LTD today.</p>
   </div>

   <!-- ============================================================
        SEAL & LEDGER — signature instrument (§2.7)
        ============================================================ -->
   <?php include __DIR__ . '/include/seal-ledger.php'; ?>

   <!-- ============================================================
        STAT CARDS — 4-column grid
        ============================================================ -->
   <div class="row">
      <div class="col-sm-6 col-xl-3">
         <div class="flf-stat-card">
            <span class="flf-accent"></span>
            <div class="flf-icon-box"><i class="fa fa-users"></i></div>
            <div class="flf-stat-info">
               <h2><?php echo $userCount; ?></h2>
               <h5>Users</h5>
               <p>Active accounts</p>
            </div>
         </div>
      </div>
      <div class="col-sm-6 col-xl-3">
         <div class="flf-stat-card">
            <span class="flf-accent"></span>
            <div class="flf-icon-box"><i class="fa fa-building"></i></div>
            <div class="flf-stat-info">
               <h2><?php echo $totalProperties; ?></h2>
               <h5>Properties</h5>
               <p>Total listings</p>
            </div>
         </div>
      </div>
      <div class="col-sm-6 col-xl-3">
         <div class="flf-stat-card">
            <span class="flf-accent"></span>
            <div class="flf-icon-box"><i class="fa fa-home"></i></div>
            <div class="flf-stat-info">
               <h2><?php echo $rentalProperties; ?></h2>
               <h5>Rentals</h5>
               <p>Listed for rent</p>
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
               <p>Published articles</p>
            </div>
         </div>
      </div>
   </div>

   <!-- ============================================================
        CHARTS — Doughnut + Line, side by side
        ============================================================ -->
   <div class="row mt-4">

      <!-- Property Status Distribution -->
      <div class="col-lg-6 mb-4">
         <div class="flf-panel">
            <div class="flf-panel-head">
               <h3><i class="fa fa-pie-chart"></i> Property Status</h3>
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

      <!-- User Growth — Last 12 Months -->
      <div class="col-lg-6 mb-4">
         <div class="flf-panel">
            <div class="flf-panel-head">
               <h3><i class="fa fa-line-chart"></i> User Growth</h3>
            </div>
            <div class="flf-chart-box">
               <canvas id="flf_growth_chart"></canvas>
            </div>
         </div>
      </div>

   </div>

   <!-- ============================================================
        ACTIVITY + QUICK ACTIONS — side by side
        ============================================================ -->
   <div class="row">

      <!-- Recent Activity -->
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
                        <span class="flf-badge flf-badge-<?php echo strtolower($ru['status']); ?>">
                           <?php echo htmlspecialchars($ru['status']); ?>
                        </span>
                        <span class="flf-time"><?php echo timeAgo($ru['created_at']); ?></span>
                     </div>
                  </li>
                  <?php endforeach; ?>
               <?php endif; ?>
            </ul>
         </div>
      </div>

      <!-- Quick Actions -->
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
<!-- end flf-wrap -->

<!-- ================================================================
     CHART.JS — Property Doughnut + User Growth Line
     ================================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function () {
   'use strict';

   if (typeof Chart === 'undefined') return;

   /* Brand colors */
   var NAVY       = '#01166A';
   var NAVY_DARK  = '#010F47';
   var GOLD       = '#C8A951';
   var GOLD_LIGHT = '#E3CE8F';
   var BLUE_SOFT  = '#8FA0CC';
   var MUTED      = '#7c88ab';
   var FONT_BODY  = "'DM Sans', sans-serif";

   /* Shared tooltip config */
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

   /* ---- Doughnut: Property Status Distribution ---- */
   var propCanvas = document.getElementById('flf_property_chart');
   if (propCanvas) {
      var propLabels = <?php echo json_encode(array_map(function ($r) { return $r['label']; }, $statusRows)); ?>;
      var propValues = <?php echo json_encode(array_map(function ($r) { return (int) $r['cnt']; }, $statusRows)); ?>;
      var palette    = [NAVY, GOLD, BLUE_SOFT, GOLD_LIGHT, NAVY_DARK, '#63719C', '#B49440'];

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
               var ctx  = chart.chart.ctx;
               var meta = chart.getDatasetMeta(0);
               if (!meta.data.length) return;

               var x     = meta.data[0]._model.x;
               var y     = meta.data[0]._model.y;
               var total = chart.data.datasets[0].data.reduce(function (a, b) { return a + b; }, 0);

               ctx.save();
               ctx.textAlign    = 'center';
               ctx.textBaseline = 'middle';
               ctx.font      = "700 34px 'Cormorant Garamond', serif";
               ctx.fillStyle = NAVY;
               ctx.fillText(total, x, y - 10);
               ctx.font      = "500 11px " + FONT_BODY;
               ctx.fillStyle = GOLD;
               ctx.fillText('TOTAL LISTINGS', x, y + 16);
               ctx.restore();
            }
         }]
      });
   }

   /* ---- Line: User Growth — Last 12 Months ---- */
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

<?php require_once 'include/footer.php'; ?>
