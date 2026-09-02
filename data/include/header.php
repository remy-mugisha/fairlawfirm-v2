<?php
/* ================================================================
   Fair Law Firm LTD - Admin Dashboard Header
   Generates the <head>, sidebar navigation, and top bar.
   Loaded on every admin page; must have NO leading whitespace.
   ================================================================ */
session_start();

if (!isset($_SESSION['email'])) {
   header('Location: index.php');
   exit();
}

/* ----------------------------------------------------------------
   Page detection & active-state helpers
   ---------------------------------------------------------------- */
$flfCurrentPage = basename($_SERVER['PHP_SELF']);

$flfActiveGroups = array(
   'dashboard.php'          => array('dashboard.php'),
   'manage_users.php'       => array('manage_users.php', 'register.php', 'edit_user.php'),
   'display_properties.php' => array('display_properties.php', 'add_property.php', 'edit_property.php', 'manage_property.php', 'property_details.php', 'property_images.php'),
   'display_rental.php'     => array('display_rental.php', 'add_rental_property.php', 'edit_rental.php'),
   'display_blog.php'       => array('display_blog.php', 'add_blog.php', 'edit_blog.php', 'view_blog.php'),
   'display_about.php'      => array('display_about.php', 'add_about.php', 'edit_about.php', 'view_about.php'),
   'profile.php'            => array('profile.php'),
);

$flfPageTitles = array(
   'dashboard.php'           => 'Dashboard',
   'manage_users.php'        => 'Manage Users',
   'register.php'            => 'Add User',
   'edit_user.php'           => 'Edit User',
   'display_properties.php'  => 'Manage Properties',
   'add_property.php'        => 'Add Property',
   'edit_property.php'       => 'Edit Property',
   'manage_property.php'     => 'Manage Property',
   'property_details.php'    => 'Property Details',
   'property_images.php'     => 'Property Images',
   'display_rental.php'      => 'Rental Properties',
   'add_rental_property.php' => 'Add Rental Property',
   'edit_rental.php'         => 'Edit Rental Property',
   'display_blog.php'        => 'Blog Posts',
   'add_blog.php'            => 'Add Blog Post',
   'edit_blog.php'           => 'Edit Blog Post',
   'view_blog.php'           => 'View Blog Post',
   'display_about.php'       => 'About Page',
   'add_about.php'           => 'Edit About Content',
   'edit_about.php'          => 'Edit About Content',
   'view_about.php'          => 'About Preview',
   'home_background.php'     => 'Home Backgrounds',
   'edit_background.php'     => 'Edit Background',
   'profile.php'             => 'My Profile',
);

$flfPageTitle = isset($flfPageTitles[$flfCurrentPage])
   ? $flfPageTitles[$flfCurrentPage]
   : ucwords(str_replace(array('-', '_'), ' ', pathinfo($flfCurrentPage, PATHINFO_FILENAME)));

function flfNavActive($groupPages, $currentPage) {
   return in_array($currentPage, $groupPages, true) ? ' class="active"' : '';
}

/* ----------------------------------------------------------------
   Notification counts (pending blogs + pending users)
   ---------------------------------------------------------------- */
$flfPendingBlogs = null;
$flfPendingUsers = null;
$flfNotifTotal   = 0;

try {
   require_once __DIR__ . '/../propertyMgt/config.php';
   $flfPendingBlogs = (int) $conn->query("SELECT COUNT(*) FROM blog WHERE status = 'pending'")->fetchColumn();
   $flfPendingUsers = (int) $conn->query("SELECT COUNT(*) FROM users WHERE status = 'Pending'")->fetchColumn();
   $flfNotifTotal   = $flfPendingBlogs + $flfPendingUsers;
} catch (Throwable $e) {
   $flfPendingBlogs = null;
   $flfPendingUsers = null;
   $flfNotifTotal   = 0;
}

/* ----------------------------------------------------------------
   User display name
   ---------------------------------------------------------------- */
$flfUserName = 'Guest';
if (isset($_SESSION['first_name'], $_SESSION['last_name'])) {
   $flfUserName = htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']);
}

$flfIsAdmin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <title><?php echo $flfPageTitle; ?> | Fair Law Firm LTD</title>

   <link rel="shortcut icon" href="images/logo/logo_icon.png" type="image/x-icon">

   <!-- Bootstrap 5 -->
   <link rel="stylesheet" href="css/bootstrap5.min.css">
   <link rel="stylesheet" href="css/perfect-scrollbar.css">

   <!-- Design System (single consolidated theme) -->
   <link rel="stylesheet" href="css/theme.css">

   <!-- Brand fonts (loaded once, used everywhere) -->
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body class="dashboard dashboard_1">

<!-- Skip to content -->
<a href="#content" class="fl-skip-link">Skip to content</a>

