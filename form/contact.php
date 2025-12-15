<?php
header('Content-Type: application/json');


error_reporting(E_ALL);
ini_set('display_errors', 1);

// =====================
// PHPMailer v6 LOAD
// =====================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/php-mailer/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/php-mailer/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/php-mailer/PHPMailer-master/src/SMTP.php';

$mail = new PHPMailer(true);

// =====================
// SITE SETTINGS
// =====================
$sitename = 'minty';
$adminEmail = 'syedaummehani.m@gmail.com';

// =====================
// SMTP CONFIG (GMAIL)
// =====================
$mail->isSMTP();
$mail->Host       = 'smtp.gmail.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'syedaummehani.m@gmail.com';
$mail->Password   = 'kzmy miqw hhay yuwd'; //
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port       = 587;

$mail->isHTML(true);
$mail->CharSet = 'UTF-8';

// =====================
// RESPONSE MESSAGE
// =====================
$msg_success = "We have <strong>successfully</strong> received your message. We'll get back to you soon.";

// =====================
// HANDLE FORM
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	if (
		empty($_POST['contact-name']) ||
		empty($_POST['contact-email']) ||
		empty($_POST['contact-message'])
	) {
		echo json_encode([
			'result' => 'error',
			'message' => 'Please <strong>fill in</strong> all required fields.'
		]);
		exit;
	}

	// Honeypot check
	if (!empty($_POST['form-anti-honeypot'])) {
		echo json_encode([
			'result' => 'error',
			'message' => 'Bot detected.'
		]);
		exit;
	}

	// =====================
	// FORM DATA
	// =====================
	$cf_name    = strip_tags($_POST['contact-name']);
	$cf_email   = filter_var($_POST['contact-email'], FILTER_SANITIZE_EMAIL);
	$cf_phone   = $_POST['contact-phone'] ?? '';
	$cf_cell    = $_POST['contact-cell'] ?? '';
	$cf_address = $_POST['contact-address'] ?? '';
	$cf_city    = $_POST['contact-citystate'] ?? '';
	$cf_service = $_POST['contact-service'] ?? '';
	$cf_time    = $_POST['contact-besttime'] ?? '';
	$cf_message = nl2br(htmlspecialchars($_POST['contact-message']));

	// =====================
	// EMAIL SETUP
	// =====================
	$mail->setFrom($adminEmail, $sitename);
	$mail->addReplyTo($cf_email, $cf_name);
	$mail->addAddress($adminEmail);

	$mail->Subject = "Contact Us - $sitename";

	// =====================
	// EMAIL BODY
	// =====================
	$body = "
        <strong>Name:</strong> {$cf_name}<br><br>
        <strong>Email:</strong> {$cf_email}<br><br>
    ";

	if ($cf_phone)   $body .= "<strong>Phone:</strong> {$cf_phone}<br><br>";
	if ($cf_cell)    $body .= "<strong>Cell:</strong> {$cf_cell}<br><br>";
	if ($cf_time)    $body .= "<strong>Best Time:</strong> {$cf_time}<br><br>";
	if ($cf_address) $body .= "<strong>Address:</strong> {$cf_address}<br><br>";
	if ($cf_city)    $body .= "<strong>City/State/Zip:</strong> {$cf_city}<br><br>";
	if ($cf_service) $body .= "<strong>Service:</strong> {$cf_service}<br><br>";

	$body .= "<strong>Message:</strong><br>{$cf_message}<br><br>";

	if (!empty($_SERVER['HTTP_REFERER'])) {
		$body .= "<hr>Sent from: {$_SERVER['HTTP_REFERER']}";
	}

	$mail->Body = $body;

	// =====================
	// SEND EMAIL
	// =====================
	try {
		$mail->send();
		echo json_encode([
			'result' => 'success',
			'message' => $msg_success
		]);
	} catch (Exception $e) {
		echo json_encode([
			'result' => 'error',
			'message' => $mail->ErrorInfo
		]);
	}
}
