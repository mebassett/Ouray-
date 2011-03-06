$(document).ready(function(){
	$("#course").autocomplete("/autocomplete_Courses.php");
});


var callbackFunction = (function(){});

function onHelperLoad()
{
	callbackFunction();
	callbackFunction = (function(){});
}

function onJoinClick(id)
{
	$("#errorBox").html("");	
	callbackFunction = (function()
	{
		var serverStatus = $("#helper").contents().find('#error').html();
		var serverStatus2 = $("#helper").contents().find('#status').html();
		var serverResponse = $("#helper").contents().find('#content').html();
		if(serverStatus == "OK")
		{
			$("#errorBox").html(serverStatus2);		
			$("#courses").html(serverResponse);
		}else
			$("#courses").html(serverStatus);
	});
	$("#helper").attr('src',"/operationJoinCourse/add/" + id);

}
function undoCourseJoin(id)
{
	$("#errorBox").html("");	
	callbackFunction = (function()
	{
		var serverStatus = $("#helper").contents().find('#error').html();
		var serverResponse = $("#helper").contents().find('#content').html();
		if(serverStatus == "OK")
		{
			$("#courses").html(serverResponse);
		}else
			$("#courses").html(serverStatus);
	});	
	$("#helper").attr('src',"/operationJoinCourse/delete/" + id);
}
var s =null;
var counter=3;

function uploadTimer()
{
	if(counter == 3) 
	{
			$('#errorBox2').html('Uploading...please wait');
			counter=0; 	
	}else
	{ 	
		counter++;
		$('#errorBox2').html($('#errorBox2').html()+'.');
	}
}
function onUploadItem()
{
	$("#textField").val($("#textBox").text());
	$("#errorBox2").html("Uploading...please wait...");
	s=setInterval("uploadTimer()",500);
}
function onUploadItemLoad()
{
	var serverStatus = $("#formHelper").contents().find('#error').html();
	if(serverStatus == "OK")
	{
		$("#errorBox2").html("");	
		clearInterval(s);
		formReset();	
	}else
		$("#errorBox2").html(serverStatus);
}
function formReset()
{	
	$("#textField").val("");
	$("#uploadedFile").val("");
	$("#course").val("");
   	$("#textBox").removeClass('textBoxActive')
   	$("#textBox").addClass('textBoxInactive');
   	$("#textBox").html("What would you like to share with the course?");
   	$(".otherOptions").hide('slow');	
}