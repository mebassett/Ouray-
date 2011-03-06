function onLike(cId,id)
{
	callbackFunction = (function()
	{
		var serverStatus = $("#helper").contents().find('#error').html();
		var serverStatus2 = $("#helper").contents().find('#status').html();
		var serverResponse = $("#helper").contents().find('#content').html();
		if(serverStatus == "OK")
		{
			$("#errorBox").html(serverStatus2);		
			$("#courseItems").html(serverResponse);
		}else
			$("#errorBox").html(serverStatus);
	});
	if(dashboard == "yes")	
		$("#helper").attr('src',"/operationLike/" + courseId + '/' + currentOrder + '/' + currentLimit + "/"+id);
	else
		$("#helper").attr('src',"/operationLike/" + courseId + '/' + currentOrder + '/' + currentLimit + "/"+id+'/yes');	
}
