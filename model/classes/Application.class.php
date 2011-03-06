<?php
/* Application.class.php
 * This file just defines a static class for some globally used functions.
 */

class Application 
{
	public $db;
	private static $init;
	
	public function __construct()
	{
		try
		{
			$this->db = new PDO(db_ConnectionString,db_user,db_pass);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
	
	public function fetchParameter($key)
	{
		$stmt = $this->db->prepare("select `value` from `".tPref."parameters` where `key` =:key");
		$stmt->bindParam(":key",$key);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result == "") throw new Exception("cannot find parameter ".$key);
		return stripslashes($result['value']);
	}
	

	
	public static function getInstance()
	{
		if(!Application::$init)
			$init = new Application();
		return $init;
	}
}
?>