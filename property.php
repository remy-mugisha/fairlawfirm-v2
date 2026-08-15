<?php
require_once 'include/header.php';
require_once 'data/propertyMgt/config.php';

$propertiesPerPage = 6;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $propertiesPerPage;

$selectActiveProperties = $conn->prepare("
    SELECT p.*, 
           (SELECT pi.image_path 
            FROM property_images pi 
            WHERE pi.property_id = p.id 
            ORDER BY pi.is_featured DESC, pi.id ASC 
            LIMIT 1) as property_image
    FROM properties p
    WHERE p.status = 'Active'
    ORDER BY p.created_at DESC 
    LIMIT :limit OFFSET :offset
");
$selectActiveProperties->bindValue(':limit', $propertiesPerPage, PDO::PARAM_INT);
$selectActiveProperties->bindValue(':offset', $offset, PDO::PARAM_INT);
$selectActiveProperties->execute();

$totalProperties = $conn->query("SELECT COUNT(*) FROM properties WHERE status = 'Active'")->fetchColumn();
$totalPages = ceil($totalProperties / $propertiesPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Properties</title>
    <style>
        :root {
            --primary: #198754;
            --dark: #1a2f24;
            --light: #f8f9fa;
            --gray: #6c757d;
        }
        
        .property-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/images/property-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        
        .property-header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .breadcrumb {
            justify-content: center;
            background: transparent;
            padding: 0;
        }
        
        .breadcrumb-item a {
            color: var(--light);
            text-decoration: none;
        }
        
        .property-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 30px;
            height: 100%;
        }
        
        .property-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        .property-img-container {
            height: 250px;
            overflow: hidden;
            position: relative;
        }
        
        .property-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .property-card:hover .property-img {
            transform: scale(1.1);
        }
        
        .property-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary);
            color: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .property-body {
            padding: 20px;
        }
        
        .property-title {
            font-size: 1.3rem;
            font-weight: 500;
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        .property-price {
            color: var(--primary);
            font-size: 1.2rem;
            font-weight: 400;
            font-size: 15px;
            margin-bottom: 10px;
        }
        
        .property-meta {
            display: flex;
            flex-wrap: wrap;
            font-size: 15px;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            color: var(--gray);
        }
        
        .meta-item i {
            margin-right: 5px;
            color: var(--primary);
        }
        
        .property-link {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 12px;
            text-decoration: none;
            font-weight: 400;
            transition: background 0.3s;
        }
        
        .property-link:hover {
            background: var(--dark);
            color: white;
        }
        
        .pagination {
            justify-content: center;
            margin-top: 50px;
        }
        
        .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
        }
        
        .page-link {
            color: var(--primary);
            margin: 0 5px;
            border-radius: 50% !important;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .no-properties {
            text-align: center;
            padding: 50px 0;
            font-size: 1.2rem;
            color: var(--gray);
        }
        
        @media (max-width: 768px) {
            .property-header {
                padding: 60px 0;
            }
            
            .property-header h1 {
                font-size: 2rem;
            }
            
            .property-img-container {
                height: 200px;
            }
        }
    </style>
</head>
<body>

    <section class="page-header">
        <!-- <div class="page-header__bg" style="background-color: #1a2f24"></div> -->
        <div class="page-header__bg" style="background-image: url(assets/images/backgrounds/background1-1.jpg);"></div>
        <div class="container">
            <h2 class="page-header__title">REAL ESTATE PROPERTIES</h2>
            <ul class="firdip-breadcrumb list-unstyled">
                <li><a href="index.php">Home</a></li>
                <li><span>Properties</span></li>
            </ul>   
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <?php if ($selectActiveProperties->rowCount() > 0): ?>
                <div class="row">
                    <?php while ($row = $selectActiveProperties->fetch()): 
                        $imagePath = !empty($row['property_image']) ? 
                            'data/propertyMgt/rentalImg/' . basename($row['property_image']) : 
                            'assets/images/default-property.jpg';
                    ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="property-card">
                                <div class="property-img-container">
                                    <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($row['title']) ?>" class="property-img">
                                    <span class="property-badge"><?= htmlspecialchars($row['property_status']) ?></span>
                                </div>
                                <div class="property-body">
                                    <h3 class="property-title"><?= htmlspecialchars($row['title']) ?></h3>
                                    <div class="property-price">
                                        <?= htmlspecialchars($row['price']) ?> Rwf
                                        <?php if ($row['property_status'] !== 'For Sale'): ?>
                                            <small>/ month</small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="property-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-home"></i>
                                            <?= htmlspecialchars($row['property_type']) ?>
                                        </div>
                                        <?php if ($row['property_type'] === "Commercial Building"): ?>
                                            <div class="meta-item">
                                                <i class="fas fa-layer-group"></i>
                                                <?= htmlspecialchars($row['floor']) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="meta-item">
                                                <i class="fas fa-bed"></i>
                                                <?= htmlspecialchars($row['bedroom']) ?> Beds
                                            </div>
                                            <div class="meta-item">
                                                <i class="fas fa-bath"></i>
                                                <?= htmlspecialchars($row['bathroom']) ?> Baths
                                            </div>
                                        <?php endif; ?>
                                        <div class="meta-item">
                                            <i class="fas fa-ruler-combined"></i>
                                            <?= htmlspecialchars($row['property_size']) ?> sqft
                                        </div>
                                    </div>
                                    <a href="property_detail?id=<?= $row['id'] ?>" class="property-link">
                                        View Details <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php else: ?>
                <div class="no-properties">
                    <i class="fas fa-home fa-3x mb-3" style="color: var(--primary);"></i>
                    <p>No properties available at the moment. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php require_once 'include/footer.php'; ?>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>