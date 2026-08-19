<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fair Law Firm LTD - Professional property management services including rental management, marketing, tax compliance, and reporting in Rwanda.">
    <?php require_once 'include/header.php'; ?>
    <title><?= __('Property Management Services') ?> - Fair Law Firm LTD</title>
    <style>
        .fl-services-detail {
            padding: var(--fl-space-20) 0;
        }

        .fl-services-detail__grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: var(--fl-space-6);
        }

        .fl-services-detail__cta {
            padding: var(--fl-space-16) 0;
            background: var(--fl-gray-100);
        }

        .fl-services-detail__cta-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--fl-space-8);
            padding: var(--fl-space-10);
            background: var(--fl-white);
            border: 1px solid var(--fl-gray-200);
            border-radius: var(--fl-radius-lg);
        }

        .fl-services-detail__cta-title {
            font-family: var(--fl-font-heading);
            font-size: var(--fl-text-2xl);
            font-weight: 600;
            color: var(--fl-charcoal);
            margin-bottom: var(--fl-space-2);
        }

        .fl-services-detail__cta-text {
            font-size: var(--fl-text-base);
            color: var(--fl-slate);
        }

        @media (max-width: 768px) {
            .fl-services-detail__grid {
                grid-template-columns: 1fr;
            }
            .fl-services-detail__cta-inner {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>

    <!-- Page Header -->
    <section class="fl-page-header">
        <div class="fl-page-header__bg" style="background-image: url(assets/images/backgrounds/bkground_1.jpg);"></div>
        <div class="fl-container">
            <div class="fl-page-header__content">
                <h1 class="fl-page-header__title"><?= __('Property Management Services') ?></h1>
                <nav class="fl-page-header__breadcrumb">
                    <a href="index.php"><?= __('Home') ?></a>
                    <span>/</span>
                    <span><?= __('Property Management') ?></span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Services Grid -->
    <section class="fl-services-detail">
        <div class="fl-container">
            <div class="fl-section-title">
                <div class="fl-section-title__label"><?= __('What We Offer') ?></div>
                <h2 class="fl-section-title__heading"><?= __('Complete Property Management') ?></h2>
                <p class="fl-section-title__desc"><?= __('End-to-end property management solutions designed to maximize your returns while ensuring full legal compliance.') ?></p>
            </div>
            <div class="fl-services-detail__grid">
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
    </section>

    <!-- CTA -->
    <section class="fl-services-detail__cta">
        <div class="fl-container">
            <div class="fl-services-detail__cta-inner">
                <div>
                    <h3 class="fl-services-detail__cta-title"><?= __('Need Property Management?') ?></h3>
                    <p class="fl-services-detail__cta-text"><?= __('Let us handle your property management needs with professionalism and expertise.') ?></p>
                </div>
                <a href="contact.php" class="fl-btn fl-btn--gold fl-btn--lg"><?= __('Get Started') ?></a>
            </div>
        </div>
    </section>

<?php require_once 'include/footer.php'; ?>
