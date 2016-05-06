<?php
$name = $_POST['name'];
$email = $_POST['email'];
$type = $_POST['type'];
$message = $_POST['message'];
$formcontent=" From: $name \n Email: $email \n Type: $type \n Message: $message";
$recipient = "info@avr-recycling.com";
$subject = "Avritek Site Contact Form";
$mailheader = "From: $email \r\n";
mail($recipient, $subject, $formcontent, $mailheader) or die("Error!") ;

echo "<div style ='font:28px Merriweather,Arial,sans-serif;text-align:center;font-weight:800;color:rgba(0, 131, 192, 1)'>Thank you! We will be in touch shortly!</div>"
?>

