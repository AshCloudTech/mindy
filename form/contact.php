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
$mail->Password   = 'mhiz lzpi rvqc ftia'; 
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Port = 465;


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
		empty($_POST['contact-message'])||
		empty($_POST['contact-phone'])||
		empty($_POST['pincode'])||
		empty($_POST['address'])||
		empty($_POST['service_type'])

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
	$cf_address = $_POST['address'] ?? '';
	$cf_pincode    = $_POST['pincode'] ?? '';
	$cf_service = $_POST['service_type'] ?? '';
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
	if ($cf_pincode)    $body .= "<strong>Pincode</strong> {$cf_pincode}<br><br>";
	if ($cf_address) $body .= "<strong>Address:</strong> {$cf_address}<br><br>";
	if ($cf_service) $body .= "<strong>Service:</strong> {$cf_service}<br><br>";

	$body .= "<strong>Message:</strong><br>{$cf_message}<br><br>";

	if (!empty($_SERVER['HTTP_REFERER'])) {
		$body .= "<hr>Sent from: {$_SERVER['HTTP_REFERER']}";
	}

	$mail->Body = $body;

	// =====================
	// SEND EMAIL
	// =====================
	$mail->SMTPDebug = 2;
$mail->Debugoutput = 'error_log';
$mail->SMTPOptions = [
    'socket' => [
        'bindto' => '0.0.0.0:0'
    ]
];
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