<div class="full_container">
<div class="inner_container">

   <!-- ============================================================
        SIDEBAR - Navy background, gold accents, nav with icons
        ============================================================ -->
   <nav id="sidebar">

      <!-- Brand lockup -->
      <div class="flf-brand">
         <a href="dashboard.php" class="flf-brand-link">
            <span class="flf-brand-icon"><i class="fa fa-gavel"></i></span>
            <span class="flf-brand-text">
               <strong>Fair Law Firm</strong>
               <small>Admin Panel</small>
            </span>
         </a>
      </div>

      <!-- Navigation links -->
      <ul class="flf-nav">
         <li<?php echo flfNavActive($flfActiveGroups['dashboard.php'], $flfCurrentPage); ?>>
            <a href="dashboard.php">
               <i class="fa fa-th-large"></i>
               <span>Dashboard</span>
            </a>
         </li>

         <?php if ($flfIsAdmin): ?>
         <li<?php echo flfNavActive($flfActiveGroups['manage_users.php'], $flfCurrentPage); ?>>
            <a href="manage_users.php">
               <i class="fa fa-users"></i>
               <span>Users</span>
            </a>
         </li>
         <?php endif; ?>

         <li<?php echo flfNavActive($flfActiveGroups['display_properties.php'], $flfCurrentPage); ?>>
            <a href="display_properties.php">
               <i class="fa fa-building"></i>
               <span>Properties</span>
            </a>
         </li>

         <li<?php echo flfNavActive($flfActiveGroups['display_rental.php'], $flfCurrentPage); ?>>
            <a href="display_rental.php">
               <i class="fa fa-home"></i>
               <span>Rentals</span>
            </a>
         </li>

         <li<?php echo flfNavActive($flfActiveGroups['display_blog.php'], $flfCurrentPage); ?>>
            <a href="display_blog.php">
               <i class="fa fa-newspaper-o"></i>
               <span>Blog</span>
            </a>
         </li>

         <li<?php echo flfNavActive($flfActiveGroups['display_about.php'], $flfCurrentPage); ?>>
            <a href="display_about.php">
               <i class="fa fa-info-circle"></i>
               <span>About</span>
            </a>
         </li>

         <li<?php echo flfNavActive($flfActiveGroups['profile.php'], $flfCurrentPage); ?>>
            <a href="profile.php">
               <i class="fa fa-user-circle"></i>
               <span>Profile</span>
            </a>
         </li>

         <li class="flf-nav-logout">
            <a href="logout.php">
               <i class="fa fa-sign-out"></i>
               <span>Logout</span>
            </a>
         </li>
      </ul>

   </nav>
   <!-- end sidebar -->

   <!-- ============================================================
        CONTENT AREA - topbar + page content
        ============================================================ -->
   <div id="content">

      <!-- Top bar -->
      <div class="topbar">
         <div class="flf-topbar-inner">

            <!-- Hamburger toggle (collapse / expand sidebar) -->
            <button type="button"
                    id="sidebarCollapse"
                    class="flf-burger"
                    aria-label="Toggle navigation">
               <i class="fa fa-bars"></i>
            </button>

            <!-- Page title -->
            <h4 class="flf-page-title"><?php echo htmlspecialchars($flfPageTitle); ?></h4>

            <!-- Search form -->
            <form class="flf-search" role="search" action="display_properties.php" method="get">
               <i class="fa fa-search"></i>
               <input type="text"
                      name="search"
                      placeholder="Search properties..."
                      autocomplete="off">
            </form>

            <!-- Right-side actions: notifications + user -->
            <div class="flf-top-actions">

               <!-- Notification bell -->
               <div class="flf-dd dropdown">
                  <a class="flf-bell"
                     data-bs-toggle="dropdown"
                     aria-haspopup="true"
                     aria-expanded="false">
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
                           <span>
                              <b>Blog posts awaiting review</b>
                              <small>Pending approval</small>
                           </span>
                           <span class="flf-notif-num"><?php echo $flfPendingBlogs; ?></span>
                        </a>
                        <?php endif; ?>

                        <?php if ($flfPendingUsers > 0): ?>
                        <a class="flf-notif-item" href="manage_users.php">
                           <i class="fa fa-users"></i>
                           <span>
                              <b>New users to activate</b>
                              <small>Pending accounts</small>
                           </span>
                           <span class="flf-notif-num"><?php echo $flfPendingUsers; ?></span>
                        </a>
                        <?php endif; ?>
                     <?php else: ?>
                        <div class="flf-notif-empty">You are all caught up.</div>
                     <?php endif; ?>
                  </div>
               </div>

               <!-- User profile chip -->
               <div class="flf-dd dropdown">
                  <a class="flf-user-chip"
                     data-bs-toggle="dropdown"
                     aria-haspopup="true"
                     aria-expanded="false">
                     <?php if (isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" alt="Profile">
                     <?php else: ?>
                        <img src="images/default-avatar.png" alt="Default Avatar">
                     <?php endif; ?>
                     <span class="flf-user-name"><?php echo $flfUserName; ?></span>
                     <i class="fa fa-angle-down"></i>
                  </a>

                  <div class="dropdown-menu flf-user-menu">
                     <a class="dropdown-item" href="profile.php">
                        <i class="fa fa-user-circle"></i> My Profile
                     </a>
                     <a class="dropdown-item" href="dashboard.php">
                        <i class="fa fa-th-large"></i> Dashboard
                     </a>
                     <div class="dropdown-divider"></div>
                     <a class="dropdown-item flf-signout" href="logout.php">
                        <i class="fa fa-sign-out"></i> Log Out
                     </a>
                  </div>
               </div>

            </div>
            <!-- end flf-top-actions -->

         </div>
         <!-- end flf-topbar-inner -->
      </div>
      <!-- end topbar -->
