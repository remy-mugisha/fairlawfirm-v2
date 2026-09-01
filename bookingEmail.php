<?php
session_start();
require_once __DIR__ . '/csrf.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['submit'])) {
    requireCsrfPost();
    require 'composer/vendor/autoload.php';
    require_once __DIR__ . '/env.php';
    loadEnv(__DIR__ . '/.env');
    $mail = new PHPMailer(true);
    
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = env('SMTP_HOST', 'mail.fairlawfirmltd.com');
    $mail->SMTPAuth = true;
    $mail->Username = env('SMTP_USER', 'info@fairlawfirmltd.com');
    $mail->Password = env('SMTP_PASS', '');
    $mail->SMTPSecure = 'ssl';
    $mail->Port = (int) env('SMTP_PORT', 465);
    $mail->setFrom(env('SMTP_USER', 'info@fairlawfirmltd.com'), 'Fair Law Firm LTD');

    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $months = isset($_POST['months']) ? $_POST['months'] : 'N/A';
    $comments = $_POST['comments'];
    $property_id = $_POST['property_id'];

    // Sanitize all user inputs for email HTML
    $name = htmlspecialchars(trim($name), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(trim($email), ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars(trim($phone), ENT_QUOTES, 'UTF-8');
    $months = htmlspecialchars(trim($months), ENT_QUOTES, 'UTF-8');
    $comments = nl2br(htmlspecialchars(trim($comments), ENT_QUOTES, 'UTF-8'));
    $property_id = htmlspecialchars(trim($property_id), ENT_QUOTES, 'UTF-8');
    
    try {
        // Email to Customer (Confirmation)
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Booking Request Received';
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    background-color: #f5f7fa;
                    margin: 0;
                    padding: 0;
                    color: #333;
                    line-height: 1.6;
                }
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: #ffffff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background-color: #1a3e72;
                    padding: 30px 20px;
                    text-align: center;
                }
                .logo {
                    max-width: 180px;
                    height: auto;
                }
                .content {
                    padding: 30px;
                }
                h1 {
                    color: #1a3e72;
                    margin-top: 0;
                    font-size: 24px;
                }
                .booking-details {
                    background: #f8f9fa;
                    border-radius: 6px;
                    padding: 20px;
                    margin: 20px 0;
                }
                .detail-row {
                    display: flex;
                    margin-bottom: 10px;
                }
                .detail-label {
                    font-weight: bold;
                    min-width: 120px;
                    color: #1a3e72;
                }
                .footer {
                    background-color: #f0f2f5;
                    padding: 20px;
                    text-align: center;
                    font-size: 14px;
                    color: #666;
                }
                @media only screen and (max-width: 600px) {
                    .content {
                        padding: 20px;
                    }
                    .detail-row {
                        flex-direction: column;
                    }
                    .detail-label {
                        margin-bottom: 5px;
                    }
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <!-- REPLACE WITH YOUR LOGO URL -->
                    <img src='https://fairlawfirmltd.com/assets/images/logo-white-1.png' alt='Fair Law Firm Logo' class='logo'>
                </div>
                
                <div class='content'>
                    <h1>Booking Confirmation</h1>
                    <p>Dear $name,</p>
                    <p>Thank you for your booking request. We've received the following details:</p>
                    
                    <div class='booking-details'>
                        <div class='detail-row'>
                            <span class='detail-label'>Property ID:</span>
                            <span>$property_id</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Duration:</span>
                            <span>$months Months</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Contact Email:</span>
                            <span>$email</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Contact Phone:</span>
                            <span>$phone</span>
                        </div>
                    </div>
                    
                    <p><strong>Your Message:</strong></p>
                    <p>$comments</p>
                    
                    <p>Our team will review your request and contact you shortly. For immediate assistance, please call us at +250 788 411 095.</p>
                </div>
                
                <div class='footer'>
                    <p>© ".date('Y')." Fair Law Firm LTD. All rights reserved.</p>
                    <p>KG 194 St, Kigali | Kimironko Near BPR Branch</p>
                </div>
            </div>
        </body>
        </html>";

        $mail->send();
        
        // Reset email settings for the owner email
        $mail->clearAddresses();
        $mail->addAddress('fairlawfirmltd@gmail.com');
        $mail->addCC('info@fairlawfirmltd.com');
        $mail->isHTML(true);
        $mail->Subject = 'New Property Booking Request - ID: '.$property_id;
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                /* Same styles as above */
                body {
                    font-family: 'Arial', sans-serif;
                    background-color: #f5f7fa;
                    margin: 0;
                    padding: 0;
                    color: #333;
                    line-height: 1.6;
                }
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: #ffffff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background-color: #d32f2f;
                    padding: 30px 20px;
                    text-align: center;
                }
                .logo {
                    max-width: 180px;
                    height: auto;
                }
                .content {
                    padding: 30px;
                }
                h1 {
                    color: #d32f2f;
                    margin-top: 0;
                    font-size: 24px;
                }
                .booking-details {
                    background: #f8f9fa;
                    border-radius: 6px;
                    padding: 20px;
                    margin: 20px 0;
                }
                .detail-row {
                    display: flex;
                    margin-bottom: 10px;
                }
                .detail-label {
                    font-weight: bold;
                    min-width: 120px;
                    color: #d32f2f;
                }
                .footer {
                    background-color: #f0f2f5;
                    padding: 20px;
                    text-align: center;
                    font-size: 14px;
                    color: #666;
                }
                .urgent {
                    color: #d32f2f;
                    font-weight: bold;
                }
                @media only screen and (max-width: 600px) {
                    .content {
                        padding: 20px;
                    }
                    .detail-row {
                        flex-direction: column;
                    }
                    .detail-label {
                        margin-bottom: 5px;
                    }
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <!-- REPLACE WITH YOUR LOGO URL -->
                    <img src='https://fairlawfirmltd.com/assets/images/logo-white-1.png' alt='Fair Law Firm Logo' class='logo'>
                </div>
                
                <div class='content'>
                    <h1>New Booking Request</h1>
                    <p class='urgent'>Action Required: Please respond within 24 hours</p>
                    
                    <div class='booking-details'>
                        <div class='detail-row'>
                            <span class='detail-label'>Property ID:</span>
                            <span>$property_id</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Customer Name:</span>
                            <span>$name</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Duration:</span>
                            <span>$months Months</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Contact Email:</span>
                            <span>$email</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Contact Phone:</span>
                            <span>$phone</span>
                        </div>
                    </div>
                    
                    <p><strong>Customer Message:</strong></p>
                    <p>$comments</p>
                    
                    <p>Please contact the customer as soon as possible to confirm the booking.</p>
                </div>
                
                <div class='footer'>
                    <p>© ".date('Y')." Fair Law Firm LTD. All rights reserved.</p>
                    <p>This is an automated notification. Do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";

        $mail->send();

        // Display popup and redirect
        echo "<script>
                alert('Thank you! Your booking request has been received.');
                window.location.href = 'property.php';
              </script>";
        exit();

    } catch (Exception $e) {
        echo "<script>
                alert('Error sending your request. Please try again later.');
                window.history.back();
              </script>";
    }
}
?>