<?php
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (isset($_POST['submit'])) {
    $description = $_POST['description'];
    $title = $_POST['title'];
    $property_status = $_POST['property_status'];
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
    $floor = ($property_type === 'Commercial Building') ? implode(', ', $_POST['floor']) : null;
    $months = ($property_status === 'For Sale') ? null : $_POST['months'];

    $query = "INSERT INTO properties (title, description, property_status, property_type, price, property_size, bedroom, bathroom, street, sector, district, country, floor, months) 
              VALUES (:title, :description, :property_status, :property_type, :price, :property_size, :bedroom, :bathroom, :street, :sector, :district, :country, :floor, :months)";

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
    $stmt->bindParam(':floor', $floor);
    $stmt->bindParam(':months', $months);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Property added successfully! You can now upload images for this property.";
        header("Location: property_images.php?property_id=" . $conn->lastInsertId());
        exit();
    } else {
        $_SESSION['error_message'] = "Error adding property: " . $stmt->errorInfo()[2];
        header("Location: add_rental_property.php");
        exit();
    }
}
?>

<style>
.flf-form-section {
    margin-bottom: 32px;
    padding-bottom: 28px;
    border-bottom: 1px solid var(--flf-blue);
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
    font-family: var(--flf-font-head);
    font-size: 20px;
    font-weight: 700;
    color: var(--flf-navy);
    margin: 0 0 22px;
}
.flf-section-title i {
    color: var(--flf-gold);
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
    font-family: var(--flf-font-body);
    font-weight: 600;
    font-size: 13.5px;
    color: var(--flf-slate);
    margin-bottom: 7px;
}
.flf-field label i {
    color: var(--flf-gold);
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
    border-radius: var(--flf-radius-sm);
    background: var(--flf-white);
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: var(--flf-font-body);
    font-size: 13px;
    color: var(--flf-charcoal);
}
.flf-checkbox-card:hover {
    border-color: var(--flf-royal);
    background: rgba(24, 53, 143, 0.03);
}
.flf-checkbox-card input[type="checkbox"] {
    accent-color: var(--flf-navy);
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}
.flf-checkbox-card:has(input:checked) {
    border-color: var(--flf-navy);
    background: rgba(233, 238, 250, 0.5);
    color: var(--flf-navy);
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
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:var(--flf-radius-sm);margin:0;">
                        <i class="fa fa-check-circle" style="margin-right:6px;"></i>
                        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:var(--flf-radius-sm);margin:0;">
                        <i class="fa fa-exclamation-triangle" style="margin-right:6px;"></i>
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
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
                            <h2><i class="fa fa-plus-circle" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>Add Rental Property</h2>
                            <a href="display_rental.php" class="btn btn-secondary btn-sm">
                                <i class="fa fa-th-list" style="margin-right:5px;"></i>View All Properties
                            </a>
                        </div>
                    </div>

                    <div class="full padding_infor_info">
                        <form action="add_rental_property.php" method="post" style="max-width:820px;">

                            <!-- Section 1: Property Information -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-edit"></i>Property Information</h3>

                                <div class="flf-field">
                                    <label><i class="fa fa-heading"></i>Title</label>
                                    <input type="text" class="form-control" name="title" placeholder="e.g. Modern Apartment in Kigali" required>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-align-left"></i>Description</label>
                                    <textarea class="form-control" name="description" rows="4" placeholder="Describe the property features, amenities, and highlights..." required></textarea>
                                </div>

                                <div class="flf-field-row">
                                    <div class="flf-field">
                                        <label><i class="fa fa-tag"></i>Property Status</label>
                                        <select class="form-control" name="property_status" id="property_status" required>
                                            <option value="">Select Status</option>
                                            <option value="For Rent">For Rent</option>
                                            <option value="For Sale">For Sale</option>
                                            <option value="Not Available">Not Available</option>
                                        </select>
                                    </div>

                                    <div class="flf-field" id="monthsField">
                                        <label><i class="fa fa-calendar"></i>Rental Duration</label>
                                        <select class="form-control" name="months">
                                            <option value="">Select Months</option>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i . ' Month' . ($i > 1 ? 's' : ''); ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-home"></i>Property Type</label>
                                    <select class="form-control" name="property_type" id="property_type" required>
                                        <option value="">Select Type</option>
                                        <option value="Apartment">Apartment</option>
                                        <option value="House">House</option>
                                        <option value="Commercial Building">Commercial Building</option>
                                    </select>
                                </div>

                                <div class="flf-field flf-dynamic-hide" id="floorField">
                                    <label><i class="fa fa-layer-group"></i>Floors</label>
                                    <div class="flf-checkbox-grid">
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="Ground Floor">Ground Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="1st Floor">1st Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="2nd Floor">2nd Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="3rd Floor">3rd Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="4th Floor">4th Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="5th Floor">5th Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="6th Floor">6th Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="7th Floor">7th Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="8th Floor">8th Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="9th Floor">9th Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="10th Floor">10th Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="11th Floor">11th Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="12th Floor">12th Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="13th Floor">13th Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="14th Floor">14th Floor</label>
                                        <label class="flf-checkbox-card"><input type="checkbox" name="floor[]" value="15th Floor">15th Floor</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Pricing & Details -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-money-bill-wave"></i>Pricing & Details</h3>

                                <div class="flf-field-row">
                                    <div class="flf-field">
                                        <label><i class="fa fa-coins"></i>Price (RWF)</label>
                                        <input type="text" class="form-control" name="price" id="price" placeholder="e.g. 500000 or 300000 - 500000" required>
                                    </div>

                                    <div class="flf-field">
                                        <label><i class="fa fa-ruler-combined"></i>Property Size (sq ft)</label>
                                        <input type="text" class="form-control" name="property_size" placeholder="e.g. 1200" required>
                                    </div>
                                </div>

                                <div class="flf-field-row flf-dynamic-hide" id="bedbathRow">
                                    <div class="flf-field">
                                        <label><i class="fa fa-bed"></i>Bedrooms</label>
                                        <input type="number" class="form-control" name="bedroom" min="0" placeholder="Number of bedrooms" id="bedroomInput" required>
                                    </div>

                                    <div class="flf-field">
                                        <label><i class="fa fa-bath"></i>Bathrooms</label>
                                        <input type="number" class="form-control" name="bathroom" min="0" placeholder="Number of bathrooms" id="bathroomInput" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Location -->
                            <div class="flf-form-section">
                                <h3 class="flf-section-title"><i class="fa fa-map-marker-alt"></i>Location</h3>

                                <div class="flf-field">
                                    <label><i class="fa fa-road"></i>Street</label>
                                    <input type="text" class="form-control" name="street" placeholder="Enter street address" required>
                                </div>

                                <div class="flf-field-row">
                                    <div class="flf-field">
                                        <label><i class="fa fa-map"></i>Sector</label>
                                        <input type="text" class="form-control" name="sector" placeholder="Enter sector">
                                    </div>

                                    <div class="flf-field">
                                        <label><i class="fa fa-city"></i>District</label>
                                        <input type="text" class="form-control" name="district" placeholder="Enter district">
                                    </div>
                                </div>

                                <div class="flf-field">
                                    <label><i class="fa fa-globe"></i>Country</label>
                                    <input type="text" class="form-control" name="country" placeholder="Enter country" required>
                                </div>
                            </div>

                            <div style="padding-top:8px;">
                                <button type="submit" name="submit" class="btn btn-info">
                                    <i class="fa fa-plus" style="margin-right:5px;"></i>Add Property
                                </button>
                                <a href="display_rental.php" class="btn btn-secondary" style="margin-left:10px;">Cancel</a>
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
            bedbathRow.classList.add('flf-dynamic-hide');
            document.getElementById('bedroomInput').removeAttribute('required');
            document.getElementById('bathroomInput').removeAttribute('required');
            floorField.classList.remove('flf-dynamic-hide');
        } else {
            bedbathRow.classList.remove('flf-dynamic-hide');
            document.getElementById('bedroomInput').setAttribute('required', '');
            document.getElementById('bathroomInput').setAttribute('required', '');
            floorField.classList.add('flf-dynamic-hide');
        }
    }

    function toggleStatusFields() {
        if (propertyStatus.value === 'For Sale') {
            monthsField.classList.add('flf-dynamic-hide');
        } else {
            monthsField.classList.remove('flf-dynamic-hide');
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