<?php
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';
require_once __DIR__ . '/csrf.php';

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

if (isset($_POST['submit'])) {
    requireCsrfPost();
    $title = $_POST['title'];
    $description = $_POST['description'];
    $property_status = trim($_POST['property_status']);
    $property_type = $_POST['property_type'];
    $price = trim($_POST['price']);

    if (preg_match('/(\d+)\s*(?:-|up to)\s*(\d+)/i', $price, $matches)) {
        $price = $matches[1] . ' - ' . $matches[2];
    } else {
        $price = preg_replace('/[^0-9]/', '', $price);
    }
    $property_size = $_POST['property_size'];
    $bedroom = ($property_type === 'Commercial Building') ? 0 : $_POST['bedroom'];
    $bathroom = ($property_type === 'Commercial Building') ? 0 : $_POST['bathroom'];
    $street = $_POST['street'];
    $sector = $_POST['sector'];
    $district = $_POST['district'];
    $country = $_POST['country'];
    $status = $_POST['status'];
    $floor = ($property_type === 'Commercial Building') ? implode(', ', $_POST['floor']) : null;
    $months = ($property_status === 'For Sale') ? null : $_POST['months'];

    try {
        $query = "UPDATE properties SET 
                  title = :title,
                  description = :description,
                  property_status = :property_status,
                  property_type = :property_type,
                  price = :price,
                  property_size = :property_size,
                  bedroom = :bedroom,
                  bathroom = :bathroom,
                  street = :street,
                  sector = :sector,
                  district = :district,
                  country = :country,
                  status = :status,
                  floor = :floor,
                  months = :months
                  WHERE id = :id";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':property_status', $property_status);
        $stmt->bindParam(':property_type', $property_type);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':property_size', $property_size);
        $stmt->bindParam(':bedroom', $bedroom);
        $stmt->bindParam(':bathroom', $bathroom);
        $stmt->bindParam(':street', $street);
        $stmt->bindParam(':sector', $sector);
        $stmt->bindParam(':district', $district);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':floor', $floor);
        $stmt->bindParam(':months', $months);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Property updated successfully!";
            header("Location: display_rental.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error updating property.";
            header("Location: edit_rental.php?id=$id");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Edit rental error: " . $e->getMessage()); $_SESSION['error_message'] = "An error occurred. Please try again.";
        header("Location: edit_rental.php?id=$id");
        exit();
    }
}

$selectedFloors = explode(', ', $property['floor']);
$allFloors = [
    'Ground Floor', '1st Floor', '2nd Floor', '3rd Floor',
    '4th Floor', '5th Floor', '6th Floor', '7th Floor',
    '8th Floor', '9th Floor', '10th Floor', '11th Floor',
    '12th Floor', '13th Floor', '14th Floor', '15th Floor'
];
?>

<style>
.flf-form-section {
    margin-bottom: 32px;
    padding-bottom: 28px;
    border-bottom: 1px solid var(--fl-chambers-100);
}
.flf-form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.flf-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: var(--fl-font-display);
    font-size: 20px;
    font-weight: 700;
    color: var(--fl-chambers-600);
    margin: 0 0 22px;
}
.flf-section-title i {
    color: var(--fl-seal-600);
    font-size: 16px;
}
.flf-field {
    margin-bottom: 20px;
}
.flf-field:last-child {
    margin-bottom: 0;
}
.flf-field label {
    display: block;
    font-family: var(--fl-font-body);
    font-weight: 600;
    font-size: 13.5px;
    color: var(--fl-ink-500);
    margin-bottom: 7px;
}
.flf-field label i {
    color: var(--fl-seal-600);
    margin-right: 5px;
    width: 16px;
    text-align: center;
}
.flf-field-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
.flf-checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 10px;
    margin-top: 4px;
}
.flf-checkbox-card {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border: 1px solid #d9dee9;
    border-radius: var(--fl-r-sm);
    background: var(--fl-surface);
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: var(--fl-font-body);
    font-size: 13px;
    color: var(--fl-chambers-900);
}
.flf-checkbox-card:hover {
    border-color: var(--fl-chambers-600);
    background: rgba(24, 53, 143, 0.03);
}
.flf-checkbox-card input[type="checkbox"] {
    accent-color: var(--fl-chambers-600);
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}
.flf-checkbox-card:has(input:checked) {
    border-color: var(--fl-chambers-600);
    background: rgba(233, 238, 250, 0.5);
    color: var(--fl-chambers-600);
    font-weight: 600;
}
.flf-dynamic-hide { display: none; }
@media (max-width: 767px) {
    .flf-field-row { grid-template-columns: 1fr; }
    .flf-checkbox-grid { grid-template-columns: 1fr 1fr; }
}
</style>

