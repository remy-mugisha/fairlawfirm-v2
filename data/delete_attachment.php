<?php
require_once 'propertyMgt/config.php';

if (isset($_GET['id']) && isset($_GET['blog_id'])) {
    $id = intval($_GET['id']);
    $blog_id = intval($_GET['blog_id']);
    
    try {
        // Get attachment info before deletion
        $query = "SELECT file_path FROM blog_attachments WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $attachment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($attachment) {
            // Delete from database
            $query = "DELETE FROM blog_attachments WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                // Delete the file
                $file_path = 'propertyMgt/blogFiles/' . $attachment['file_path'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                
                $_SESSION['success_message'] = "Attachment deleted successfully!";
            } else {
                $_SESSION['error_message'] = "Failed to delete attachment.";
            }
        } else {
            $_SESSION['error_message'] = "Attachment not found.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
    
    header("Location: edit_blog.php?id=" . $blog_id);
    exit();
}

header("Location: display_blog.php");
exit();
?>