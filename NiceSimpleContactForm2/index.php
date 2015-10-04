<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>A Nice &amp; Simple Contact Form</title>
	
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>

    <?php include("../header.php"); ?>

	<div id="page-wrap">

		<img src="images/title.gif" alt="A Nice &amp; Simple Contact Form" /><br /><br />
		<p>By <a href="http://css-tricks.com">CSS-Tricks</a></p>
		
		<p>There are a million contact form examples on the web, why this one? Because it's SIMPLE, it's FREE, it WORKS, it's VALID, and it's primarily styled with CSS.</p>
		
		<p>If you are trying to contact CSS-Tricks, DO NOT USE THIS FORM. This is just an example, my real <a href="http://css-tricks.com/contact/">contact form is here</a>.</p>
		
		<p><a href="http://css-tricks.com/examples/NiceSimpleContactForm2-recaptcha.zip">Download this example</a></p>
						
		<div id="contact-area">
			
			<form method="post" action="contactengine.php">
				<table>
					<tr>
						<td class="left"><label for="Name">Name:</label></td>
						<td><input type="text" name="Name" /></td>
					</tr>
					<tr>
						<td class="left"><label for="City">City:</label></td>
						<td><input type="text" name="City" /></td>
					</tr>
					<tr>
						<td class="left"><label for="Email">Email:</label></td>
						<td><input type="text" name="Email" /></td>
					</tr>
					<tr>
						<td class="left"><label for="Message">Message:</label></td>
						<td><textarea name="Message" rows="20" cols="20"></textarea></td>
					</tr>
				</table>
				
				<div id="captcha-area">
				
				<?php
				
				require_once('recaptchalib.php');
				$publickey = "6LdmigAAAAAAAHJEZiIdo6bYZtwReBZavbXxGacx";
				$privatekey = "6LdmigAAAAAAAPTBvc0XBOdlKn5dPyTgazNvmHBx";
				
				# the response from reCAPTCHA
				$resp = null;
				# the error code from reCAPTCHA, if any
				$error = null;
				
				# are we submitting the page?
				if ($_POST["submit"]) {
				  $resp = recaptcha_check_answer ($privatekey,
												  $_SERVER["REMOTE_ADDR"],
												  $_POST["recaptcha_challenge_field"],
												  $_POST["recaptcha_response_field"]);
				
				  if ($resp->is_valid) {
					echo "You got it!";
					# in a real application, you should send an email, create an account, etc
				  } else {
					# set the error code so that we can display it. You could also use
					# die ("reCAPTCHA failed"), but using the error message is
					# more user friendly
					$error = $resp->error;
				  }
				}
				echo recaptcha_get_html($publickey, $error);
				?>
				
				</div>
								
				<input type="submit" name="submit" value="Submit" class="submit-button" />
			</form>
		
		</div>
	
	</div>
	
	<?php include("../footer.php"); ?>

</body>

</html>