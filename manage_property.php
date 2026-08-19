<?php
require_once 'include/header.php';
require_once 'data/propertyMgt/config.php';

$items_per_page = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

try {
    $total_query = $conn->query("SELECT COUNT(*) FROM add_property WHERE status='Active'");
    $total_properties = $total_query->fetchColumn();
    $total_pages = ceil($total_properties / $items_per_page);

    $stmt = $conn->prepare("SELECT * FROM add_property WHERE status='Active' ORDER BY id DESC LIMIT :offset, :items_per_page");
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $properties = [];
    $total_pages = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fair Law Firm LTD - Property management showcase. Browse our managed properties across Rwanda.">
    <title><?= __('Property Management') ?> - Fair Law Firm LTD</title>
    <style>
        .fl-manage-props {
            padding: var(--fl-space-20) 0;
        }

        .fl-manage-props__grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--fl-space-6);
        }

        .fl-manage-props__card {
            border-radius: var(--fl-radius-lg);
            overflow: hidden;
            background: var(--fl-white);
            border: 1px solid var(--fl-gray-200);
            transition: box-shadow var(--fl-transition), transform var(--fl-transition);
        }

        .fl-manage-props__card:hover {
            box-shadow: var(--fl-shadow-lg);
            transform: translateY(-4px);
        }

        .fl-manage-props__card-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }

        .fl-manage-props__card-body {
            padding: var(--fl-space-5) var(--fl-space-6);
        }

        .fl-manage-props__card-location {
            font-size: var(--fl-text-sm);
            color: var(--fl-royal);
            display: flex;
            align-items: center;
            gap: var(--fl-space-2);
            margin-bottom: var(--fl-space-2);
        }

        .fl-manage-props__card-title {
            font-family: var(--fl-font-heading);
            font-size: var(--fl-text-lg);
            font-weight: 600;
            color: var(--fl-charcoal);
            margin-bottom: 0;
        }

        .fl-manage-props__empty {
            text-align: center;
            padding: var(--fl-space-16) 0;
        }

        .fl-manage-props__empty-icon {
            font-size: 3rem;
            color: var(--fl-gray-300);
            margin-bottom: var(--fl-space-4);
        }

        .fl-manage-props__empty-text {
            font-size: var(--fl-text-lg);
            color: var(--fl-slate);
        }

        @media (max-width: 992px) {
            .fl-manage-props__grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .fl-manage-props__grid {
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
                <h1 class="fl-page-header__title"><?= __('Property Management') ?></h1>
                <nav class="fl-page-header__breadcrumb">
                    <a href="index.php"><?= __('Home') ?></a>
                    <span>/</span>
                    <span><?= __('Property Management') ?></span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Properties Grid -->
    <section class="fl-manage-props">
        <div class="fl-container">
            <?php if (!empty($properties)): ?>
                <div class="fl-manage-props__grid">
                    <?php foreach ($properties as $prop): ?>
                    <div class="fl-manage-props__card">
                        <img src="data/propertyMgt/proImg/<?= htmlspecialchars($prop['image']) ?>" alt="<?= htmlspecialchars($prop['location']) ?>" class="fl-manage-props__card-image">
                        <div class="fl-manage-props__card-body">
                            <div class="fl-manage-props__card-location">
                                <i class="fa fa-map-marker-alt"></i> <?= htmlspecialchars($prop['location']) ?>
                            </div>
                            <h3 class="fl-manage-props__card-title"><?= htmlspecialchars($prop['title']) ?></h3>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                <nav aria-label="<?= __('Page navigation') ?>" style="margin-top:var(--fl-space-10);">
                    <ul class="fl-pagination">
                        <?php if ($page > 1): ?>
                            <li><a class="fl-pagination__link" href="?page=<?= $page - 1 ?>">&laquo;</a></li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li><a class="fl-pagination__link <?= $i == $page ? 'fl-pagination__link--active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a></li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <li><a class="fl-pagination__link" href="?page=<?= $page + 1 ?>">&raquo;</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="fl-manage-props__empty">
                    <div class="fl-manage-props__empty-icon"><i class="fa fa-home"></i></div>
                    <p class="fl-manage-props__empty-text"><?= __('No properties available at the moment.') ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php require_once 'include/footer.php'; ?>
