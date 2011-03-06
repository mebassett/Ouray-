<?php
/*	dashboard.php
 * 	loads all TwitterAccounts and RSSFeeds
 * 	haldes deleting of TwitterAccounts and RSSFeeds
 *  written by gegn corp (http://www.gegn.net) for Andrew Chapman
 */

	if(!$user)
		$user = User::createSessionUser(); //disables unauthorized access (the "if" allows this controller to be invoked from other controllers)
	if(array_key_exists('q',$_GET))
	{
		$db = Application::getInstance()->db;
		$s = $db->prepare("select id, serverName,fileType  from ".tPref."Item where serverName = :id");
		$s->bindParam(':id',urlencode($_GET['q']));
		$s->execute();
		$ret= $s->fetch();

		if($ret)
		{
			$s = $db->prepare("update Item set downloads=downloads+1 where id=:id");
			$s->bindParam(':id',$ret['id']);
			$s->execute();
			
			$len = filesize(fileUpload.$ret['serverName']);
			$file_extension = strtolower(substr(strrchr($ret['serverName'],"."),1));
			header('Content-Type: application/octet-stream; name="'.$ret['serverName'].'"');
			header('Content-Disposition: attachment; filename="'.$ret['serverName'].'"'); 
			header('Accept-Ranges: bytes');
			header('Pragma: no-cache');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-transfer-encoding: binary'); 		
    		header("Content-Length: ".$len);
    		@ob_clean();
			@flush();
			@readfile(fileUpload.$ret['serverName']);
			exit;
		}
		exit;
	}	
?>
