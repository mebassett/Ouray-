<?php
/*	dashboard.php
 * 	loads all TwitterAccounts and RSSFeeds
 * 	haldes deleting of TwitterAccounts and RSSFeeds
 *  written by gegn corp (http://www.gegn.net) for Andrew Chapman
 */

	
	if(!$user)
		$user = User::createSessionUser(); //disables unauthorized access (the "if" allows this controller to be invoked from other controllers)
	
	$errStr="";
	if(array_key_exists(1,$uri) && array_key_exists(2,$uri) && is_numeric($uri[2]))
	{
		try
		{
			$db = Application::getInstance()->db;
			switch($uri[1])
			{
				case 'add':				
						$s = $db->prepare("insert into ".tPref."BridgeCourseUser (courseId,userId) values(:c,:u)");				
						$s->bindParam(':c',$uri[2]);
						$s->bindParam(':u',$user->id);
						$s->execute();
						$errStr="OK";	
				break;
				case 'delete':					
						$s = $db->prepare("delete from ".tPref."BridgeCourseUser where courseId = :c and userId = :u");				
						$s->bindParam(':c',$uri[2]);
						$s->bindParam(':u',$user->id);
						$s->execute();
						$errStr="OK";
				
				break;
				
			}
	
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
		}catch(Exception $e)
		{		
			$errStr = "There was an error, try again later.";
		}	

	}else
	{
		$errStr = "I didn't understand that request.";
	}
	
?>
