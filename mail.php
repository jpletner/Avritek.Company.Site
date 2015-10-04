<?php
$name = $_POST['name'];
$email = $_POST['email'];
$type = $_POST['type'];
$message = $_POST['message'];
$formcontent=" From: $name \n Email: $email \n Type: $type \n Message: $message";
$recipient = "jennykay55@gmail.COM";
$subject = "Avritek Site Contact Form";
$mailheader = "From: $email \r\n";
mail($recipient, $subject, $formcontent, $mailheader) or die("Error!") ;


?>

    