<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$token = $_SESSION['token'];
$domain = "udel.instructure.com";//$_REQUEST['custom_domain_url'];
function sortByWeight($a,$b){
	return $a['weight'] < $b['weight'] ? 1 : -1;
}
if(empty($domain)){
	$domain = $_COOKIE['domain'];
}else{//lost the POST vars when we redirected
	setcookie('domain',$domain,0,'/');
}
	include 'canvasapi.php';
	
	if(isset($token)){
		//echo "token found" . " " . $domain;
		$api = new CanvasAPI($token,$domain);
		$valid = $api->ready;
		//echo $valid;
	}else{
			//query db for an all purpose token. 

  		$query=sprintf("select token from tokens where context='%s' or context='%s'",$context_id,$domain);
			 
		  $result = mysql_query($query);
		  if(mysql_num_rows($result)){
			 $row = mysql_fetch_array($result);
			 
			$token = $_SESSION['token']=$row['token'];
			//$token = $_SESSION['token']= "ejustetesting";
			
			
			
		  }//end token in db
		$api = new CanvasAPI($token,$domain);
		$valid = $api->ready;
	
	}
	function get_mc_from_quiz($courseid,$quizid,$api){
		$uri = '/api/v1/courses/' . $courseid . '/quizzes/' . $quizid . '/questions';
		$questions = $api->get_canvas($uri,true);
		$mc = array();
		foreach($questions as $question){
			if($question['question_type']=="multiple_choice_question") $mc[]=$question;
		}
		//print_r($mc);
		return $mc;
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
  <script src="listtricks-quiz.js"></script>
   <!--<script src="https://www.udel.edu/it/canvas/branding-inc/beta/udats-plugins.js"></script>-->
    <link href="https://www.udel.edu/it/canvas/branding-inc/beta/udats-plugins.css" media="all" rel="stylesheet" type="text/css">
<link href="/canvas/mashup/mashup.css" rel="stylesheet" type="text/css">
<style>
.left .quiz{background: rgb(69, 194, 253);}
iframe{
	
	margin-left:2px;
}
.message{border:solid thin red; padding:6px;font-family:Arial, Verdana, Geneva, sans-serif;}
</style>
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
	$.getScript("https://www.udel.edu/it/canvas/branding-inc/beta/quiz.js", function(){
				$(".udats-quiz ol li > ol").delegate("li","click",function(obj){
					if($(obj.target).attr("href"))return true;
					$(this).children().show();
					
					var outcome = $(this).parents("li").attr("title");
					//alert("add " + points + " to " + outcome);
					//target_outcome = $("#" + outcome);
					var status = $(this).data("points")=="1" ? "udats-correct" : "udats-incorrect";
					var value = $(this).parents("li").children("span.value").text();
					
					if(status=="udats-incorrect"){
						
						$(this).parents("li").children(".value").text(Number(value)-1);
					}else{
						var mydiv = $(this).parents(".udats-quiz");
						var score = mydiv.data("score") + Number(value);
						var qp = mydiv.data("quizPoints")
						mydiv.data("score",score);
						mydiv.children(".udats-score").text("Score: "+ score + " out of " + qp);
					}
					$(this).addClass(status);
					//$("#next_btn").show();
					
				});//delegate

				$(".udats-quiz ol li ol").not("ol li ol li ol").each(function(index, element) {
			
					//$(this).parents("div").data("quizPoints",$(this).children("li").length);
					$(this).before(" [ points possible: <span class='value'>" + ($(this).children("li").length-1) + "</span> ] ")
					$(this).children('li:first').attr("data-points",1).siblings('li').attr("data-points",0);
				   //$(this).children('li:first').data("points",1);
				});//each
				$("div.udats-quiz").each(function(index, element) {
           		 	$(this).data("score",0);
					var qp = $(this).find("ol li ol li").not("ol li ol li ol li").length - 1;
					$(this).data("quizPoints",qp);
					$(this).find("ol ol ol").hide();
					$(this).find('ol li').not('ol li ol li').each(function(index, element) {
						var mixed = $(this).children('ol').shuffle();
						$(mixed).children("li").each(function(){
							$(this).data("points",$(this).attr("data-points")).removeAttr("data-points");
						});
					});
					$('<div class="udats-score">Score: 0 out of ' + qp + '</div>').prependTo($(this))
					//this.children(".udats-score").text("Score: "+ score + " out of " + qp);
       			 });
			});//load script
	
	//$("#quiz").listtricksQuiz({sendScore:sendScore});//,addClass:"left"
    
});
</script>

</head>

<body>
<div class="udats-quiz">
<ol>
<?php
	$quiz = get_mc_from_quiz($_GET['cid'],$_GET['qid'],$api);
	foreach($quiz as $question){
		$prompt = $question['question_text'];
		
		$answers = $question['answers'];//array
		usort($answers,'sortByWeight');
		//print_r($answers) . "\r\r";
		echo '<li>' . $prompt;
		echo '<ol>';//option list
		//sort answers by weight
		foreach($answers as $answer){
			//print_r($answer);	
			echo '<li>' . $answer['text'];
			if(!empty($answer['comments'])){
				echo '<ol><li>' . $answer['comments'] . '</li></ol>';
			}
			echo '</li>';	}
		echo '</ol>';//close option list
		echo '</li>';//close question
	}

?>
</ol>
</div>
</body>
</html>