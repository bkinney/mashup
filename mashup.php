<?php 
//this is not redirecting. seems it is losing the $_REQUEST vars- replace with $context->info
// Load up the Basic LTI Support code, no oAuth dance
session_start();
require_once '/www/canvas/ims-blti/blti.php';
require_once "/home/bkinney/includes/lti_db.php";
/*error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);*/
header('Content-Type: text/html; charset=utf-8'); 

// Initialize, set session, and do not redirect
$secret =array("table"=>"blti_keys","key_column"=>"oauth_consumer_key","secret_column"=>"secret","context_column"=>"context_id");
$context = new BLTI($secret, true, false);//try redirect
if($context->valid){
	//echo($_REQUEST['ext_ims_lis_basic_outcome_url']);
	//print_r($context->info);
	setcookie('starttime',time());

}else{
	print_r($_REQUEST);
	echo $context->message;
	echo '<p><a href=../logout.php>Log out and try again</a></p>';
	die("<br>this assignment must be accessed from within an LTI provider, such as the Canvas LMS");
}
?>

<html>
<head>
  <title>LTI Lessons@UD</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<!--  <style>
  .hidden{display:none}
  </style>-->
</head>
<body >
<?php 
$udelnetid = strtolower($context->info['lis_person_contact_email_primary']);
$udelnetid=str_replace("@udel.edu","",$udelnetid);
$_SESSION['owner']=$udelnetid;		

if(stristr($context->info['roles'],"Instructor") || stristr($context->info['roles'],"ContentDeveloper") ){
	if(empty($context->info['custom_redirect'])) {
		$folder = false;
		echo '<blockquote><h3>Sample Mashups</h3>';
		echo '<p>Click to select an exercise for this assignment (students will never see this list).</p>';
		
		include "lesson_picker.php" ;
		echo '</blockquote>';
		$folder = $udelnetid;
			echo '<blockquote><h3>Your Mashups</h3>';
		echo '<h3><a href="editor.php?folder=' . $folder . ' " target="_blank">Create new</a></h3>';
		
		
		include "lesson_picker.php";
		echo '</blockquote>';
	}else{
		
		echo '<p class="instructor"><a href="editor.php?custom_redirect=' . $context->info['custom_redirect'] . '&folder=' . $udelnetid . '" target="_blank">Edit this mashup</a> (students will never see this link)' . $_SESSION['owner'];
	}
}

?>
<div id="lesson" style="height:90%">
<iframe id="lesson" src="passthru.php?custom_redirect=<?php echo $context->info['custom_redirect'] ?>" width="100%" height="100%" frameborder="0" scrolling="no">Content you select will appear here.</iframe>
</div>
<div id="outcome"></div>




<script type="text/javascript">



var http=false;
function loadhttp(){
	if(window.location.protocol=="https:"){
	window.location.href= window.location.href.replace('https','http');
	
	}
}
/*loadhttp();
if(<?php echo in_array('reload',$_GET)?>)loadhttp();*/

if("<?php echo strpos($context->info['custom_redirect'],'/insecure')>0; ?>" =='true')loadhttp();
</script>
</body></html>