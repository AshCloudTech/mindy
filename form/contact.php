<?php
header("Content-Type: application/json; charset=UTF-8");
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 
require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}
 
/* ======================
   Sanitize & Validate
====================== */
$name    = trim($_POST['contact-name'] ?? '');
$phone   = trim($_POST['contact-phone'] ?? '');
$email   = trim($_POST['contact-email'] ?? '');
$pincode = trim($_POST['pincode'] ?? '');
$service = trim($_POST['service_type'] ?? '');
$address = trim($_POST['address'] ?? '');
$message = trim($_POST['contact-message'] ?? '');
 
if (!$name || !$phone || !$email || !$service || !$message) {
    echo json_encode([
        'status' => 'error',
        'message' => 'All required fields must be filled'
    ]);
    exit;
}
 
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email address'
    ]);
    exit;
}
 
$e = fn($v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
 
/* ======================
   Send Email
====================== */
try {
    $mail = new PHPMailer(true);
 
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'syedaummehani.m@gmail.com';
 
    $mail->Password   = 'izyanagpjdelnnrl';
 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
 
    $mail->setFrom('syedaummehani.m@gmail.com', 'Website Contact');
    $mail->addAddress('syedaummehani.m@gmail.com');
 
    // Reply goes to user
    $mail->addReplyTo($email, $name);
 
    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission';
 
    $mail->Body = "
<strong>Name:</strong> {$e($name)}<br>
<strong>Phone:</strong> {$e($phone)}<br>
<strong>Email:</strong> {$e($email)}<br>
<strong>Pincode:</strong> {$e($pincode)}<br>
<strong>Service:</strong> {$e($service)}<br>
<strong>Address:</strong> {$e($address)}<br><br>
<strong>Message:</strong><br>{$e($message)}
    ";
 
    $mail->send();
 
    echo json_encode([
        'status' => 'success',
        'message' => 'Thank you! Your message has been sent successfully.'
    ]);
    exit;
 
} catch (Exception $ex) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Mail failed. Please try again later.'
    ]);
    exit;
}