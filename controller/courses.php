<?php
/*	dashboard.php
 * 	loads all TwitterAccounts and RSSFeeds
 * 	haldes deleting of TwitterAccounts and RSSFeeds
 *  written by gegn corp (http://www.gegn.net) for Andrew Chapman
 */

	if(!$user)
		$user = User::createSessionUser(); //disables unauthorized access (the "if" allows this controller to be invoked from other controllers)
	$db = Application::getInstance()->db;
	$s = $db->prepare("select id, title,urlTitle,description, (select userId from BridgeCourseUser where courseId=id and userId=:u group by userId) as userId from Course");
	$s->bindParam(':u',$user->id);
	$s->execute();
	$userCourses = array();
	$otherCourses = array();
	while($course = $s->fetchObject('Course'))
		if($course->userId == $user->id)
			array_push($userCourses,$course);//$courses = $s->fetchAll(PDO::FETCH_OBJ);
		else
			array_push($otherCourses,$course);	
	
	
	
?>
