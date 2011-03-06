<?php 
	require "model/loadApi.php";	
	
	$user = User::createSessionUser();
	$db = Application::getInstance()->db;
	
	$u = mysql_escape_string($_GET['q']);
	$limit = is_numeric($_GET['limit']) ? $_GET['limit'] : 10;
	$s = $db->prepare("select title from Course where title like '%".$u."%' and (select 1 from BridgeCourseUser where userId=:u and courseId=id group by courseId)=1  limit ".$limit);
	$s->bindParam(':u',$user->id);
	$s->execute();
	while($obj = $s->fetchObject())
		print $obj->title."\n";
?>
