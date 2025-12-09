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
$subject = "Contact Us" . ' - ' . $sitename; 

// Success Messages
$msg_success = "We have <strong>successfully</strong> received your message. We'll get back to you soon.";

if( $_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST["contact-email"]) && $_POST["contact-email"] != '' && isset($_POST["contact-name"]) && $_POST["contact-name"] != '') {
		// Form Fields
		$cf_email = $_POST["contact-email"];
		$cf_name = $_POST["contact-name"];
		$cf_phone = isset($_POST["contact-phone"]) ? $_POST["contact-phone"] : '';
		$cf_pcell = isset($_POST["contact-cell"]) ? $_POST["contact-cell"] : '';
		$cf_service = isset($_POST["contact-service"]) ? $_POST["contact-service"] : '';
		$cf_address = isset($_POST["contact-address"]) ? $_POST["contact-address"] : '';
		$cf_ctstate = isset($_POST["contact-citystate"]) ? $_POST["contact-citystate"] : '';
		$cf_bsttime = isset($_POST["contact-besttime"]) ? $_POST["contact-besttime"] : '';
		$cf_message = isset($_POST["contact-message"]) ? $_POST["contact-message"] : '';

		$honeypot 	= isset($_POST["form-anti-honeypot"]) ? $_POST["form-anti-honeypot"] : '';
		$bodymsg = '';
		
		if ($honeypot == '' && !(empty($emailTO))) {
			$mail->IsHTML(true);
			$mail->CharSet = 'UTF-8';

			$mail->From = $cf_email;
			$mail->FromName = $cf_name . ' - ' . $sitename;
			$mail->AddReplyTo($cf_email, $cf_name);
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
			$bodymsg .= isset($cf_name) ? "Contact Name: $cf_name<br><br>" : '';
			$bodymsg .= isset($cf_email) ? "Contact Email: $cf_email<br><br>" : '';
			$bodymsg .= (isset($cf_phone) && $cf_phone !='') ? "Phone Number: $cf_phone<br><br>" : '';
			$bodymsg .= (isset($cf_pcell) && $cf_pcell !='') ? "Cell Number: $cf_pcell<br><br>" : '';
			$bodymsg .= (isset($cf_bsttime) && $cf_bsttime !='') ? "Time to Reach: $cf_bsttime<br><br>" : '';
			
			$bodymsg .= (isset($cf_address) && $cf_address !='') ? "<br>Address: $cf_address<br><br>" : '';
			$bodymsg .= (isset($cf_ctstate) && $cf_ctstate !='') ? "City, State, Zip: $cf_ctstate<br><br>" : '';
			
			$bodymsg .= (isset($cf_service) && $cf_service !='') ? "<br>Service Interested: $cf_service<br><br>" : '';
			$bodymsg .= (isset($cf_message) && $cf_message !='') ? "<br>Message: $cf_message<br><br>" : '';
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
			echo json_encode(array ('result' => "error", 'message' => "Bot <strong>Detected</strong>.! Clean yourself Botster.!"));
		}
	} else {
		echo json_encode(array ('result' => "error", 'message' => "Please <strong>Fill up</strong> all required fields and try again."));
	}
}