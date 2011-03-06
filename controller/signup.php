<?php
/*	login.php
 * 	authenticates a login based an application parameters set in the database
 *  written by gegn corp (http://www.gegn.net)
 */
	if(array_key_exists('signup',$_POST))
	{
		$emailValidator = new EmailAddressValidator;
		if (!$_POST['email'] || !$_POST['password'] || !$_POST['name'])
 		{
 			$errStr = "Please complete form.";
 		}else if(!$emailValidator->check_email_address($_POST['email']))
 		{
 			$errStr = "Email is invalid.";
 		}else if(User::getByEmail($_POST['email']))
 		{
 			$errStr = "Account with that email already exists.";
 		}else
 		{		
			$user = User::createSignupUser($_POST['email'],$_POST['name'],$_POST['password']);  //this saves session/cookie data
			header("Location: dashboard");
 		}
		
	}
?>
