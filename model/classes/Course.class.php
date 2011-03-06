<?php
class Course
{
	public $id;
	public $title;
	public $urlTitle;
	public $description;
	public $creatorId;
	
	public $userId;
	
	
	function __construct()
	{		
	}
	
	public function saveToDb()
	{
		$db = Application::getInstance()->db;
		if(!$this->id)
			$s = $db->prepare("insert into ".tPref."Course (title,urlTitle,description,creatorID) values (:t,:u,:d,:c)");
		else
			$s = $db->prepare("update ".tPref."Course set title=:t,urlTitle=:u, description=:d,creatorID=:c where id=:id ");
		$s->bindParam(':t',$this->title);
		$s->bindParam(':u',urlencode(str_replace(" ","_",$this->title)));
		$this->urlTitle=urlencode(str_replace(" ","_",$this->title));
		$s->bindParam(':d',$this->description);
		$s->bindParam(':c',$this->creatorId);
		
		if($this->id)
			$s->bindParam(':id',$this->id);
		$s->execute();		
		if(!$this->id)
		{
			$this->id = $db->lastInsertId();
			$s = $db->prepare("insert into ".tPref."BridgeCourseUser (courseId,userId) values (:c,:u)");
			$s->bindParam(':c',$this->id);
			$s->bindParam('u',$this->creatorId);
			$s->execute();	
		}
	}
	

}
?>
