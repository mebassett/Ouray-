$(document).ready(function(){
	$("#course").autocomplete("/autocomplete_Courses.php");
});

var callbackFunction = (function(){});
var standardCourseFunction = (function()
		{
			var serverStatus = $("#helper").contents().find('#error').html();
			var serverStatus2 = $("#helper").contents().find('#status').html();
			var serverResponse = $("#helper").contents().find('#content').html();

			if(serverStatus == "OK")
			{
				//$("#errorBox").html(serverStatus2);

				$("#courseItems").html(serverResponse);
			}else
				$("#errorBox").html(serverStatus);
				
		});


var currentOrder = "recent";
var currentLimit = 10;

function onHelperLoad()
{

	callbackFunction();

	//callbackFunction = (function(){});
}


function reOrder(sortBy)
{
		currentOrder=sortBy
		callbackFunction = standardCourseFunction;		
		$("#helper").attr('src',"/operationGetCourseList/" + courseId + '/' + sortBy + '/' + currentLimit);		
}
function showMore()
{
		currentLimit += 10;
		
		callbackFunction = standardCourseFunction;		
		$("#helper").attr('src',"/operationGetCourseList/" + courseId + '/' + currentOrder + '/' + currentLimit );
		var newHtml = '<a onclick="showLess()">Show less</a>';
		if(currentLimit < itemCount)
			newHtml += ' - <a onclick="showMore()">Show more</a>';		
		$("#moreLess").html(newHtml);
}
function onCourseChange()
{
	courseId = $("#courseChooser").val();
	if(courseId != "all")
		$("#course").val($("#courseChooser option:selected").text());
	else
		$("#course").val("");
	callbackFunction = standardCourseFunction;		
	$("#helper").attr('src',"/operationGetCourseList/" + courseId + '/' + currentOrder + '/' + currentLimit + "/all" );		
}
function showLess()
{
		if(currentLimit > 10)
		{
			currentLimit -= 10;
			
			callbackFunction = standardCourseFunction;		
			$("#helper").attr('src',"/operationGetCourseList/" + courseId + '/' + currentOrder + '/' + currentLimit );	
			var newHtml='';
			if(currentLimit > 10)
				newHtml = '<a onclick="showLess()">Show less</a> - ';	
			if(currentLimit < itemCount)	
				newHtml += '<a onclick="showMore()">Show more</a>';		
			$("#moreLess").html(newHtml);
		}
}
var s =null;
var counter=3;
function onUploadItem()
{
	$("#textField").val($("#textBox").text());
	$("#errorBox2").html("Uploading...please wait...");
	s=setInterval("uploadTimer()",500);

}
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
function onUploadItemLoad()
{
	clearInterval(s);
	var serverStatus = $("#formHelper").contents().find('#error').html();
	if(serverStatus == "OK")
	{
		$("#errorBox2").html("");	
		//$('form').reset();
		
		callbackFunction = standardCourseFunction;
		
		$("#helper").attr('src',"/operationGetCourseList/" + courseId + '/' + currentOrder + '/' + currentLimit);	
		
		formReset();	
	}else if(serverStatus != null)
		$("#errorBox2").html('<strong> ERROR: <em>' + serverStatus + '</em></strong>');
	
}
function updateItems()
{
		callbackFunction = standardCourseFunction;		
		if(dashboard == "yes")
			$("#helper").attr('src',"/operationGetCourseList/" + courseId + '/' + currentOrder + '/' + currentLimit + "/all" );		
		else
			$("#helper").attr('src',"/operationGetCourseList/" + courseId + '/' + currentOrder + '/' + currentLimit);	

}
function formReset()
{	
	$("#textField").val("");
	$("#uploadedFile").val("");
	if(dashboard!='no')
		$("#course").val("");
   	$("#textBox").removeClass('textBoxActive')
   	$("#textBox").addClass('textBoxInactive');
   	$("#textBox").html("What would you like to share with the course?");
   	$(".otherOptions").hide('slow');	
}
setInterval("updateItems()",25000);
