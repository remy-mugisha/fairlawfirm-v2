<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="bracket-web">
    <meta name="description" content="Firdip is beautifully designed Figma template especially for the fire department, fireman, fire prevention, fire fighting, fire station, protection, firefighter and all other fire & safety business and websites.">

    <title>Fair Law Firm LTD</title>

    <!-- Include necessary CSS and JS libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>

    <?php
    require_once 'include/header.php';
    ?>

    <style>
        .cta-one__video {
            position: relative;
            text-align: center;
        }

        .cta-one__video__icon {
            display: inline-block;
            width: 80px;
            height: 80px;
            line-height: 80px;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            color: #000;
            font-size: 24px;
            transition: all 0.3s ease;
        }

        .cta-one__video__icon:hover {
            background-color: rgba(255, 255, 255, 1);
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <!-- Hero Section Start -->
    <section class="main-slider-one">
        <div class="main-slider-one__carousel firdip-owl__carousel owl-carousel" data-owl-options='{
            "loop": true,
            "animateOut": "fadeOut",
            "animateIn": "fadeIn",
            "items": 1,
            "autoplay": true,
            "autoplayTimeout": 7000,
            "smartSpeed": 1000,
            "nav": false,
            "dots": true,
            "margin": 0
        }'>
            <div class="item">
                <div class="main-slider-one__item">
                    <div class="main-slider-one__bg" style="background-image: url(assets/images/backgrounds/bkground_1.jpg);"></div>
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="main-slider-one__content">
                                    <h2 class="main-slider-one__title"><?= __('Property Management & Legal Services') ?></h2>
                                    <div class="main-slider-one__btn"><a href="contact.php" class="firdip-btn main-slider-one__btn__link"><?= __('Contact Us') ?></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="item">
                <div class="main-slider-one__item">
                    <div class="main-slider-one__bg" style="background-image: url(assets/images/backgrounds/bkground_2.jpg);"></div>
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="main-slider-one__content">
                                    <h2 class="main-slider-one__title"><?= __('Property Management & Legal Services') ?></h2>
                                    <div class="main-slider-one__btn"> <a href="contact.php" class="firdip-btn main-slider-one__btn__link"><?= __('Contact Us') ?></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="main-slider-one__item">
                    <div class="main-slider-one__bg" style="background-image: url(assets/images/backgrounds/bkground_3.jpg);"></div>
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="main-slider-one__content">
                                    <h2 class="main-slider-one__title"><?= __('Property Management & Legal Services') ?></h2>
                                    <div class="main-slider-one__btn"> <a href="contact.php" class="firdip-btn main-slider-one__btn__link"><?= __('Contact Us') ?></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- About Section Start -->
    <?php
require_once 'data/propertyMgt/config.php';

