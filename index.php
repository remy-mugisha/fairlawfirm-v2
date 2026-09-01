<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fair Law Firm LTD - Trusted legal counsel and professional property management services in Rwanda. Founded in 2021.">
    <meta name="author" content="Fair Law Firm LTD">

    <title>Fair Law Firm LTD - Legal & Property Management</title>

    <?php require_once 'include/header.php'; ?>

    <!-- Magnific Popup (needed for video popup on homepage) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">

    <style>
        /* ---- Hero (editorial hero with seal mark) ---- */
        .fl-hero {
            position: relative;
            min-height: 85vh;
            display: flex;
            align-items: center;
            background: var(--fl-chambers-900);
            overflow: hidden;
        }

        .fl-hero__bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0.25;
        }

        .fl-hero__content {
            position: relative;
            z-index: 2;
            max-width: 640px;
        }

        .fl-hero__label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: var(--fl-font-body);
            font-size: var(--fl-text-kicker);
            font-weight: 600;
            letter-spacing: var(--fl-ls-kicker);
            text-transform: uppercase;
            color: var(--fl-seal-500);
            margin-bottom: var(--fl-sp-6);
        }

        .fl-hero__label::before {
            content: '';
            width: 32px;
            height: 2px;
            background: var(--fl-seal-500);
        }

        .fl-hero__title {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-display-1);
            font-weight: 700;
            color: var(--fl-surface);
            line-height: 1.1;
            margin-bottom: var(--fl-sp-6);
        }

        .fl-hero__title em {
            font-style: italic;
            color: var(--fl-seal-400);
        }

        .fl-hero__desc {
            font-size: var(--fl-text-body);
            color: rgba(255,255,255,0.6);
            line-height: var(--fl-lh-body);
            margin-bottom: var(--fl-sp-8);
            max-width: 520px;
        }

        .fl-hero__actions {
            display: flex;
            gap: var(--fl-sp-4);
            flex-wrap: wrap;
        }

        .fl-hero__stats {
            position: relative;
            z-index: 2;
            margin-top: var(--fl-sp-16);
            padding-top: var(--fl-sp-8);
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .fl-hero__stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--fl-sp-8);
        }

        .fl-hero__stat {
            text-align: left;
        }

        .fl-hero__stat-value {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-display-2);
            font-weight: 700;
            color: var(--fl-surface);
            line-height: 1;
            margin-bottom: var(--fl-sp-2);
        }

        .fl-hero__stat-label {
            font-family: var(--fl-font-body);
            font-size: var(--fl-text-body-sm);
            color: rgba(255,255,255,0.45);
            font-weight: 500;
        }

        /* ---- About Section ---- */
        .fl-about-home {
            padding: var(--fl-sp-8) 0;
            background: var(--fl-surface);
        }

        .fl-about-home__grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--fl-sp-8);
            align-items: center;
        }

        .fl-about-home__image {
            position: relative;
            border-radius: var(--fl-r-md);
            overflow: hidden;
        }

        .fl-about-home__image img {
            width: 100%;
            height: 420px;
            object-fit: cover;
            display: block;
            border-radius: var(--fl-r-md);
        }

        .fl-about-home__image-accent {
            position: absolute;
            bottom: -12px;
            right: -12px;
            width: 120px;
            height: 120px;
            background: var(--fl-seal-600);
            opacity: 0.1;
            border-radius: var(--fl-r-md);
            z-index: -1;
        }

        .fl-about-home__label {
            font-size: var(--fl-text-kicker);
            font-weight: 600;
            letter-spacing: var(--fl-ls-kicker);
            text-transform: uppercase;
            color: var(--fl-chambers-600);
            margin-bottom: var(--fl-sp-4);
            display: flex;
            align-items: center;
            gap: var(--fl-sp-3);
        }

        .fl-about-home__label::before {
            content: '';
            width: 24px;
            height: 2px;
            background: var(--fl-seal-500);
        }

        .fl-about-home__title {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-h1);
            font-weight: 600;
            color: var(--fl-chambers-900);
            margin-bottom: var(--fl-sp-5);
            line-height: 1.15;
        }

        .fl-about-home__text {
            font-size: var(--fl-text-body);
            color: var(--fl-ink-600);
            line-height: var(--fl-lh-body);
            margin-bottom: var(--fl-sp-6);
        }

        /* ---- Mission / Vision ---- */
        .fl-mv {
            padding: var(--fl-sp-6) 0 var(--fl-sp-8);
        }

        .fl-mv__grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--fl-sp-5);
        }

        .fl-mv__card {
            padding: var(--fl-sp-6);
            border: var(--fl-border-hairline);
            border-radius: var(--fl-r-md);
            background: var(--fl-surface);
            box-shadow: var(--fl-e-1);
            transition: box-shadow var(--fl-transition-fast);
        }

        .fl-mv__card:hover {
            box-shadow: var(--fl-e-2);
        }

        .fl-mv__card-icon {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--fl-chambers-100);
            color: var(--fl-chambers-600);
            border-radius: var(--fl-r-md);
            font-size: 1.1rem;
            margin-bottom: var(--fl-sp-4);
        }

        .fl-mv__card-title {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-h2);
            font-weight: 600;
            color: var(--fl-chambers-800);
            margin-bottom: var(--fl-sp-3);
        }

        .fl-mv__card-text {
            font-size: var(--fl-text-body-sm);
            color: var(--fl-ink-500);
            line-height: var(--fl-lh-body);
            margin-bottom: 0;
        }

        /* ---- Services Split ---- */
        .fl-services-home {
            padding: var(--fl-sp-8) 0;
            background: var(--fl-ink-50);
        }

        .fl-services-split {
            margin-bottom: var(--fl-sp-8);
        }

        .fl-services-split:last-child {
            margin-bottom: 0;
        }

        .fl-services-split__label {
            display: flex;
            align-items: center;
            gap: var(--fl-sp-3);
            font-family: var(--fl-font-body);
            font-size: var(--fl-text-kicker);
            font-weight: 600;
            letter-spacing: var(--fl-ls-kicker);
            text-transform: uppercase;
            color: var(--fl-chambers-700);
            margin-bottom: var(--fl-sp-5);
        }

        .fl-services-split__label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--fl-ink-100);
        }

        .fl-services-home__grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--fl-sp-5);
        }

        .fl-services-home__more {
            text-align: center;
            margin-top: var(--fl-sp-6);
        }

        /* ---- Featured Properties ---- */
        .fl-properties-home {
            padding: var(--fl-sp-8) 0;
            background: var(--fl-surface);
        }

        .fl-properties-home__grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--fl-sp-5);
        }

        /* ---- Blog ---- */
        .fl-blog-home {
            padding: var(--fl-sp-8) 0;
        }

        .fl-blog-home__grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--fl-sp-5);
        }

        /* ---- CTA ---- */
        .fl-cta {
            padding: var(--fl-sp-8) 0;
            background: var(--fl-chambers-800);
            position: relative;
            overflow: hidden;
        }

        .fl-cta::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 60%;
            height: 200%;
            background: radial-gradient(circle, rgba(156,120,24,0.05) 0%, transparent 70%);
        }

        .fl-cta__inner {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 640px;
            margin: 0 auto;
        }

        .fl-cta__title {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-h1);
            font-weight: 600;
            color: var(--fl-surface);
            margin-bottom: var(--fl-sp-4);
        }

        .fl-cta__text {
            font-size: var(--fl-text-body);
            color: rgba(255,255,255,0.6);
            margin-bottom: var(--fl-sp-8);
            line-height: var(--fl-lh-body);
        }

        /* ---- Section Title (shared) ---- */
        .fl-section-title {
            text-align: center;
            margin-bottom: var(--fl-sp-8);
        }

        .fl-section-title__label {
            font-family: var(--fl-font-body);
            font-size: var(--fl-text-kicker);
            font-weight: 600;
            letter-spacing: var(--fl-ls-kicker);
            text-transform: uppercase;
            color: var(--fl-seal-600);
            margin-bottom: var(--fl-sp-2);
        }

        .fl-section-title__heading {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-display-2);
            font-weight: 700;
            color: var(--fl-chambers-900);
            margin-bottom: var(--fl-sp-3);
        }

        .fl-section-title__desc {
            font-size: var(--fl-text-body);
            color: var(--fl-ink-500);
            max-width: 560px;
            margin: 0 auto;
            line-height: var(--fl-lh-body);
        }

        /* ---- Service Card (shared) ---- */
        .fl-service-card {
            padding: var(--fl-sp-5);
            background: var(--fl-surface);
            border: var(--fl-border-hairline);
            border-radius: var(--fl-r-md);
            box-shadow: var(--fl-e-1);
            transition: box-shadow var(--fl-transition-fast);
        }

        .fl-service-card:hover {
            box-shadow: var(--fl-e-2);
        }

        .fl-service-card__icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--fl-r-md);
            font-size: 1rem;
            margin-bottom: var(--fl-sp-4);
        }

        .fl-service-card__title {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-h2);
            font-weight: 600;
            color: var(--fl-chambers-800);
            margin-bottom: var(--fl-sp-2);
        }

        .fl-service-card__text {
            font-size: var(--fl-text-body-sm);
            color: var(--fl-ink-500);
            line-height: var(--fl-lh-body);
            margin-bottom: 0;
        }

        /* Legal services: chambers icon tiles */
        .fl-services-split:first-child .fl-service-card__icon {
            background: var(--fl-chambers-100);
            color: var(--fl-chambers-600);
            border: 1px solid var(--fl-chambers-200);
        }

        /* Property services: clay icon tiles */
        .fl-services-split:last-child .fl-service-card__icon {
            background: var(--fl-clay-100);
            color: var(--fl-clay-600);
            border: 1px solid var(--fl-clay-100);
        }

        /* ---- Property Card (shared) ---- */
        .fl-property-card {
            background: var(--fl-surface);
            border: var(--fl-border-hairline);
            border-radius: var(--fl-r-md);
            box-shadow: var(--fl-e-1);
            overflow: hidden;
            transition: box-shadow var(--fl-transition-fast);
        }

        .fl-property-card:hover {
            box-shadow: var(--fl-e-2);
        }

        .fl-property-card__image-wrap {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .fl-property-card__image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .fl-property-card__badge {
            position: absolute;
            top: var(--fl-sp-2);
            left: var(--fl-sp-2);
            display: inline-flex;
            align-items: center;
            gap: var(--fl-sp-1);
            padding: var(--fl-sp-1) var(--fl-sp-2);
            font-family: var(--fl-font-body);
            font-size: var(--fl-text-body-sm);
            font-weight: 500;
            border-radius: var(--fl-r-sm);
            background: var(--fl-status-active-bg);
            color: var(--fl-status-active-fg);
            border: 1px solid var(--fl-status-active-border);
        }

        .fl-property-card__badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: var(--fl-r-full);
            background: var(--fl-status-active-fg);
        }

        .fl-property-card__body {
            padding: var(--fl-sp-4);
        }

        .fl-property-card__title {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-h2);
            font-weight: 600;
            margin-bottom: var(--fl-sp-2);
        }

        .fl-property-card__title a {
            color: var(--fl-chambers-800);
            text-decoration: none;
        }

        .fl-property-card__title a:hover {
            color: var(--fl-chambers-600);
        }

        .fl-property-card__price {
            font-family: var(--fl-font-mono);
            font-size: var(--fl-text-data);
            color: var(--fl-chambers-700);
            margin-bottom: var(--fl-sp-3);
        }

        .fl-property-card__price small {
            color: var(--fl-ink-400);
        }

        .fl-property-card__meta {
            display: flex;
            flex-wrap: wrap;
            gap: var(--fl-sp-3);
            margin-bottom: var(--fl-sp-3);
        }

        .fl-property-card__meta-item {
            font-size: var(--fl-text-body-sm);
            color: var(--fl-ink-500);
            display: flex;
            align-items: center;
            gap: var(--fl-sp-1);
        }

        .fl-property-card__meta-item i {
            color: var(--fl-ink-400);
            font-size: 0.75rem;
        }

        .fl-property-card__link {
            font-size: var(--fl-text-body-sm);
            font-weight: 500;
            color: var(--fl-chambers-600);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: var(--fl-sp-1);
        }

        .fl-property-card__link:hover {
            color: var(--fl-chambers-700);
        }

        /* ---- Blog Card (shared) ---- */
        .fl-blog-card {
            background: var(--fl-surface);
            border: var(--fl-border-hairline);
            border-radius: var(--fl-r-md);
            box-shadow: var(--fl-e-1);
            overflow: hidden;
            transition: box-shadow var(--fl-transition-fast);
        }

        .fl-blog-card:hover {
            box-shadow: var(--fl-e-2);
        }

        .fl-blog-card__image-wrap {
            position: relative;
            height: 180px;
            overflow: hidden;
        }

        .fl-blog-card__image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .fl-blog-card__category {
            position: absolute;
            top: var(--fl-sp-2);
            left: var(--fl-sp-2);
            padding: var(--fl-sp-1) var(--fl-sp-2);
            font-family: var(--fl-font-body);
            font-size: var(--fl-text-body-sm);
            font-weight: 500;
            border-radius: var(--fl-r-sm);
            background: var(--fl-chambers-50);
            color: var(--fl-chambers-700);
            border: 1px solid var(--fl-chambers-100);
        }

        .fl-blog-card__body {
            padding: var(--fl-sp-4);
        }

        .fl-blog-card__date {
            font-family: var(--fl-font-mono);
            font-size: var(--fl-text-data);
            color: var(--fl-ink-400);
            margin-bottom: var(--fl-sp-2);
        }

        .fl-blog-card__title {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-h2);
            font-weight: 600;
            margin-bottom: var(--fl-sp-2);
        }

        .fl-blog-card__title a {
            color: var(--fl-chambers-800);
            text-decoration: none;
        }

        .fl-blog-card__title a:hover {
            color: var(--fl-chambers-600);
        }

        .fl-blog-card__excerpt {
            font-size: var(--fl-text-body-sm);
            color: var(--fl-ink-500);
            line-height: var(--fl-lh-body);
            margin-bottom: var(--fl-sp-3);
        }

        .fl-blog-card__link {
            font-size: var(--fl-text-body-sm);
            font-weight: 500;
            color: var(--fl-chambers-600);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: var(--fl-sp-1);
        }

        .fl-blog-card__link:hover {
            color: var(--fl-chambers-700);
        }

        /* ---- Responsive ---- */
        @media (max-width: 992px) {
            .fl-about-home__grid {
                grid-template-columns: 1fr;
                gap: var(--fl-sp-6);
            }
            .fl-properties-home__grid {
                grid-template-columns: 1fr 1fr;
            }
            .fl-blog-home__grid {
                grid-template-columns: 1fr 1fr;
            }
            .fl-hero__stats-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: var(--fl-sp-6);
            }
        }

        @media (max-width: 768px) {
            .fl-hero {
                min-height: 70vh;
            }
            .fl-mv__grid {
                grid-template-columns: 1fr;
            }
            .fl-services-home__grid {
                grid-template-columns: 1fr;
            }
            .fl-properties-home__grid {
                grid-template-columns: 1fr;
            }
            .fl-blog-home__grid {
                grid-template-columns: 1fr;
            }
            .fl-hero__stats-grid {
                grid-template-columns: 1fr;
                gap: var(--fl-sp-5);
            }
            .fl-hero__actions {
                flex-direction: column;
            }
            .fl-hero__actions .fl-btn {
                text-align: center;
            }
            .fl-services-split__label {
                font-size: var(--fl-text-kicker);
            }
        }
    </style>

    <!-- ============ HERO ============ -->
    <section class="fl-hero" id="main-content">
        <div class="fl-hero__bg" style="background-image: url(assets/images/backgrounds/bkground_1.jpg);"></div>
        <div class="fl-container" style="padding-top:120px;padding-bottom:80px;">
            <div class="fl-hero__content">
                <div class="fl-hero__label"><?= __('Trusted Legal Counsel') ?></div>
                <h1 class="fl-hero__title">
                    <?= __('Justice & Property') ?><br>
                    <?= __('Management in') ?> <em><?= __('Rwanda') ?></em>
                </h1>
                <p class="fl-hero__desc">
                    <?= __('Fair Law Firm LTD provides comprehensive legal representation and professional property management solutions. Founded in 2021, we serve individuals and businesses across Rwanda with integrity and expertise.') ?>
                </p>
                <div class="fl-hero__actions">
                    <a href="contact.php" class="fl-btn fl-btn--primary fl-btn--lg"><?= __('Book Consultation') ?></a>
                    <a href="legal_services.php" class="fl-btn fl-btn--secondary fl-btn--lg" style="color:rgba(255,255,255,0.85);border-color:rgba(255,255,255,0.3);"><?= __('Explore Services') ?></a>
                </div>
            </div>
            <!-- Est. 2021 Seal -->
            <?php include 'include/seal-ledger.php'; ?>
            <div class="fl-hero__stats">
                <div class="fl-hero__stats-grid">
                    <div class="fl-hero__stat">
                        <div class="fl-hero__stat-value">2021</div>
                        <div class="fl-hero__stat-label"><?= __('Founded') ?></div>
                    </div>
                    <div class="fl-hero__stat">
                        <div class="fl-hero__stat-value">7+</div>
                        <div class="fl-hero__stat-label"><?= __('Legal Services') ?></div>
                    </div>
                    <div class="fl-hero__stat">
                        <div class="fl-hero__stat-value">7+</div>
                        <div class="fl-hero__stat-label"><?= __('Property Services') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ ABOUT ============ -->
    <?php
    require_once 'data/propertyMgt/config.php';
    try {
        $stmt = $conn->query("SELECT * FROM about_content WHERE status='Active' ORDER BY id DESC LIMIT 1");
        $aboutContent = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $aboutContent = null;
    }
    ?>
    <section class="fl-about-home">
        <div class="fl-container">
            <div class="fl-about-home__grid">
                <div class="fl-about-home__image">
                    <?php if ($aboutContent && !empty($aboutContent['image'])): ?>
                        <img src="data/propertyMgt/aboutImg/<?php echo htmlspecialchars($aboutContent['image']); ?>" alt="<?= __('About Fair Law Firm') ?>">
                    <?php else: ?>
                        <img src="assets/images/about/nyirabyo-1-1.png" alt="<?= __('About Fair Law Firm') ?>">
                    <?php endif; ?>
                    <div class="fl-about-home__image-accent"></div>
                </div>
                <div>
                    <div class="fl-about-home__label"><?= __('About Us') ?></div>
                    <h2 class="fl-about-home__title"><?= __('A Firm Built on Trust and Expertise') ?></h2>
                    <p class="fl-about-home__text">
                        <?php echo $aboutContent ? htmlspecialchars($aboutContent['description']) : __('Fair Law Firm Ltd, a Rwandan company founded in 2021, provides a full range of legal services and property management solutions. Our expertise ensures clients receive professional support and efficient property management across Rwanda.'); ?>
                    </p>
                    <a href="about_us.php" class="fl-btn fl-btn--primary"><?= __('Discover Our Story') ?></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ MISSION & VISION ============ -->
    <section class="fl-mv">
        <div class="fl-container">
            <div class="fl-mv__grid">
                <div class="fl-mv__card">
                    <div class="fl-mv__card-icon"><i class="fa fa-bullseye"></i></div>
                    <h3 class="fl-mv__card-title"><?= __('Our Mission') ?></h3>
                    <p class="fl-mv__card-text"><?= __('For legal services, we provide timely services with professionalism. For property management, we ensure excellent maintenance and maximize profits for our clients.') ?></p>
                </div>
                <div class="fl-mv__card">
                    <div class="fl-mv__card-icon"><i class="fa fa-eye"></i></div>
                    <h3 class="fl-mv__card-title"><?= __('Our Vision') ?></h3>
                    <p class="fl-mv__card-text"><?= __('Our goal is to enable our clients to access professional and trustworthy services on a global scale, setting the standard for legal and property management excellence in Rwanda.') ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ SERVICES ============ -->
    <section class="fl-services-home">
        <div class="fl-container">
            <div class="fl-section-title">
                <div class="fl-section-title__label"><?= __('Our Practice') ?></div>
                <h2 class="fl-section-title__heading"><?= __('Legal & Property Services') ?></h2>
                <p class="fl-section-title__desc"><?= __('Comprehensive solutions for legal representation, advisory, and property management across Rwanda.') ?></p>
            </div>

            <!-- Legal Services -->
            <div class="fl-services-split">
                <div class="fl-services-split__label"><?= __('Legal Services') ?></div>
                <div class="fl-services-home__grid">
                    <div class="fl-service-card">
                        <div class="fl-service-card__icon"><i class="fa fa-balance-scale"></i></div>
                        <h4 class="fl-service-card__title"><?= __('Legal Representation') ?></h4>
                        <p class="fl-service-card__text"><?= __('Representation in penal, civil, commercial, social, and administrative litigations; through consultancy, filing, and representation before courts or administrative entities.') ?></p>
                    </div>
                    <div class="fl-service-card">
                        <div class="fl-service-card__icon"><i class="fa fa-file-signature"></i></div>
                        <h4 class="fl-service-card__title"><?= __('Drafting of Acts & Contracts') ?></h4>
                        <p class="fl-service-card__text"><?= __('We draft specific civil and commercial contracts, without forgetting the memorandums of understanding.') ?></p>
                    </div>
                    <div class="fl-service-card">
                        <div class="fl-service-card__icon"><i class="fa fa-handshake"></i></div>
                        <h4 class="fl-service-card__title"><?= __('Mediation & Conciliation') ?></h4>
                        <p class="fl-service-card__text"><?= __('Alternative dispute resolution through a more interventionist role in bringing parties together and suggesting possible solutions to achieve a settlement.') ?></p>
                    </div>
                    <div class="fl-service-card">
                        <div class="fl-service-card__icon"><i class="fa fa-clipboard-list"></i></div>
                        <h4 class="fl-service-card__title"><?= __('Internal Regulations & Procedures') ?></h4>
                        <p class="fl-service-card__text"><?= __('We help companies and communities to draft internal rules, regulations, and procedure manuals for good management of staff and financials.') ?></p>
                    </div>
                    <div class="fl-service-card">
                        <div class="fl-service-card__icon"><i class="fa fa-exchange-alt"></i></div>
                        <h4 class="fl-service-card__title"><?= __('Business Transaction Facilitation') ?></h4>
                        <p class="fl-service-card__text"><?= __('We facilitate buyer and seller in movable and immovable sales transactions; in winding up and dissolution of the company.') ?></p>
                    </div>
                    <div class="fl-service-card">
                        <div class="fl-service-card__icon"><i class="fa fa-gavel"></i></div>
                        <h4 class="fl-service-card__title"><?= __('Legal Advisory') ?></h4>
                        <p class="fl-service-card__text"><?= __('Professional legal advice in civil and business fields, protecting individual rights and resolving complex disputes.') ?></p>
                    </div>
                    <div class="fl-service-card">
                        <div class="fl-service-card__icon"><i class="fa fa-file-contract"></i></div>
                        <h4 class="fl-service-card__title"><?= __('Movable & Immovable Sales Facilitation') ?></h4>
                        <p class="fl-service-card__text"><?= __('Specialized facilitation services for the sale and transfer of both movable and immovable assets.') ?></p>
                    </div>
                </div>
            </div>

            <!-- Property Management Services -->
            <div class="fl-services-split">
                <div class="fl-services-split__label"><?= __('Property Management Services') ?></div>
                <div class="fl-services-home__grid">
                    <div class="fl-service-card">
                        <div class="fl-service-card__icon"><i class="fa fa-money-bill-wave"></i></div>
                        <h4 class="fl-service-card__title"><?= __('Rent Recovery') ?></h4>
                        <p class="fl-service-card__text"><?= __('Our daily task is collecting the rent with maximum pre-payment. We have effective strategies to make clients respect contracts.') ?></p>
                    </div>
                    <div class="fl-service-card">
                        <div class="fl-service-card__icon"><i class="fa fa-bullhorn"></i></div>
                        <h4 class="fl-service-card__title"><?= __('Marketing & Advisory') ?></h4>
                        <p class="fl-service-card__text"><?= __('We help partners promote their products and services through our social and commercial media to maximize profit.') ?></p>
                    </div>
                    <div class="fl-service-card">
                        <div class="fl-service-card__icon"><i class="fa fa-chart-line"></i></div>
                        <h4 class="fl-service-card__title"><?= __('Maximizing Rental Profits') ?></h4>
                        <p class="fl-service-card__text"><?= __('Throughout marketing and advertising, we find more clients, deliver maximum services, and minimize expenses to maximize profit.') ?></p>
                    </div>
                    <div class="fl-service-card">
                        <div class="fl-service-card__icon"><i class="fa fa-gavel"></i></div>
                        <h4 class="fl-service-card__title"><?= __('Legal & Administrative Compliance') ?></h4>
                        <p class="fl-service-card__text"><?= __('Property management must comply with laws and administrative directives. We ensure full compliance under mandate.') ?></p>
                    </div>
                    <div class="fl-service-card">
                        <div class="fl-service-card__icon"><i class="fa fa-receipt"></i></div>
                        <h4 class="fl-service-card__title"><?= __('Payment of Taxes') ?></h4>
                        <p class="fl-service-card__text"><?= __('Through invoicing, rent collection, and maintenance tracking, we calculate and pay rental taxes on behalf of our clients.') ?></p>
                    </div>
                    <div class="fl-service-card">
                        <div class="fl-service-card__icon"><i class="fa fa-file-alt"></i></div>
                        <h4 class="fl-service-card__title"><?= __('Reporting & Filing') ?></h4>
                        <p class="fl-service-card__text"><?= __('Daily, weekly, and monthly reports for evaluation of performance. Important archives kept in transparency about business.') ?></p>
                    </div>
                    <div class="fl-service-card">
                        <div class="fl-service-card__icon"><i class="fa fa-file-signature"></i></div>
                        <h4 class="fl-service-card__title"><?= __('Rental Contract Representation') ?></h4>
                        <p class="fl-service-card__text"><?= __('We negotiate rental contract terms and represent landlords in the signing and execution of contracts with clients.') ?></p>
                    </div>
                </div>
            </div>

            <div class="fl-services-home__more">
                <a href="legal_services.php" class="fl-btn fl-btn--secondary"><?= __('View All Legal Services') ?> <i class="fa fa-arrow-right" style="margin-left:8px;font-size:0.8em;"></i></a>
                &nbsp;&nbsp;
                <a href="property_service.php" class="fl-btn fl-btn--secondary"><?= __('View All Property Services') ?> <i class="fa fa-arrow-right" style="margin-left:8px;font-size:0.8em;"></i></a>
            </div>
        </div>
    </section>

    <!-- ============ FEATURED PROPERTIES ============ -->
    <?php
    require_once 'data/propertyMgt/config.php';
    try {
        $featuredProps = $conn->prepare("
            SELECT p.*, 
                   (SELECT pi.image_path 
                    FROM property_images pi 
                    WHERE pi.property_id = p.id 
                    ORDER BY pi.is_featured DESC, pi.id ASC 
                    LIMIT 1) as property_image
            FROM properties p
            WHERE p.status = 'Active'
            ORDER BY p.created_at DESC 
            LIMIT 3
        ");
        $featuredProps->execute();
    } catch (PDOException $e) {
        $featuredProps = null;
    }
    ?>
    <?php if ($featuredProps && $featuredProps->rowCount() > 0): ?>
    <section class="fl-properties-home">
        <div class="fl-container">
            <div class="fl-section-title">
                <div class="fl-section-title__label"><?= __('Featured Properties') ?></div>
                <h2 class="fl-section-title__heading"><?= __('Available Properties') ?></h2>
                <p class="fl-section-title__desc"><?= __('Browse our curated selection of properties available for sale or rent across Rwanda.') ?></p>
            </div>
            <div class="fl-properties-home__grid">
                <?php while ($prop = $featuredProps->fetch()): 
                    $imagePath = !empty($prop['property_image']) ? 
                        'data/propertyMgt/rentalImg/' . basename($prop['property_image']) : 
                        'assets/images/default-property.jpg';
                ?>
                <div class="fl-property-card">
                    <div class="fl-property-card__image-wrap">
                        <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($prop['title']) ?>" class="fl-property-card__image">
                        <span class="fl-property-card__badge"><?= htmlspecialchars($prop['property_status']) ?></span>
                    </div>
                    <div class="fl-property-card__body">
                        <h3 class="fl-property-card__title">
                            <a href="property_detail.php?id=<?= $prop['id'] ?>"><?= htmlspecialchars($prop['title']) ?></a>
                        </h3>
                        <div class="fl-property-card__price">
                            <?php
                            $priceVal = $prop['price'];
                            if (is_numeric($priceVal)) {
                                echo number_format((float)$priceVal);
                            } elseif (preg_match('/^(\d[\d\s,\-\.]+)\s*[-–]\s*(\d[\d\s,\-\.]+)$/', trim($priceVal), $m)) {
                                echo number_format((float)str_replace([' ', ','], '', $m[1])) . ' – ' . number_format((float)str_replace([' ', ','], '', $m[2]));
                            } else {
                                echo htmlspecialchars($priceVal);
                            }
                            ?> Rwf
                            <?php if ($prop['property_status'] !== 'For Sale'): ?>
                                <small>/ <?= __('month') ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="fl-property-card__meta">
                            <span class="fl-property-card__meta-item">
                                <i class="fa fa-home"></i> <?= htmlspecialchars($prop['property_type']) ?>
                            </span>
                            <?php if ($prop['property_type'] === 'Commercial Building'): ?>
                                <span class="fl-property-card__meta-item">
                                    <i class="fa fa-layer-group"></i> <?= htmlspecialchars($prop['floor']) ?> <?= __('Floor') ?>
                                </span>
                            <?php else: ?>
                                <span class="fl-property-card__meta-item">
                                    <i class="fa fa-bed"></i> <?= $prop['bedroom'] ?> <?= __('Beds') ?>
                                </span>
                                <span class="fl-property-card__meta-item">
                                    <i class="fa fa-bath"></i> <?= $prop['bathroom'] ?> <?= __('Baths') ?>
                                </span>
                            <?php endif; ?>
                            <span class="fl-property-card__meta-item">
                                <i class="fa fa-ruler-combined"></i> <?= htmlspecialchars($prop['property_size']) ?> sqft
                            </span>
                        </div>
                        <a href="property_detail.php?id=<?= $prop['id'] ?>" class="fl-property-card__link">
                            <?= __('View Details') ?> <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="fl-services-home__more">
                <a href="property.php" class="fl-btn fl-btn--secondary"><?= __('View All Properties') ?> <i class="fa fa-arrow-right" style="margin-left:8px;font-size:0.8em;"></i></a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ============ BLOG ============ -->
    <?php
    $selectAllUsers = $conn->prepare("SELECT description_blog, image, date, id, title, category_blog FROM blog WHERE status = 'active' ORDER BY date DESC LIMIT 3");
    $selectAllUsers->execute();
    if ($selectAllUsers->rowCount() > 0):
    ?>
    <section class="fl-blog-home">
        <div class="fl-container">
            <div class="fl-section-title">
                <div class="fl-section-title__label"><?= __('Latest Insights') ?></div>
                <h2 class="fl-section-title__heading"><?= __('Legal & Property Updates') ?></h2>
            </div>
            <div class="fl-blog-home__grid">
                <?php while ($blog = $selectAllUsers->fetch()): ?>
                <article class="fl-blog-card">
                    <div class="fl-blog-card__image-wrap">
                        <img src="data/propertyMgt/blogImg/<?php echo htmlspecialchars($blog['image']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="fl-blog-card__image">
                        <?php if (!empty($blog['category_blog'])): ?>
                            <span class="fl-blog-card__category"><?php echo htmlspecialchars($blog['category_blog']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="fl-blog-card__body">
                        <div class="fl-blog-card__date"><i class="fa fa-calendar-alt" style="margin-right:6px;font-size:0.75rem;"></i> <?php echo date('M d, Y', strtotime($blog['date'])); ?></div>
                        <h3 class="fl-blog-card__title">
                            <a href="blog_details?id=<?php echo $blog['id']; ?>"><?php echo htmlspecialchars($blog['title']); ?></a>
                        </h3>
                        <p class="fl-blog-card__excerpt"><?php echo htmlspecialchars($blog['description_blog']); ?></p>
                        <a href="blog_details?id=<?php echo $blog['id']; ?>" class="fl-blog-card__link"><?= __('Read More') ?> <i class="fa fa-arrow-right"></i></a>
                    </div>
                </article>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ============ CTA ============ -->
    <section class="fl-cta">
        <div class="fl-container">
            <div class="fl-cta__inner">
                <h2 class="fl-cta__title"><?= __('Need Legal Guidance or Property Assistance?') ?></h2>
                <p class="fl-cta__text"><?= __('Contact Fair Law Firm LTD for professional legal counsel and property management solutions in Rwanda.') ?></p>
                <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
                    <a href="contact.php" class="fl-btn fl-btn--primary fl-btn--lg"><?= __('Contact Fair Law Firm') ?></a>
                    <a href="property.php" class="fl-btn fl-btn--secondary fl-btn--lg" style="color:rgba(255,255,255,0.85);border-color:rgba(255,255,255,0.3);"><?= __('Browse Properties') ?></a>
                </div>
            </div>
        </div>
    </section>

<?php require_once 'include/footer.php'; ?>

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
