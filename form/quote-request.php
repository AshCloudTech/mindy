<?php
header('Content-type: application/json');
require_once('php-mailer/PHPMailerAutoload.php'); // Include PHPMailer

$mail = new PHPMailer();
$emailTO = $emailBCC =  $emailCC = array();

// Enter Your Sitename 
$sitename = 'Your Site Name';

// Enter your email addresses: @required
$emailTO[] = array( 'email' => 'email@yoursite.com', 'name' => 'Your Name' ); 

// Enable bellow parameters & update your BCC email if require.
//$emailBCC[] = array( 'email' => 'email@yoursite.com', 'name' => 'Your Name' );

// Enable bellow parameters & update your CC email if require.
//$emailCC[] = array( 'email' => 'email@yoursite.com', 'name' => 'Your Name' );

// Enter Email Subject
$subject = "Request Service" . ' - ' . $sitename; 

// Success Messages
$msg_success = "We have <strong>successfully</strong> received your request. We'll get back to you soon.";

if( $_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST["quote-request-email"]) && $_POST["quote-request-email"] != '' && isset($_POST["quote-request-name"]) && $_POST["quote-request-name"] != '') {
		// Form Fields
		$qr_email	= $_POST["quote-request-email"];
		$qr_name	= $_POST["quote-request-name"];

		$qr_phone	= isset($_POST["quote-request-phone"]) ? $_POST["quote-request-phone"] : '';
		$qr_company	= isset($_POST["quote-request-company"]) ? $_POST["quote-request-company"] : '';
		
		$qr_address	= isset($_POST["quote-request-address"]) ? $_POST["quote-request-address"] : '';
		$qr_ctstate	= isset($_POST["quote-request-citystate"]) ? $_POST["quote-request-citystate"] : '';
		
		$qr_reach	= isset($_POST["quote-request-reach"]) ? $_POST["quote-request-reach"] : '';
		$qr_hear	= isset($_POST["quote-request-hear"]) ? $_POST["quote-request-hear"] : '';

		$qr_interest = isset($_POST["quote-request-interest"]) ? $_POST["quote-request-interest"] : '';
		$qr_interested = '';
		if (is_array($qr_interest)) {
			foreach ($qr_interest as $interest) {
				$qr_interested .= ', '.$interest;
			}
		} else {
			$qr_interested = $qr_interest;
		}
		$qr_interested = ($qr_interested !='') ? substr($qr_interested, 2) : '';

		$qr_message	= isset($_POST["quote-request-message"]) ? $_POST["quote-request-message"] : '';

		$honeypot 	= isset($_POST["form-anti-honeypot"]) ? $_POST["form-anti-honeypot"] : '';
		$bodymsg	= '';
		
		if ($honeypot == '' && !(empty($emailTO))) {
			$mail->IsHTML(true);
			$mail->CharSet = 'UTF-8';

			$mail->From = $qr_email;
			$mail->FromName = $qr_name . ' - ' . $sitename;
			$mail->AddReplyTo($qr_email);
			$mail->Subject = $subject;
			
			foreach( $emailTO as $to ) {
				$mail->AddAddress( $to['email'] , $to['name'] );
			}
			
			// if CC found
			if (!empty($emailCC)) {
				foreach( $emailCC as $cc ) {
					$mail->AddCC( $cc['email'] , $cc['name'] );
				}
			}
			
			// if BCC found
			if (!empty($emailBCC)) {
				foreach( $emailBCC as $bcc ) {
					$mail->AddBCC( $bcc['email'] , $bcc['name'] );
				}				
			}

			// Include Form Fields into Body Message
			$bodymsg .= isset($qr_name) ? "Name: $qr_name<br><br>" : '';
			$bodymsg .= (isset($qr_company) && $qr_company !='') ? "Company: $qr_company<br><br>" : '';
			$bodymsg .= isset($qr_email) ? "Email: $qr_email<br><br>" : '';
			$bodymsg .= (isset($qr_phone) && $qr_phone !='') ? "Phone: $qr_phone<br><br>" : '';
			$bodymsg .= (isset($qr_reach) && $qr_reach !='') ? "Time to Reach: $qr_reach<br><br>" : '';
			
			$bodymsg .= (isset($qr_address) && $qr_address !='') ? "<br>Address: $qr_address<br><br>" : '';
			$bodymsg .= (isset($qr_ctstate) && $qr_ctstate !='') ? "City, State, Zip: $qr_ctstate<br><br>" : '';
			
			$bodymsg .= (isset($qr_hear) && $qr_hear !='') ? "<br>Hear From: $qr_hear<br><br>" : '';
			
			$bodymsg .= (isset($qr_interested) && $qr_interested !='') ? "<br>Services Interested: $qr_interested<br><br>" : '';
			$bodymsg .= (isset($qr_message) && $qr_message !='') ? "<br>Messages: $qr_message<br><br>" : '';
			$bodymsg .= $_SERVER['HTTP_REFERER'] ? '<br>---<br><br>This email was sent from: ' . $_SERVER['HTTP_REFERER'] : '';
			
			$mail->MsgHTML( $bodymsg );
			$is_emailed = $mail->Send();

			if( $is_emailed === true ) {
				$response = array ('result' => "success", 'message' => $msg_success);
			} else {
				$response = array ('result' => "error", 'message' => $mail->ErrorInfo);
			}
			echo json_encode($response);
			
		} else {
			echo json_encode(array ('result' => "error", 'message' => "Bot <strong>Detected</strong>! Please try later."));
		}
	} else {
		echo json_encode(array ('result' => "error", 'message' => "Please <strong>Fill up</strong> all required fields and try again."));
	}
}