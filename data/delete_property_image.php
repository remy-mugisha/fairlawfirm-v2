<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if (!isset($_GET['image_id']) || !isset($_GET['property_id'])) {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: display_rental.php");
    exit();
}

$image_id = $_GET['image_id'];
$property_id = $_GET['property_id'];

try {
    // Get image path before deleting
    $query = "SELECT image_path FROM property_images WHERE id = :image_id AND property_id = :property_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
    $stmt->bindParam(':property_id', $property_id, PDO::PARAM_INT);
    $stmt->execute();
    $image = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($image) {
        // Delete from database
        $query = "DELETE FROM property_images WHERE id = :image_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Delete file
        $file_path = 'propertyMgt/propertyImg/' . $image['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        $_SESSION['success_message'] = "Image deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Image not found.";
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Database error: " . $e->getMessage();
}

header("Location: property_details.php?id=" . $property_id);
exit();
?>