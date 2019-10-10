<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/SMTP.php';
include 'exempt.php';  

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$name = trim(filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING));
	$email = trim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL));
	$details = trim(filter_input(INPUT_POST, "details", FILTER_SANITIZE_SPECIAL_CHARS));

	if ($name == "" || $email == "" || $details == "") {
		echo "Please fill in the required fields: Name, Email, and Details";
		exit;
	}

	if ($_POST["validation"] != "") {
		echo "Bad form input";
		exit;
	}

	if (!PHPMailer::validateAddress($email)) {
		echo "Invalid Email Address";
		exit;
	}

	$email_body = "";
	$email_body .= "Name " . $name . "\n";
	$email_body .= "Email " . $email . "\n";
	$email_body .= "Details " . $details . "\n";

	//To Do: Send email
	$mail = new PHPMailer;
        // $mail->isSMTP();
        // $mail->Host = 'localhost';
        // $mail->Port = 25;
        // $mail->CharSet = PHPMailer::CHARSET_UTF8;



		$mail->isSMTP();
		//Enable SMTP debugging
		// SMTP::DEBUG_OFF = off (for production use)
		// SMTP::DEBUG_CLIENT = client messages
		// SMTP::DEBUG_SERVER = client and server messages
		$mail->SMTPDebug = SMTP::DEBUG_SERVER;
		//Set the hostname of the mail server
		$mail->Host = 'smtp.gmail.com';
		// use
		// $mail->Host = gethostbyname('smtp.gmail.com');
		// if your network does not support SMTP over IPv6
		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$mail->Port = 587;
		//Set the encryption mechanism to use - STARTTLS or SMTPS
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication - use full email address for gmail
		$mail->Username = 'rossjcoop@gmail.com';
		//Password to use for SMTP authentication
		$mail->Password = $gcode;

        //It's important not to use the submitter's address as the from address as it's forgery,
        //which will cause your messages to fail SPF checks.
        //Use an address in your own domain as the from address, put the submitter's address in a reply-to
        $mail->setFrom('rossjcoop@gmail.com', $name);
        $mail->addAddress('rossjcoop@gmail.com');
        $mail->addReplyTo($email, $name);
        $mail->Subject = 'Library suggestion from ' . $name;
        $mail->Body = $email_body;
        if (!$mail->send()) {
            echo 'Mailer Error: '. $mail->ErrorInfo;
            exit;
        } 

	header("location:suggest.php?status=thanks");

}

$pageTitle = 'Suggest a Media Item';
$section = "suggest";

include("inc/header.php"); ?>

<div class="section page">
	<div class="wrapper">
		<h1>Suggest a Media Item</h1>

		<?php if (isset($_GET["status"]) && $_GET["status"] == "thanks") {
			echo "<p>Thanks for the email! I&rsquo;ll check out your suggestion shortly!</p>";
		} else { ?>

		<p>If you think there is something I&rsquo;m missing, let me know! Complete the form to send me an email.</p>
		<form method="post" action="suggest.php">
			<table>
				<tr>
					<th><label for="name">Name</label></th>
					<td><input type="text" name="name" id="name"/></td>
				</tr>
				<tr>
					<th><label for="email">Email</label></th>
					<td><input type="text" name="email" id="email"/></td>
				</tr>
				<tr>
					<th><label for="details">Suggest Item Details</label></th>
					<td><textarea name="details" id="details"></textarea></td>
				</tr>
				<tr style="display:none">
					<td><input type="text" name="validation" id="validation"/>
					<p>Please leave this field blank</p></td>
				</tr>
			</table>
			<input type="submit" name="" value="Send"/>
		</form>";
		<?php } ?>
	</div>
</div>


<?php include("inc/footer.php"); ?>