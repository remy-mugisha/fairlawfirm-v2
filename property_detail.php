<?php
require_once 'include/header.php';
require_once 'data/propertyMgt/config.php';

$property_id = $_GET['id'];
$property_query = $conn->prepare("SELECT * FROM properties WHERE id = ?");
$property_query->execute([$property_id]);
$property = $property_query->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    header("Location: property.php");
    exit();
}

// Get all images for this property
$images_query = $conn->prepare("SELECT * FROM property_images WHERE property_id = ? ORDER BY is_featured DESC");
$images_query->execute([$property_id]);
$property_images = $images_query->fetchAll(PDO::FETCH_ASSOC);

// Process image paths
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
    <title><?= htmlspecialchars($property['title']) ?></title>
    <style>
        .default-property-img {
            width: 100%;
            height: 450px;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
        }
        .main-blog-image {
            width: 100%;
            height: 450px;
            object-fit: cover;
        }
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .gallery-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .main-blog-image, .default-property-img {
                height: 300px;
            }
            .image-gallery {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 0.75rem;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body class="custom-cursor">

    <section class="page-header">
        <div class="page-header__bg" style="background-image: url(<?= $featured_image ?: 'assets/images/backgrounds/default-property.jpg' ?>);"></div>
        <div class="container">
            <h2 class="page-header__title"><?= htmlspecialchars($property['title']) ?></h2>
            <ul class="firdip-breadcrumb list-unstyled">
                <li><a href="index.php">Home</a></li>
                <li><span>Details</span></li>
            </ul>
        </div>
    </section>

    <section class="blog-one blog-one--page">
        <div class="container">
            <div class="row gutter-y-60">
                <div class="col-lg-8">
                    <div class="blog-details">
                        <div class="blog-image-container">
                            <?php if ($featured_image && file_exists($featured_image)): ?>
                                <img src="<?= $featured_image ?>" 
                                     class="main-blog-image"
                                     alt="<?= htmlspecialchars($property['title']) ?>">
                            <?php else: ?>
                                <div class="default-property-img">
                                    No Image Available
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="blog-card-two__content">
                            <h3 class="blog-card-two__title">Description</h3>
                            <p class="blog-card__two__text"><?= nl2br(htmlspecialchars($property['description'])) ?></p>
                        </div>

                        <?php if (count($gallery_images) > 1): ?>
                            <div class="image-gallery">
                                <?php foreach (array_slice($gallery_images, 1) as $image): ?>
                                    <?php if (file_exists($image)): ?>
                                        <img src="<?= $image ?>" class="gallery-image" alt="Property image">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <table class="table">
                            <h5>Details</h5>
                            <tbody>
                                <tr>
                                    <th scope="row">Status:</th>
                                    <td><?= htmlspecialchars($property['property_status']) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Type:</th>
                                    <td><?= htmlspecialchars($property['property_type']) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Price:</th>
                                    <td><?= htmlspecialchars($property['price']) ?> Rwf</td>
                                </tr>
                                <tr>
                                    <th scope="row">Size:</th>
                                    <td><?= htmlspecialchars($property['property_size']) ?> sq ft</td>
                                </tr>
                                <?php if ($property['property_type'] === "Commercial Building"): ?>
                                    <tr>
                                        <th scope="row">Floor:</th>
                                        <td><?= htmlspecialchars($property['floor']) ?></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <th scope="row">Bedrooms:</th>
                                        <td><?= htmlspecialchars($property['bedroom']) ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Bathrooms:</th>
                                        <td><?= htmlspecialchars($property['bathroom']) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($property['property_status'] !== 'For Sale' && !empty($property['months'])): ?>
                                <tr>
                                    <th scope="row">Duration:</th>
                                    <td><?= htmlspecialchars($property['months']) ?> Months</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <table class="table">
                            <h5>Address</h5>
                            <tbody>
                                <tr>
                                    <th scope="row">Street:</th>
                                    <td><?= htmlspecialchars($property['street']) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Sector:</th>
                                    <td><?= htmlspecialchars($property['sector']) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">District:</th>
                                    <td><?= htmlspecialchars($property['district']) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Country:</th>
                                    <td><?= htmlspecialchars($property['country']) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="sidebar">
                        <div class="card">
                            <div class="card-body">
                                <h5>Help Center</h5><br>
                                <li class="sidebar__comments__item">
                                    <div class="sidebar__comments__icon"><i class="icon-pin2"></i></div>
                                    <h6 class="sidebar__comments__title">
                                        <p>KG 194 St, Kigali<br>Kimironko Near bpr Branch</p>
                                    </h6>
                                </li>
                                <li class="sidebar__comments__item">
                                    <div class="sidebar__comments__icon"><i class="icon-telephone-call-1"></i></div>
                                    <h6 class="sidebar__comments__title">
                                        <a href="https:/wa.me/+250788411095">+250 788 411 095</a>
                                    </h6>
                                </li>
                            </div>
                        </div>
                        <div class="container1">
                            <h1>Contact for Booking</h1>
                            <form method="POST" action="bookingEmail.php">
                                <input type="hidden" name="property_id" value="<?= $property_id ?>">
                                <div class="form-group">
                                    <input type="text" name="name" placeholder="Full name" required>
                                </div>
                                <div class="form-group">
                                    <input type="email" name="email" placeholder="Email" required>
                                </div>
                                <div class="form-group">
                                    <input type="tel" name="phone" placeholder="Phone" required>
                                </div>
                                <?php if ($property['property_status'] !== 'For Sale'): ?>
                                <div class="form-group">
                                    <input type="number" name="months" min="1" placeholder="Number of Months" required>
                                </div>
                                <?php endif; ?>
                                <div class="form-group">
                                    <textarea name="comments" rows="4" placeholder="Write a Message"></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" name="submit">Send Now</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require_once 'include/footer.php'; ?>
</body>
</html>