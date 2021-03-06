<?php
/*	User.class.php
 *  Models a user session for a multi-user application
 *  written by gegn corp (http://www.gegn.net)
 */
class User 
{
	public $id;
	public $email;
	public $name;
	public $signupDate;
	public $lastLogin;
	
	
	const fieldList 		= "id, email,name,UNIX_TIMESTAMP(signupDate) AS signupDate,UNIX_TIMESTAMP(lastLogin) AS lastLogin";
	const createFieldList 	= "email,name, password";

	function __construct()
	{
		
	}
	/*function __construct($type,$argsArray=null)
	{
		
		switch($type)
		{
			case 'login': 		self::__constructLogin($argsArray); 	break;
			case 'new':			self::__constructNew($argsArray);		break;
			case 'fromSession':	self::__constructFromSession();			break;
			case 'fromId':		self::__constructFromId($argsArray);	break;
		}
	}*/
	
	public static function createFacebookLogin($fbUser=0)
	{
		global $fb;
		$db = Application::getInstance()->db;
		$cookie = get_facebook_cookie(facebookAPIID, facebookKEY);


        // create curl resource
        $ch = curl_init('https://graph.facebook.com/me?access_token=' .    $cookie['access_token']);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $fbResponse = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch); 


		$userDetails = json_decode($fbResponse);
	

	
		
		
		try
		{
			$s = $db->prepare("select ".User::fieldList." from User where fbId=:fbid");
			$s->bindParam(':fbid',$userDetails->id);
			$s->execute();
			$person = $s->fetchObject('User');
			
			if($person != null && $person->id)
			{
				if($person->email == '')
				{
					$person->email = $userDetails->email;
					$s = $db->prepare("update User set lastLogin=now(), email= :e where id=:id");
					$s->bindParam(':e',$person->email);
				}else			
					$s = $db->prepare("update User set lastLogin=now() where id=:id");
				$s->bindParam(':id',$person->id);
				$s->execute();
				$person->login();
				
				return $person;
			}else
			{




				$person = new User();
				$person->name = $userDetails->name;//['name'];
				$person->email = '';//$userDetails->email;//['proxied_email'];
				
				$s = $db->prepare("insert into ".tPref."User (".User::createFieldList.",fbId) values (:e,:n,:p,:fb)");
				$s->bindParam(':e',$person->email);//$variable,$data_type,$length,$driver_options)
				$s->bindParam(':n',$person->name);
				$s->bindParam(':p',sha1(time()));	
				$s->bindParam(':fb',$userDetails->id);		
				$s->execute();
		
				$person->id = $db->lastInsertId();
			$s = $db->prepare("update User set lastLogin=now() where id=:id");
			$s->bindParam(':id',$person->id);
			$s->execute();
				$person->login();

				return $person;
				
			}
			
		}catch(PDOException $e)
		{
			echo "test";
			echo $e->getMessage();
		}
	}
	
	public static function createLoginUser($username,$password)
	{

		$db = Application::getInstance()->db;
		try
		{
			$s = $db->prepare("select ".User::fieldList." from ".tPref."User where email=:u and password=:p");
			$s->bindParam(':u',$username);//$variable,$data_type,$length,$driver_options)
			$s->bindParam(':p',sha1($password));
			$s->execute();
			$user = $s->fetchObject('User');
			$s = $db->prepare("update User set lastLogin=now() where id=:id");
			$s->bindParam(':id',$user->id);
			$s->execute();
		}catch(PDOException $e)
		{
			echo "test";
			echo $e->getMessage();
		}
		
		if(!$user || !$user->id )
			throw new Exception("Login invalid");
		else	
		{
			$user->login();
			return $user;
		}		
	}
	
	public static function createSignupUser($email,$name,$password)
	{

		
		$db = Application::getInstance()->db;		
		$s = $db->prepare("insert into ".tPref."User (".User::createFieldList.") values (:e,:n,:p)");
		$s->bindParam(':e',$email);//$variable,$data_type,$length,$driver_options)
		$s->bindParam(':n',$name);
		$s->bindParam(':p',sha1($password));		
		$s->execute();
		$user = new User();
		$user->id = $db->lastInsertId();
		
		if(!$user->id)
			throw new Exception("There was an error creating the User record.");
		
		$user->email=$email;
		$user->signupDate=time();
		$user->login();
		return $user;	
	}
	
	public static function createSessionUser()
	{
		$sessID = isset($_COOKIE['sessID']) ? $_COOKIE['sessID'] : '';
		$user = new User();
       	if($sessID)
       	{
            session_id($sessID);
			session_start();
			//session_register('uId', 'userTime');
			$user->id = $_SESSION['uId'];
			$user->loadData();
			if($user->id > 0)
			{
				

				$_SESSION['uId'] = $user->id;
				$_SESSION['userTime'] = time();		

			} else throw new Exception("No session found.");
       	} else throw new Exception("No session found.");
       	return $user;		
	}
	

	private function loadData()
	{
		$db = Application::getInstance()->db;	
		$stmt = $db->prepare("select ".User::fieldList." from ".tPref."User where id = :id");//.$this->id."'");
		$stmt->bindParam(':id',$this->id);
		$stmt->execute();
		foreach($stmt->fetchObject('User') as $key => $value)
		{
			$this->{$key} = $value;
		}
	}
	
	public static function load( $id)
	{
		if(!is_numeric($id))
			throw new Exception("Not a valid User ID");
		$db = Application::getInstance()->db;	
		$stmt = $db->prepare("select ".User::fieldList." from ".tPref."User where id = :id");//.$this->id."'");
		$stmt->bindParam(':id',$id);
		$stmt->execute();
		
		return $stmt->fetchObject('User');		
	}
	
	public function login()
	{
		if($this->id > 0)
		{
        	mt_srand((double)microtime()*100000);
            $s = md5(uniqid(mt_rand()));
            setcookie('sessID', $s, time()+(6*3600),'/');
            session_id($s);
			@session_start();
			session_register('uId', 'userTime');          
			$_SESSION['uId'] = $this->id;
			$_SESSION['userTime'] = time();			
		}		
	}
	public function logout()
	{
		setcookie('sessID', 0, time()-3600,'/');
		$_COOKIE['sessID'] = 0;
		$_SESSION['userTime'] = 0;
		$_SESSION['uId'] = 0;
		setcookie('sessId',0,time()-3600);
		@session_destroy();
		//echo "gone!";
	}	
	public function save()
	{
		if(!is_numeric($this->id))
			throw new Exception("Not a valid User ID");
		$db = Application::getInstance()->db;	
		$s = $db->prepare("update ".tPref."User set email=:e where id=:id");
		$s->bindParam(':e',$this->email);
		$s->bindParam('id',$this->id);
		$s->execute();				
	}
	
	public function changePassword($password)
	{
		$db = Application::getInstance()->db;	
		$s = $db->prepare("update ".tPref."User set password=:p where id=:id");
		$s->bindParam(':p',sha1($password));
		$s->bindParam(':id',$this->id);
		$s->execute();						
	}
	
	public static function getByEmail($email)
	{
		
		$db = Application::getInstance()->db;	
 		$s = $db->prepare("select id from ".tPref."User where email=:e");//,$email));
 		$s->bindParam(':e',$email);
 		$s->execute();
 		$checkUser = $s->fetch(PDO::FETCH_ASSOC);
 		
 		if(!$checkUser['id'])
 			return null;
 		else
 			return User::load($checkUser['id']);
 		
	}
	
	/*public function saveCategories($categoryIds)
	{
		$this->db->send(sprintf("delete from ".tPref."BridgeUserCategory where UserId='%d'",$this->id));
		$sql = "insert into ".tPref."BridgeUserCategory (UserId, CategoryId) ";
		
		if(count($categoryIds) > 0)
		{
			$sql .= "values ";
				foreach($categoryIds as $id)
				{
					if(is_numeric($id))
						$sql .= sprintf("('%d','%d'),",$this->id,$id);
				}
				$sql = substr($sql,0,-1);
				$this->db->send($sql);
		}
	}
	
	public function getCategories()
	{
		$returnValue=array();
		$this->db->send(sprintf("select b.CategoryId as id a.name as name from ".tPref."category a, ".tPref."BridgeUserCategory b where a.id=b.CategoryId and b.UserId='%d' order by name",$this->id));
		while($ret=$this->db->ret())
			array_push($returnValue,$ret);
		return $returnValue;
	}
	
	
	
	public static function getAllbyType($type)
	{
		$returnValue=array();
		$d = new MySQL();
		if($type != User::proType && $type != User::normType)
			throw new Exception("invalid Type!");
		$d->send(sprintf("select id from ".tPref."User where type='%s' order by email asc",$type));
		while($ret=$d->ret())
			array_push($returnValue,new User('fromId',$ret['id']));
		return $returnValue;
	}
	
	*/
}
?>
