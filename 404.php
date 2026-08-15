<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="bracket-web">
    <meta name="description" content="Firdip is beautifully designed Figma template especially for the fire department, fireman, fire prevention, fire fighting, fire station, protection, firefighter and all other fire & safety business and websites.">
    <?php 
    require_once 'include/header.php';
    ?>
</head>

<body class="custom-cursor">

    <div class="page-wrapper">
        <section class="page-header">
        <div class="page-header__bg" style="background-color: #1a2f24"></div>
            <!-- <div class="page-header__bg" style="background-image: url(assets/images/backgrounds/page-header-bg-1-1.jpg);"></div> -->
            <div class="container">
                <h2 class="page-header__title">404 <?= __('Error')?></h2>
                <ul class="firdip-breadcrumb list-unstyled">
                    <li><a href="index.php"><?= __('Home')?></a></li>
                    <li><span>404 <?= __('Error')?></span></li>
                </ul>
            </div>
        </section>

        <section class="error-404">
            <div class="container">
                <h3 class="error-404__sub-title">Oops! <?= __('Page not Found')?></h3>
                <p class="error-404__text"><?= __('The page you are looking for is not exist.')?></p>
                <div class="error-404__btns text-center">
                <a href="index.php"><button type="submit" name="submit" style="color: white; font-size: 12px;padding: 10px 40px 10px 40px; margin-top:10px;"><?= __('back to home')?></button></a> 
                </div>
            </div>
        </section>


    </div><!-- /.page-wrapper -->


</body>
<?php
require_once 'include/footer.php';
?>
</html>