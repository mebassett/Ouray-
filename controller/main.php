<?php
/*	login.php
 * 	authenticates a login based an application parameters set in the database
 *  written by gegn corp (http://www.gegn.net)
 */

	$db = Application::getInstance()->db;
	$s = $db->prepare("select id, title,urlTitle,description from Course order by title desc");
	$s->execute();
	$userCourses = array();
	$otherCourses = array();
	while($course = $s->fetchObject('Course'))
			array_push($otherCourses,$course);	
?>