try {
    // Fetch only active about content
    $stmt = $conn->query("SELECT * FROM about_content WHERE status='Active' ORDER BY id DESC LIMIT 1");
    $aboutContent = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching about content: " . $e->getMessage();
}
?>
<br>
<section class="about-one">
    <div class="container">
        <div class="row gutter-y-30">
            <div class="col-lg-6">
                <div class="about-one__left">
                    <div class="about-one__thumb wow fadeInLeft" data-wow-duration='1500ms' data-wow-delay='300ms'>
                        <div class="about-one__thumb__item about-one__thumb__item--one">
                            <?php if ($aboutContent && !empty($aboutContent['image'])): ?>
                                <img src="data/propertyMgt/aboutImg/<?php echo htmlspecialchars($aboutContent['image']); ?>" alt="About Image" height="400px" width="450px">
                            <?php else: ?>
                                <img src="assets/images/about/nyirabyo-1-1.png" alt="firdip image">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-one__right" style="padding-top:30px;">
                    <div class="about-one__top">
                        <div class="sec-title  wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='000ms'>
                            <h3 class="sec-title__title"><?= __('About Fair Law Firm LTD') ?></h3>
                        </div>
                        <p><?php echo $aboutContent ? htmlspecialchars($aboutContent['description']) : __('Fair Law Firm Ltd, a Rwandan company founded in 2021, provides a full range of legal services and property management solutions.'); ?></p>
                    </div>
                    <div class="about-one__list">
                        <div class="row gutter-y-30">
                            <div class="col-md-7">
                                <div class="about-one__list__left wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='300ms'>
                                    <div class="about-one__list__btn">
                                        <a href="about_us.php"><button type="submit" name="submit" style="color: white; font-size: 12px;padding: 10px 40px 10px 40px; margin-top:10px;"><?= __('Discover More') ?></button></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    <!-- About Section End -->

    <section class="feature-one">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="feature-one__item__title"><?= __('Mission') ?></h5>
                            <p class="feature-one__item__text"><?= __('For legal services, we provide timely our services in professionalism. For property management, we ensure excellent maintenance and maximize profits.') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="feature-one__item__title"><?= __('Vision') ?></h5>
                            <p class="feature-one__item__text"><?= __('Our goal is to enable our clients to access professional and trustworthy services on a global scale.') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Section Start -->
    <section class="feature-one">
        <div class="service-page__bg" data-jarallax data-speed="0.3" data-imgPosition="50% -100%" style="background-image: url(assets/images/shapes/wallpaper2.jpg);"></div>
        <div class="container">
            <div class="sec-title  text-center wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='000ms'>
                <h3 class="sec-title__title" style="color:white; padding-top:30px;"><?= __('Services') ?> </h3>
            </div>
            <div class="row gutter-y-30" style="padding-bottom: 30px">
                
                <div class="col-lg-4 col-md-4">
                    <div class="feature-one__item wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='500ms'>
                        <div class="feature-one__item__icon">
                        </div>
                        <h4 class="feature-one__item__title"><?= __('legal Representation') ?></h4>
                        <p class="feature-one__item__text"><?= __('In penal, civil, commercial, social and administrative litigations; we go through consultancy, advices, mandate of filing the case and representation or assistance before court or administrative entities.') ?></p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="feature-one__item wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='700ms'>
                        <div class="feature-one__item__icon">
                        </div>
                        <h4 class="feature-one__item__title"><?= __('Legal advises in various professional fields') ?> </h4>
                        <p class="feature-one__item__text"> <?= __('In this world governed by intricate laws and regulations, professional legal advice serves as a guiding light, protecting individual rights, resolving disputes and so on. We provide advice in civil and business field') ?> </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                        <div class="feature-one__item wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='300ms'>
                            <div class="feature-one__item__icon">
                            </div>
                            <h4 class="feature-one__item__title"><?= __('Facilitation in Business Transaction')?></h4>
                            <p class="feature-one__item__text"><?= __('We facilitate buyer and seller in movable immovable sales transactions; in winding up and dissolution of the company.')?></p>
                        </div>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="legal_services.php"><button type="submit" name="submit" style="color: white; font-size: 12px;padding: 10px 40px 10px 40px; margin-top:10px;"><?= __('Discover More Legal Services') ?>&nbsp;&nbsp;<i class="fa fa-arrow-right" aria-hidden="true"></i></button></a>
                </div>
                
                <div class="col-lg-4 col-md-4">
                        <div class="feature-one__item wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='300ms'>
                            <div class="feature-one__item__icon">
                            </div>
                            <h4 class="feature-one__item__title"><?= __('Recovery of Rent')?> </h4>
                            <p class="feature-one__item__text"><?= __('Our daily task is collecting the rent with maximum pre-payment. We have more effective strategies to make clients respect contracts.')?></p>
                        </div>
                    </div>
                <div class="col-lg-4 col-md-4">
                    <div class="feature-one__item wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='300ms'>
                        <div class="feature-one__item__icon">
                        </div>
                        <h4 class="feature-one__item__title"><?= __('MARKETING AND ADVISES') ?></h4>
                        <p class="feature-one__item__text"><?= __('We help our partners to promote their products and services; to find customers and offer them services through our social and commercial media, so that they can maximize the profit.') ?></p><br>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                        <div class="feature-one__item wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='300ms'>
                            <div class="feature-one__item__icon">
                            </div>
                            <h4 class="feature-one__item__title"><?= __('Comply with Law & administrative directives')?></h4>
                            <p class="feature-one__item__text"><?= __('As well as other commercial activity, the property management must comply with laws and administrative directives. And we are aware and able to do it under mandate; as an institution with experience in the legal and commercial field.')?></p>
                        </div>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="property_service.php"><button type="submit" name="submit" style="color: white; font-size: 12px;padding: 10px 40px 10px 40px; margin-top:10px;"><?= __('Discover More Property Services') ?>&nbsp;&nbsp;<i class="fa fa-arrow-right" aria-hidden="true"></i></button></a>
                </div>
            </div>
        </div>
    </section>
    <!--end of feature section-->
    <?php
    require_once 'data/propertyMgt/config.php';

    // Fetch only active blog posts, ordered by date in descending order, and limit to 3 posts
    $selectAllUsers = $conn->prepare("SELECT description_blog, image, date, id, title FROM blog WHERE status = 'active' ORDER BY date DESC LIMIT 3");
    $selectAllUsers->execute();

    if ($selectAllUsers->rowCount() > 0) {
        ?>
        <!-- Blog Section Start -->
        <section class="blog-one blog-one--page">
            <div class="container">
                <div class="sec-title text-center wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='000ms'>
                    <h3 class="sec-title__title"><?= __('Blog') ?></h3>
                </div>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php
                    while ($blog = $selectAllUsers->fetch()) {
                        ?>
                        <div class="col">
                            <div class="card">
                                <img src="data/propertyMgt/blogImg/<?php echo $blog['image']; ?>" class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h6 class="about-two__top__text"><?php echo $blog['date']; ?></h6>
                                    <a href="blog_details?id=<?php echo $blog['id']; ?>">
                                        <h5 class="feature-one__item__title"><?php echo $blog['title']; ?></h5>
                                    </a>
                                    <p class="about-two__top__text"><?php echo $blog["description_blog"]; ?></p>
                                    <a href="blog_details?id=<?php echo $blog['id']; ?>">
                                        <button type="submit" name="submit" style="color: white; font-size: 12px; padding: 10px 40px 10px 40px; margin-top:10px;"><?= __('Read More') ?></button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </section>
        <!-- Blog Section End -->
        <?php
    } else {
        // No blog posts found
        echo "<div class='container'><p>No blog posts available.</p></div>";
    }
    ?>


    <!--Cta Section Start -->
    <section class="cta-one">
        <div class="cta-one__bg jarallax" data-jarallax data-speed="0.3" data-imgPosition="50% -100%" style="background-image: url(assets/images/backgrounds/background5k.jpg);"></div>
        <div class="container">
            <div class="row align-items-center gutter-x-0">
                <div class="col-lg-7">
                    <div class="cta-one__left">
                        <div class="cta-one__inner">
                            <div class="cta-one__content wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='300ms'>
                                <h2 class="cta-one__content__title"><?= __('Talk to us') ?>  </h2>
                                <p class="cta-one__content__text"><?= __('Contact us for help with legal issues and efficient management of property rentals and sales.') ?></p>
                                <a href="contact.php"><button type="submit" name="submit" style="color: white; font-size: 12px;padding: 10px 40px 10px 40px; margin-top:10px;"><?= __('Get in Touch') ?></button></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Cta Section End -->

  
</body>

<?php
require_once 'include/footer.php';
?>

<script>
    $(document).ready(function() {
        $('.video-popup').magnificPopup({
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            fixedContentPos: false
        });
    });
</script>

</html>