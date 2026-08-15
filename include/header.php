   
    <?php
    require 'Lang/lang.php';
    ?>
    
    <link rel="shortcut icon" href="assets/images/favicons/small-logo.jpg" type="image/x-icon">
    <link rel="manifest" href="assets/images/favicons/site.webmanifest">


    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/vendors/bootstrap/css/bootstrap.min.css">
    <!--  bootstrap-select css plugins -->
    <link rel="stylesheet" href="assets/vendors/bootstrap-select/bootstrap-select.min.css">
    <!--  animate css plugins -->
    <link rel="stylesheet" href="assets/vendors/animate/animate.min.css">
    <!--  fontawesome css plugins -->
    <link rel="stylesheet" href="assets/vendors/fontawesome/css/all.min.css">
    <!--  jquery-ui css plugins -->
    <link rel="stylesheet" href="assets/vendors/jquery-ui/jquery-ui.css">
    <!--  jarallax css plugins -->
    <link rel="stylesheet" href="assets/vendors/jarallax/jarallax.css">
    <!--  magnific-popup css plugins -->
    <link rel="stylesheet" href="assets/vendors/jquery-magnific-popup/jquery.magnific-popup.css">
    <!--  nouislider css plugins -->
    <link rel="stylesheet" href="assets/vendors/nouislider/nouislider.min.css">
    <!--  nouislider css plugins -->
    <link rel="stylesheet" href="assets/vendors/nouislider/nouislider.pips.css">
    <!--  nouislider css plugins -->
    <link rel="stylesheet" href="assets/vendors/firdip-icons/style.css">
    <!--  slider css plugins -->
    <link rel="stylesheet" href="assets/vendors/owl-carousel/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel/css/owl.theme.default.min.css">

    <link rel="stylesheet" href="assets/vendors/slick-carousel/slick.css">
    <link rel="stylesheet" href="assets/vendors/slick-carousel/slick-theme.css">
    <!-- template styles -->
    <link rel="stylesheet" href="assets/css/firdip.css">
</head>

<body>

    <div class="page-wrapper">
        <div class="topbar-one">
            <div class="container-fluid">
                <div class="topbar-one__inner">
                    <ul class="list-unstyled topbar-one__info">
                        <li class="topbar-one__info__item">
                        <i class="fa fa-clock" aria-hidden="true"></i>
                            <span class="topbar-one__info__item__location"><?= __('Open Hours')?>: <?= __('Mon')?> - <?= __('Fri')?> 09:00 - 17:00</span>
                        </li>
                        <li class="topbar-one__info__item">
                            <i class="icon-message topbar-one__info__icon"></i>
                            <a href="mailto:#">fairlawfirmltd@gmail.com</a>
                        </li>
                        <li class="topbar-one__info__item">
                        <i class="icon-call"></i>
                        <span>+250 788 411 095</span>
                        </li>
                    </ul>
                    <div class="topbar-one__right">
                        <div class="topbar-one__social">
                            <!-- <a href="https://facebook.com/"><i class="icon-facebook-f" aria-hidden="true"></i><span class="sr-only">Facebook</span></a> -->
                            <a href="https://x.com/fairlawfirmltd"><i class="icon-x-twitter" aria-hidden="true"></i> <span class="sr-only">Twitter</span></a>
                            <a href="https://www.linkedin.com/in/fair-law-firm-ltd-6154b3317/"><i class="fab fa-linkedin"></i><span class="sr-only">linkedin</span></a>
                            <a href="https://www.instagram.com/fair_law_firm_ltd/"><i class="fab fa-instagram"></i><span class="sr-only">instagram</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <header class="main-header sticky-header sticky-header--normal">
            <div class="container-fluid">
                <div class="main-header__inner">
                    <div class="main-header__logo logo-firdip">
                        <a href="index.php">
                            <img src="assets/images/logo-0-0-0.png" alt="firdip HTML" height="50" width="170">
                        </a>
                    </div>
                    <nav class="main-header__nav main-menu">
                        <ul class="main-menu__list">

                            <li>
                                <a href="index.php"><?= __('Home')?></a>
                            </li>


                            <li>
                                <a href="about_us.php"><?= __('About')?></a>
                            </li>
                            <li class="dropdown">
                                <a><?= __('Service')?></a>
                                <ul class="sub-menu">
                                    <li><a href="legal_services.php"><?= __('Legal Services')?></a></li>
                                    <li><a href="property_service.php"><?= __('Property Management')?></a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a><?= __('Property')?></a>
                                <ul class="sub-menu">
                                    <li><a href="property.php"><?= __('Rental & Sale house')?></a></li>
                                    <li><a href="manage_property.php"><?= __('Manage Properties')?></a></li>
                                </ul>
                            </li>
                            <li class="">
                                <a href="blog.php"><?= __('Blog')?></a>
                            </li>
                            <li>
                                <a href="contact.php"><?= __('Contact')?></a>
                            </li>
                            
                        </ul>
                    </nav>
                    <div class="main-header__right">
                    <a href="?lang=en" class="main-header__right__info__item" >
                          <img src="assets/images/en.png" alt="English" />
                     </a>
                     <a href="?lang=fr" class="main-header__right__info__item">
                          <img src="assets/images/fr.png" alt="French" />
                     </a>
                        <div class="mobile-nav__btn mobile-nav__toggler">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <script>
        function changeLanguage() {
            var lang = document.getElementById('lang').value;
            window.location.href = '?lang=' + lang;
        }
    </script> 
     <?php
    // Include the PHP logic here
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_GET['lang'])) {
        $_SESSION['lang'] = $_GET['lang'];
    }
    ?>