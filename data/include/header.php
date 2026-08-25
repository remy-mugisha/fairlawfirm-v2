<?php
// This MUST be the very first thing in the file - no whitespace before!
session_start();

if(!isset($_SESSION["email"])){
   header("location: index.php");
   exit(); // Always exit after header redirect
}

$flfCurrentPage = basename($_SERVER['PHP_SELF']);

$flfActiveGroups = array(
    'dashboard.php'          => array('dashboard.php'),
    'manage_users.php'       => array('manage_users.php', 'register.php', 'edit_user.php'),
    'display_properties.php' => array('display_properties.php', 'add_property.php', 'edit_property.php', 'manage_property.php', 'property_details.php', 'property_images.php'),
    'display_rental.php'     => array('display_rental.php', 'add_rental_property.php', 'edit_rental.php'),
    'display_blog.php'       => array('display_blog.php', 'add_blog.php', 'edit_blog.php', 'view_blog.php'),
    'display_about.php'      => array('display_about.php', 'add_about.php', 'edit_about.php', 'view_about.php'),
    'profile.php'            => array('profile.php')
);

$flfPageTitles = array(
    'dashboard.php'          => 'Dashboard',
    'manage_users.php'       => 'Manage Users',
    'register.php'           => 'Add User',
    'edit_user.php'          => 'Edit User',
    'display_properties.php' => 'Manage Properties',
    'add_property.php'       => 'Add Property',
    'edit_property.php'      => 'Edit Property',
    'manage_property.php'    => 'Manage Property',
    'property_details.php'   => 'Property Details',
    'property_images.php'    => 'Property Images',
    'display_rental.php'     => 'Rental Houses',
    'add_rental_property.php'=> 'Add Rental Property',
    'edit_rental.php'        => 'Edit Rental Property',
    'display_blog.php'       => 'Blog Posts',
    'add_blog.php'           => 'Add Blog Post',
    'edit_blog.php'          => 'Edit Blog Post',
    'view_blog.php'          => 'View Blog Post',
    'display_about.php'      => 'About Page',
    'add_about.php'          => 'Edit About Content',
    'edit_about.php'         => 'Edit About Content',
    'view_about.php'         => 'About Preview',
    'home_background.php'    => 'Home Backgrounds',
    'edit_background.php'    => 'Edit Background',
    'profile.php'            => 'My Profile'
);

if (isset($flfPageTitles[$flfCurrentPage])) {
    $flfPageTitle = $flfPageTitles[$flfCurrentPage];
} else {
    $flfPageTitle = ucwords(str_replace(array('-', '_'), ' ', pathinfo($flfCurrentPage, PATHINFO_FILENAME)));
}

function flfNavActive($groupPages, $currentPage) {
    return in_array($currentPage, $groupPages, true) ? ' class="active"' : '';
}

$flfPendingBlogs  = null;
$flfPendingUsers  = null;
$flfNotifTotal    = 0;

try {
    require_once __DIR__ . '/../propertyMgt/config.php';
    $stmt = $conn->query("SELECT COUNT(*) FROM blog WHERE status = 'pending'");
    $flfPendingBlogs = (int)$stmt->fetchColumn();
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE status = 'Pending'");
    $flfPendingUsers = (int)$stmt->fetchColumn();
    $flfNotifTotal = $flfPendingBlogs + $flfPendingUsers;
} catch (Throwable $e) {
    $flfPendingBlogs = null;
    $flfPendingUsers = null;
    $flfNotifTotal   = 0;
}

