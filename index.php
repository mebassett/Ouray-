<?php
/*	index.php
 * 	one big happy switchboard to process an incomming HTTP request
 *  written by gegn corp (http://www.gegn.net)
*/


function get_facebook_cookie($app_id, $application_secret) {
  $args = array();
  parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
  ksort($args);
  $payload = '';
  foreach ($args as $key => $value) {
    if ($key != 'sig') {
      $payload .= $key . '=' . $value;
    }
  }
  if (md5($payload . $application_secret) != $args['sig']) {
    return null;
  }
  return $args;
}
	require "model/loadApi.php";	
	//$fb=new Facebook(facebookAPIID,facebookKEY);
	//$fbUser=$fb->get_loggedin_user();
	
	function setView($setName)
	{
		global $controllerPageMap,$controller;
		$controllerPageMap[$controller][1] = $setName;
	}
	





	//some magic to grab the uri
	$replace = substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], "/index.php"));
	$uri = substr(str_replace($replace,'',$_SERVER['REQUEST_URI']),1); //strip the app's dir from the uri
	$uri = explode("/",$uri); //expload the URI into happy information!
	
	$errStr="";
	$user="";
	
	//this singleton class may (or may not) contain some useful functions for this app
	$app = Application::getInstance();
	
	//create the "switchboard".  The map keeps track of what request goes to what controller/view (of course, the controller can change its view)
	$controllerPageMap = array();	
	$controllerPageMap['index'] 			= array("main.php",				"login.phtml");
	$controllerPageMap['login'] 			= array("login.php",			"login.phtml");
	$controllerPageMap['dashboard']			= array("dashboard.php",		"dashboard.phtml");	
	$controllerPageMap['courses']			= array("courses.php",			"courses.phtml");	
	$controllerPageMap['newSubjectFeed']	= array("newSubjectFeed.php",	"newSubjectFeed.phtml");
	
	
	
	$controllerPageMap['files']				= array("accessItem.php",		"dashboard.phtml");
	
	
	$controllerPageMap['signup']			= array("signup.php",			"signup.phtml");	
	$controllerPageMap['logout']			= array("logout.php",			"login.phtml");
	
	
	//ajax ops
	$controllerPageMap['operationJoinCourse'] 	= array("operationJoinCourse.php",	"operationJoinCourse.phtml");
	$controllerPageMap['operationUploadItem'] 	= array("operationUploadItem.php",	"operationUploadItem.phtml");
	$controllerPageMap['operationGetCourseList']= array("viewCourse.php",			"operationGetCourseList.phtml");
	$controllerPageMap['operationLike'] 	 	= array("operationLike.php",		"operationGetCourseList.phtml");
	
	//includes the appropriate controller/view
	$controller=$uri[0];	
	if($controller=="" && isset($_COOKIE['sessId']) && $_COOKIE['sessID'] != 0)
		$controller = 'dashboard';
		
	if(!isset($controllerPageMap[$controller]) && $controller != "")
	{
		$db = Application::getInstance()->db;
		$s = $db->prepare("select id, title,urlTitle, description, creatorId from Course where urlTitle=:c");
		$s->bindParam(':c',$controller);
		$s->execute();
		$course = $s->fetchObject('Course');
	/*	if(!$course)		
		{
		  $newTitle = str_replace("_"," ",urldecode($controller));
  			$newCourse = new Course();
			$newCourse->title 		= $newTitle;//$controller;//	$_POST['title'];
			$newCourse->description	=	$newTitle." url created list";//$_POST['description'];
			$newCourse->creatorId	=	1;//$user->id;
			$newCourse->saveToDb();
			$course=&$newCourse;
		}*/
		$controllerPageMap[$controller] = array("viewCourse.php","viewCourse.phtml");		
	}
	
	if (!isset($controllerPageMap[$controller]) || 	!file_exists('view/'.$controllerPageMap[$controller][1]))
		$controller="index";	
	
	
	
				
	try 
	{
		if($controllerPageMap[$controller][0] && file_exists('controller/'.$controllerPageMap[$controller][0]))
			include 'controller/'.$controllerPageMap[$controller][0];
		if($controllerPageMap[$controller][0] !="viewCourse.php")
		{
			setcookie('ouray_lastCourse',0,time()-3600,'/');
		}			
			
		include 'view/'.$controllerPageMap[$controller][1];
	}catch(Exception $e)
	{
		if($e->getMessage() == "No session found.")
		{
			include 'controller/login.php';
			include 'view/noLogin.phtml';
		}else
		{
			echo "error! ".$e->getMessage();
		//	echo $e->getFile();
			//echo $e->getLine();
		}
	}




?>
