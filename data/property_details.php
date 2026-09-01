<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Property ID is missing.";
    header("Location: display_properties.php");
    exit();
}

$id = $_GET['id'];

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

$image_query = "SELECT * FROM property_images WHERE property_id = :id ORDER BY is_featured DESC";
$image_stmt = $conn->prepare($image_query);
$image_stmt->bindParam(':id', $id, PDO::PARAM_INT);
$image_stmt->execute();
$images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);

function formatDisplayPrice($price) {
    if (preg_match('/(\d+)\s*-\s*(\d+)/', $price, $matches)) {
        return number_format($matches[1], 0, '', ',') . ' Rwf - ' . number_format($matches[2], 0, '', ',') . ' Rwf';
    }
    $cleanPrice = preg_replace('/[^0-9]/', '', $price);
    return number_format($cleanPrice, 0, '', ',') . ' Rwf';
}
?>

<style>
.flf-details-hero { border-radius: var(--fl-r-md); overflow: hidden; margin-bottom: 0; }
.flf-carousel { position: relative; width: 100%; overflow: hidden; border-radius: var(--fl-r-md) var(--fl-r-md) 0 0; }
.flf-carousel-inner { position: relative; height: 420px; background: var(--flf-midnight); }
.flf-carousel-slide {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    opacity: 0; transition: opacity 0.5s ease; z-index: 0;
}
.flf-carousel-slide.active { opacity: 1; z-index: 1; }
.flf-carousel-slide img { width: 100%; height: 100%; object-fit: cover; }
.flf-carousel-featured {
    position: absolute; top: 16px; left: 16px; padding: 5px 14px; border-radius: 20px;
    background: var(--fl-seal-600); color: var(--fl-chambers-600);
    font-family: var(--fl-font-body); font-size: 11px; font-weight: 700;
    letter-spacing: 0.5px; text-transform: uppercase; z-index: 5;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.flf-carousel-counter {
    position: absolute; bottom: 16px; right: 16px; padding: 5px 12px; border-radius: 20px;
    background: rgba(0,0,0,0.6); color: white;
    font-family: var(--fl-font-body); font-size: 12px; font-weight: 600; z-index: 5;
}
.flf-carousel-btn {
    position: absolute; top: 50%; transform: translateY(-50%); z-index: 5;
    width: 44px; height: 44px; border-radius: 50%; border: none; cursor: pointer;
    background: rgba(255,255,255,0.9); color: var(--fl-chambers-600); font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.15); transition: all 0.2s;
}
.flf-carousel-btn:hover { background: var(--fl-surface); box-shadow: 0 4px 16px rgba(0,0,0,0.2); transform: translateY(-50%) scale(1.05); }
.flf-carousel-prev { left: 16px; }
.flf-carousel-next { right: 16px; }
.flf-carousel-dots {
    position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%);
    display: flex; gap: 8px; z-index: 5;
}
.flf-carousel-dot {
    width: 10px; height: 10px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.8);
    background: transparent; cursor: pointer; transition: all 0.3s; padding: 0;
}
.flf-carousel-dot.active { background: var(--fl-seal-600); border-color: var(--fl-seal-600); }
.flf-carousel-empty {
    height: 420px; display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, var(--flf-midnight), var(--fl-chambers-600));
    border-radius: var(--fl-r-md) var(--fl-r-md) 0 0;
}
.flf-carousel-empty i { font-size: 60px; color: rgba(255,255,255,0.2); }
.flf-details-body { padding: 0; }
.flf-details-header {
    padding: 24px 30px 20px; border-bottom: 1px solid var(--fl-chambers-100);
}
.flf-details-title {
    font-family: var(--fl-font-display); font-size: 28px; font-weight: 600;
    color: var(--fl-chambers-600); margin: 0 0 12px;
}
.flf-details-meta { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
.flf-details-badge {
    display: inline-flex; align-items: center; gap: 5px; padding: 5px 14px;
    border-radius: 20px; font-family: var(--fl-font-body); font-size: 12px;
    font-weight: 700; letter-spacing: 0.3px;
}
.flf-badge-active { background: rgba(26, 122, 76, 0.1); color: var(--flf-success); }
.flf-badge-inactive { background: rgba(161, 39, 52, 0.1); color: var(--flf-danger); }
.flf-badge-pending { background: rgba(107, 118, 153, 0.1); color: var(--fl-ink-400); }
.flf-badge-forrent { background: rgba(24, 53, 143, 0.1); color: var(--fl-chambers-600); }
.flf-badge-forsale { background: rgba(200, 169, 81, 0.12); color: #8B7330; }
.flf-details-price {
    font-family: var(--fl-font-body); font-size: 22px; font-weight: 700;
    color: var(--fl-chambers-600); margin: 0 0 6px;
}
.flf-details-type {
    font-family: var(--fl-font-body); font-size: 14px; color: var(--fl-ink-400); margin: 0;
}
.flf-details-section {
    padding: 24px 30px; border-bottom: 1px solid var(--fl-chambers-100);
}
.flf-details-section:last-child { border-bottom: none; }
.flf-section-label {
    font-family: var(--fl-font-body); font-size: 12px; font-weight: 700;
    letter-spacing: 0.8px; text-transform: uppercase; color: var(--fl-ink-400);
    margin-bottom: 14px; display: flex; align-items: center; gap: 8px;
}
.flf-section-label i { color: var(--fl-seal-600); font-size: 13px; }
.flf-features-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 14px;
}
.flf-feature-card {
    text-align: center; padding: 18px 12px; border-radius: var(--fl-r-sm);
    background: var(--fl-chambers-100); border: 1px solid rgba(24, 53, 143, 0.08);
}
.flf-feature-card i { font-size: 22px; color: var(--fl-chambers-600); margin-bottom: 8px; display: block; }
.flf-feature-card .flf-feature-value {
    font-family: var(--fl-font-body); font-size: 20px; font-weight: 700;
    color: var(--fl-chambers-600); display: block; margin-bottom: 2px;
}
.flf-feature-card .flf-feature-label {
    font-family: var(--fl-font-body); font-size: 12px; color: var(--fl-ink-400);
    text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;
}
.flf-location-block {
    display: flex; align-items: flex-start; gap: 14px;
}
.flf-location-icon {
    width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
    background: rgba(24, 53, 143, 0.08); display: flex; align-items: center;
    justify-content: center; font-size: 18px; color: var(--fl-chambers-600);
}
.flf-location-text {
    font-family: var(--fl-font-body); font-size: 15px; color: var(--fl-chambers-900);
    line-height: 1.5;
}
.flf-description-text {
    font-family: var(--fl-font-body); font-size: 14px; color: var(--fl-chambers-900);
    line-height: 1.7; white-space: pre-wrap; word-wrap: break-word;
}
.flf-actions-bar {
    padding: 20px 30px; background: var(--fl-chambers-100); border-radius: 0 0 var(--fl-r-md) var(--fl-r-md);
    display: flex; gap: 12px; flex-wrap: wrap;
}
.flf-gallery-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 14px;
}
.flf-gallery-thumb {
    position: relative; border-radius: var(--fl-r-sm); overflow: hidden;
    border: 2px solid var(--fl-chambers-100); aspect-ratio: 4/3; cursor: pointer;
    transition: border-color 0.25s, box-shadow 0.25s;
}
.flf-gallery-thumb:hover { border-color: var(--fl-chambers-600); box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
.flf-gallery-thumb.current { border-color: var(--fl-chambers-600); box-shadow: 0 2px 12px rgba(1, 22, 106, 0.15); }
.flf-gallery-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.flf-gallery-thumb .flf-thumb-featured {
    position: absolute; top: 6px; left: 6px; padding: 2px 8px; border-radius: 10px;
    background: var(--fl-seal-600); color: var(--fl-chambers-600);
    font-family: var(--fl-font-body); font-size: 9px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.3px;
}
@media (max-width: 768px) {
    .flf-carousel-inner { height: 280px; }
    .flf-carousel-empty { height: 280px; }
    .flf-details-header, .flf-details-section, .flf-actions-bar { padding-left: 20px; padding-right: 20px; }
    .flf-features-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>

<?php if (!empty($_SESSION['success_message'])): ?>
    <div class="row mb-3"><div class="col-md-12">
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:var(--flf-radius-sm);margin:0;">
            <i class="fa fa-check-circle" style="margin-right:6px;"></i>
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    </div></div>
<?php endif; ?>

<div class="midde_cont">
    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-md-12">
                <a href="display_properties.php" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left" style="margin-right:5px;"></i>Back to Properties
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30" style="padding:0;overflow:hidden;">

                    <?php if (count($images) > 0): ?>
                        <div class="flf-details-hero">
                            <div class="flf-carousel" id="flfCarousel">
                                <div class="flf-carousel-inner">
                                    <?php foreach ($images as $key => $image): ?>
                                        <div class="flf-carousel-slide <?php echo $key == 0 ? 'active' : ''; ?>" data-index="<?php echo $key; ?>">
                                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Property Image">
                                            <?php if ($image['is_featured']): ?>
                                                <span class="flf-carousel-featured"><i class="fa fa-star" style="margin-right:4px;"></i>Featured</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (count($images) > 1): ?>
                                    <button class="flf-carousel-btn flf-carousel-prev" onclick="flfPrev()"><i class="fa fa-chevron-left"></i></button>
                                    <button class="flf-carousel-btn flf-carousel-next" onclick="flfNext()"><i class="fa fa-chevron-right"></i></button>
                                    <div class="flf-carousel-dots" id="flfDots"></div>
                                <?php endif; ?>
                                <span class="flf-carousel-counter" id="flfCounter">1 / <?php echo count($images); ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flf-carousel-empty">
                            <i class="fa fa-image"></i>
                        </div>
                    <?php endif; ?>

                    <div class="flf-details-body">
                        <div class="flf-details-header">
                            <h1 class="flf-details-title"><?php echo htmlspecialchars($property['title']); ?></h1>
                            <div class="flf-details-meta">
                                <span class="flf-details-badge <?php
                                    if ($property['status'] == 'Active') echo 'flf-badge-active';
                                    elseif ($property['status'] == 'Inactive') echo 'flf-badge-inactive';
                                    else echo 'flf-badge-pending';
                                ?>">
                                    <i class="fa fa-circle" style="font-size:6px;"></i>
                                    <?php echo htmlspecialchars($property['status']); ?>
                                </span>
                                <span class="flf-details-badge <?php echo ($property['property_status'] == 'For Rent') ? 'flf-badge-forrent' : 'flf-badge-forsale'; ?>">
                                    <i class="fa <?php echo ($property['property_status'] == 'For Rent') ? 'fa-key' : 'fa-tag'; ?>" style="font-size:11px;"></i>
                                    <?php echo htmlspecialchars($property['property_status']); ?>
                                </span>
                                <span class="flf-details-badge" style="background:rgba(107,118,153,0.08);color:var(--flf-charcoal);">
                                    <i class="fa fa-building" style="font-size:11px;"></i>
                                    <?php echo htmlspecialchars($property['property_type']); ?>
                                </span>
                            </div>
                            <div style="margin-top:16px;">
                                <div class="flf-details-price"><?php echo formatDisplayPrice($property['price']); ?></div>
                                <div class="flf-details-type"><?php echo htmlspecialchars($property['property_size']); ?> sq ft</div>
                            </div>
                        </div>

                        <?php if ($property['property_type'] !== 'Commercial Building'): ?>
                        <div class="flf-details-section">
                            <div class="flf-section-label"><i class="fa fa-th-large"></i>Features</div>
                            <div class="flf-features-grid">
                                <div class="flf-feature-card">
                                    <i class="fa fa-bed"></i>
                                    <span class="flf-feature-value"><?php echo htmlspecialchars($property['bedroom']); ?></span>
                                    <span class="flf-feature-label">Bedrooms</span>
                                </div>
                                <div class="flf-feature-card">
                                    <i class="fa fa-bath"></i>
                                    <span class="flf-feature-value"><?php echo htmlspecialchars($property['bathroom']); ?></span>
                                    <span class="flf-feature-label">Bathrooms</span>
                                </div>
                                <?php if (!empty($property['property_status']) && $property['property_status'] !== 'For Sale' && !empty($property['months'])): ?>
                                <div class="flf-feature-card">
                                    <i class="fa fa-calendar"></i>
                                    <span class="flf-feature-value"><?php echo htmlspecialchars($property['months']); ?></span>
                                    <span class="flf-feature-label">Months</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="flf-details-section">
                            <div class="flf-section-label"><i class="fa fa-th-large"></i>Features</div>
                            <div class="flf-features-grid">
                                <div class="flf-feature-card">
                                    <i class="fa fa-building"></i>
                                    <span class="flf-feature-value"><?php echo htmlspecialchars($property['floor']); ?></span>
                                    <span class="flf-feature-label">Floors</span>
                                </div>
                                <?php if (!empty($property['property_status']) && $property['property_status'] !== 'For Sale' && !empty($property['months'])): ?>
                                <div class="flf-feature-card">
                                    <i class="fa fa-calendar"></i>
                                    <span class="flf-feature-value"><?php echo htmlspecialchars($property['months']); ?></span>
                                    <span class="flf-feature-label">Months</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="flf-details-section">
                            <div class="flf-section-label"><i class="fa fa-map-marker-alt"></i>Location</div>
                            <div class="flf-location-block">
                                <div class="flf-location-icon"><i class="fa fa-map-marker-alt"></i></div>
                                <div class="flf-location-text">
                                    <?php
                                    $parts = [];
                                    if (!empty($property['street']))  $parts[] = htmlspecialchars($property['street']);
                                    if (!empty($property['sector']))  $parts[] = htmlspecialchars($property['sector']);
                                    if (!empty($property['district'])) $parts[] = htmlspecialchars($property['district']);
                                    if (!empty($property['country'])) $parts[] = htmlspecialchars($property['country']);
                                    echo implode(', ', $parts);
                                    ?>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($property['description'])): ?>
                        <div class="flf-details-section">
                            <div class="flf-section-label"><i class="fa fa-align-left"></i>Description</div>
                            <div class="flf-description-text"><?php echo htmlspecialchars($property['description']); ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if (count($images) > 0): ?>
                        <div class="flf-details-section">
                            <div class="flf-section-label"><i class="fa fa-images"></i>Image Gallery</div>
                            <div class="flf-gallery-grid" id="flfGallery">
                                <?php foreach ($images as $key => $image): ?>
                                    <div class="flf-gallery-thumb <?php echo $key == 0 ? 'current' : ''; ?>" data-index="<?php echo $key; ?>" onclick="flfGoTo(<?php echo $key; ?>)">
                                        <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Property Image">
                                        <?php if ($image['is_featured']): ?>
                                            <span class="flf-thumb-featured"><i class="fa fa-star" style="margin-right:2px;"></i>Featured</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="flf-actions-bar">
                            <a href="edit_rental.php?id=<?php echo htmlspecialchars($property['id']); ?>" class="btn btn-info">
                                <i class="fa fa-pencil" style="margin-right:5px;"></i>Edit Property
                            </a>
                            <a href="property_images.php?property_id=<?php echo htmlspecialchars($property['id']); ?>" class="btn btn-success">
                                <i class="fa fa-images" style="margin-right:5px;"></i>Manage Images
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
(function() {
    var slides = document.querySelectorAll('.flf-carousel-slide');
    var dots = document.getElementById('flfDots');
    var counter = document.getElementById('flfCounter');
    var gallery = document.getElementById('flfGallery');
    var current = 0;
    var total = slides.length;

    if (total === 0) return;

    if (dots) {
        for (var i = 0; i < total; i++) {
            var dot = document.createElement('button');
            dot.className = 'flf-carousel-dot' + (i === 0 ? ' active' : '');
            dot.setAttribute('data-index', i);
            dot.addEventListener('click', function() { flfGoTo(parseInt(this.getAttribute('data-index'))); });
            dots.appendChild(dot);
        }
    }

    function update() {
        slides.forEach(function(s) { s.classList.remove('active'); });
        slides[current].classList.add('active');

        var allDots = dots ? dots.querySelectorAll('.flf-carousel-dot') : [];
        allDots.forEach(function(d) { d.classList.remove('active'); });
        if (allDots[current]) allDots[current].classList.add('active');

        if (counter) counter.textContent = (current + 1) + ' / ' + total;

        var thumbs = gallery ? gallery.querySelectorAll('.flf-gallery-thumb') : [];
        thumbs.forEach(function(t) { t.classList.remove('current'); });
        if (thumbs[current]) thumbs[current].classList.add('current');
    }

    window.flfGoTo = function(idx) { current = idx; update(); };
    window.flfPrev = function() { current = (current - 1 + total) % total; update(); };
    window.flfNext = function() { current = (current + 1) % total; update(); };

    setInterval(function() { flfNext(); }, 5000);
})();
</script>

<?php
require_once 'include/footer.php';
?>