<?php
/*	dashboard.php
 * 	loads all TwitterAccounts and RSSFeeds
 * 	haldes deleting of TwitterAccounts and RSSFeeds
 *  written by gegn corp (http://www.gegn.net) for Andrew Chapman
 */

	$defaultTitle = "";
	$defaultDescription = "";
	if(!$user)
		$user = User::createSessionUser(); //disables unauthorized access (the "if" allows this controller to be invoked from other controllers)
	
	if(array_key_exists('submit',$_POST))
	{
		
		if(array_key_exists('title',$_POST) && array_key_exists('description',$_POST) && 
		   $_POST['title'] != "" && $_POST['description'] != "")
		{
			$newCourse = new Course();
			$newCourse->title 		=	$_POST['title'];
			$newCourse->description	=	$_POST['description'];
			$newCourse->creatorId	=	$user->id;
			$newCourse->saveToDb();
			$controllerPageMap[$controller][1] = "completeNewSubject.phtml";

			header("location: /".$newCourse->urlTitle);
		}else
		{
			$errStr = "All details are needed, please!";
			$defaultTitle 		=	$_POST['title'];
			$defaultDescription	=	$_POST['description'];
			
		}
		
	}
	
?>
