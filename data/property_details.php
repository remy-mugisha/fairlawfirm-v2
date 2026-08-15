<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Property ID is missing.";
    header("Location: display_properties.php");
    exit();
}

$id = $_GET['id'];

// Get property details
$query = "SELECT * FROM properties WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    $_SESSION['error_message'] = "Property not found.";
    header("Location: display_properties.php");
    exit();
}

// Get property images
$image_query = "SELECT * FROM property_images WHERE property_id = :id ORDER BY is_featured DESC";
$image_stmt = $conn->prepare($image_query);
$image_stmt->bindParam(':id', $id, PDO::PARAM_INT);
$image_stmt->execute();
$images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);

function formatDisplayPrice($price) {
    if (preg_match('/(\d+)\s*-\s*(\d+)/', $price, $matches)) {
        return number_format($matches[1], 0, '', ',') . 'Rwf - ' . number_format($matches[2], 0, '', ',') . 'Rwf';
    }
    $cleanPrice = preg_replace('/[^0-9]/', '', $price);
    return number_format($cleanPrice, 0, '', ',') . 'Rwf';
}
?>

<style>
.description-text {
    max-height: 150px;
    overflow-y: auto;
    word-wrap: break-word;
    white-space: pre-wrap;
}
.carousel-item img {
    height: 400px;
    object-fit: cover;
}
</style>

<div class="row column1">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                    <h2>Property Details</h2>
                    <div>
                        <a href="edit_rental.php?id=<?php echo htmlspecialchars($property['id']); ?>" class="btn btn-info btn-sm mr-2">Edit Property</a>
                        <a href="property_images.php?property_id=<?php echo htmlspecialchars($property['id']); ?>" class="btn btn-primary btn-sm mr-2">Manage Images</a>
                        <a href="display_rental.php" class="btn btn-info btn-sm">Back to Properties</a>
                    </div>
                </div>
            </div>
            
            <div class="full progress_bar_inner">
                <div class="row">
                    <div class="col-md-12">
                        <div class="full padding_infor_info">
                            <!-- Image Carousel -->
                            <?php if (count($images) > 0): ?>
                                <div id="propertyCarousel" class="carousel slide mb-5" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        <?php foreach ($images as $key => $image): ?>
                                            <li data-target="#propertyCarousel" data-slide-to="<?php echo $key; ?>" <?php echo $key == 0 ? 'class="active"' : ''; ?>></li>
                                        <?php endforeach; ?>
                                    </ol>
                                    <div class="carousel-inner">
                                        <?php foreach ($images as $key => $image): ?>
                                            <div class="carousel-item <?php echo $key == 0 ? 'active' : ''; ?>">
                                                <img src="<?php echo htmlspecialchars($image['image_path']); ?>" class="d-block w-100" alt="Property Image">
                                                <?php if ($image['is_featured']): ?>
                                                    <div class="carousel-caption d-none d-md-block">
                                                        <span class="badge badge-success">Featured Image</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <a class="carousel-control-prev" href="#propertyCarousel" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#propertyCarousel" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mb-4">No images available for this property.</div>
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="property-info">
                                        <h3 class="property-title"><?php echo htmlspecialchars($property['title']); ?></h3>
                                        
                                        <div class="property-meta mb-4">
                                            <span class="badge <?php echo ($property['status'] == 'Active') ? 'badge-success' : (($property['status'] == 'Inactive') ? 'badge-danger' : 'badge-warning'); ?> mr-2">
                                                <?php echo htmlspecialchars($property['status']); ?>
                                            </span>
                                            <span class="badge <?php echo ($property['property_status'] == 'For Rent') ? 'badge-success' : 'badge-secondary'; ?> mr-2">
                                                <?php echo htmlspecialchars($property['property_status']); ?>
                                            </span>
                                            <span class="property-type mr-2"><?php echo htmlspecialchars($property['property_type']); ?></span>
                                            <span class="property-price">
                                                <?php echo formatDisplayPrice($property['price']); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="property-features mb-4">
                                            <div class="row">
                                                <?php if ($property['property_type'] !== 'Commercial Building'): ?>
                                                <div class="col-6">
                                                    <p><i class="fa fa-bed mr-2"></i> <strong>Bedrooms:</strong> <?php echo htmlspecialchars($property['bedroom']); ?></p>
                                                </div>
                                                <div class="col-6">
                                                    <p><i class="fa fa-bath mr-2"></i> <strong>Bathrooms:</strong> <?php echo htmlspecialchars($property['bathroom']); ?></p>
                                                </div>
                                                <?php else: ?>
                                                <div class="col-12">
                                                    <p><i class="fa fa-building mr-2"></i> <strong>Floors:</strong> <?php echo htmlspecialchars($property['floor']); ?></p>
                                                </div>
                                                <?php endif; ?>
                                                <div class="col-12">
                                                    <p><i class="fa fa-ruler-combined mr-2"></i> <strong>Size:</strong> <?php echo htmlspecialchars($property['property_size']); ?> sq ft</p>
                                                </div>
                                                <?php if ($property['property_status'] !== 'For Sale' && !empty($property['months'])): ?>
                                                <div class="col-12">
                                                    <p><i class="fa fa-calendar mr-2"></i> <strong>Months:</strong> <?php echo htmlspecialchars($property['months']); ?> Months</p>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="property-address mb-4">
                                            <h5>Location</h5>
                                            <p>
                                                <i class="fa fa-map-marker-alt mr-2"></i>
                                                <?php 
                                                echo htmlspecialchars($property['street']);
                                                if (!empty($property['sector'])) echo ', ' . htmlspecialchars($property['sector']);
                                                if (!empty($property['district'])) echo ', ' . htmlspecialchars($property['district']);
                                                if (!empty($property['country'])) echo ', ' . htmlspecialchars($property['country']);
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="property-description">
                                        <h5>Description</h5>
                                        <p class="description-text"><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Thumbnail Gallery -->
                            <?php if (count($images) > 0): ?>
                                <div class="mt-5">
                                    <h5>Image Gallery</h5>
                                    <div class="d-flex flex-wrap">
                                        <?php foreach ($images as $image): ?>
                                            <div class="image-container m-2">
                                                <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Property Image" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                                <?php if ($image['is_featured']): ?>
                                                    <span class="badge badge-success" style="position: absolute; top: 10px; right: 10px;">Featured</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
              
<?php
require_once 'include/footer.php';
?>