<div class="midde_cont">
    <div class="container-fluid">

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:var(--fl-r-sm);margin:0;">
                        <i class="fa fa-check-circle" style="margin-right:6px;"></i>
                        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:var(--fl-r-sm);margin:0;">
                        <i class="fa fa-exclamation-triangle" style="margin-right:6px;"></i>
                        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                            <h2><i class="fa fa-pencil-square-o" style="color:var(--fl-seal-600);margin-right:10px;font-size:20px;"></i>Edit Rental Property</h2>
                            <a href="display_rental.php" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left" style="margin-right:5px;"></i>Back to Listings
                            </a>
                        </div>
                    </div>

                    <div class="full padding_infor_info">
                        <form action="edit_rental.php?id=<?php echo htmlspecialchars($id); ?>" method="post" style="max-width:820px;"><?php echo csrfHiddenField(); ?>

                            <!-- Section 1: Property Information -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-edit"></i>Property Information</h3>

                                <div class="flf-field">
                                    <label><i class="fa fa-heading"></i>Title</label>
                                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($property['title']); ?>" required>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-align-left"></i>Description</label>
                                    <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($property['description']); ?></textarea>
                                </div>

                                <div class="flf-field-row">
                                    <div class="flf-field">
                                        <label><i class="fa fa-tag"></i>Property Status</label>
                                        <select class="form-control" name="property_status" id="property_status" required>
                                            <option value="For Rent" <?php echo ($property['property_status'] == 'For Rent') ? 'selected' : ''; ?>>For Rent</option>
                                            <option value="For Sale" <?php echo ($property['property_status'] == 'For Sale') ? 'selected' : ''; ?>>For Sale</option>
                                            <option value="Not Available" <?php echo ($property['property_status'] == 'Not Available') ? 'selected' : ''; ?>>Not Available</option>
                                        </select>
                                    </div>

                                    <div class="flf-field" id="monthsField" style="<?php echo ($property['property_status'] == 'For Sale') ? 'display:none;' : ''; ?>">
                                        <label><i class="fa fa-calendar"></i>Rental Duration</label>
                                        <select class="form-control" name="months">
                                            <option value="">Select Months</option>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo ($property['months'] == $i) ? 'selected' : ''; ?>>
                                                    <?php echo $i . ' Month' . ($i > 1 ? 's' : ''); ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-home"></i>Property Type</label>
                                    <select class="form-control" name="property_type" id="property_type" required>
                                        <option value="Apartment" <?php echo ($property['property_type'] == 'Apartment') ? 'selected' : ''; ?>>Apartment</option>
                                        <option value="House" <?php echo ($property['property_type'] == 'House') ? 'selected' : ''; ?>>House</option>
                                        <option value="Commercial Building" <?php echo ($property['property_type'] == 'Commercial Building') ? 'selected' : ''; ?>>Commercial Building</option>
                                    </select>
                                </div>

                                <div class="flf-field" id="floorField" style="<?php echo ($property['property_type'] == 'Commercial Building') ? '' : 'display:none;'; ?>">
                                    <label><i class="fa fa-layer-group"></i>Floors</label>
                                    <div class="flf-checkbox-grid">
                                        <?php foreach ($allFloors as $floorOption): ?>
                                            <label class="flf-checkbox-card">
                                                <input type="checkbox" name="floor[]" value="<?php echo $floorOption; ?>"
                                                       <?php echo in_array($floorOption, $selectedFloors) ? 'checked' : ''; ?>>
                                                <?php echo $floorOption; ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Pricing & Details -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-money-bill-wave"></i>Pricing & Details</h3>

                                <div class="flf-field-row">
                                    <div class="flf-field">
                                        <label><i class="fa fa-coins"></i>Price (RWF)</label>
                                        <input type="text" class="form-control" name="price" id="price" value="<?php echo htmlspecialchars($property['price']); ?>" required>
                                    </div>

                                    <div class="flf-field">
                                        <label><i class="fa fa-ruler-combined"></i>Property Size (sq ft)</label>
                                        <input type="text" class="form-control" name="property_size" value="<?php echo htmlspecialchars($property['property_size']); ?>" required>
                                    </div>
                                </div>

                                <div class="flf-field-row" id="bedbathRow" style="<?php echo ($property['property_type'] == 'Commercial Building') ? 'display:none;' : ''; ?>">
                                    <div class="flf-field">
                                        <label><i class="fa fa-bed"></i>Bedrooms</label>
                                        <input type="number" class="form-control" name="bedroom" min="0" value="<?php echo htmlspecialchars($property['bedroom']); ?>" id="bedroomInput" required>
                                    </div>

                                    <div class="flf-field">
                                        <label><i class="fa fa-bath"></i>Bathrooms</label>
                                        <input type="number" class="form-control" name="bathroom" min="0" value="<?php echo htmlspecialchars($property['bathroom']); ?>" id="bathroomInput" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Location -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-map-marker-alt"></i>Location</h3>

                                <div class="flf-field">
                                    <label><i class="fa fa-road"></i>Street</label>
                                    <input type="text" class="form-control" name="street" value="<?php echo htmlspecialchars($property['street']); ?>" required>
                                </div>

                                <div class="flf-field-row">
                                    <div class="flf-field">
                                        <label><i class="fa fa-map"></i>Sector</label>
                                        <input type="text" class="form-control" name="sector" value="<?php echo htmlspecialchars($property['sector']); ?>">
                                    </div>

                                    <div class="flf-field">
                                        <label><i class="fa fa-city"></i>District</label>
                                        <input type="text" class="form-control" name="district" value="<?php echo htmlspecialchars($property['district']); ?>">
                                    </div>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-globe"></i>Country</label>
                                    <input type="text" class="form-control" name="country" value="<?php echo htmlspecialchars($property['country']); ?>" required>
                                </div>
                            </div>

                            <!-- Section 4: Status -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-toggle-on"></i>Listing Status</h3>

                                <div class="flf-field" style="max-width:320px;">
                                    <label><i class="fa fa-eye"></i>Status</label>
                                    <select class="form-control" name="status" required>
                                        <option value="Active" <?php echo ($property['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="Inactive" <?php echo ($property['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                        <option value="Pending" <?php echo ($property['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    </select>
                                </div>
                            </div>

                            <div style="padding-top:8px;">
                                <button type="submit" name="submit" class="btn btn-info">
                                    <i class="fa fa-save" style="margin-right:5px;"></i>Update Property
                                </button>
                                <a href="display_rental.php" class="btn btn-secondary" style="margin-left:10px;">Cancel</a>
                                <a href="property_images.php?property_id=<?php echo htmlspecialchars($id); ?>" class="btn btn-success" style="margin-left:10px;">
                                    <i class="fa fa-image" style="margin-right:5px;"></i>Manage Images
                                </a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var propertyType = document.getElementById('property_type');
    var propertyStatus = document.getElementById('property_status');
    var bedbathRow = document.getElementById('bedbathRow');
    var floorField = document.getElementById('floorField');
    var monthsField = document.getElementById('monthsField');

    function toggleTypeFields() {
        if (propertyType.value === 'Commercial Building') {
            bedbathRow.style.display = 'none';
            document.getElementById('bedroomInput').removeAttribute('required');
            document.getElementById('bathroomInput').removeAttribute('required');
            floorField.style.display = '';
        } else {
            bedbathRow.style.display = '';
            document.getElementById('bedroomInput').setAttribute('required', '');
            document.getElementById('bathroomInput').setAttribute('required', '');
            floorField.style.display = 'none';
        }
    }

    function toggleStatusFields() {
        if (propertyStatus.value === 'For Sale') {
            monthsField.style.display = 'none';
        } else {
            monthsField.style.display = '';
        }
    }

    propertyType.addEventListener('change', toggleTypeFields);
    propertyStatus.addEventListener('change', toggleStatusFields);
});
</script>

<script>
document.getElementById('price').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9\s-]/g, '');
    if (this.value.includes('-')) {
        this.value = this.value.replace(/\s*-\s*/, ' - ');
    }
});
</script>

<?php
require_once 'include/footer.php';
?>