<?php
if(empty($_POST['title']))$_POST['title']="noname";
$message = '<blockquote class="message">Your changes to the <b>' . $_POST['title'] . '</b> assignment mashup have been saved. Use your browser\'s back arrow to continue editing. When you are satisfied with your assigment, close this browser tab or window and return to Canvas.</blockquote>';
$insecure = strpos($_POST['content_url'],"http://")>-1;
if($insecure)$message .= '<blockquote class="message">Your content page is insecure. You can still use this address, but be sure to use the "new window" assignment option, and double-check to be sure the content you have chosen is from a trusted source. Click <a href="#" onclick ="window.location.href= window.location.href.replace(\"https\",\"http\");">here to view this assignment insecurely.</blockquote>';
$contents = '<!doctype html>
<html>
<head>
<!--
This page should be loaded into the mashup.php template iframe, which is similar to the multitool.php page, except that the score reporting functions are replaced by the callback within the listricksQuiz closure. Hopefully this makes it a tiny bit harder to hack.
-->
<meta charset="utf-8">
<title>'.urlencode($_POST['title']).'</title>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
  <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script src="/canvas/mashup/listtricks-quiz.js"></script>
  

<link href="/canvas/liststricks-quiz.css" rel="stylesheet" type="text/css">
<link href="/canvas/mashup/mashup.css" rel="stylesheet" type="text/css">
<style>
.left .quiz{background: rgb(69, 194, 253);}
iframe{
	
	margin-left:2px;
}
.message{border:solid thin red; padding:6px;font-family:Arial, Verdana, Geneva, sans-serif;}
</style>
</head>

<body>
' . $message . '
<div id="quiz">' . $_POST['content'] .'<div id="success"></div></div>
   
<iframe src="' .$_POST['content_url'] . '" width="800" height="600" frameborder="0" scrolling="no"></iframe>
<script>
function sendScore(obj){

var post = new Object();
	for(var x in obj){
		post[x]=obj[x];
		
	}
	
		$("#success").load("reportScore.php",post,function(response){
		$("body").html("<p>Your grade has been submitted</p>");
	});//.hide();;
	
}
$(document).ready(function(e) {
	
	$("#quiz").listtricksQuiz({sendScore:sendScore,addClass:"left"});
    
});
</script>
</body>
</html>
' ;

$folder = '/home/bkinney/writable/' . $_REQUEST['folder'];

if(!is_dir($folder)) mkdir($folder,0777,true);
$filepath = $folder ."/" . urlencode($_POST['title']) . ".html";
file_put_contents($filepath,str_replace($message,'',$contents));
chmod($filepath,0777);

echo $contents;
?>