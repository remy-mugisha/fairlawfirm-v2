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
    <section class="page-header">
        <!-- <div class="page-header__bg" style="background-color: #1a2f24"></div> -->
        <div class="page-header__bg" style="background-image: url(assets/images/backgrounds/background1-1.jpg);"></div>
        <div class="container">
            <h2 class="page-header__title"><?= __('Property Management')?></h2>
            <ul class="firdip-breadcrumb list-unstyled">
                <li><a href="index.php"><?= __('Home')?></a></li>
                <li><span><?= __('service')?></span></li>
            </ul>   
        </div>
    </section>

    <?php
    require_once 'data/propertyMgt/config.php';
    
    // Pagination settings
    $items_per_page = 9;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;
    
    if (!isset($conn)) {
        $possibleConnVars = ['$conn', '$db', '$pdo', '$connection', '$config'];
        $foundVar = 'No connection variable found';
        foreach ($possibleConnVars as $var) {
            $varName = substr($var, 1);
            if (isset($$varName)) {
                $foundVar = $var;
                $conn = $$varName;
                break;
            }
        }
        if ($foundVar == 'No connection variable found') {
            require_once 'propertyMgt/conn.php';
            if (isset($db)) {
                $conn = $db;
            }
        }
    }
    
    try {
        if (isset($conn)) {
            // Get total number of active properties
            $total_query = $conn->query("SELECT COUNT(*) FROM add_property WHERE status='Active'");
            $total_properties = $total_query->fetchColumn();
            $total_pages = ceil($total_properties / $items_per_page);

            // Fetch properties for current page
            $stmt = $conn->prepare("SELECT * FROM add_property WHERE status='Active' ORDER BY id DESC LIMIT :offset, :items_per_page");
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
            $stmt->execute();
            $login_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($login_db)) {
    ?>
    <section class="blog-one blog-one--page">
        <div class="container">
            <div class="row gutter-y-30">
                <?php foreach ($login_db as $add_property): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="blog-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='000ms'>
                        <a href="#" class="blog-card__image">
                            <img class="blog-card__image" src="data/propertyMgt/proImg/<?php echo $add_property['image']; ?>" alt="<?php echo htmlspecialchars($add_property['location']); ?>">
                        </a>

                        <div class="blog-card__three__content">
                            <ul class="list-unstyled blog-card__three__meta">
                                <li class="blog-card__three__meta__item" style="color: #198754;">
                                <i class="icon-pin2" style="color: #198754;"></i>
                                    <?php echo htmlspecialchars(__($add_property['location'])); ?>
                                </li>
                            </ul>
                            <ul class="list-unstyled blog-card__three__meta">
                                <li class="blog-card__three__meta__item"><?php echo htmlspecialchars(__($add_property['title'])); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <nav aria-label="Property navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" style="color:#198754;" href="?page=<?php echo ($page - 1); ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo; Previous</span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" style="color:#198754;" href="?page=<?php echo ($page + 1); ?>" aria-label="Next">
                                    <span aria-hidden="true">Next &raquo;</span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
            } else {
                echo '<div class="container text-center py-5"><h3>No properties available at the moment.</h3></div>';
            }
        } else {
            echo '<div class="container text-center py-5"><h3>Database connection not available.</h3></div>';
        }
    } catch(PDOException $e) {
        echo '<div class="container text-center py-5"><h3>Unable to load properties. Please try again later.</h3></div>';
        error_log("Property display error: " . $e->getMessage());
    }
    ?>
</body>

<?php 
require_once 'include/footer.php';
?>
</html>