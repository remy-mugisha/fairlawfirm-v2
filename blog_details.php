<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="bracket-web">
    <meta name="description" content="Firdip is beautifully designed Figma template especially for the fire department, fireman, fire prevention, fire fighting, fire station, protection, firefighter and all other fire & safety business and websites.">

    <?php require_once 'include/header.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Custom styles for image gallery */
        .blog-image-container {
            position: relative;
            margin-bottom: 30px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .main-blog-image {
            width: 100%;
            height: 450px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .blog-image-container:hover .main-blog-image {
            transform: scale(1.02);
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
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .gallery-image:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        
        .img-thumbnail {
            border: 3px solid #fff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            margin: 10px;
            transition: all 0.3s ease;
            max-width: 100%;
        }
        
        .img-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .float-image {
            max-width: 45%;
            margin: 15px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        
        .float-start {
            float: left;
        }
        
        .float-end {
            float: right;
        }
        
        @media (max-width: 768px) {
            .main-blog-image {
                height: 300px;
            }
            
            .image-gallery {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .float-image {
                max-width: 100%;
                float: none !important;
                margin: 10px 0;
            }
        }
        
        /* Attachment styling */
        .attachment-item {
            border-left: 3px solid #f48029;
            transition: all 0.3s ease;
        }
        
        .attachment-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
    </style>
</head>

<body class="custom-cursor">
    <?php 
    require 'data/propertyMgt/config.php';

    // Validate and sanitize ID
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($id > 0) {
        // Get blog post
        $stmt = $conn->prepare("SELECT * FROM blog WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $blog = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get attachments
        $stmt = $conn->prepare("SELECT * FROM blog_attachments WHERE blog_id = :blog_id");
        $stmt->bindParam(':blog_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($blog) {
    ?>
            <!-- Page Header Section -->
            <section class="page-header">
                <div class="page-header__bg" style="background-image: url(data/propertyMgt/blogImg/<?php echo htmlspecialchars($blog['image']); ?>);"></div>
                <div class="container">
                    <h2 class="page-header__title"><?php echo htmlspecialchars($blog['title']); ?></h2>
                    <ul class="firdip-breadcrumb list-unstyled">
                        <li><a href="index.php"><?= __('Home') ?></a></li>
                        <li><span><?= __('Blog') ?></span></li>
                    </ul>
                </div>
            </section>

            <!-- Blog Content Section -->
            <section class="blog-one blog-one--page">
                <div class="container">
                    <div class="row gutter-y-60">
                        <div class="col-lg-8">
                            <div class="blog-details">
                                <div class="blog-card__two">
                                    <!-- Main Featured Image -->
                                    <div class="blog-image-container">
                                        <img src="data/propertyMgt/blogImg/<?php echo htmlspecialchars($blog['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                                             class="main-blog-image">
                                    </div>
                                    
                                    <!-- Blog Content -->
                                    <div class="blog-card-two__content">
                                        <p class="blog-card__two__text"><?php echo nl2br(htmlspecialchars($blog['blog_description_details'])); ?></p>
                                    </div>
                                    
                                    <?php if (!empty($attachments)): ?>
                                    <!-- Attachments Section -->
                                    <div class="blog-attachments mt-5">
                                        <h4 class="sidebar__title">Attachments</h4>
                                        <div class="list-group">
                                            <?php foreach ($attachments as $attachment): ?>
                                                <div class="list-group-item attachment-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <i class="<?php echo getFileIconClass($attachment['file_name']); ?> mr-2 fa-lg"></i>
                                                            <span class="font-weight-bold"><?php echo htmlspecialchars($attachment['file_name']); ?></span>
                                                            <small class="text-muted ml-2">(<?php echo formatFileSize($attachment['file_size']); ?>)</small>
                                                        </div>
                                                        <a href="data/propertyMgt/blogFiles/<?php echo htmlspecialchars($attachment['file_path']); ?>" 
                                                           class="btn btn-sm btn-success" 
                                                           download
                                                           title="Download <?php echo htmlspecialchars($attachment['file_name']); ?>">
                                                            <i class="fas fa-download"></i> 
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="sidebar">
                                <aside class="widget-area">
                                    <div class="sidebar__single wow fadeInUp" data-wow-delay="300ms">
                                        <h4 class="sidebar__title">Category</h4>
                                        <ul class="sidebar__categories list-unstyled">
                                            <li class="sidebar__categories__item">
                                                <a><?php echo htmlspecialchars($blog['category_blog']); ?></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="sidebar__single wow fadeInUp" data-wow-delay="300ms">
                                        <h4 class="sidebar__title">Posted On</h4>
                                        <p><?php echo date('F j, Y', strtotime($blog['date'])); ?></p>
                                    </div>
                                </aside>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
    <?php
        } else {
            echo "<p style='text-align: center; color: red;'>No blog post found!</p>";
        }
    } else {
        echo "<p style='text-align: center; color: red;'>Invalid blog ID!</p>";
    }
    ?>

    <?php 
    function getFileIconClass($filename) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        switch(strtolower($ext)) {
            case 'pdf': return 'far fa-file-pdf text-danger';
            case 'doc':
            case 'docx': return 'far fa-file-word text-primary';
            case 'xls':
            case 'xlsx': return 'far fa-file-excel text-success';
            case 'ppt':
            case 'pptx': return 'far fa-file-powerpoint text-warning';
            case 'zip':
            case 'rar': return 'far fa-file-archive text-secondary';
            case 'txt': return 'far fa-file-alt text-info';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif': return 'far fa-file-image text-info';
            default: return 'far fa-file-alt';
        }
    }

    function formatFileSize($bytes) {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    require_once 'include/footer.php'; 
    ?>
</body>

</html>