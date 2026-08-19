<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page not found - Fair Law Firm LTD">
    <?php require_once 'include/header.php'; ?>
    <title>404 <?= __('Error') ?> - Fair Law Firm LTD</title>
    <style>
        .fl-error {
            padding: var(--fl-space-24) 0;
            text-align: center;
        }

        .fl-error__code {
            font-family: var(--fl-font-heading);
            font-size: clamp(6rem, 12vw, 10rem);
            font-weight: 700;
            color: var(--fl-gray-200);
            line-height: 1;
            margin-bottom: var(--fl-space-4);
        }

        .fl-error__title {
            font-family: var(--fl-font-heading);
            font-size: var(--fl-text-3xl);
            font-weight: 600;
            color: var(--fl-charcoal);
            margin-bottom: var(--fl-space-4);
        }

        .fl-error__text {
            font-size: var(--fl-text-md);
            color: var(--fl-slate);
            margin-bottom: var(--fl-space-8);
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

    <section class="fl-error">
        <div class="fl-container">
            <div class="fl-error__code">404</div>
            <h2 class="fl-error__title"><?= __('Page Not Found') ?></h2>
            <p class="fl-error__text"><?= __('The page you are looking for doesn\'t exist or has been moved.') ?></p>
            <a href="index.php" class="fl-btn fl-btn--primary fl-btn--lg"><?= __('Back to Home') ?></a>
        </div>
    </section>

<?php require_once 'include/footer.php'; ?>
