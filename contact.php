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

<body>
    
        <section class="page-header">
        <!-- <div class="page-header__bg" style="background-color: #1a2f24"></div> -->
            <div class="page-header__bg" style="background-image: url(assets/images/backgrounds/wallpaper2.jpg);"></div>
            <div class="container">
                <h2 class="page-header__title"><?= __('Contact')?></h2>
                <ul class="firdip-breadcrumb list-unstyled">
                    <li><a href="index.php"><?= __('Home')?></a></li>
                    <li><span><?= __('Contact')?></span></li>
                </ul>
            </div>
        </section>


        <section class="blog-one blog-one--page">
            <div class="container">
                <div class="row gutter-y-60">
                    <div class="col-lg-4">
                        <div class="sidebar">
                            <!-- <aside class="areawidget"> -->

                                <div class="sidebar__single wow fadeInUp" data-wow-delay='300ms'>
                                    <h4 class="sidebar__title"><?= __('Contact Information')?></h4>
                                    <ul class="sidebar__comments list-unstyled">
                                        <li class="sidebar__comments__item">
                                            <div class="sidebar__comments__icon"> <i class="icon-telephone-call-1"></i></div>
                                            <h6 class="sidebar__comments__title">
                                            <a href="tel:+92-3800-8060" class="footer-widget__contact__text">+250788411095</a>
                                            <a href="tel:+21-9555-0114" class="footer-widget__contact__text">+250784183352</a>
                                            </h6>
                                        </li>
                                        <li class="sidebar__comments__item">
                                            <div class="sidebar__comments__icon"> <i class="icon-message topbar-one__info__icon"></i> </div>
                                            <h6 class="sidebar__comments__title">
                                            <a href="" class="footer-widget__contact__text">fairlawfirmltd@gmail.com</a>
                                            </h6>
                                        </li>
                                        <li class="sidebar__comments__item">
                                            <div class="sidebar__comments__icon"> <i class="icon-glove"></i> </div>
                                            <h6 class="sidebar__comments__title">
                                            <a href="" class="footer-widget__contact__text">www.website.com</a>
                                            </h6>
                                        </li>
                                        <li class="sidebar__comments__item">
                                            <div class="sidebar__comments__icon"> <i class="icon-pin2"></i> </div>
                                            <h6 class="sidebar__comments__title">
                                            <p class="footer-widget__contact__text" style="color: #15283c;">KG 194 St, Kigali <br> Kimironko Near bpr Branch</p>
                                            </h6>
                                        </li>
                                    </ul>
                                </div>

                            <!-- </aside> -->
                        </div>
                    </div><!-- /.col-lg-4 -->
                    <div class="col-lg-8">
                        

                        <div class="comments-form">
                            <h4 class="sidebar__title"><?= __('Send A Message')?></h4>
                            <form  method="POST" action="contactEmail.php" class="form-one "
                             data-wow-delay='300ms' enctype="multipart/form-data">
                                <div class="form-one__group">
                                    <div class="form-one__control">
                                        <input type="text" name="name" id="name" placeholder="<?= __('Your Name')?>">
                                    </div>
                                
                                    <div class="form-one__control">
                                        <input type="email" name="email" id="email" placeholder="<?= __('Email Address')?>">
                                    </div>
                                    <div class="form-one__control">
                                        <input type="text" name="phone" id="phone" placeholder="<?= __('Phone')?>">
                                    </div>
                                    <div class="form-one__control">
                                        <input type="text" name="subject" id="subject" placeholder="<?= __('Subject')?>">
                                    </div>
                                    <div class="form-one__control form-one__control--full">
                                        <textarea name="message" id="message" placeholder="<?= __('Write a Message')?>"></textarea>
                                    </div>
                                    <div class="form-one__control">
                                    <button type="submit" name="submit" style="color: white; font-size: 12px;padding: 10px 40px 10px 40px"><?= __('Sent Now')?></button>                                    </div>
                                </div>
                            </form>
                            <div class="result"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section end -->
        <section class="contact-bottom">
            <div class="container">
        
        <div class="google-map google-map__contact">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3987.5144093392646!2d30.05627337396137!3d-1.9472190980351214!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19dca592e923eef3%3A0x22109f0f703e6e8d!2sMC%20Fantastic%20Technology%20Ltd!5e0!3m2!1sen!2srw!4v1718208135680!5m2!1sen!2srw" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        </div>
        </section>



</body>
<?php 
        require_once 'include/footer.php';
        ?>
</html>