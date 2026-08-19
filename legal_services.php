<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fair Law Firm LTD - Professional legal services including representation, advisory, mediation, contract drafting, and business facilitation in Rwanda.">
    <?php require_once 'include/header.php'; ?>
    <title><?= __('Legal Services') ?> - Fair Law Firm LTD</title>
    <style>
        /* ---- Editorial Hero ---- */
        .fl-legal-hero {
            position: relative;
            padding: var(--fl-space-24) 0 var(--fl-space-20);
            background: var(--fl-navy-dark);
            overflow: hidden;
        }

        .fl-legal-hero__bg {
            position: absolute;
            inset: 0;
            background-image: url(assets/images/backgrounds/bkground_1.jpg);
            background-size: cover;
            background-position: center;
            opacity: 0.12;
        }

        .fl-legal-hero__content {
            position: relative;
            z-index: 1;
            max-width: 680px;
        }

        .fl-legal-hero__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: var(--fl-space-3);
            font-family: var(--fl-font-body);
            font-size: var(--fl-text-sm);
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--fl-gold);
            margin-bottom: var(--fl-space-6);
        }

        .fl-legal-hero__eyebrow::before {
            content: '';
            width: 32px;
            height: 2px;
            background: var(--fl-gold);
        }

        .fl-legal-hero__title {
            font-family: var(--fl-font-heading);
            font-size: clamp(2.5rem, 5vw, 4.25rem);
            font-weight: 700;
            color: var(--fl-white);
            line-height: 1.08;
            margin-bottom: var(--fl-space-6);
        }

        .fl-legal-hero__title em {
            font-style: italic;
            color: var(--fl-gold);
        }

        .fl-legal-hero__desc {
            font-size: var(--fl-text-md);
            color: rgba(255,255,255,0.55);
            line-height: 1.75;
            max-width: 540px;
            margin-bottom: var(--fl-space-8);
        }

        .fl-legal-hero__breadcrumb {
            display: flex;
            align-items: center;
            gap: var(--fl-space-2);
            font-size: var(--fl-text-sm);
        }

        .fl-legal-hero__breadcrumb a {
            color: rgba(255,255,255,0.4);
            text-decoration: none;
            transition: color 0.2s;
        }

        .fl-legal-hero__breadcrumb a:hover {
            color: var(--fl-white);
        }

        .fl-legal-hero__breadcrumb span {
            color: var(--fl-gold);
        }

        /* ---- Editorial Intro ---- */
        .fl-legal-intro {
            padding: var(--fl-space-16) 0;
            border-bottom: 1px solid var(--fl-gray-200);
        }

        .fl-legal-intro__grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--fl-space-12);
            align-items: center;
        }

        .fl-legal-intro__label {
            font-size: var(--fl-text-sm);
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--fl-royal);
            margin-bottom: var(--fl-space-4);
            display: flex;
            align-items: center;
            gap: var(--fl-space-3);
        }

        .fl-legal-intro__label::before {
            content: '';
            width: 24px;
            height: 2px;
            background: var(--fl-gold);
        }

        .fl-legal-intro__heading {
            font-family: var(--fl-font-heading);
            font-size: var(--fl-text-4xl);
            font-weight: 600;
            color: var(--fl-charcoal);
            line-height: 1.12;
            margin-bottom: var(--fl-space-5);
        }

        .fl-legal-intro__text {
            font-size: var(--fl-text-base);
            color: var(--fl-slate);
            line-height: 1.8;
            margin-bottom: var(--fl-space-5);
        }

        .fl-legal-intro__stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--fl-space-6);
            padding-top: var(--fl-space-6);
            border-top: 1px solid var(--fl-gray-200);
        }

        .fl-legal-intro__stat-value {
            font-family: var(--fl-font-heading);
            font-size: var(--fl-text-3xl);
            font-weight: 700;
            color: var(--fl-navy);
            line-height: 1;
            margin-bottom: var(--fl-space-1);
        }

        .fl-legal-intro__stat-label {
            font-size: var(--fl-text-xs);
            color: var(--fl-gray-500);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .fl-legal-intro__visual {
            position: relative;
        }

        .fl-legal-intro__visual-img {
            width: 100%;
            height: 480px;
            object-fit: cover;
            border-radius: var(--fl-radius-lg);
        }

        .fl-legal-intro__visual-accent {
            position: absolute;
            top: -16px;
            left: -16px;
            width: 80px;
            height: 80px;
            border: 2px solid var(--fl-gold);
            opacity: 0.2;
            border-radius: var(--fl-radius-lg);
        }

        /* ---- Service Blocks (editorial) ---- */
        .fl-legal-services {
            padding: var(--fl-space-20) 0;
        }

        .fl-legal-services__section-label {
            font-size: var(--fl-text-sm);
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--fl-navy);
            margin-bottom: var(--fl-space-3);
        }

        .fl-legal-services__section-heading {
            font-family: var(--fl-font-heading);
            font-size: var(--fl-text-4xl);
            font-weight: 600;
            color: var(--fl-charcoal);
            margin-bottom: var(--fl-space-12);
            padding-bottom: var(--fl-space-6);
            border-bottom: 2px solid var(--fl-gray-200);
        }

        .fl-legal-block {
            display: grid;
            grid-template-columns: 80px 1fr;
            gap: var(--fl-space-8);
            padding: var(--fl-space-10) 0;
            border-bottom: 1px solid var(--fl-gray-200);
        }

        .fl-legal-block:last-child {
            border-bottom: none;
        }

        .fl-legal-block--reverse .fl-legal-block__content {
            order: 2;
        }

        .fl-legal-block--reverse .fl-legal-block__icon-col {
            order: 1;
        }

        .fl-legal-block__icon-col {
            display: flex;
            justify-content: center;
            padding-top: var(--fl-space-2);
        }

        .fl-legal-block__icon {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--fl-blue-soft);
            border-radius: var(--fl-radius-lg);
        }

        .fl-legal-block__icon svg {
            width: 28px;
            height: 28px;
            stroke: var(--fl-navy);
            stroke-width: 1.5;
            fill: none;
        }

        .fl-legal-block__content {
            max-width: 720px;
        }

        .fl-legal-block__number {
            font-family: var(--fl-font-heading);
            font-size: var(--fl-text-xs);
            font-weight: 600;
            color: var(--fl-gold);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: var(--fl-space-3);
        }

        .fl-legal-block__title {
            font-family: var(--fl-font-heading);
            font-size: var(--fl-text-2xl);
            font-weight: 600;
            color: var(--fl-charcoal);
            margin-bottom: var(--fl-space-4);
            line-height: 1.2;
        }

        .fl-legal-block__text {
            font-size: var(--fl-text-base);
            color: var(--fl-slate);
            line-height: 1.8;
        }

        .fl-legal-block__tags {
            display: flex;
            flex-wrap: wrap;
            gap: var(--fl-space-2);
            margin-top: var(--fl-space-5);
        }

        .fl-legal-block__tag {
            font-size: var(--fl-text-xs);
            font-weight: 600;
            color: var(--fl-royal);
            background: var(--fl-blue-soft);
            padding: 0.3rem 0.75rem;
            border-radius: var(--fl-radius-sm);
            letter-spacing: 0.02em;
        }

        /* ---- CTA ---- */
        .fl-legal-cta {
            padding: var(--fl-space-20) 0;
            background: var(--fl-navy-dark);
            position: relative;
            overflow: hidden;
        }

        .fl-legal-cta::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -15%;
            width: 50%;
            height: 200%;
            background: radial-gradient(circle, rgba(200,169,81,0.06) 0%, transparent 70%);
        }

        .fl-legal-cta__inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--fl-space-8);
        }

        .fl-legal-cta__title {
            font-family: var(--fl-font-heading);
            font-size: var(--fl-text-3xl);
            font-weight: 600;
            color: var(--fl-white);
            margin-bottom: var(--fl-space-3);
        }

        .fl-legal-cta__text {
            font-size: var(--fl-text-md);
            color: rgba(255,255,255,0.55);
            max-width: 480px;
        }

        /* ---- Responsive ---- */
        @media (max-width: 992px) {
            .fl-legal-intro__grid {
                grid-template-columns: 1fr;
                gap: var(--fl-space-8);
            }
            .fl-legal-intro__visual {
                order: -1;
            }
            .fl-legal-intro__visual-img {
                height: 360px;
            }
        }

        @media (max-width: 768px) {
            .fl-legal-hero {
                padding: var(--fl-space-20) 0 var(--fl-space-16);
            }
            .fl-legal-block {
                grid-template-columns: 1fr;
                gap: var(--fl-space-4);
            }
            .fl-legal-block__icon-col {
                justify-content: flex-start;
            }
            .fl-legal-block--reverse .fl-legal-block__content {
                order: 1;
            }
            .fl-legal-block--reverse .fl-legal-block__icon-col {
                order: 1;
            }
            .fl-legal-intro__stats {
                grid-template-columns: repeat(3, 1fr);
                gap: var(--fl-space-4);
            }
            .fl-legal-cta__inner {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>

    <!-- Editorial Hero -->
    <section class="fl-legal-hero">
        <div class="fl-legal-hero__bg"></div>
        <div class="fl-container">
            <div class="fl-legal-hero__content">
                <div class="fl-legal-hero__eyebrow"><?= __('Our Expertise') ?></div>
                <h1 class="fl-legal-hero__title">
                    <?= __('Legal') ?> <em><?= __('Services') ?></em>
                </h1>
                <p class="fl-legal-hero__desc">
                    <?= __('From courtroom representation to contract drafting, we provide comprehensive legal counsel built on years of experience and unwavering commitment to our clients.') ?>
                </p>
                <nav class="fl-legal-hero__breadcrumb">
                    <a href="index.php"><?= __('Home') ?></a>
                    <span>/</span>
                    <span><?= __('Legal Services') ?></span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Editorial Intro -->
    <section class="fl-legal-intro">
        <div class="fl-container">
            <div class="fl-legal-intro__grid">
                <div>
                    <div class="fl-legal-intro__label"><?= __('Why Fair Law Firm') ?></div>
                    <h2 class="fl-legal-intro__heading"><?= __('Trusted Counsel for Complex Matters') ?></h2>
                    <p class="fl-legal-intro__text">
                        <?= __('Fair Law Firm LTD offers a full spectrum of legal services designed to protect your interests and achieve favorable outcomes. Our team combines deep legal knowledge with practical business understanding to deliver results that matter.') ?>
                    </p>
                    <p class="fl-legal-intro__text">
                        <?= __('Whether you face litigation, need contracts drafted, or require ongoing legal advisory, we approach every engagement with the same level of dedication and professionalism.') ?>
                    </p>
                    <div class="fl-legal-intro__stats">
                        <div>
                            <div class="fl-legal-intro__stat-value">7+</div>
                            <div class="fl-legal-intro__stat-label"><?= __('Service Areas') ?></div>
                        </div>
                        <div>
                            <div class="fl-legal-intro__stat-value">500+</div>
                            <div class="fl-legal-intro__stat-label"><?= __('Clients Served') ?></div>
                        </div>
                        <div>
                            <div class="fl-legal-intro__stat-value">2021</div>
                            <div class="fl-legal-intro__stat-label"><?= __('Established') ?></div>
                        </div>
                    </div>
                </div>
                <div class="fl-legal-intro__visual">
                    <img src="assets/images/about/nyirabyo-1-1.png" alt="<?= __('Fair Law Firm Legal Team') ?>" class="fl-legal-intro__visual-img">
                    <div class="fl-legal-intro__visual-accent"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Editorial Services -->
    <section class="fl-legal-services">
        <div class="fl-container">
            <div class="fl-legal-services__section-label"><?= __('What We Do') ?></div>
            <h2 class="fl-legal-services__section-heading"><?= __('Our Practice Areas') ?></h2>

            <!-- 1. Legal Representation -->
            <div class="fl-legal-block">
                <div class="fl-legal-block__icon-col">
                    <div class="fl-legal-block__icon">
                        <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                    </div>
                </div>
                <div class="fl-legal-block__content">
                    <div class="fl-legal-block__number"><?= __('Service 01') ?></div>
                    <h3 class="fl-legal-block__title"><?= __('Legal Representation') ?></h3>
                    <p class="fl-legal-block__text">
                        <?= __('We provide robust representation in penal, civil, commercial, social, and administrative litigations. From initial consultancy and case filing to full representation before courts and administrative entities, our team ensures your interests are fiercely defended at every stage of proceedings.') ?>
                    </p>
                    <div class="fl-legal-block__tags">
                        <span class="fl-legal-block__tag"><?= __('Criminal Law') ?></span>
                        <span class="fl-legal-block__tag"><?= __('Civil Litigation') ?></span>
                        <span class="fl-legal-block__tag"><?= __('Commercial Disputes') ?></span>
                        <span class="fl-legal-block__tag"><?= __('Administrative Law') ?></span>
                    </div>
                </div>
            </div>

            <!-- 2. Drafting of Acts & Contracts -->
            <div class="fl-legal-block fl-legal-block--reverse">
                <div class="fl-legal-block__icon-col">
                    <div class="fl-legal-block__icon">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                </div>
                <div class="fl-legal-block__content">
                    <div class="fl-legal-block__number"><?= __('Service 02') ?></div>
                    <h3 class="fl-legal-block__title"><?= __('Drafting of Acts & Contracts') ?></h3>
                    <p class="fl-legal-block__text">
                        <?= __('Precision in language is everything in law. We draft specific civil and commercial contracts, memorandums of understanding, and all manner of legal instruments. Every document is crafted to protect your interests while maintaining full legal enforceability.') ?>
                    </p>
                    <div class="fl-legal-block__tags">
                        <span class="fl-legal-block__tag"><?= __('Civil Contracts') ?></span>
                        <span class="fl-legal-block__tag"><?= __('Commercial Agreements') ?></span>
                        <span class="fl-legal-block__tag"><?= __('MOUs') ?></span>
                    </div>
                </div>
            </div>

            <!-- 3. Mediation & Conciliation -->
            <div class="fl-legal-block">
                <div class="fl-legal-block__icon-col">
                    <div class="fl-legal-block__icon">
                        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                </div>
                <div class="fl-legal-block__content">
                    <div class="fl-legal-block__number"><?= __('Service 03') ?></div>
                    <h3 class="fl-legal-block__title"><?= __('Mediation & Conciliation') ?></h3>
                    <p class="fl-legal-block__text">
                        <?= __('Not every dispute needs a courtroom. Our mediation and conciliation services take a more interventionist role in bringing parties together, suggesting practical solutions to help achieve settlements that are fair, efficient, and enduring.') ?>
                    </p>
                    <div class="fl-legal-block__tags">
                        <span class="fl-legal-block__tag"><?= __('Alternative Dispute Resolution') ?></span>
                        <span class="fl-legal-block__tag"><?= __('Settlement Negotiation') ?></span>
                    </div>
                </div>
            </div>

            <!-- 4. Internal Regulations & Procedures -->
            <div class="fl-legal-block fl-legal-block--reverse">
                <div class="fl-legal-block__icon-col">
                    <div class="fl-legal-block__icon">
                        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                </div>
                <div class="fl-legal-block__content">
                    <div class="fl-legal-block__number"><?= __('Service 04') ?></div>
                    <h3 class="fl-legal-block__title"><?= __('Internal Regulations & Procedures') ?></h3>
                    <p class="fl-legal-block__text">
                        <?= __('Strong governance starts with clear rules. We help companies and communities draft internal rules, regulations, and procedure manuals designed for good management of staff, finances, and daily operations.') ?>
                    </p>
                    <div class="fl-legal-block__tags">
                        <span class="fl-legal-block__tag"><?= __('Corporate Governance') ?></span>
                        <span class="fl-legal-block__tag"><?= __('Policy Drafting') ?></span>
                        <span class="fl-legal-block__tag"><?= __('Compliance Manuals') ?></span>
                    </div>
                </div>
            </div>

            <!-- 5. Business Transaction Facilitation -->
            <div class="fl-legal-block">
                <div class="fl-legal-block__icon-col">
                    <div class="fl-legal-block__icon">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    </div>
                </div>
                <div class="fl-legal-block__content">
                    <div class="fl-legal-block__number"><?= __('Service 05') ?></div>
                    <h3 class="fl-legal-block__title"><?= __('Business Transaction Facilitation') ?></h3>
                    <p class="fl-legal-block__text">
                        <?= __('We facilitate buyer and seller in movable and immovable sales transactions, as well as in winding up and dissolution of companies. Our team ensures every transaction is legally sound and protects all parties involved.') ?>
                    </p>
                    <div class="fl-legal-block__tags">
                        <span class="fl-legal-block__tag"><?= __('Sales Transactions') ?></span>
                        <span class="fl-legal-block__tag"><?= __('Company Dissolution') ?></span>
                        <span class="fl-legal-block__tag"><?= __('Due Diligence') ?></span>
                    </div>
                </div>
            </div>

            <!-- 6. Legal Advisory -->
            <div class="fl-legal-block fl-legal-block--reverse">
                <div class="fl-legal-block__icon-col">
                    <div class="fl-legal-block__icon">
                        <svg viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                </div>
                <div class="fl-legal-block__content">
                    <div class="fl-legal-block__number"><?= __('Service 06') ?></div>
                    <h3 class="fl-legal-block__title"><?= __('Legal Advisory') ?></h3>
                    <p class="fl-legal-block__text">
                        <?= __('In a world governed by intricate laws and regulations, professional legal advice serves as a guiding light. We provide advice in civil and business fields, protecting individual rights, resolving disputes, and helping you navigate the legal landscape with confidence.') ?>
                    </p>
                    <div class="fl-legal-block__tags">
                        <span class="fl-legal-block__tag"><?= __('Civil Law') ?></span>
                        <span class="fl-legal-block__tag"><?= __('Business Advisory') ?></span>
                        <span class="fl-legal-block__tag"><?= __('Rights Protection') ?></span>
                    </div>
                </div>
            </div>

            <!-- 7. Movable & Immovable Sales Facilitation -->
            <div class="fl-legal-block">
                <div class="fl-legal-block__icon-col">
                    <div class="fl-legal-block__icon">
                        <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    </div>
                </div>
                <div class="fl-legal-block__content">
                    <div class="fl-legal-block__number"><?= __('Service 07') ?></div>
                    <h3 class="fl-legal-block__title"><?= __('Movable & Immovable Sales Facilitation') ?></h3>
                    <p class="fl-legal-block__text">
                        <?= __('Specialized facilitation services for the sale and transfer of both movable and immovable assets. We manage the legal complexities so you can focus on the transaction itself, ensuring compliance and protecting your interests throughout.') ?>
                    </p>
                    <div class="fl-legal-block__tags">
                        <span class="fl-legal-block__tag"><?= __('Property Sales') ?></span>
                        <span class="fl-legal-block__tag"><?= __('Asset Transfer') ?></span>
                        <span class="fl-legal-block__tag"><?= __('Legal Compliance') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="fl-legal-cta">
        <div class="fl-container">
            <div class="fl-legal-cta__inner">
                <div>
                    <h2 class="fl-legal-cta__title"><?= __('Ready to Discuss Your Legal Needs?') ?></h2>
                    <p class="fl-legal-cta__text"><?= __('Our experienced legal team is prepared to help you navigate even the most complex legal challenges.') ?></p>
                </div>
                <a href="contact.php" class="fl-btn fl-btn--gold fl-btn--lg"><?= __('Book Consultation') ?></a>
            </div>
        </div>
    </section>

<?php require_once 'include/footer.php'; ?>
