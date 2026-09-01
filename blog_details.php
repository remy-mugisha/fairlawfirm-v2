<?php
require_once 'include/header.php';
require 'data/propertyMgt/config.php';

function getFileIconClass($filename) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    switch(strtolower($ext)) {
        case 'pdf': return 'far fa-file-pdf';
        case 'doc':
        case 'docx': return 'far fa-file-word';
        case 'xls':
        case 'xlsx': return 'far fa-file-excel';
        case 'ppt':
        case 'pptx': return 'far fa-file-powerpoint';
        case 'zip':
        case 'rar': return 'far fa-file-archive';
        case 'txt': return 'far fa-file-alt';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif': return 'far fa-file-image';
        default: return 'far fa-file-alt';
    }
}

function formatFileSize($bytes) {
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$blog = null;
$attachments = [];

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM blog WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($blog) {
        $stmt = $conn->prepare("SELECT * FROM blog_attachments WHERE blog_id = :blog_id");
        $stmt->bindParam(':blog_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $blog ? htmlspecialchars($blog['title']) . ' - Fair Law Firm LTD' : 'Blog post not found' ?>">
    <title><?= $blog ? htmlspecialchars($blog['title']) . ' - Fair Law Firm LTD' : __('Blog') . ' - Fair Law Firm LTD' ?></title>
    <style>
        .fl-blog-detail {
            padding: var(--fl-sp-8) 0 var(--fl-sp-8);
        }

        .fl-blog-detail__grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: var(--fl-sp-6);
            align-items: start;
        }

        .fl-blog-detail__image {
            width: 100%;
            height: 480px;
            object-fit: cover;
            border-radius: var(--fl-r-md);
            margin-bottom: var(--fl-sp-5);
        }

        .fl-blog-detail__meta {
            display: flex;
            align-items: center;
            gap: var(--fl-sp-3);
            margin-bottom: var(--fl-sp-4);
            font-size: var(--fl-text-sm);
            color: var(--fl-ink-500);
        }

        .fl-blog-detail__meta-item {
            display: flex;
            align-items: center;
            gap: var(--fl-sp-1);
        }

        .fl-blog-detail__meta-item i {
            color: var(--fl-chambers-600);
            font-size: var(--fl-text-xs);
        }

        .fl-blog-detail__content {
            font-size: var(--fl-text-base);
            color: var(--fl-ink-500);
            line-height: 1.85;
        }

        .fl-blog-detail__content p {
            margin-bottom: var(--fl-sp-4);
        }

        .fl-blog-detail__attachments {
            margin-top: var(--fl-sp-5);
            padding-top: var(--fl-sp-4);
            border-top: 1px solid var(--fl-ink-100);
        }

        .fl-blog-detail__attachments-title {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-lg);
            font-weight: 600;
            color: var(--fl-chambers-900);
            margin-bottom: var(--fl-sp-3);
        }

        .fl-blog-detail__attachment {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--fl-sp-2) var(--fl-sp-3);
            border: 1px solid var(--fl-ink-100);
            border-radius: var(--fl-r-md);
            margin-bottom: var(--fl-sp-2);
            transition: background var(--fl-transition-fast);
        }

        .fl-blog-detail__attachment:hover {
            background: var(--fl-ink-50);
        }

        .fl-blog-detail__attachment-info {
            display: flex;
            align-items: center;
            gap: var(--fl-sp-2);
        }

        .fl-blog-detail__attachment-icon {
            font-size: 1.2rem;
            color: var(--fl-chambers-600);
        }

        .fl-blog-detail__attachment-name {
            font-size: var(--fl-text-sm);
            font-weight: 600;
            color: var(--fl-chambers-900);
        }

        .fl-blog-detail__attachment-size {
            font-size: var(--fl-text-xs);
            color: var(--fl-ink-300);
            margin-left: var(--fl-sp-1);
        }

        .fl-blog-detail__attachment-download {
            font-size: var(--fl-text-sm);
            font-weight: 600;
            color: var(--fl-chambers-600);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: var(--fl-sp-1);
            transition: color var(--fl-transition-fast);
        }

        .fl-blog-detail__attachment-download:hover {
            color: var(--fl-seal-600);
        }

        .fl-blog-detail__sidebar {
            position: sticky;
            top: 100px;
        }

        .fl-blog-detail__sidebar-card {
            background: var(--fl-surface);
            border: 1px solid var(--fl-ink-100);
            border-radius: var(--fl-r-md);
            padding: var(--fl-sp-4);
            margin-bottom: var(--fl-sp-4);
        }

        .fl-blog-detail__sidebar-title {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-lg);
            font-weight: 600;
            color: var(--fl-chambers-900);
            margin-bottom: var(--fl-sp-3);
        }

        .fl-blog-detail__sidebar-text {
            font-size: var(--fl-text-base);
            color: var(--fl-ink-500);
        }

        .fl-blog-detail__sidebar-badge {
            display: inline-block;
            background: var(--fl-chambers-100);
            color: var(--fl-chambers-600);
            font-size: var(--fl-text-sm);
            font-weight: 600;
            padding: 0.4rem 0.8rem;
            border-radius: var(--fl-r-sm);
        }

        .fl-blog-detail__error {
            text-align: center;
            padding: var(--fl-sp-8) 0;
        }

        .fl-blog-detail__error-icon {
            font-size: 3rem;
            color: var(--fl-ink-200);
            margin-bottom: var(--fl-sp-3);
        }

        .fl-blog-detail__error-text {
            font-size: var(--fl-text-lg);
            color: var(--fl-ink-500);
            margin-bottom: var(--fl-sp-4);
        }

        @media (max-width: 992px) {
            .fl-blog-detail__grid {
                grid-template-columns: 1fr;
            }
            .fl-blog-detail__sidebar {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .fl-blog-detail__image {
                height: 300px;
            }
        }
    </style>
</head>

    <?php if ($blog): ?>
    <!-- Page Header -->
    <section class="fl-page-header">
        <div class="fl-page-header__bg" style="background-image: url(data/propertyMgt/blogImg/<?= htmlspecialchars($blog['image']) ?>);"></div>
        <div class="fl-container">
            <div class="fl-page-header__content">
                <h1 class="fl-page-header__title"><?= htmlspecialchars($blog['title']) ?></h1>
                <nav class="fl-page-header__breadcrumb">
                    <a href="index.php"><?= __('Home') ?></a>
                    <span>/</span>
                    <a href="blog.php"><?= __('Blog') ?></a>
                    <span>/</span>
                    <span><?= __('Article') ?></span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Blog Detail -->
    <section class="fl-blog-detail">
        <div class="fl-container">
            <div class="fl-blog-detail__grid">
                <!-- Main Content -->
                <div>
                    <img src="data/propertyMgt/blogImg/<?= htmlspecialchars($blog['image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="fl-blog-detail__image">

                    <div class="fl-blog-detail__meta">
                        <div class="fl-blog-detail__meta-item">
                            <i class="fa fa-calendar-alt"></i> <?= date('F j, Y', strtotime($blog['date'])) ?>
                        </div>
                        <?php if (!empty($blog['category_blog'])): ?>
                        <div class="fl-blog-detail__meta-item">
                            <i class="fa fa-tag"></i> <?= htmlspecialchars($blog['category_blog']) ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="fl-blog-detail__content">
                        <?php echo nl2br(htmlspecialchars($blog['blog_description_details'])); ?>
                    </div>

                    <?php if (!empty($attachments)): ?>
                    <div class="fl-blog-detail__attachments">
                        <h4 class="fl-blog-detail__attachments-title"><?= __('Attachments') ?></h4>
                        <?php foreach ($attachments as $attachment): ?>
                        <div class="fl-blog-detail__attachment">
                            <div class="fl-blog-detail__attachment-info">
                                <i class="<?= getFileIconClass($attachment['file_name']) ?> fl-blog-detail__attachment-icon"></i>
                                <span class="fl-blog-detail__attachment-name"><?= htmlspecialchars($attachment['file_name']) ?></span>
                                <span class="fl-blog-detail__attachment-size">(<?= formatFileSize($attachment['file_size']) ?>)</span>
                            </div>
                            <a href="data/propertyMgt/blogFiles/<?= htmlspecialchars($attachment['file_path']) ?>" class="fl-blog-detail__attachment-download" download title="<?= __('Download') ?> <?= htmlspecialchars($attachment['file_name']) ?>">
                                <i class="fa fa-download"></i>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="fl-blog-detail__sidebar">
                    <?php if (!empty($blog['category_blog'])): ?>
                    <div class="fl-blog-detail__sidebar-card">
                        <h4 class="fl-blog-detail__sidebar-title"><?= __('Category') ?></h4>
                        <span class="fl-blog-detail__sidebar-badge"><?= htmlspecialchars($blog['category_blog']) ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="fl-blog-detail__sidebar-card">
                        <h4 class="fl-blog-detail__sidebar-title"><?= __('Posted On') ?></h4>
                        <p class="fl-blog-detail__sidebar-text"><?= date('F j, Y', strtotime($blog['date'])) ?></p>
                    </div>

                    <div class="fl-blog-detail__sidebar-card">
                        <h4 class="fl-blog-detail__sidebar-title"><?= __('Need Legal Help?') ?></h4>
                        <p class="fl-blog-detail__sidebar-text" style="margin-bottom:var(--fl-space-4);"><?= __('Contact our experienced team for professional legal counsel.') ?></p>
                        <a href="contact.php" class="fl-btn fl-btn--primary" style="width:100%;justify-content:center;"><?= __('Contact Us') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php else: ?>
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
    <section class="fl-blog-detail">
        <div class="fl-container">
            <div class="fl-blog-detail__error">
                <div class="fl-blog-detail__error-icon"><i class="fa fa-exclamation-triangle"></i></div>
                <p class="fl-blog-detail__error-text"><?= __('Blog post not found.') ?></p>
                <a href="blog.php" class="fl-btn fl-btn--primary"><?= __('Back to Blog') ?></a>
            </div>
        </div>
    </section>
    <?php endif; ?>

<?php require_once 'include/footer.php'; ?>
