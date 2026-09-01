<?php
require_once 'data/propertyMgt/config.php';

try {
    $stmt = $conn->query("SELECT * FROM about_content WHERE status='Active' ORDER BY id DESC LIMIT 1");
    $aboutContent = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $aboutContent = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Learn about Fair Law Firm LTD - our mission, values, and commitment to legal excellence and property management in Rwanda.">
    <?php require_once 'include/header.php'; ?>
    <title><?= __('About Us') ?> - Fair Law Firm LTD</title>
    <style>
        .fl-about-story {
            padding: var(--fl-sp-8) 0;
        }

        .fl-about-story__grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--fl-sp-8);
            align-items: center;
        }

        .fl-about-story__image {
            position: relative;
            border-radius: var(--fl-r-md);
            overflow: hidden;
        }

        .fl-about-story__image img {
            width: 100%;
            height: 480px;
            object-fit: cover;
            display: block;
            border-radius: var(--fl-r-md);
        }

        .fl-about-story__image-accent {
            position: absolute;
            bottom: -16px;
            right: -16px;
            width: 140px;
            height: 140px;
            background: var(--fl-seal-600);
            opacity: 0.12;
            border-radius: var(--fl-r-md);
            z-index: -1;
        }

        .fl-about-story__label {
            font-size: var(--fl-text-sm);
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--fl-chambers-600);
            margin-bottom: var(--fl-sp-3);
            display: flex;
            align-items: center;
            gap: var(--fl-sp-2);
        }

        .fl-about-story__label::before {
            content: '';
            width: 24px;
            height: 2px;
            background: var(--fl-seal-600);
        }

        .fl-about-story__title {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-4xl);
            font-weight: 600;
            color: var(--fl-chambers-900);
            margin-bottom: var(--fl-sp-4);
            line-height: 1.15;
        }

        .fl-about-story__text {
            font-size: var(--fl-text-base);
            color: var(--fl-ink-500);
            line-height: 1.8;
            margin-bottom: var(--fl-sp-4);
        }

        .fl-about-stats {
            padding: var(--fl-sp-8) 0;
            background: var(--fl-chambers-900);
            position: relative;
            overflow: hidden;
        }

        .fl-about-stats::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 40%;
            height: 200%;
            background: radial-gradient(circle, rgba(156,120,24,0.06) 0%, transparent 70%);
        }

        .fl-about-stats__grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: var(--fl-sp-5);
            position: relative;
            z-index: 1;
        }

        .fl-about-stats__item {
            text-align: center;
        }

        .fl-about-stats__value {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-4xl);
            font-weight: 700;
            color: var(--fl-seal-600);
            line-height: 1;
            margin-bottom: var(--fl-space-2);
        }

        .fl-about-stats__label {
            font-size: var(--fl-text-sm);
            color: rgba(255,255,255,0.6);
            font-weight: 500;
        }

        .fl-about-values {
            padding: var(--fl-sp-8) 0;
            background: var(--fl-ink-100);
        }

        .fl-about-values__grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--fl-sp-4);
        }

        .fl-about-values__card {
            padding: var(--fl-sp-5);
            background: var(--fl-surface);
            border: 1px solid var(--fl-ink-100);
            border-radius: var(--fl-r-md);
            transition: box-shadow var(--fl-transition-normal);
        }

        .fl-about-values__card:hover {
            box-shadow: var(--fl-e-2);
        }

        .fl-about-values__icon {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--fl-chambers-100);
            color: var(--fl-chambers-600);
            border-radius: var(--fl-r-md);
            font-size: 1.3rem;
            margin-bottom: var(--fl-sp-4);
        }

        .fl-about-values__title {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-xl);
            font-weight: 600;
            color: var(--fl-chambers-900);
            margin-bottom: var(--fl-sp-2);
        }

        .fl-about-values__text {
            font-size: var(--fl-text-sm);
            color: var(--fl-ink-500);
            line-height: 1.75;
            margin-bottom: 0;
        }

        @media (max-width: 992px) {
            .fl-about-story__grid {
                grid-template-columns: 1fr;
                gap: var(--fl-sp-5);
            }
            .fl-about-stats__grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .fl-about-stats__grid {
                grid-template-columns: 1fr;
                gap: var(--fl-sp-4);
            }
            .fl-about-values__grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

    <!-- Page Header -->
    <section class="fl-page-header">
        <div class="fl-page-header__bg" style="background-image: url(assets/images/backgrounds/bkground_1.jpg);"></div>
        <div class="fl-container">
            <div class="fl-page-header__content">
                <h1 class="fl-page-header__title"><?= __('About Fair Law Firm LTD') ?></h1>
                <nav class="fl-page-header__breadcrumb">
                    <a href="index.php"><?= __('Home') ?></a>
                    <span>/</span>
                    <span><?= __('About Us') ?></span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Our Story -->
    <section id="main-content" class="fl-about-story">
        <div class="fl-container">
            <div class="fl-about-story__grid">
                <div class="fl-about-story__image">
                    <?php if ($aboutContent && !empty($aboutContent['image'])): ?>
                        <img src="data/propertyMgt/aboutImg/<?php echo htmlspecialchars($aboutContent['image']); ?>" alt="<?= __('About Fair Law Firm') ?>">
                    <?php else: ?>
                        <img src="assets/images/about/nyirabyo-1-1.png" alt="<?= __('About Fair Law Firm') ?>">
                    <?php endif; ?>
                    <div class="fl-about-story__image-accent"></div>
                </div>
                <div>
                    <div class="fl-about-story__label"><?= __('Our Story') ?></div>
                    <h2 class="fl-about-story__title"><?= __('A Firm Built on Trust and Expertise') ?></h2>
                    <p class="fl-about-story__text">
                        <?php echo $aboutContent ? htmlspecialchars($aboutContent['description']) : __('Fair Law Firm Ltd, a Rwandan company founded in 2021, provides a full range of legal services and property management solutions. Our expertise ensures clients receive professional support and efficient property management across Rwanda.'); ?>
                    </p>
                    <p class="fl-about-story__text">
                        <?php echo $aboutContent ? htmlspecialchars($aboutContent['more_description']) : __('In the realm of legal services, the firm provides robust representation and assistance in court, ensuring clients have professional support during litigation. Our expertise extends to mediation and conciliation, helping parties to resolve disputes amicably. The firm also facilitates business transactions, ensuring all legal aspects are meticulously handled.'); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="fl-about-stats">
        <div class="fl-container">
            <div class="fl-about-stats__grid">
                <div class="fl-about-stats__item">
                    <div class="fl-about-stats__value"><?php echo $aboutContent ? htmlspecialchars($aboutContent['client']) : '500'; ?>+</div>
                    <div class="fl-about-stats__label"><?= __('Clients Served') ?></div>
                </div>
                <div class="fl-about-stats__item">
                    <div class="fl-about-stats__value"><?php echo $aboutContent ? htmlspecialchars($aboutContent['cases_won']) : '300'; ?></div>
                    <div class="fl-about-stats__label"><?= __('Cases Won') ?></div>
                </div>
                <div class="fl-about-stats__item">
                    <div class="fl-about-stats__value"><?php echo $aboutContent ? htmlspecialchars($aboutContent['achievements']) : '65'; ?>%</div>
                    <div class="fl-about-stats__label"><?= __('Success Rate') ?></div>
                </div>
                <div class="fl-about-stats__item">
                    <div class="fl-about-stats__value"><?php echo $aboutContent ? htmlspecialchars($aboutContent['our_team']) : '3'; ?></div>
                    <div class="fl-about-stats__label"><?= __('Team Members') ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="fl-about-values">
        <div class="fl-container">
            <div class="fl-section-title">
                <div class="fl-section-title__label"><?= __('Why Choose Us') ?></div>
                <h2 class="fl-section-title__heading"><?= __('Our Core Values') ?></h2>
                <p class="fl-section-title__desc"><?= __('The principles that guide our practice and define our commitment to clients.') ?></p>
            </div>
            <div class="fl-about-values__grid">
                <div class="fl-about-values__card">
                    <div class="fl-about-values__icon"><i class="fa fa-balance-scale"></i></div>
                    <h3 class="fl-about-values__title"><?= __('Integrity') ?></h3>
                    <p class="fl-about-values__text"><?= __('We uphold the highest ethical standards in every interaction, ensuring transparency and honesty in all our legal and property management services.') ?></p>
                </div>
                <div class="fl-about-values__card">
                    <div class="fl-about-values__icon"><i class="fa fa-award"></i></div>
                    <h3 class="fl-about-values__title"><?= __('Excellence') ?></h3>
                    <p class="fl-about-values__text"><?= __('We are committed to delivering exceptional results through continuous professional development and meticulous attention to every case.') ?></p>
                </div>
                <div class="fl-about-values__card">
                    <div class="fl-about-values__icon"><i class="fa fa-handshake"></i></div>
                    <h3 class="fl-about-values__title"><?= __('Client-Centered') ?></h3>
                    <p class="fl-about-values__text"><?= __('Our clients are at the heart of everything we do. We tailor our services to meet individual needs and build lasting relationships based on trust.') ?></p>
                </div>
            </div>
        </div>
    </section>

<?php require_once 'include/footer.php'; ?>
