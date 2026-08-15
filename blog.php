<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="bracket-web">
    <meta name="description" content="Firdip is beautifully designed Figma template especially for the fire department, fireman, fire prevention, fire fighting, fire station, protection, firefighter and all other fire & safety business and websites.">

    <?php
    require_once 'include/header.php';
    ?>
</head>

<body class="custom-cursor">

    <div class="page-wrapper">

        <section class="page-header">
            <!-- <div class="page-header__bg" style="background-color: #1a2f24"></div> -->
            <div class="page-header__bg" style="background-image: url(assets/images/backgrounds/background1-1.jpg);"></div>
            <div class="container">
                <h2 class="page-header__title"><?= __('Blog') ?></h2>
                <ul class="firdip-breadcrumb list-unstyled">
                    <li><a href="index.php"><?= __('Home') ?></a></li>
                    <li><span><?= __('Blog') ?></span></li>
                </ul>
            </div>
        </section>

        <section class="blog-one blog-one--page">
            <div class="container">
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php
                    require 'data/propertyMgt/config.php';

                    $itemsPerPage = 9;

                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($page - 1) * $itemsPerPage;

                    $totalPosts = $conn->query("SELECT COUNT(*) FROM blog WHERE status = 'active'")->fetchColumn();

                    $totalPages = ceil($totalPosts / $itemsPerPage);

                    $selectAllUsers = $conn->prepare("SELECT * FROM blog WHERE status = 'active' ORDER BY date DESC LIMIT :limit OFFSET :offset");
                    $selectAllUsers->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
                    $selectAllUsers->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $selectAllUsers->execute();

                    if ($selectAllUsers->rowCount() > 0) {
                        while ($blog = $selectAllUsers->fetch()) {
                            ?>
                            <div class="col">
                                <div class="card">
                                    <?php if (!empty($blog['image']) && file_exists("data/propertyMgt/blogImg/" . $blog['image'])): ?>
                                        <img src="data/propertyMgt/blogImg/<?php echo htmlspecialchars($blog['image']); ?>" class="card-img-top" alt="Blog Image">
                                    <?php else: ?>
                                        <img src="assets/images/placeholder.jpg" class="card-img-top" alt="Placeholder Image">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h6 class="about-two__top__text"><?php echo htmlspecialchars($blog['date']); ?></h6>
                                        <a href="blog_details?id=<?php echo $blog['id']; ?>">
                                            <h5 class="feature-one__item__title"><?php echo htmlspecialchars($blog['title']); ?></h5>
                                        </a>
                                        <a href="blog_details?id=<?php echo $blog['id']; ?>">
                                            <button type="submit" name="submit" style="color: white; font-size: 12px;padding: 10px 40px 10px 40px; margin-top:10px;">Read More</button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="col-12 text-center"><h4>No blog posts found.</h4></div>';
                    }
                    ?>
                </div>

                <!-- Pagination -->
                <div class="row mt-4">
                    <div class="col-12 d-flex justify-content-center">
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo; Previous</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                            <span aria-hidden="true">Next &raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
        <!-- Blog Section End -->

    </div>

    <?php
    require_once 'include/footer.php';
    ?>

</body>

</html>