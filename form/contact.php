<?php
header("Content-Type: application/json; charset=UTF-8");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
   echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
   exit;
}
$name    = $_POST['contact-name'] ?? '';
$phone   = $_POST['contact-phone'] ?? '';
$email   = $_POST['contact-email'] ?? '';
$pincode = $_POST['pincode'] ?? '';
$service = $_POST['service_type'] ?? '';
$address = $_POST['address'] ?? '';
$message = $_POST['contact-message'] ?? '';
// basic escaping to avoid HTML injection in email body
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
try {
   $mail = new PHPMailer(true);
   $mail->isSMTP();
   $mail->Host       = 'smtp.gmail.com';
   $mail->SMTPAuth   = true;
   $mail->Username   = 'syedaummehani.m@gmail.com';
   $mail->Password   = 'YOUR_APP_PASSWORD_HERE'; // use Gmail App Password
   $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
   $mail->Port       = 587;
   // Optional but helpful:
   // $mail->SMTPDebug  = 2;
   // $mail->Debugoutput = 'error_log';
   $mail->setFrom('syedaummehani.m@gmail.com', 'Website Contact');
   $mail->addAddress('syedaummehani.m@gmail.com');
   // If you want replies to go to the user:
   if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
       $mail->addReplyTo($email, $name ?: $email);
   }
   $mail->isHTML(true);
   $mail->Subject = 'New Contact Form Submission';
   $mail->Body = "
<strong>Name:</strong> {$e($name)} <br>
<strong>Phone:</strong> {$e($phone)} <br>
<strong>Email:</strong> {$e($email)} <br>
<strong>Pincode:</strong> {$e($pincode)} <br>
<strong>Service:</strong> {$e($service)} <br>
<strong>Address:</strong> {$e($address)} <br><br>
<strong>Message:</strong><br>{$e($message)}
   ";
   $mail->send();
   echo json_encode(['status' => 'success', 'message' => 'Message sent successfully']);
   exit;
} catch (Exception $e) {
   echo json_encode(['status' => 'error', 'message' => $mail->ErrorInfo ?: $e->getMessage()]);
   exit;
}