<?php

header("Content-Type: application/json; charset=UTF-8");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/php-mailer/PHPMailer-master/src/Exception.php';
require __DIR__ . '/php-mailer/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/php-mailer/PHPMailer-master/src/SMTP.php';

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

try {
	$mail = new PHPMailer(true);

	$mail->isSMTP();
	$mail->Host       = 'smtp.gmail.com';
	$mail->SMTPAuth   = true;
	$mail->Username   = 'syedaummehani.m@gmail.com';
	$mail->Password   = 'keak xxsm gkpy teyq';
	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
	$mail->Port       = 587;

	$mail->setFrom('syedaummehani.m@gmail.com', 'Website Contact');
	$mail->addAddress('syedaummehani.m@gmail.com');

	$mail->isHTML(true);
	$mail->Subject = 'New Contact Form Submission';
	$mail->Body = "
        <strong>Name:</strong> $name <br>
        <strong>Phone:</strong> $phone <br>
        <strong>Email:</strong> $email <br>
        <strong>Pincode:</strong> $pincode <br>
        <strong>Service:</strong> $service <br>
        <strong>Address:</strong> $address <br><br>
        <strong>Message:</strong><br>$message
    ";

	$mail->send();
	header("Location: /mindy/contact.html?status=success");
	exit;
} catch (Exception $e) {
	header("Location: /mindy/contact.html?status=error");
	exit;
}
