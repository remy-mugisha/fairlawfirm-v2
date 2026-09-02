<?php
require 'Lang/lang.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$currentLang = $_SESSION['lang'] ?? 'en';
?>
<!-- Favicon -->
<link rel="shortcut icon" href="assets/images/favicons/small-logo.jpg" type="image/x-icon">
<link rel="manifest" href="assets/images/favicons/site.webmanifest">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@400;600;700&family=Public+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

<!-- Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

<!-- Design System -->
<link rel="stylesheet" href="assets/css/fl-design-system.css">

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
<link rel="stylesheet" href="assets/vendors/bootstrap/css/bootstrap.min.css">

<!-- Design System Tokens (loaded after all vendor CSS) -->
<link rel="stylesheet" href="assets/css/fairlaw-tokens.css">

<!-- Legacy Template CSS (kept for backward compat on inner pages) -->
<link rel="stylesheet" href="assets/vendors/owl-carousel/css/owl.carousel.min.css">
<link rel="stylesheet" href="assets/vendors/owl-carousel/css/owl.theme.default.min.css">
<link rel="stylesheet" href="assets/vendors/jquery-magnific-popup/jquery.magnific-popup.css">

</head>
<body>

<!-- Skip to content -->
<a href="#main-content" class="fl-skip-link">Skip to content</a>

<!-- Utility Bar -->
<div class="fl-utility-bar">
    <div class="fl-utility-bar__inner">
        <div class="fl-utility-bar__left">
            <div class="fl-utility-bar__item">
                <i class="fa fa-clock"></i>
                <span><?= __('Open Hours') ?>: <?= __('Mon') ?> - <?= __('Fri') ?> 09:00 - 17:00</span>
            </div>
            <div class="fl-utility-bar__divider"></div>
            <div class="fl-utility-bar__item">
                <i class="fa fa-envelope"></i>
                <a href="mailto:fairlawfirmltd@gmail.com">fairlawfirmltd@gmail.com</a>
            </div>
            <div class="fl-utility-bar__divider"></div>
            <div class="fl-utility-bar__item">
                <i class="fa fa-phone"></i>
                <a href="tel:+250788411095">+250 788 411 095</a>
            </div>
        </div>
        <div class="fl-utility-bar__right">
            <div class="fl-utility-bar__item">
                <a href="https://x.com/fairlawfirmltd" target="_blank" rel="noopener"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="https://www.linkedin.com/in/fair-law-firm-ltd-6154b3317/" target="_blank" rel="noopener"><i class="fa-brands fa-linkedin-in"></i></a>
                <a href="https://www.instagram.com/fair_law_firm_ltd/" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Navigation -->
<nav class="fl-nav" id="flNav">
    <div class="fl-nav__inner">
        <!-- Logo -->
        <a href="index.php" class="fl-nav__logo" aria-label="Fair Law Firm LTD Home">
            <img src="assets/images/logo-white-1.png" alt="Fair Law Firm LTD" height="48">
        </a>

        <!-- Desktop Links -->
        <ul class="fl-nav__links">
            <li>
                <a href="index.php" class="fl-nav__link"><?= __('Home') ?></a>
            </li>
            <li>
                <a href="about_us.php" class="fl-nav__link"><?= __('About') ?></a>
            </li>
            <li class="fl-nav__dropdown">
                <a href="#" class="fl-nav__link" onclick="return false;"><?= __('Services') ?></a>
                <ul class="fl-nav__dropdown-menu">
                    <li class="fl-nav__dropdown-item"><a href="legal_services.php"><?= __('Legal Services') ?></a></li>
                    <li class="fl-nav__dropdown-item"><a href="property_service.php"><?= __('Property Management') ?></a></li>
                </ul>
            </li>
            <li class="fl-nav__dropdown">
                <a href="#" class="fl-nav__link" onclick="return false;"><?= __('Property') ?></a>
                <ul class="fl-nav__dropdown-menu">
                    <li class="fl-nav__dropdown-item"><a href="property.php"><?= __('Rental & Sale Properties') ?></a></li>
                    <li class="fl-nav__dropdown-item"><a href="manage_property.php"><?= __('Manage Properties') ?></a></li>
                </ul>
            </li>
            <li>
                <a href="blog.php" class="fl-nav__link"><?= __('Blog') ?></a>
            </li>
            <li>
                <a href="contact.php" class="fl-nav__link"><?= __('Contact') ?></a>
            </li>
        </ul>

        <!-- Right Side: Lang + CTA + Mobile Toggle -->
        <div style="display:flex;align-items:center;gap:12px;">
            <!-- Language Switcher -->
            <div class="fl-lang-switch fl-nav__lang-desktop">
                <a href="?lang=en" class="<?= $currentLang === 'en' ? 'fl-lang-active' : '' ?>" title="English">EN</a>
                <span style="color:var(--fl-gray-300)">|</span>
                <a href="?lang=fr" class="<?= $currentLang === 'fr' ? 'fl-lang-active' : '' ?>" title="Français">FR</a>
            </div>

            <!-- CTA Button -->
            <a href="contact.php" class="fl-btn fl-btn--primary fl-btn--sm fl-nav__cta"><?= __('Book Consultation') ?></a>

            <!-- Mobile Toggle -->
            <button class="fl-nav__toggle" id="flNavToggle" aria-label="Open menu">
                <i class="fa fa-bars"></i>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile Navigation -->
