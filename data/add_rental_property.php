<?php
ob_start();
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (isset($_POST['submit'])) {
    // Retrieve form data
    $description = $_POST['description'];
    $title = $_POST['title'];
    $property_status = $_POST['property_status'];
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
    $floor = ($property_type === 'Commercial Building') ? implode(', ', $_POST['floor']) : null;
    $months = ($property_status === 'For Sale') ? null : $_POST['months'];

    // Insert data into the database (removed image field)
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Rental Property</title>
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
                        <h2>Add Rental Property</h2>
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
                                <form class="form-horizontal" action="add_rental_property.php" method="post">
                                    <!-- Title -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Title</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="title" placeholder="Enter property title" required>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Description</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="description" rows="4" placeholder="Enter property description" required></textarea>
                                        </div>
                                    </div>

                                    <!-- Property Status -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Property Status</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="property_status" id="property_status" required>
                                                <option value="">Select Status</option>
                                                <option value="For Rent">For Rent</option>
                                                <option value="For Sale">For Sale</option>
                                                <option value="Not Available">Not Available</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Months Dropdown -->
                                    <div class="form-group row" id="monthsField">
                                        <label class="control-label col-sm-3">Select Months</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="months">
                                                <option value="">Select Months</option>
                                                <option value="1">1 Month</option>
                                                <option value="2">2 Months</option>
                                                <option value="3">3 Months</option>
                                                <option value="4">4 Months</option>
                                                <option value="5">5 Months</option>
                                                <option value="6">6 Months</option>
                                                <option value="7">7 Months</option>
                                                <option value="8">8 Months</option>
                                                <option value="9">9 Months</option>
                                                <option value="10">10 Months</option>
                                                <option value="11">11 Months</option>
                                                <option value="12">12 Months</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Property Type -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Property Type</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="property_type" id="property_type" required>
                                                <option value="">Select Type</option>
                                                <option value="Apartment">Apartment</option>
                                                <option value="House">House</option>
                                                <option value="Commercial Building">Commercial Building</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Floor (Only for Commercial Building) -->
                                    <div class="form-group row" id="floorField" style="display: none;">
                                        <label class="control-label col-sm-3">Floors</label>
                                        <div class="col-sm-9">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="Ground Floor" id="groundFloor">
                                                <label class="form-check-label" for="groundFloor">Ground Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="1st Floor" id="firstFloor">
                                                <label class="form-check-label" for="firstFloor">1st Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="2nd Floor" id="secondFloor">
                                                <label class="form-check-label" for="secondFloor">2nd Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="3rd Floor" id="thirdFloor">
                                                <label class="form-check-label" for="thirdFloor">3rd Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="4th Floor" id="fourthFloor">
                                                <label class="form-check-label" for="fourthFloor">4th Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="5th Floor" id="fifthFloor">
                                                <label class="form-check-label" for="fifthFloor">5th Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="6th Floor" id="sixthFloor">
                                                <label class="form-check-label" for="sixthFloor">6th Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="7th Floor" id="seventhFloor">
                                                <label class="form-check-label" for="seventhFloor">7th Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="8th Floor" id="eighthFloor">
                                                <label class="form-check-label" for="eighthFloor">8th Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="9th Floor" id="ninthFloor">
                                                <label class="form-check-label" for="ninthFloor">9th Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="10th Floor" id="tenthFloor">
                                                <label class="form-check-label" for="tenthFloor">10th Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="11th Floor" id="eleventhFloor">
                                                <label class="form-check-label" for="eleventhFloor">11th Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="12th Floor" id="twelfthFloor">
                                                <label class="form-check-label" for="twelfthFloor">12th Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="13th Floor" id="thirteenthFloor">
                                                <label class="form-check-label" for="thirteenthFloor">13th Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="14th Floor" id="fourteenthFloor">
                                                <label class="form-check-label" for="fourteenthFloor">14th Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="floor[]" value="15th Floor" id="fifteenthFloor">
                                                <label class="form-check-label" for="fifteenthFloor">15th Floor</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Price</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="price" id="price" 
                                            placeholder="Price" required>                                        
                                        </div>
                                    </div>

                                    <!-- Property Size -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Property Size</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="property_size" placeholder="Enter size (sq ft)" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">sq ft</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bedroom (Hidden for Commercial Building) -->
                                    <div class="form-group row" id="bedroomField">
                                        <label class="control-label col-sm-3">Bedrooms</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control" name="bedroom" min="0" placeholder="Number of bedrooms" required>
                                        </div>
                                    </div>

                                    <!-- Bathroom (Hidden for Commercial Building) -->
                                    <div class="form-group row" id="bathroomField">
                                        <label class="control-label col-sm-3">Bathrooms</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control" name="bathroom" min="0" placeholder="Number of bathrooms" required>
                                        </div>
                                    </div>

                                    <!-- Street -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Street</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="street" placeholder="Enter street" required>
                                        </div>
                                    </div>

                                    <!-- Sector -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Sector</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="sector" placeholder="Enter sector">
                                        </div>
                                    </div>

                                    <!-- District -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">District</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="district" placeholder="Enter district">
                                        </div>
                                    </div>

                                    <!-- Country -->
                                    <div class="form-group row">
                                        <label class="control-label col-sm-3">Country</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="country" placeholder="Enter country" required>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="form-group row">
                                        <div class="col-sm-9 offset-sm-3">
                                            <button type="submit" name="submit" class="btn btn-info">Add Property</button>
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