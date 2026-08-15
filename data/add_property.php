<?php
require_once 'propertyMgt/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $location = $_POST['location'];
    $title = $_POST['title'];   
    $targetDir = "propertyMgt/proImg/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . time() . '_' . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
    if (in_array(strtolower($fileType), $allowTypes)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            try {
                $imagePathForDB = time() . '_' . $fileName;
                $stmt = $conn->prepare("INSERT INTO add_property (image, location, title) VALUES (:image, :location, :title)");
                $stmt->bindParam(':image', $imagePathForDB);
                $stmt->bindParam(':location', $location);
                $stmt->bindParam(':title', $title);
                $stmt->execute();
                
                $_SESSION['success_message'] = "Property added successfully!";
                echo "<script>window.location.href = 'manage_property.php';</script>";
                exit();
            } catch(PDOException $e) {
                $_SESSION['error_message'] = "Database Error: " . $e->getMessage();
            }
        } else {
            $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
        }
    } else {
        $_SESSION['error_message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    }
}

if (isset($_SESSION['error_message'])) {
    echo "<script>window.location.href = 'manage_property.php';</script>";
    exit();
}
?>