$flfUserName = 'Guest';
if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
    $flfUserName = htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
      <!-- basic -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- mobile metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      <link rel="shortcut icon" href="images/logo/small-logo.jpg" type="image/x-icon">
      <link rel="manifest" href="images/logo/site.webmanifest">

      <!-- site metas -->
      <title><?php echo $flfPageTitle; ?> | Fair Law Firm LTD</title>
      <meta name="keywords" content="">
      <meta name="description" content="">
      <meta name="author" content="">
      <!-- site icon -->
      <link rel="icon" href="images/fevicon.html" type="image/png" />
      <!-- bootstrap css -->
      <link rel="stylesheet" href="css/bootstrap.min.css" />
      <!-- site css -->
      <link rel="stylesheet" href="style.css" />
      <!-- responsive css -->
      <link rel="stylesheet" href="css/responsive.css" />
      <!-- color css -->
      <link rel="stylesheet" href="css/colors.html" />
      <!-- select bootstrap -->
      <link rel="stylesheet" href="css/bootstrap-select.css" />
      <!-- scrollbar css -->
      <link rel="stylesheet" href="css/perfect-scrollbar.css" />
      <!-- custom css -->
      <link rel="stylesheet" href="css/custom.css" />
      <!-- brand fonts -->
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->

      <!-- admin dashboard brand styles -->
      <link rel="stylesheet" href="css/dashboard.css" />
   </head>
   <body class="dashboard dashboard_1">
      <div class="full_container">
         <div class="inner_container">
            <!-- Sidebar  -->
            <nav id="sidebar">
               <div class="flf-brand">
                  <a href="dashboard.php" class="flf-brand-link">
                     <span class="flf-brand-icon"><i class="fa fa-gavel"></i></span>
                     <span class="flf-brand-text">
                        <strong>Fair Law Firm</strong>
                        <small>LTD &middot; Admin Panel</small>
                     </span>
                  </a>
               </div>
               <ul class="flf-nav">
                  <li<?php echo flfNavActive($flfActiveGroups['dashboard.php'], $flfCurrentPage); ?>>
                     <a href="dashboard.php"><i class="fa fa-th-large"></i> <span>Dashboard</span></a>
                  </li>
                  <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                  <li<?php echo flfNavActive($flfActiveGroups['manage_users.php'], $flfCurrentPage); ?>>
                     <a href="manage_users.php"><i class="fa fa-users"></i> <span>Users</span></a>
                  </li>
                  <?php endif; ?>
                  <li<?php echo flfNavActive($flfActiveGroups['display_properties.php'], $flfCurrentPage); ?>>
                     <a href="display_properties.php"><i class="fa fa-building"></i> <span>Properties</span></a>
                  </li>
                  <li<?php echo flfNavActive($flfActiveGroups['display_rental.php'], $flfCurrentPage); ?>>
                     <a href="display_rental.php"><i class="fa fa-home"></i> <span>Rentals</span></a>
                  </li>
                  <li<?php echo flfNavActive($flfActiveGroups['display_blog.php'], $flfCurrentPage); ?>>
                     <a href="display_blog.php"><i class="fa fa-newspaper-o"></i> <span>Blog</span></a>
                  </li>
                  <li<?php echo flfNavActive($flfActiveGroups['display_about.php'], $flfCurrentPage); ?>>
                     <a href="display_about.php"><i class="fa fa-info-circle"></i> <span>About</span></a>
                  </li>
                  <li<?php echo flfNavActive($flfActiveGroups['profile.php'], $flfCurrentPage); ?>>
                     <a href="profile.php"><i class="fa fa-user-circle"></i> <span>Profile</span></a>
                  </li>
                  <li class="flf-nav-logout">
                     <a href="logout.php"><i class="fa fa-sign-out"></i> <span>Logout</span></a>
                  </li>
               </ul>
            </nav>
            <!-- end sidebar -->
            <!-- right content -->
            <div id="content">
               <!-- topbar -->
               <div class="topbar">
                  <div class="flf-topbar-inner">
                     <button type="button" id="sidebarCollapse" class="flf-burger" aria-label="Toggle navigation"><i class="fa fa-bars"></i></button>

                     <h4 class="flf-page-title"><?php echo htmlspecialchars($flfPageTitle); ?></h4>

                     <form class="flf-search" role="search" action="display_properties.php" method="get">
                        <i class="fa fa-search"></i>
                        <input type="text" name="search" placeholder="Search properties..." autocomplete="off">
                     </form>

                     <div class="flf-top-actions">
                        <div class="flf-dd dropdown">
                           <a class="flf-bell" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <i class="fa fa-bell-o"></i>
                              <?php if ($flfNotifTotal > 0): ?>
                                 <span class="flf-notif-count"><?php echo $flfNotifTotal > 9 ? '9+' : $flfNotifTotal; ?></span>
                              <?php endif; ?>
                           </a>
                           <div class="dropdown-menu flf-notif-menu">
                              <div class="flf-notif-head">Notifications</div>
                              <?php if ($flfNotifTotal > 0): ?>
                                 <?php if ($flfPendingBlogs > 0): ?>
                                 <a class="flf-notif-item" href="display_blog.php">
                                    <i class="fa fa-newspaper-o"></i>
                                    <span><b>Blog posts awaiting review</b><small>Pending approval</small></span>
                                    <span class="flf-notif-num"><?php echo $flfPendingBlogs; ?></span>
                                 </a>
                                 <?php endif; ?>
                                 <?php if ($flfPendingUsers > 0): ?>
                                 <a class="flf-notif-item" href="manage_users.php">
                                    <i class="fa fa-users"></i>
                                    <span><b>New users to activate</b><small>Pending accounts</small></span>
                                    <span class="flf-notif-num"><?php echo $flfPendingUsers; ?></span>
                                 </a>
                                 <?php endif; ?>
                              <?php else: ?>
                                 <div class="flf-notif-empty">You are all caught up.</div>
                              <?php endif; ?>
                           </div>
                        </div>

                        <div class="flf-dd dropdown">
                           <a class="flf-user-chip" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <?php if (isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image'])): ?>
                                 <img src="<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" alt="Profile Image" />
                              <?php else: ?>
                                 <img src="images/default-avatar.png" alt="Default Avatar" />
                              <?php endif; ?>
                              <span class="flf-user-name"><?php echo $flfUserName; ?></span>
                              <i class="fa fa-angle-down"></i>
                           </a>
                           <div class="dropdown-menu flf-user-menu">
                              <a class="dropdown-item" href="profile.php"><i class="fa fa-user-circle"></i> My Profile</a>
                              <a class="dropdown-item" href="dashboard.php"><i class="fa fa-th-large"></i> Dashboard</a>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item flf-signout" href="logout.php"><i class="fa fa-sign-out"></i> Log Out</a>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- end topbar -->
