<?php
//require_once('class.phpmailer.php');
require '../PHPMailerAutoload.php';

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->CharSet="UTF-8";
//$mail->Host = 'ssl://smtp.gmail.com:465:1';
//$mail->Port = 465;

$mail->Host = 'smtp.gmail.com';
$mail->Port = 587;
$mail->Username = 'kisfinancebilling@kis.ac.th';
$mail->Password = 'syd386$FM';
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';

$mail->From = 'kisfinance@kis.ac.th';
$mail->FromName = 'KIS Finance';
$mail->AddAddress('sittiporn.nu@kis.ac.th');
$mail->AddReplyTo('kisfinance@kis.ac.th', 'KIS Finance');

$mail->IsHTML(true);
$mail->Subject    = "PHPMailer Test Subject via Sendmail, basic";
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
$mail->Body    = "·´ÊÍº";

if(!$mail->Send())
{
  echo "Mailer Error: " . $mail->ErrorInfo;
}
else
{
  echo "Message sent!";
}
?>
