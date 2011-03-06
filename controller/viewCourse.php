<?php
	
	$sessID = isset($_COOKIE['sessID']) ? $_COOKIE['sessID'] : '';
	if($sessID)
	{
		if(!$user)
			$user = User::createSessionUser(); //disables unauthorized access (the "if" allows this controller to be invoked from other controllers)
	}else
	{
		$user = new User;
		$user->name = "Guest";
		$user->id = 0;
		if($controllerPageMap[$controller][1] == "viewCourse.phtml")
		{
			$controllerPageMap[$controller][1] = "viewCourseNoUser.phtml";
			setcookie('ouray_lastCourse',$course->urlTitle,time()+5*60,'/');
		}

		
	}
	
	$db = Application::getInstance()->db;
	
	$param = isset($course) ? $course->id : $uri[1];
	
	if(array_key_exists(1,$uri) && $uri[1] == "enroll")
	{
		$s = $db->prepare("insert into ".tPref."BridgeCourseUser (courseId,userId) values(:c,:u)");				
		$s->bindParam(':c',$param);
		$s->bindParam(':u',$user->id);
		$s->execute();
		$errStr="Welcome to the course!";	
	}
	
	$orderStr = "order by uploadDate desc";
	if(array_key_exists(2,$uri) && $uri[2]=='popular')
		$orderStr = "order by (likes+downloads) desc";	
		
	$limit=10;
	if(array_key_exists(3,$uri) && is_numeric($uri[3]))
		$limit = $uri[3];
	
	
	if($param == "all")	
	{	
		$s = $db->prepare("select ".Item::fieldList." from Item where courseId in (select courseId from BridgeCourseUser where userId = :uid) and enabled = 1 $orderStr limit 0,$limit");
		$s->bindParam(':uid',$user->id);
		$s2 = $db->prepare("select count(id) as num from Item where courseId in (select courseId from BridgeCourseUser where userId = :uid) and enabled = 1");
		$s2->bindParam(':uid',$user->id);
	}else
	{	
		$s = $db->prepare("select ".Item::fieldList." from Item where courseId = :c and enabled = 1 $orderStr limit 0,$limit");	
		$s->bindParam(':c',$param);
		$s2 = $db->prepare("select count(id) as num from Item where courseId=:c and enabled = 1");
		$s2->bindParam(':c',$param);	
		
		$s3 = $db->prepare("select User.name from User, BridgeCourseUser where BridgeCourseUser.userId = User.id and BridgeCourseUser.courseId = :c group by User.name;");
		$s3->bindParam(':c',$param);
		$s3->execute();
		$memberList = "";$i=0;$membersCount = $s3->rowCount();
		while($obj = $s3->fetchColumn())
		{
			$memberList .= stripslashes($obj); $i++;
			if($i<$membersCount)
				$memberList .= ', ';

		}

	
	}
	$s->execute();
	
	$items = array();
	
	while($obj = $s->fetchObject('Item'))
		array_push($items,$obj);
	if(!isset($course))
		$errStr="OK";
		
	$s = $db->prepare("select 1 as courseMember from BridgeCourseUser where userId=:uid and courseId=:cid");
	$s->bindParam(':uid',$user->id);
	$s->bindParam(':cid',$param);
	$s->execute();
	$obj = $s->fetch();
	$userMember = $obj['courseMember'];


	$s2->execute();
	$obj = $s2->fetch();
	$itemCount = $obj['num'];
		
?>
