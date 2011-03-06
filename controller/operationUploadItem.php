<?php
/*	dashboard.php
 * 	loads all TwitterAccounts and RSSFeeds
 * 	haldes deleting of TwitterAccounts and RSSFeeds
 *  written by gegn corp (http://www.gegn.net) for Andrew Chapman
 */
require_once "Mail.php";

	if(!$user)
		$user = User::createSessionUser(); //disables unauthorized access (the "if" allows this controller to be invoked from other controllers)

	if(array_key_exists('upload',$_POST) && array_key_exists('text',$_POST))
	{
		try
		{			
			$db = Application::getInstance()->db;
			$s = $db->prepare("select id, title,urlTitle from Course where title = :t and id in (select courseId from BridgeCourseUser where userId=:uid)");
			$s->bindParam(":t",$_POST['course']);
			$s->bindParam(":uid",$user->id);
			$s->execute();
			$obj = $s->fetchObject();
			
			if(!$obj || !$obj->id )
				$errStr = "No such course exists.";	
			elseif(!$_POST['text'] && !array_key_exists('uploadedFile',$_FILES))
				$errStr = "You have to type something to post.";
			else
			{
				$item = new Item();
				$item->originalText = $_POST['text'];
				$item->courseId=$obj->id;				
				$item->uploadDate = time();
				$item->userId=$user->id;
				
				
				if(array_key_exists('uploadedFile',$_FILES) && $_FILES['uploadedFile']['name'] )
					if(!$_FILES['uploadedFile']['error'])
					{
						$fileInfo = pathinfo($_FILES['uploadedFile']['name']);			
						$item->originalName = $_FILES['uploadedFile']['name'];
						$item->serverName = urlencode(str_replace(" ","_",$_FILES['uploadedFile']['name']));//sha1($_FILES['uploadedFile']['name']).'.'.$fileInfo['extension'];
						$item->fileType = $_FILES['uploadedFile']['type'];
						
						$item->itemType = 'Book';
						if(!move_uploaded_file($_FILES['uploadedFile']['tmp_name'],fileUpload.$item->serverName))
							$errStr = "There was an error uploading.";
						else
							chmod(fileUpload.$item->serverName,0777);
					}else
						$errStr = "There was an error uploading.".$_FILES['uploadedFile']['error'];

				if(!$errStr || $errStr == "OK")
				{				
					$item->saveToDb();
					$status = "Posted";
					$errStr = "OK";


					
					$from = "Ouray <sneffel@gegn.net>";
					$subject = stripslashes($user->name) . " updated ". stripslashes($obj->title);
					
					
					$host = "ssl://smtp.gmail.com";
					$port = "465";
					$username = "sneffel@gegn.net";
					$password = "A_lpha1";

					$s = $db->prepare("SELECT User.name, User.email FROM User, BridgeCourseUser WHERE User.id = BridgeCourseUser.userId AND BridgeCourseUser.courseId = :cid AND User.email != '' AND User.id != :uid GROUP BY email"); 
					$s->bindParam(":cid",$obj->id);
					$s->bindParam(":uid",$user->id);
					$s->execute();
			
					while($recp = $s->fetchObject())
					{									
						$to = $recp->name . "<" . $recp->email . ">";
						$body = "Dear ".stripslashes($recp->name).",\n\n".stripslashes($obj->title)." was updated by ".stripslashes($user->name). " with the following:\n\n";
						$body .= htmlspecialchars(stripslashes($item->originalText));
						$body .= "\n\n";
						$body .= $item->serverName . "\n\n";
						$body .= "You can view this updated at http://ouray.sneffel.com/".$obj->urlTitle." . To stop recieving these notifications, email matthew@sneffel.com and tell him to turn them off.\n\nThanks,\n\nOuray";
						
						$headers = array ('From' => $from,
						  'To' => $to,
							  'Subject' => $subject);
						$smtp = Mail::factory('smtp',
						  array ('host' => $host,
						    'port' => $port,
						    'auth' => true,
						    'username' => $username,
	    					'password' => $password));
						$mail = $smtp->send($to, $headers, $body);
	
					}

				}
			}
		}	catch(Exception $e)
		{
			$errStr = "There was an error posting.".$e->getMessage();	
		}
	} else 
		$errStr = "I didn't understand that request.";	
	
?>
