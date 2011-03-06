<?php
/*	dashboard.php
 * 	loads all TwitterAccounts and RSSFeeds
 * 	haldes deleting of TwitterAccounts and RSSFeeds
 *  written by gegn corp (http://www.gegn.net) for Andrew Chapman
 */

	if(!$user)
		$user = User::createSessionUser(); //disables unauthorized access (the "if" allows this controller to be invoked from other controllers)
	
	$uri[1] = "all";
	$uri[2] = "recent";
	$uri[3] = 10;
	include("viewCourse.php");
	
	
	
	//get the user's courses
	$db = Application::getInstance()->db;
	$userCourses=array();
	$s = $db->prepare("select title, id from Course where id in (select courseId from BridgeCourseUser where userId=:uid)");
	$s->bindParam(":uid",$user->id);
	$s->execute();
	while($obj = $s->fetchObject('Course'))
		array_push($userCourses,$obj);
	unset($obj);
	
	

	if(count($userCourses)==0)
		include("courses.php");
	$errStr="";	
	
	/*$s = $db->prepare("select id, title,urlTitle, (select userId from BridgeCourseUser where courseId=id and userId=:u group by userId) as userId from Course");
	$s->bindParam(':u',$user->id);
	$s->execute();
	$userCourses = array();
	$otherCourses = array();
	while($course = $s->fetchObject('Course'))
		if($course->userId == $user->id)
			array_push($userCourses,$course);//$courses = $s->fetchAll(PDO::FETCH_OBJ);
		else
			array_push($otherCourses,$course);

	*/
	
	
	
?>
