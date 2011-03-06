<?php
require "model/loadApi.php";	

$db = Application::getInstance()->db;

$s = $db->prepare("Select * from Course where id >= 37");
$s->execute();
while($obj = $s->fetchObject())
{
	echo "<p>";
	
	echo stripslashes($obj->title)."<br/>";
	echo "http://ouray.sneffel.com/".$obj->urlTitle;	
	echo "<br/>".stripslashes($obj->description);

	echo "</p>";
}
?>
