<?php
class Item
{
	public $id;
	public $originalText;
	public $renderText;
	public $courseId;
	public $courseTitle;
	public $courseUrl;
	public $originalName;
	public $serverName;
	public $fileType;
	public $uploadDate;
	public $likes=0;
	public $downloads=0;
	public $userId;
	public $userName;
	public $itemType;
	 
	const fieldList = 'id,renderText,originalText,courseId, (select title from Course where id = courseId) as courseTitle,(select urlTitle from Course where id=courseId) as courseUrl,originalName, serverName,fileType,uploadDate,userId,(select name from User where id = userId) as userName,itemType,likes,downloads';
	
	
	function __construct()
	{		

		//$this->serverName = uploadUrlPrefix.$this->serverName;
		//$this->renderText = preg_replace("/(http:\/\/[^\s]+)/", "<a href=\"$1\">$1</a>", strip_tags($this->renderText));
	}
	
	public function saveToDb()
	{
		$renderer = new LatexRender();
		$this->renderText = preg_replace("/(http:\/\/[^\s]+)/", "<a href=\"$1\">$1</a>", $this->originalText);
		$this->renderText = $renderer->render(stripslashes($this->renderText));
		
		$db = Application::getInstance()->db;
		if(!$this->id)
			$s = $db->prepare("insert into ".tPref."Item (enabled,renderText,originalText,courseId,originalName, serverName,fileType,uploadDate,userId,likes,downloads,itemType) values (1,:rt,:ot,:c,:o,:s,:f,:u,:uid,:likes,:downs,:i)");
		else
			$s = $db->prepare("update ".tPref."Item set renderText=:rt,originalText=:ot,courseId=:c, originalName=:o, serverName=:s,fileType=:f,uploadDate=:u,likes=:likes,downloads=:downs,userId=:id,itemType=:i where id=:id ");
		

		
		$s->bindParam(':rt',$this->renderText);
		$s->bindParam(':ot',$this->originalText);
		$s->bindParam(':c',$this->courseId);
		$s->bindParam(':o',$this->originalName);
		$s->bindParam(':s',$this->serverName);
		$s->bindParam(':f',$this->fileType);
		$s->bindParam(':u',$this->uploadDate);
		$s->bindParam(':likes',$this->likes);
		$s->bindParam(':downs',$this->downloads);
		$s->bindParam(':uid',$this->userId);			
		$s->bindParam(':i',$this->itemType);
		if($this->id)
			$s->bindParam(':id',$this->id);
		$s->execute();		
		if(!$this->id)
			$this->id = $db->lastInsertId();
	}
	

}
?>
