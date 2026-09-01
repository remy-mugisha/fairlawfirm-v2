<?php
require_once 'include/header.php';
require_once 'data/propertyMgt/config.php';

$itemsPerPage = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

$totalPosts = $conn->query("SELECT COUNT(*) FROM blog WHERE status = 'active'")->fetchColumn();
$totalPages = ceil($totalPosts / $itemsPerPage);

$selectAllUsers = $conn->prepare("SELECT * FROM blog WHERE status = 'active' ORDER BY date DESC LIMIT :limit OFFSET :offset");
$selectAllUsers->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$selectAllUsers->bindValue(':offset', $offset, PDO::PARAM_INT);
$selectAllUsers->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fair Law Firm LTD - Legal and property management insights, news, and updates from Rwanda.">
    <title><?= __('Blog') ?> - Fair Law Firm LTD</title>
    <style>
        .fl-blog-page {
            padding: var(--fl-sp-8) 0;
        }

        .fl-blog-page__grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--fl-sp-4);
        }

        .fl-blog-page__empty {
            text-align: center;
            padding: var(--fl-sp-8) 0;
            grid-column: 1 / -1;
        }

        .fl-blog-page__empty-icon {
            font-size: 3rem;
            color: var(--fl-ink-200);
            margin-bottom: var(--fl-sp-3);
        }

        .fl-blog-page__empty-text {
            font-size: var(--fl-body-lg);
            color: var(--fl-ink-500);
        }

        @media (max-width: 992px) {
            .fl-blog-page__grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .fl-blog-page__grid {
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
                <h1 class="fl-page-header__title"><?= __('Blog') ?></h1>
                <nav class="fl-page-header__breadcrumb">
                    <a href="index.php"><?= __('Home') ?></a>
                    <span>/</span>
                    <span><?= __('Blog') ?></span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Blog Grid -->
    <section class="fl-blog-page">
        <div class="fl-container">
            <div class="fl-blog-page__grid">
                <?php if ($selectAllUsers->rowCount() > 0): ?>
                    <?php while ($blog = $selectAllUsers->fetch()): ?>
                    <article class="fl-blog-card">
                        <div class="fl-blog-card__image-wrap">
                            <?php if (!empty($blog['image']) && file_exists("data/propertyMgt/blogImg/" . $blog['image'])): ?>
                                <img src="data/propertyMgt/blogImg/<?= htmlspecialchars($blog['image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="fl-blog-card__image">
                            <?php else: ?>
                                <img src="assets/images/placeholder.jpg" alt="<?= htmlspecialchars($blog['title']) ?>" class="fl-blog-card__image">
                            <?php endif; ?>
                            <?php if (!empty($blog['category_blog'])): ?>
                                <span class="fl-blog-card__category"><?= htmlspecialchars($blog['category_blog']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="fl-blog-card__body">
                            <div class="fl-blog-card__date"><i class="fa fa-calendar-alt" style="margin-right:6px;font-size:0.75rem;"></i> <?= date('M d, Y', strtotime($blog['date'])) ?></div>
                            <h3 class="fl-blog-card__title">
                                <a href="blog_details?id=<?= $blog['id'] ?>"><?= htmlspecialchars($blog['title']) ?></a>
                            </h3>
                            <p class="fl-blog-card__excerpt"><?= htmlspecialchars($blog['description_blog']) ?></p>
                            <a href="blog_details?id=<?= $blog['id'] ?>" class="fl-blog-card__link"><?= __('Read More') ?> <i class="fa fa-arrow-right"></i></a>
                        </div>
                    </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="fl-blog-page__empty">
                        <div class="fl-blog-page__empty-icon"><i class="fa fa-newspaper"></i></div>
                        <p class="fl-blog-page__empty-text"><?= __('No blog posts found.') ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <nav aria-label="<?= __('Page navigation') ?>" style="margin-top:var(--fl-space-10);">
                <ul class="fl-pagination">
                    <?php if ($page > 1): ?>
                        <li><a class="fl-pagination__link" href="?page=<?= $page - 1 ?>">&laquo;</a></li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li><a class="fl-pagination__link <?= $i == $page ? 'fl-pagination__link--active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <li><a class="fl-pagination__link" href="?page=<?= $page + 1 ?>">&raquo;</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </section>

<?php require_once 'include/footer.php'; ?>
