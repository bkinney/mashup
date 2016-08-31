<?php
/*here's the plan:
record all correct answers in a session variable when the page is first loaded. Then use a jquery ajax call to compare a given answer to the stored answer and return feedback. 

remaining issue is how to prevent a reload of the assignment page.
manually check for page loads?
*/
error_reporting(E_ALL);
ini_set('display_errors', 1);
//print_r($_REQUEST);
session_start();
function evaluateAnswer(){
	$qnum=$_REQUEST['qnum'];
	$anum=$_REQUEST['anum'];
	$question = $_SESSION['quiz'][$qnum];
	
	$answer = $question['answers'][$anum];
	
	$answerstatus = $answer['weight']*1==100 ? "udats-correct" : "udats-incorrect";
	if($answerstatus=="udats-incorrect")$_SESSION['quiz'][$qnum]['points_possible']*=.5;
	echo '<li class="'.$answerstatus . '">' . $answer['text'];
	//print_r($answer);
			if(!empty($answer['comments'])){
				echo '<ol><li>' . $answer['comments'] . '</li></ol>';
			}
			echo '</li>';
}
function updateTotalScore($init){
	$score=0;
	foreach($_SESSION['quiz'] as $question){
		$score += $question['points_possible']*1;
		
	}
	if($init){
		$_SESSION['maxpoints']=$score;
		echo $score;
	}else{
		$_SESSION['score']=$score;
		echo 'Score: ' . $score . ' out of ' . $_SESSION['maxpoints'] ; 
	}
}
if(array_key_exists('anum',$_REQUEST) && array_key_exists('qnum',$_REQUEST)){
	evaluateAnswer();
	exit();
}else if(array_key_exists('totalscore',$_REQUEST)){
	updateTotalScore(false);
	exit();
}
$token = $_SESSION['token'];
$domain = "udel.instructure.com";//$_REQUEST['custom_domain_url'];

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
 <!-- <script src="ifat.js"></script>
   <script src="listtricks-quiz.js"></script>
  <script src="https://www.udel.edu/it/canvas/branding-inc/beta/udats-plugins.js"></script>
    
<link href="/canvas/mashup/mashup.css" rel="stylesheet" type="text/css">
<link href="https://www.udel.edu/it/canvas/branding-inc/beta/udats-plugins.css" media="all" rel="stylesheet" type="text/css">-->
<link href="https://udel.instructure.com/assets/quizzes_legacy_high_contrast.css" rel="stylesheet" type="text/css" media="all">
<style>
#ajax{display:none}
.message{border:solid thin red; padding:6px;font-family:Arial, Verdana, Geneva, sans-serif;}
.q-container{ border-bottom:double;}
.ui-helper-hidden-accessible, .flag_question{ display:none }
.udats-correct ol, .udats-incorrect ol{padding:0;list-style:none;margin:4px 15px;}
.udats-correct ol li, .udats-incorrect ol li{ padding:5px; margin-bottom:2px}
.answers li.unanswered {
	
	list-style:none;
	cursor:pointer;
}
.answers li{
	border-top: 1px solid #ddd;
	padding:7px;
}
.udats-correct{
	
	list-style-image:url(https://www.udel.edu/it/canvas/branding-inc/beta/check.gif);
	color:#063;
}

.udats-incorrect{
	
		list-style-image:url(https://www.udel.edu/it/canvas/branding-inc/beta/x.gif);
	color:#f00;
}
.udats-correct ol li{
	border-top: 1px solid #ddd;
	color:black;
	border:solid thin #063;
}
.udats-incorrect ol li{color:black;border:solid thin #F00;}
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
function getMaxPoints(question){
	
}
$(document).ready(function(e) {
	

	$(".answers").delegate("li.unanswered","click",function(obj){
		if($(obj.target).attr("href"))return true;
		var ans = $(obj.target);
		var anum=$(this).index();
		var question = $(this).parents("div.question");
		
		var qnum = question.index();
		$("#ajax").load("fatt.php","anum=" + anum + "&qnum=" + qnum,function(response){
			$(obj.target).after(response);
			$(obj.target).remove();
			var status = $("#ajax li").attr("class");
			
			var value = question.find("span.question_points").text();
		
		if(status=="udats-incorrect"){
			
			question.find("span.question_points").text(Number(value)*.5);
		}else{
		/*	var mydiv = $(".udats-quiz");
			var score = mydiv.data("score") + Number(value);
			var qp = mydiv.data("quizPoints")
			mydiv.data("score",score);
			mydiv.children(".udats-score").text("Score: "+ score + " out of " + qp);*/
			$("#running").load("fatt.php","totalscore=1");
		}
		});
		
		//var outcome = $(this).parents("li").attr("title");
		//alert("add " + points + " to " + outcome);
		//target_outcome = $("#" + outcome);
		
		
		
		
		//$("#next_btn").show();
		
	});//delegate
/*	
 calculate value of each question and total quiz. moved server side
	var maxpoints=0;
	$(".udats-quiz ol li ol").not("ol li ol li ol").each(function(index, element) {
		var mypoints = $(this).children("li").length-1;
		//$(this).parents("div").data("quizPoints",$(this).children("li").length);
		$(this).before(" [ points possible: <span class='value'>" + mypoints + "</span> ] ")
		$(this).children('li:first').attr("data-points",1).siblings('li').attr("data-points",0);
		maxpoints += mypoints;
	   //$(this).children('li:first').data("points",1);
	});//each*/
	$("div.udats-quiz").each(function(index, element) {
		//$(this).data("score",0);
		
		//$(this).data("quizPoints",maxpoints);
	
		$('<div id="running" class="udats-score">Score: 0 out of <?php updateTotalScore(true)?></div>').prependTo($(this));
		//this.children(".udats-score").text("Score: "+ score + " out of " + qp);
	 });


//$("#quiz").listtricksQuiz({sendScore:sendScore});//,addClass:"left"

});
</script>

</head>

<body>
<div id="ajax"></div>
<div id="questions" class="assessing">
<div class="question_holder">
<?php
	$quiz = get_mc_from_quiz($_GET['cid'],$_GET['qid'],$api);
	
	$_SESSION['quiz'] = $quiz;
	$qnum=1;
	foreach($quiz as $question){
		echo '<div class="question"><div class="header">
      <span class="name question_name" tabindex="0" role="heading">Question ' . $question['position'] . '</span>
      <span class="question_points_holder" style="">
        <span class="points question_points">1</span> pts
    </span>
        <span class="ui-helper-hidden-accessible">' . urlencode($question['question_text']) . '</span>
    </div><div class="text">';
		
		$prompt = $question['question_text'];
		
		$answers = $question['answers'];//array
		//usort($answers,'sortByWeight');
		//print_r($answers) . "\r\r";
		echo '<div  class="question_text user_content enhanced">
          <p>' . $prompt;
		echo '</p></div>';//option list
		echo '<div class="answers">';
		foreach($answers as $answer){
			//print_r($answer);	
			echo '<li class="unanswered">' . $answer['text'];//only put the onclick here
		
			echo '</li>';	}
		echo '</ol>';//close option list
		echo '</li></div></div></div>';//close question
	}

?>
</div>
</div>
</body>
</html>