<div class="fl-mobile-nav__overlay" id="flMobileOverlay"></div>
<div class="fl-mobile-nav" id="flMobileNav">
    <div class="fl-mobile-nav__header">
        <a href="index.php">
            <img src="assets/images/logo-white-1.png" alt="Fair Law Firm" height="40">
        </a>
        <button class="fl-mobile-nav__close" id="flMobileClose" aria-label="Close menu">
            <i class="fa fa-times"></i>
        </button>
    </div>

    <ul class="fl-mobile-nav__links">
        <li><a href="index.php"><?= __('Home') ?></a></li>
        <li><a href="about_us.php"><?= __('About') ?></a></li>
        <li><a href="legal_services.php"><?= __('Legal Services') ?></a></li>
        <li><a href="property_service.php"><?= __('Property Management') ?></a></li>
        <li><a href="property.php"><?= __('Rental & Sale Properties') ?></a></li>
        <li><a href="manage_property.php"><?= __('Manage Properties') ?></a></li>
        <li><a href="blog.php"><?= __('Blog') ?></a></li>
        <li><a href="contact.php"><?= __('Contact') ?></a></li>
    </ul>

    <div class="fl-mobile-nav__lang">
        <a href="?lang=en" style="color: <?= $currentLang === 'en' ? 'var(--fl-navy)' : 'var(--fl-gray-500)' ?>; font-weight: <?= $currentLang === 'en' ? '700' : '500' ?>;">EN</a>
        <span style="color:var(--fl-gray-300)">|</span>
        <a href="?lang=fr" style="color: <?= $currentLang === 'fr' ? 'var(--fl-navy)' : 'var(--fl-gray-500)' ?>; font-weight: <?= $currentLang === 'fr' ? '700' : '500' ?>;">FR</a>
    </div>

    <ul class="fl-mobile-nav__contact">
        <li>
            <i class="fa fa-envelope"></i>
            <a href="mailto:fairlawfirmltd@gmail.com">fairlawfirmltd@gmail.com</a>
        </li>
        <li>
            <i class="fa fa-phone"></i>
            <a href="tel:+250788411095">+250 788 411 095</a>
        </li>
        <li>
            <i class="fa fa-clock"></i>
            <span>Mon - Fri 09:00 - 17:00</span>
        </li>
    </ul>

    <div style="padding:16px 24px;">
        <a href="contact.php" class="fl-btn fl-btn--primary" style="width:100%;text-align:center;"><?= __('Book Consultation') ?></a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var nav = document.getElementById('flNav');
    var toggle = document.getElementById('flNavToggle');
    var mobileNav = document.getElementById('flMobileNav');
    var overlay = document.getElementById('flMobileOverlay');
    var closeBtn = document.getElementById('flMobileClose');

    // Sticky nav shadow on scroll
    if (nav) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                nav.style.boxShadow = '0 2px 12px rgba(1,22,106,0.08)';
            } else {
                nav.style.boxShadow = 'none';
            }
        });
    }

    // Active nav link
    var currentPage = window.location.pathname.split('/').pop() || 'index.php';
    var links = document.querySelectorAll('.fl-nav__link');
    links.forEach(function(link) {
        var href = link.getAttribute('href');
        if (href === currentPage || (currentPage === '' && href === 'index.php')) {
            link.classList.add('fl-nav__link--active');
        }
    });

    // Mobile nav open
    if (toggle) {
        toggle.addEventListener('click', function() {
            mobileNav.classList.add('fl-mobile-nav--open');
            overlay.classList.add('fl-mobile-nav__overlay--visible');
            document.body.style.overflow = 'hidden';
        });
    }

    // Mobile nav close
    function closeMobile() {
        mobileNav.classList.remove('fl-mobile-nav--open');
        overlay.classList.remove('fl-mobile-nav__overlay--visible');
        document.body.style.overflow = '';
    }
    if (closeBtn) closeBtn.addEventListener('click', closeMobile);
    if (overlay) overlay.addEventListener('click', closeMobile);

    // Mobile active link
    var mobileLinks = document.querySelectorAll('.fl-mobile-nav__links a');
    mobileLinks.forEach(function(link) {
        var href = link.getAttribute('href');
        if (href === currentPage || (currentPage === '' && href === 'index.php')) {
            link.classList.add('fl-nav__link--active');
        }
    });
});
</script>
