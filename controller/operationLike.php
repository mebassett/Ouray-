<?php
	if(!$user)
		$user = User::createSessionUser(); //disables unauthorized access (the "if" allows this controller to be invoked from other controllers)
	$db = Application::getInstance()->db;
	$s=$db->prepare("update Item set likes=likes+1 where id=:id");
	$s->bindParam(':id',$uri[4]);
	$s->execute();


	include("viewCourse.php");
?>
