<?php
require_once 'include/header.php';
require_once 'data/propertyMgt/config.php';

$property_id = $_GET['id'] ?? null;
if (!$property_id) {
    header("Location: property.php");
    exit();
}

$property_query = $conn->prepare("SELECT * FROM properties WHERE id = ?");
$property_query->execute([$property_id]);
$property = $property_query->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    header("Location: property.php");
    exit();
}

$images_query = $conn->prepare("SELECT * FROM property_images WHERE property_id = ? ORDER BY is_featured DESC");
$images_query->execute([$property_id]);
$property_images = $images_query->fetchAll(PDO::FETCH_ASSOC);

$featured_image = null;
$gallery_images = [];
foreach ($property_images as $image) {
    $imagePath = 'data/propertyMgt/rentalImg/' . basename($image['image_path']);
    if ($image['is_featured'] == 1 || !$featured_image) {
        $featured_image = $imagePath;
    }
    $gallery_images[] = $imagePath;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($property['title']) ?> - Fair Law Firm LTD property listing.">
    <title><?= htmlspecialchars($property['title']) ?> - Fair Law Firm LTD</title>
    <style>
        .fl-prop-detail {
            padding: var(--fl-space-16) 0 var(--fl-space-20);
        }

        .fl-prop-detail__grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: var(--fl-space-10);
            align-items: start;
        }

        .fl-prop-detail__main {}

        .fl-prop-detail__image {
            width: 100%;
            height: 480px;
            object-fit: cover;
            border-radius: var(--fl-radius-lg);
            margin-bottom: var(--fl-space-8);
        }

        .fl-prop-detail__placeholder {
            width: 100%;
            height: 480px;
            background: var(--fl-gray-100);
            border-radius: var(--fl-radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--fl-gray-400);
            font-size: var(--fl-text-lg);
            margin-bottom: var(--fl-space-8);
        }

        .fl-prop-detail__section {
            margin-bottom: var(--fl-space-8);
        }

        .fl-prop-detail__section-title {
            font-family: var(--fl-font-heading);
            font-size: var(--fl-text-xl);
            font-weight: 600;
            color: var(--fl-charcoal);
            margin-bottom: var(--fl-space-5);
            padding-bottom: var(--fl-space-3);
            border-bottom: 2px solid var(--fl-gray-200);
        }

        .fl-prop-detail__desc {
            font-size: var(--fl-text-base);
            color: var(--fl-slate);
            line-height: 1.8;
        }

        .fl-prop-detail__gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: var(--fl-space-4);
        }

        .fl-prop-detail__gallery-img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: var(--fl-radius-md);
            cursor: pointer;
            transition: transform var(--fl-transition);
        }

        .fl-prop-detail__gallery-img:hover {
            transform: scale(1.03);
        }

        .fl-prop-detail__sidebar {
            position: sticky;
            top: 100px;
        }

        .fl-prop-detail__sidebar-card {
            background: var(--fl-white);
            border: 1px solid var(--fl-gray-200);
            border-radius: var(--fl-radius-lg);
            padding: var(--fl-space-8);
            margin-bottom: var(--fl-space-6);
        }

        .fl-prop-detail__sidebar-title {
            font-family: var(--fl-font-heading);
            font-size: var(--fl-text-lg);
            font-weight: 600;
            color: var(--fl-charcoal);
            margin-bottom: var(--fl-space-5);
        }

        .fl-prop-detail__contact-item {
            display: flex;
            align-items: flex-start;
            gap: var(--fl-space-3);
            padding: var(--fl-space-3) 0;
        }

        .fl-prop-detail__contact-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--fl-blue-soft);
            color: var(--fl-navy);
            border-radius: var(--fl-radius-md);
            flex-shrink: 0;
        }

        .fl-prop-detail__contact-text {
            font-size: var(--fl-text-sm);
            color: var(--fl-slate);
            line-height: 1.6;
        }

        .fl-prop-detail__contact-text a {
            color: var(--fl-navy);
            text-decoration: none;
        }

        .fl-prop-detail__contact-text a:hover {
            color: var(--fl-gold);
        }

        @media (max-width: 992px) {
            .fl-prop-detail__grid {
                grid-template-columns: 1fr;
            }
            .fl-prop-detail__sidebar {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .fl-prop-detail__image,
            .fl-prop-detail__placeholder {
                height: 300px;
            }
        }
    </style>
</head>

    <!-- Page Header -->
    <section class="fl-page-header">
        <div class="fl-page-header__bg" style="background-image: url(<?= $featured_image ?: 'assets/images/backgrounds/bkground_1.jpg' ?>);"></div>
        <div class="fl-container">
            <div class="fl-page-header__content">
                <h1 class="fl-page-header__title"><?= htmlspecialchars($property['title']) ?></h1>
                <nav class="fl-page-header__breadcrumb">
                    <a href="index.php"><?= __('Home') ?></a>
                    <span>/</span>
                    <a href="property.php"><?= __('Properties') ?></a>
                    <span>/</span>
                    <span><?= __('Details') ?></span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Property Detail -->
    <section class="fl-prop-detail">
        <div class="fl-container">
            <div class="fl-prop-detail__grid">
                <!-- Main Content -->
                <div class="fl-prop-detail__main">
                    <?php if ($featured_image && file_exists($featured_image)): ?>
                        <img src="<?= htmlspecialchars($featured_image) ?>" alt="<?= htmlspecialchars($property['title']) ?>" class="fl-prop-detail__image">
                    <?php else: ?>
                        <div class="fl-prop-detail__placeholder">
                            <i class="fa fa-image" style="font-size:3rem;margin-right:12px;"></i> <?= __('No Image Available') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Description -->
                    <div class="fl-prop-detail__section">
                        <h3 class="fl-prop-detail__section-title"><?= __('Description') ?></h3>
                        <p class="fl-prop-detail__desc"><?= nl2br(htmlspecialchars($property['description'])) ?></p>
                    </div>

                    <!-- Gallery -->
                    <?php if (count($gallery_images) > 1): ?>
                    <div class="fl-prop-detail__section">
                        <h3 class="fl-prop-detail__section-title"><?= __('Property Gallery') ?></h3>
                        <div class="fl-prop-detail__gallery">
                            <?php foreach (array_slice($gallery_images, 1) as $image): ?>
                                <?php if (file_exists($image)): ?>
                                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= __('Property image') ?>" class="fl-prop-detail__gallery-img">
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Details Table -->
                    <div class="fl-prop-detail__section">
                        <h3 class="fl-prop-detail__section-title"><?= __('Property Details') ?></h3>
                        <div class="fl-table-wrap">
                            <table class="fl-table">
                                <tbody>
                                    <tr>
                                        <th><?= __('Status') ?></th>
                                        <td><?= htmlspecialchars($property['property_status']) ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Type') ?></th>
                                        <td><?= htmlspecialchars($property['property_type']) ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Price') ?></th>
                                        <td><?php
                                        $priceVal = $property['price'];
                                        if (is_numeric($priceVal)) {
                                            echo number_format((float)$priceVal);
                                        } elseif (preg_match('/^(\d[\d\s,\-\.]+)\s*[-–]\s*(\d[\d\s,\-\.]+)$/', trim($priceVal), $m)) {
                                            echo number_format((float)str_replace([' ', ','], '', $m[1])) . ' – ' . number_format((float)str_replace([' ', ','], '', $m[2]));
                                        } else {
                                            echo htmlspecialchars($priceVal);
                                        }
                                        ?> Rwf<?= $property['property_status'] !== 'For Sale' ? ' / ' . __('month') : '' ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Size') ?></th>
                                        <td><?= htmlspecialchars($property['property_size']) ?> sqft</td>
                                    </tr>
                                    <?php if ($property['property_type'] === 'Commercial Building'): ?>
                                        <tr>
                                            <th><?= __('Floor') ?></th>
                                            <td><?= htmlspecialchars($property['floor']) ?></td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <th><?= __('Bedrooms') ?></th>
                                            <td><?= htmlspecialchars($property['bedroom']) ?></td>
                                        </tr>
                                        <tr>
                                            <th><?= __('Bathrooms') ?></th>
                                            <td><?= htmlspecialchars($property['bathroom']) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if ($property['property_status'] !== 'For Sale' && !empty($property['months'])): ?>
                                    <tr>
                                        <th><?= __('Duration') ?></th>
                                        <td><?= htmlspecialchars($property['months']) ?> <?= __('Months') ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="fl-prop-detail__section">
                        <h3 class="fl-prop-detail__section-title"><?= __('Address') ?></h3>
                        <div class="fl-table-wrap">
                            <table class="fl-table">
                                <tbody>
                                    <tr>
                                        <th><?= __('Street') ?></th>
                                        <td><?= htmlspecialchars($property['street']) ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Sector') ?></th>
                                        <td><?= htmlspecialchars($property['sector']) ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('District') ?></th>
                                        <td><?= htmlspecialchars($property['district']) ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('Country') ?></th>
                                        <td><?= htmlspecialchars($property['country']) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="fl-prop-detail__sidebar">
                    <!-- Contact Card -->
                    <div class="fl-prop-detail__sidebar-card">
                        <h4 class="fl-prop-detail__sidebar-title"><?= __('Contact for Booking') ?></h4>
                        <div class="fl-prop-detail__contact-item">
                            <div class="fl-prop-detail__contact-icon"><i class="fa fa-map-marker-alt"></i></div>
                            <div class="fl-prop-detail__contact-text">KG 194 St, Kigali<br>Kimironko Near BPR Branch</div>
                        </div>
                        <div class="fl-prop-detail__contact-item">
                            <div class="fl-prop-detail__contact-icon"><i class="fa fa-phone"></i></div>
                            <div class="fl-prop-detail__contact-text"><a href="https://wa.me/+250788411095">+250 788 411 095</a></div>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <div class="fl-prop-detail__sidebar-card">
                        <h4 class="fl-prop-detail__sidebar-title"><?= __('Book This Property') ?></h4>
                        <form method="POST" action="bookingEmail.php">
                            <input type="hidden" name="property_id" value="<?= htmlspecialchars($property_id) ?>">
                            <div class="fl-form-group">
                                <input type="text" name="name" class="fl-input" placeholder="<?= __('Full name') ?>" required>
                            </div>
                            <div class="fl-form-group">
                                <input type="email" name="email" class="fl-input" placeholder="<?= __('Email') ?>" required>
                            </div>
                            <div class="fl-form-group">
                                <input type="tel" name="phone" class="fl-input" placeholder="<?= __('Phone') ?>" required>
                            </div>
                            <?php if ($property['property_status'] !== 'For Sale'): ?>
                            <div class="fl-form-group">
                                <input type="number" name="months" class="fl-input" min="1" placeholder="<?= __('Number of Months') ?>" required>
                            </div>
                            <?php endif; ?>
                            <div class="fl-form-group">
                                <textarea name="comments" class="fl-textarea" rows="4" placeholder="<?= __('Write a message') ?>"></textarea>
                            </div>
                            <button type="submit" name="submit" class="fl-btn fl-btn--gold" style="width:100%;justify-content:center;"><?= __('Send Inquiry') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php require_once 'include/footer.php'; ?>
