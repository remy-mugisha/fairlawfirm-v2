<?php
ob_start();
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

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $property_status = trim($_POST['property_status']);
    $property_type = $_POST['property_type'];
    // Replace the price processing with:
    $price = trim($_POST['price']);

    // Clean the price while preserving range format
    if (preg_match('/(\d+)\s*(?:-|up to)\s*(\d+)/i', $price, $matches)) {
        $price = $matches[1] . ' - ' . $matches[2];
    } else {
        // If single price, just clean it
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
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        header("Location: edit_rental.php?id=$id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Rental Property</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-group { margin-bottom: 1.5rem; }
        .control-label { font-weight: 500; padding-top: 7px; }
        .padding_infor_info { padding: 30px; }
    </style>
</head>
<body>
    <div class="row column1">
        <div class="col-md-12">
            <div class="white_shd full margin_bottom_30">
                <div class="full graph_head">
                    <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                        <h2>Edit Rental Property</h2>
                        <a href="display_rental.php" class="btn btn-info btn-sm">View All Properties</a>
                    </div>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="full progress_bar_inner">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="full padding_infor_info">
                                <form class="form-horizontal" action="edit_rental.php?id=<?php echo htmlspecialchars($id); ?>" method="post">
                                    <!-- Title -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Title</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($property['title']); ?>" required>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Description</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($property['description']); ?></textarea>
                                        </div>
                                    </div>

                                    <!-- Property Status -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Property Status</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="property_status" id="property_status" required>
                                                <option value="For Rent" <?php echo ($property['property_status'] == 'For Rent') ? 'selected' : ''; ?>>For Rent</option>
                                                <option value="For Sale" <?php echo ($property['property_status'] == 'For Sale') ? 'selected' : ''; ?>>For Sale</option>
                                                <option value="Not Available" <?php echo ($property['property_status'] == 'Not Available') ? 'selected' : ''; ?>>Not Available</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Months Dropdown (Only for For Rent) -->
                                    <div class="form-group row" id="monthsField" style="<?php echo ($property['property_status'] == 'For Sale') ? 'display: none;' : 'display: block;'; ?>">
                                        <label class="control-label col-sm-3">Select Months</label>
                                        <div class="col-sm-9">
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

                                    <!-- Property Type -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Property Type</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="property_type" id="property_type" required>
                                                <option value="Apartment" <?php echo ($property['property_type'] == 'Apartment') ? 'selected' : ''; ?>>Apartment</option>
                                                <option value="House" <?php echo ($property['property_type'] == 'House') ? 'selected' : ''; ?>>House</option>
                                                <option value="Commercial Building" <?php echo ($property['property_type'] == 'Commercial Building') ? 'selected' : ''; ?>>Commercial Building</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Floor (Only for Commercial Building) -->
                                    <div class="form-group row" id="floorField" style="<?php echo ($property['property_type'] == 'Commercial Building') ? 'display: block;' : 'display: none;'; ?>">
                                        <label class="control-label col-sm-3">Floors</label>
                                        <div class="col-sm-9">
                                            <?php 
                                            $selectedFloors = explode(', ', $property['floor']);
                                            $allFloors = [
                                                'Ground Floor', '1st Floor', '2nd Floor', '3rd Floor', 
                                                '4th Floor', '5th Floor', '6th Floor', '7th Floor',
                                                '8th Floor', '9th Floor', '10th Floor', '11th Floor',
                                                '12th Floor', '13th Floor', '14th Floor', '15th Floor'
                                            ];
                                            
                                            foreach ($allFloors as $floorOption): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="floor[]" 
                                                           value="<?php echo $floorOption; ?>" 
                                                           id="floor<?php echo str_replace(' ', '', $floorOption); ?>"
                                                           <?php echo in_array($floorOption, $selectedFloors) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="floor<?php echo str_replace(' ', '', $floorOption); ?>">
                                                        <?php echo $floorOption; ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <!-- Price -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Price</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="price" id="price" 
                                            value="<?php echo htmlspecialchars($property['price']); ?>" 
                                            placeholder="Price" required>                                        
                                        </div>
                                    </div>

                                    <!-- Property Size -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Property Size</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="property_size" value="<?php echo htmlspecialchars($property['property_size']); ?>" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">sq ft</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bedroom (Hidden for Commercial Building) -->
                                    <div class="form-group row" id="bedroomField" style="<?php echo ($property['property_type'] == 'Commercial Building') ? 'display: none;' : 'display: block;'; ?>">
                                        <label class="control-label col-sm-3">Bedrooms</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control" name="bedroom" min="0" value="<?php echo htmlspecialchars($property['bedroom']); ?>" required>
                                        </div>
                                    </div>

                                    <!-- Bathroom (Hidden for Commercial Building) -->
                                    <div class="form-group row" id="bathroomField" style="<?php echo ($property['property_type'] == 'Commercial Building') ? 'display: none;' : 'display: block;'; ?>">
                                        <label class="control-label col-sm-3">Bathrooms</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control" name="bathroom" min="0" value="<?php echo htmlspecialchars($property['bathroom']); ?>" required>
                                        </div>
                                    </div>

                                    <!-- Street -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Street</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="street" value="<?php echo htmlspecialchars($property['street']); ?>" required>
                                        </div>
                                    </div>

                                    <!-- Sector -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Sector</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="sector" value="<?php echo htmlspecialchars($property['sector']); ?>">
                                        </div>
                                    </div>

                                    <!-- District -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">District</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="district" value="<?php echo htmlspecialchars($property['district']); ?>">
                                        </div>
                                    </div>

                                    <!-- Country -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Country</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="country" value="<?php echo htmlspecialchars($property['country']); ?>" required>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Status</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="status" required>
                                                <option value="Active" <?php echo ($property['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                                <option value="Inactive" <?php echo ($property['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                                <option value="Pending" <?php echo ($property['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="form-group row">
                                        <div class="col-sm-9 offset-sm-3">
                                            <button type="submit" name="submit" class="btn btn-info">Update Property</button>
                                            <a href="property_details.php?id=<?php echo htmlspecialchars($id); ?>" class="btn btn-secondary ml-2">Cancel</a>
                                            <a href="property_images.php?property_id=<?php echo htmlspecialchars($id); ?>" class="btn btn-primary ml-2">Manage Images</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const propertyType = document.getElementById('property_type');
        const propertyStatus = document.getElementById('property_status');
        const bedroomField = document.getElementById('bedroomField');
        const bathroomField = document.getElementById('bathroomField');
        const floorField = document.getElementById('floorField');
        const monthsField = document.getElementById('monthsField');

        // Handle Property Type Change
        propertyType.addEventListener('change', function() {
            if (this.value === 'Commercial Building') {
                bedroomField.style.display = 'none';
                bathroomField.style.display = 'none';
                floorField.style.display = 'block';
            } else {
                bedroomField.style.display = 'block';
                bathroomField.style.display = 'block';
                floorField.style.display = 'none';
            }
        });

        // Handle Property Status Change
        propertyStatus.addEventListener('change', function() {
            if (this.value === 'For Sale') {
                monthsField.style.display = 'none';
            } else {
                monthsField.style.display = 'block';
            }
        });
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
</body>
</html>