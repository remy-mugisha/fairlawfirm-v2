<?php
require_once 'include/header.php';
require_once 'data/propertyMgt/config.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
if (!in_array($filter, ['all', 'For Rent', 'For Sale'])) {
    $filter = 'all';
}

$propertiesPerPage = 6;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;
$offset = ($currentPage - 1) * $propertiesPerPage;

$whereClause = "WHERE p.status = 'Active'";
$params = [];

if ($filter === 'For Rent') {
    $whereClause .= " AND p.property_status = 'For Rent'";
} elseif ($filter === 'For Sale') {
    $whereClause .= " AND p.property_status = 'For Sale'";
}

$countQuery = $conn->prepare("SELECT COUNT(*) FROM properties p $whereClause");
$countQuery->execute($params);
$totalProperties = $countQuery->fetchColumn();
$totalPages = ceil($totalProperties / $propertiesPerPage);

$selectProperties = $conn->prepare("
    SELECT p.*, 
           (SELECT pi.image_path 
            FROM property_images pi 
            WHERE pi.property_id = p.id 
            ORDER BY pi.is_featured DESC, pi.id ASC 
            LIMIT 1) as property_image
    FROM properties p
    $whereClause
    ORDER BY p.created_at DESC 
    LIMIT :limit OFFSET :offset
");
$selectProperties->bindValue(':limit', $propertiesPerPage, PDO::PARAM_INT);
$selectProperties->bindValue(':offset', $offset, PDO::PARAM_INT);
$selectProperties->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse available properties for sale or rent across Rwanda. Fair Law Firm LTD offers professional property management and real estate services.">
    <title><?= __('Properties') ?> - Fair Law Firm LTD</title>
    <style>
        /* ---- Hero ---- */
        .fl-prop-hero {
            position: relative;
            padding: var(--fl-space-24) 0 var(--fl-space-20);
            background: var(--fl-navy-dark);
            overflow: hidden;
        }

        .fl-prop-hero__bg {
            position: absolute;
            inset: 0;
            background-image: url(assets/images/backgrounds/bkground_1.jpg);
            background-size: cover;
            background-position: center;
            opacity: 0.12;
        }

        .fl-prop-hero__content {
            position: relative;
            z-index: 1;
            max-width: 640px;
        }

        .fl-prop-hero__eyebrow {
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

        .fl-prop-hero__eyebrow::before {
            content: '';
            width: 32px;
            height: 2px;
            background: var(--fl-gold);
        }

        .fl-prop-hero__title {
            font-family: var(--fl-font-heading);
            font-size: clamp(2.5rem, 5vw, 4.25rem);
            font-weight: 700;
            color: var(--fl-white);
            line-height: 1.08;
            margin-bottom: var(--fl-space-6);
        }

        .fl-prop-hero__title em {
            font-style: italic;
            color: var(--fl-gold);
        }

        .fl-prop-hero__desc {
            font-size: var(--fl-text-md);
            color: rgba(255,255,255,0.55);
            line-height: 1.75;
            max-width: 520px;
            margin-bottom: var(--fl-space-8);
        }

        .fl-prop-hero__breadcrumb {
            display: flex;
            align-items: center;
            gap: var(--fl-space-2);
            font-size: var(--fl-text-sm);
        }

        .fl-prop-hero__breadcrumb a {
            color: rgba(255,255,255,0.4);
            text-decoration: none;
            transition: color 0.2s;
        }

        .fl-prop-hero__breadcrumb a:hover {
            color: var(--fl-white);
        }

        .fl-prop-hero__breadcrumb span {
            color: var(--fl-gold);
        }

        /* ---- Filter Bar ---- */
        .fl-prop-filters {
            padding: var(--fl-space-8) 0 0;
        }

        .fl-prop-filters__bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--fl-space-6);
            padding-bottom: var(--fl-space-6);
            border-bottom: 1px solid var(--fl-gray-200);
        }

        .fl-prop-filters__tabs {
            display: flex;
            gap: var(--fl-space-1);
            background: var(--fl-gray-100);
            border-radius: var(--fl-radius-md);
            padding: 4px;
        }

        .fl-prop-filters__tab {
            font-family: var(--fl-font-body);
            font-size: var(--fl-text-sm);
            font-weight: 500;
            color: var(--fl-slate);
            padding: 0.6rem 1.25rem;
            border-radius: var(--fl-radius-sm);
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            background: none;
            cursor: pointer;
        }

        .fl-prop-filters__tab:hover {
            color: var(--fl-charcoal);
        }

        .fl-prop-filters__tab--active {
            background: var(--fl-white);
            color: var(--fl-navy);
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .fl-prop-filters__count {
            font-size: var(--fl-text-sm);
            color: var(--fl-gray-500);
        }

        .fl-prop-filters__count strong {
            color: var(--fl-charcoal);
            font-weight: 600;
        }

        /* ---- Property Grid ---- */
        .fl-prop-grid {
            padding: var(--fl-space-10) 0 var(--fl-space-20);
        }

        .fl-prop-grid__inner {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--fl-space-6);
        }

        /* ---- Premium Card ---- */
        .fl-prop-card {
            background: var(--fl-white);
            border: 1px solid var(--fl-gray-200);
            border-radius: var(--fl-radius-xl);
            overflow: hidden;
            transition: box-shadow 0.35s, transform 0.35s;
            display: flex;
            flex-direction: column;
        }

        .fl-prop-card:hover {
            box-shadow: 0 16px 40px rgba(23, 32, 51, 0.1);
            transform: translateY(-6px);
        }

        .fl-prop-card__image-wrap {
            position: relative;
            overflow: hidden;
            height: 240px;
        }

        .fl-prop-card__image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .fl-prop-card:hover .fl-prop-card__image {
            transform: scale(1.06);
        }

        .fl-prop-card__badge {
            position: absolute;
            top: var(--fl-space-4);
            left: var(--fl-space-4);
            font-family: var(--fl-font-body);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 0.35rem 0.85rem;
            border-radius: var(--fl-radius-sm);
            z-index: 2;
        }

        .fl-prop-card__badge--rent {
            background: var(--fl-navy);
            color: var(--fl-white);
        }

        .fl-prop-card__badge--sale {
            background: var(--fl-gold);
            color: var(--fl-navy-dark);
        }

        .fl-prop-card__body {
            padding: var(--fl-space-5) var(--fl-space-6) var(--fl-space-6);
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .fl-prop-card__location {
            display: flex;
            align-items: center;
            gap: var(--fl-space-2);
            font-size: var(--fl-text-xs);
            color: var(--fl-gray-500);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: var(--fl-space-2);
        }

        .fl-prop-card__location svg {
            width: 14px;
            height: 14px;
            stroke: var(--fl-royal);
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
        }

        .fl-prop-card__title {
            font-family: var(--fl-font-heading);
            font-size: var(--fl-text-xl);
            font-weight: 600;
            color: var(--fl-charcoal);
            line-height: 1.25;
            margin-bottom: var(--fl-space-3);
        }

        .fl-prop-card__title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s;
        }

        .fl-prop-card__title a:hover {
            color: var(--fl-navy);
        }

        .fl-prop-card__divider {
            width: 32px;
            height: 2px;
            background: var(--fl-gold);
            margin-bottom: var(--fl-space-3);
        }

        .fl-prop-card__price {
            font-family: var(--fl-font-heading);
            font-size: var(--fl-text-2xl);
            font-weight: 700;
            color: var(--fl-navy);
            margin-bottom: var(--fl-space-4);
            margin-top: auto;
        }

        .fl-prop-card__price small {
            font-family: var(--fl-font-body);
            font-size: var(--fl-text-sm);
            font-weight: 400;
            color: var(--fl-gray-500);
        }

        .fl-prop-card__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: var(--fl-space-4);
            border-top: 1px solid var(--fl-gray-100);
        }

        .fl-prop-card__meta {
            display: flex;
            gap: var(--fl-space-4);
        }

        .fl-prop-card__meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: var(--fl-text-xs);
            color: var(--fl-gray-500);
        }

        .fl-prop-card__meta-item svg {
            width: 14px;
            height: 14px;
            stroke: var(--fl-royal);
            fill: none;
            stroke-width: 2;
        }

        .fl-prop-card__link {
            font-size: var(--fl-text-xs);
            font-weight: 600;
            color: var(--fl-navy);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s;
        }

        .fl-prop-card__link:hover {
            color: var(--fl-gold);
        }

        .fl-prop-card__link svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            transition: transform 0.2s;
        }

        .fl-prop-card__link:hover svg {
            transform: translateX(3px);
        }

        /* ---- Empty State ---- */
        .fl-prop-empty {
            text-align: center;
            padding: var(--fl-space-20) 0;
            grid-column: 1 / -1;
        }

        .fl-prop-empty__icon {
            width: 72px;
            height: 72px;
            margin: 0 auto var(--fl-space-5);
            background: var(--fl-gray-100);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fl-prop-empty__icon svg {
            width: 32px;
            height: 32px;
            stroke: var(--fl-gray-400);
            fill: none;
            stroke-width: 1.5;
        }

        .fl-prop-empty__title {
            font-family: var(--fl-font-heading);
            font-size: var(--fl-text-2xl);
            color: var(--fl-charcoal);
            margin-bottom: var(--fl-space-2);
        }

        .fl-prop-empty__text {
            font-size: var(--fl-text-base);
            color: var(--fl-slate);
        }

        /* ---- Responsive ---- */
        @media (max-width: 992px) {
            .fl-prop-grid__inner {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .fl-prop-hero {
                padding: var(--fl-space-20) 0 var(--fl-space-16);
            }
            .fl-prop-grid__inner {
                grid-template-columns: 1fr;
            }
            .fl-prop-filters__bar {
                flex-direction: column;
                align-items: flex-start;
            }
            .fl-prop-card__image-wrap {
                height: 220px;
            }
        }
    </style>
</head>

    <!-- Hero -->
    <section class="fl-prop-hero">
        <div class="fl-prop-hero__bg"></div>
        <div class="fl-container">
            <div class="fl-prop-hero__content">
                <div class="fl-prop-hero__eyebrow"><?= __('Real Estate') ?></div>
                <h1 class="fl-prop-hero__title">
                    <?= __('Browse Our') ?> <em><?= __('Properties') ?></em>
                </h1>
                <p class="fl-prop-hero__desc">
                    <?= __('Explore our curated selection of properties available for sale or rent across Rwanda, managed with the professionalism and legal expertise Fair Law Firm is known for.') ?>
                </p>
                <nav class="fl-prop-hero__breadcrumb">
                    <a href="index.php"><?= __('Home') ?></a>
                    <span>/</span>
                    <span><?= __('Properties') ?></span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Filters + Grid -->
    <section class="fl-prop-filters">
        <div class="fl-container">
            <div class="fl-prop-filters__bar">
                <div class="fl-prop-filters__tabs">
                    <a href="?filter=all" class="fl-prop-filters__tab <?= $filter === 'all' ? 'fl-prop-filters__tab--active' : '' ?>"><?= __('All') ?></a>
                    <a href="?filter=For+Rent" class="fl-prop-filters__tab <?= $filter === 'For Rent' ? 'fl-prop-filters__tab--active' : '' ?>"><?= __('For Rent') ?></a>
                    <a href="?filter=For+Sale" class="fl-prop-filters__tab <?= $filter === 'For Sale' ? 'fl-prop-filters__tab--active' : '' ?>"><?= __('For Sale') ?></a>
                </div>
                <div class="fl-prop-filters__count">
                    <strong><?= $totalProperties ?></strong> <?= $totalProperties === 1 ? __('property') : __('properties') ?> <?= __('found') ?>
                </div>
            </div>
        </div>
    </section>

    <section class="fl-prop-grid">
        <div class="fl-container">
            <div class="fl-prop-grid__inner">
                <?php if ($selectProperties->rowCount() > 0): ?>
                    <?php while ($row = $selectProperties->fetch()):
                        $imagePath = !empty($row['property_image']) ? 
                            'data/propertyMgt/rentalImg/' . basename($row['property_image']) : 
                            'assets/images/default-property.jpg';

                        $location = htmlspecialchars($row['street'] ?? '');
                        if (!empty($row['sector'])) $location .= ', ' . htmlspecialchars($row['sector']);
                        if (!empty($row['district'])) $location .= ', ' . htmlspecialchars($row['district']);

                        $badgeClass = $row['property_status'] === 'For Sale' ? 'fl-prop-card__badge--sale' : 'fl-prop-card__badge--rent';
                    ?>
                    <article class="fl-prop-card">
                        <div class="fl-prop-card__image-wrap">
                            <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($row['title']) ?>" class="fl-prop-card__image" loading="lazy">
                            <span class="fl-prop-card__badge <?= $badgeClass ?>"><?= htmlspecialchars($row['property_status']) ?></span>
                        </div>
                        <div class="fl-prop-card__body">
                            <div class="fl-prop-card__location">
                                <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <?= $location ?: htmlspecialchars($row['country'] ?? 'Rwanda') ?>
                            </div>
                            <h3 class="fl-prop-card__title">
                                <a href="property_detail.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a>
                            </h3>
                            <div class="fl-prop-card__divider"></div>
                            <div class="fl-prop-card__price">
                                <?php
                                $priceVal = $row['price'];
                                if (is_numeric($priceVal)) {
                                    echo number_format((float)$priceVal);
                                } elseif (preg_match('/^(\d[\d\s,\-\.]+)\s*[-–]\s*(\d[\d\s,\-\.]+)$/', trim($priceVal), $m)) {
                                    echo number_format((float)str_replace([' ', ','], '', $m[1])) . ' – ' . number_format((float)str_replace([' ', ','], '', $m[2]));
                                } else {
                                    echo htmlspecialchars($priceVal);
                                }
                                ?> Rwf
                                <?php if ($row['property_status'] !== 'For Sale'): ?>
                                    <small>/ <?= __('month') ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="fl-prop-card__footer">
                                <div class="fl-prop-card__meta">
                                    <?php if ($row['property_type'] === 'Commercial Building'): ?>
                                        <span class="fl-prop-card__meta-item">
                                            <svg viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="6" x2="9" y2="6.01"/><line x1="15" y1="6" x2="15" y2="6.01"/><line x1="9" y1="10" x2="9" y2="10.01"/><line x1="15" y1="10" x2="15" y2="10.01"/><line x1="9" y1="14" x2="9" y2="14.01"/><line x1="15" y1="14" x2="15" y2="14.01"/><line x1="9" y1="22" x2="9" y2="18"/><line x1="15" y1="22" x2="15" y2="18"/></svg>
                                            <?= htmlspecialchars($row['floor']) ?> <?= __('Floor') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="fl-prop-card__meta-item">
                                            <svg viewBox="0 0 24 24"><path d="M3 22V9l9-7 9 7v13"/><rect x="9" y="14" width="6" height="8"/></svg>
                                            <?= $row['bedroom'] ?> <?= __('Beds') ?>
                                        </span>
                                        <span class="fl-prop-card__meta-item">
                                            <svg viewBox="0 0 24 24"><path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1z"/><path d="M6 12V5a2 2 0 0 1 2-2h3v2.25"/></svg>
                                            <?= $row['bathroom'] ?> <?= __('Baths') ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="fl-prop-card__meta-item">
                                        <svg viewBox="0 0 24 24"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                                        <?= htmlspecialchars($row['property_size']) ?> sqft
                                    </span>
                                </div>
                                <a href="property_detail.php?id=<?= $row['id'] ?>" class="fl-prop-card__link">
                                    <?= __('Details') ?>
                                    <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="fl-prop-empty">
                        <div class="fl-prop-empty__icon">
                            <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <h3 class="fl-prop-empty__title"><?= __('No Properties Found') ?></h3>
                        <p class="fl-prop-empty__text">
                            <?php if ($filter !== 'all'): ?>
                                <?= __('No properties available for') ?> <?= $filter === 'For Rent' ? __('rent') : __('sale') ?> <?= __('at the moment. Try browsing all properties.') ?>
                            <?php else: ?>
                                <?= __('No properties available at the moment. Please check back later.') ?>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <nav aria-label="<?= __('Page navigation') ?>">
                <ul class="fl-pagination">
                    <?php if ($currentPage > 1): ?>
                        <li><a class="fl-pagination__link" href="?filter=<?= urlencode($filter) ?>&page=<?= $currentPage - 1 ?>">&laquo;</a></li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li><a class="fl-pagination__link <?= $i == $currentPage ? 'fl-pagination__link--active' : '' ?>" href="?filter=<?= urlencode($filter) ?>&page=<?= $i ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                    <?php if ($currentPage < $totalPages): ?>
                        <li><a class="fl-pagination__link" href="?filter=<?= urlencode($filter) ?>&page=<?= $currentPage + 1 ?>">&raquo;</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </section>

<?php require_once 'include/footer.php'; ?>
