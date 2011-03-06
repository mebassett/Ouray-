<?php
/*	login.php
 * 	authenticates a login based an application parameters set in the database
 *  written by gegn corp (http://www.gegn.net)
 */
	$cookie = @get_facebook_cookie(facebookAPIID, facebookKEY);
	if($cookie)
	{
		$user = User::createFacebookLogin();
		if($user->id)
		{	$course = isset($_COOKIE['ouray_lastCourse']) ? $_COOKIE['ouray_lastCourse'] : '';
			if($course)
				header("Location: /".$course);
			else
				header("Location: /dashboard");}
		
	}
	if(isset($_POST['login']))
	{
			$user = User::createLoginUser($_POST['email'],$_POST['password']);  //this saves session/cookie data
			
			$course = isset($_COOKIE['ouray_lastCourse']) ? $_COOKIE['ouray_lastCourse'] : '';
			if($course)
				header("Location: /".$course);
			else
				header("Location: /dashboard");
		
	}
?>
