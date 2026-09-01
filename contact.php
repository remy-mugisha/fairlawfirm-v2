<?php require_once __DIR__ . '/csrf.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Contact Fair Law Firm LTD for professional legal counsel and property management services in Rwanda.">
    <?php require_once 'include/header.php'; ?>
    <title><?= __('Contact') ?> - Fair Law Firm LTD</title>
    <style>
        .fl-contact {
            padding: var(--fl-sp-8) 0;
        }

        .fl-contact__grid {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: var(--fl-sp-6);
            align-items: start;
        }

        .fl-contact__sidebar {}

        .fl-contact__info-card {
            background: var(--fl-surface);
            border: 1px solid var(--fl-ink-100);
            border-radius: var(--fl-r-md);
            padding: var(--fl-sp-5);
            margin-bottom: var(--fl-sp-4);
        }

        .fl-contact__info-title {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-h2);
            font-weight: 600;
            color: var(--fl-chambers-900);
            margin-bottom: var(--fl-sp-4);
        }

        .fl-contact__info-item {
            display: flex;
            align-items: flex-start;
            gap: var(--fl-sp-2);
            padding: var(--fl-sp-2) 0;
            border-bottom: 1px solid var(--fl-ink-50);
        }

        .fl-contact__info-item:last-child {
            border-bottom: none;
        }

        .fl-contact__info-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--fl-chambers-100);
            color: var(--fl-chambers-600);
            border-radius: var(--fl-r-md);
            flex-shrink: 0;
        }

        .fl-contact__info-label {
            font-size: var(--fl-text-body-sm);
            color: var(--fl-gray-400);
            margin-bottom: var(--fl-sp-1);
        }

        .fl-contact__info-value {
            font-size: var(--fl-text-body);
            color: var(--fl-chambers-900);
            font-weight: 500;
        }

        .fl-contact__info-value a {
            color: var(--fl-chambers-900);
            text-decoration: none;
        }

        .fl-contact__info-value a:hover {
            color: var(--fl-chambers-600);
        }

        .fl-contact__form-card {
            background: var(--fl-surface);
            border: 1px solid var(--fl-ink-100);
            border-radius: var(--fl-r-md);
            padding: var(--fl-sp-5);
        }

        .fl-contact__form-title {
            font-family: var(--fl-font-display);
            font-size: var(--fl-text-h2);
            font-weight: 600;
            color: var(--fl-chambers-900);
            margin-bottom: var(--fl-sp-4);
        }

        .fl-contact__form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--fl-sp-3);
        }

        .fl-contact__map {
            margin-top: var(--fl-sp-8);
        }

        .fl-contact__map iframe {
            width: 100%;
            height: 400px;
            border: none;
            border-radius: var(--fl-r-md);
        }

        .fl-input,
        .fl-textarea {
            font-family: var(--fl-font-body);
            border-radius: var(--fl-r-sm);
        }

        .fl-input:focus,
        .fl-textarea:focus {
            box-shadow: 0 0 0 2px var(--fl-chambers-400);
            border-color: var(--fl-chambers-400);
        }

        @media (max-width: 992px) {
            .fl-contact__grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .fl-contact__form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

    <!-- Page Header -->
    <section class="fl-page-header">
        <div class="fl-page-header__bg" style="background-image: url(assets/images/backgrounds/bkground_1.jpg);"></div>
        <div class="fl-container">
            <div class="fl-page-header__content">
                <h1 class="fl-page-header__title"><?= __('Contact Us') ?></h1>
                <nav class="fl-page-header__breadcrumb">
                    <a href="index.php"><?= __('Home') ?></a>
                    <span>/</span>
                    <span><?= __('Contact') ?></span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="fl-contact">
        <div class="fl-container">
            <div class="fl-contact__grid">
                <!-- Sidebar -->
                <div class="fl-contact__sidebar">
                    <div class="fl-contact__info-card">
                        <h3 class="fl-contact__info-title"><?= __('Contact Information') ?></h3>
                        <div class="fl-contact__info-item">
                            <div class="fl-contact__info-icon"><i class="fa fa-phone"></i></div>
                            <div>
                                <div class="fl-contact__info-label"><?= __('Phone') ?></div>
                                <div class="fl-contact__info-value">
                                    <a href="tel:+250788411095">+250 788 411 095</a><br>
                                    <a href="tel:+250784183352">+250 784 183 352</a>
                                </div>
                            </div>
                        </div>
                        <div class="fl-contact__info-item">
                            <div class="fl-contact__info-icon"><i class="fa fa-envelope"></i></div>
                            <div>
                                <div class="fl-contact__info-label"><?= __('Email') ?></div>
                                <div class="fl-contact__info-value"><a href="mailto:fairlawfirmltd@gmail.com">fairlawfirmltd@gmail.com</a></div>
                            </div>
                        </div>
                        <div class="fl-contact__info-item">
                            <div class="fl-contact__info-icon"><i class="fa fa-map-marker-alt"></i></div>
                            <div>
                                <div class="fl-contact__info-label"><?= __('Address') ?></div>
                                <div class="fl-contact__info-value">KG 194 St, Kigali<br>Kimironko, Near BPR Branch</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="fl-contact__form-card">
                    <h3 class="fl-contact__form-title"><?= __('Send A Message') ?></h3>
                    <form method="POST" action="contactEmail.php" enctype="multipart/form-data">
                        <?php echo csrfHiddenField(); ?>
                        <div class="fl-contact__form-row">
                            <div class="fl-form-group">
                                <label class="fl-label" for="name"><?= __('Full Name') ?></label>
                                <input type="text" name="name" id="name" class="fl-input" placeholder="<?= __('Your name') ?>" required>
                            </div>
                            <div class="fl-form-group">
                                <label class="fl-label" for="email"><?= __('Email Address') ?></label>
                                <input type="email" name="email" id="email" class="fl-input" placeholder="<?= __('Your email') ?>" required>
                            </div>
                        </div>
                        <div class="fl-contact__form-row">
                            <div class="fl-form-group">
                                <label class="fl-label" for="phone"><?= __('Phone') ?></label>
                                <input type="text" name="phone" id="phone" class="fl-input" placeholder="<?= __('Your phone') ?>">
                            </div>
                            <div class="fl-form-group">
                                <label class="fl-label" for="subject"><?= __('Subject') ?></label>
                                <input type="text" name="subject" id="subject" class="fl-input" placeholder="<?= __('Subject') ?>">
                            </div>
                        </div>
                        <div class="fl-form-group">
                            <label class="fl-label" for="message"><?= __('Message') ?></label>
                            <textarea name="message" id="message" class="fl-textarea" rows="6" placeholder="<?= __('Write your message') ?>" required></textarea>
                        </div>
                        <button type="submit" name="submit" class="fl-btn fl-btn--primary fl-btn--lg"><?= __('Send Message') ?></button>
                    </form>
                </div>
            </div>

            <!-- Google Map -->
            <div class="fl-contact__map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3987.5144093392646!2d30.05627337396137!3d-1.9472190980351214!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19dca592e923eef3%3A0x22109f0f703e6e8d!2sMC%20Fantastic%20Technology%20Ltd!5e0!3m2!1sen!2srw!4v1718208135680!5m2!1sen!2srw" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>

<?php require_once 'include/footer.php'; ?>
