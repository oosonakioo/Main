<?
require 'class.phpmailer.php';

    $mail = new PHPMailer();
	$mail->From = "info@phanutours.com";
    $mail->FromName = "info@phanutours.com";
	$mail->Host = "localhost";
	$mail->Mailer = "smtp";

	$mail->AddAddress("naa.nwb@hotmail.com"); // name is optional
	$mail->WordWrap = 50;                                 // set word wrap to 50 characters
	$mail->Subject = "Here is the subject";
	$mail->Body    = "Hello world 2013";
	$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
	$mail->IsHTML(false);
	
	$mail->SMTPAuth = "true";
	$mail->Username = "info@phanutours.com"; 
	$mail->Password = "ypCl2^50";
	$mail->charSet = "UTF-8";

if(!$mail->Send())
{
   echo "Message could not be sent. <p>";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

echo "Message has been sent";
?>