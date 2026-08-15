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

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="bracket-web">
    <meta name="description" content="Firdip is beautifully designed Figma template especially for the fire department, fireman, fire prevention, fire fighting, fire station, protection, firefighter and all other fire & safety business and websites.">
    <?php 
    require_once 'include/header.php';
    ?>
</head>
<body>
    <section class="page-header">
        <!-- <div class="page-header__bg" style="background-color: #1a2f24,"></div> -->
        <div class="page-header__bg" style="background-image: url(assets/images/backgrounds/background5k.jpg);"></div>
        <div class="container">
            <h2 class="page-header__title"><?= __('About Fair Law Firm LTD') ?></h2>
            <ul class="firdip-breadcrumb list-unstyled">
                <li><a href="index.php"><?= __('Home') ?></a></li>
                <li><span><?= __('About') ?></span></li>
            </ul>
        </div>
    </section>

    <section class="about-two">
        <div class="container">
            <div class="row gutter-y-30">
                <div class="col-lg-6">
                    <div class="about-two__left">
                        <div class="about-two__top">
                            <p class="about-two__top__text"><?php echo $aboutContent ? htmlspecialchars($aboutContent['more_description']) : __('In the realm of legal services, the firm provides robust representation and assistance in court, ensuring clients have professional support during litigation. Our expertise extends to mediation and conciliation, helping parties to resolve disputes amicably. The firm also facilitates business transactions, ensuring all legal aspects are meticulously handled. Additionally, they draft internal rules and regulations, draft contracts, and offer legal advice across various professional fields, tailoring their services to meet the specific needs of their clients.'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-two__right">
                        <div class="about-two__thumb">
                            <div class="about-two__thumb__item about-two__thumb__item--one wow fadeInRight" data-wow-duration='1500ms' data-wow-delay='300ms'>
                                <?php if ($aboutContent && !empty($aboutContent['image'])): ?>
                                    <img src="data/propertyMgt/aboutImg/<?php echo htmlspecialchars($aboutContent['image']); ?>" alt="About Image" height="400px" width="450px">
                                <?php else: ?>
                                    <img src="assets/images/about/nyirabyo-1-1.png" alt="firdip">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="funfact-one funfact-one--home">
        <div class="funfact-one__bg" style="background-image: url(assets/images/shapes/funfact-bg.png);"></div>
        <div class="container">
            <div class="row gutter-y-30">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="funfact-one__item count-box wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='300ms'>
                        <h2 class="funfact-one__item__title">
                            <span class="count-text" data-stop="<?php echo $aboutContent ? htmlspecialchars($aboutContent['client']) : '500'; ?>" data-speed="1500"></span>
                            <span>+</span>
                        </h2>
                        <p class="funfact-one__item__text"><?= __('Client') ?></p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="funfact-one__item count-box wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='500ms'>
                        <h2 class="funfact-one__item__title">
                            <span class="count-text" data-stop="<?php echo $aboutContent ? htmlspecialchars($aboutContent['cases_won']) : '300'; ?>" data-speed="1500"></span>
                            <span></span>
                        </h2>
                        <p class="funfact-one__item__text"><?= __('Cases won') ?></p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="funfact-one__item count-box wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='700ms'>
                        <h2 class="funfact-one__item__title">
                            <span class="count-text" data-stop="<?php echo $aboutContent ? htmlspecialchars($aboutContent['achievements']) : '65'; ?>" data-speed="1500"></span>
                            <span>%</span>
                        </h2>
                        <p class="funfact-one__item__text"><?= __('Achievement') ?></p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="funfact-one__item count-box wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='900ms'>
                        <h2 class="funfact-one__item__title">
                            <span class="count-text" data-stop="<?php echo $aboutContent ? htmlspecialchars($aboutContent['our_team']) : '3'; ?>" data-speed="1500"></span>
                            <span></span>
                        </h2>
                        <p class="funfact-one__item__text"><?= __('Our team') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
    require_once 'include/footer.php';
    ?>
</body>
</html>