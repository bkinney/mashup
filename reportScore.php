<html>
<head>
  <title>IMS Basic Learning Tools Interoperability</title>
</head>
<body style="font-family:sans-serif">


<?php 
// Load up the Basic LTI Support code
/*<form id="tocanvas" name="tocanvas" method="POST" action="myoutcome.php" target="lti" >
<input type="text" name="url" size="80"  value="<?php echo($_POST['ext_ims_lis_basic_outcome_url']);?>"/>
<input type="text" name="sourcedid"  size="100" value="<?php echo($_POST['lis_result_sourcedid']);?>"/>
<input type="text" name="key"  size="80" value="<?php echo($_POST['oauth_consumer_key']);?>"/>
<input type="text" name="grade" id="grade" value="" />

</form>*/

//print_r($_REQUEST);

require_once '/www/LTI436/ims-blti/blti_util.php';

error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);

$oauth_consumer_secret = 'allsecrets';

	session_start();

if ( isset($_REQUEST['score']) && isset ($_REQUEST['outof']) ) {
/*	$elapsed = time() - $_COOKIE['starttime'];
	if(Math.abs(elapsed - $_REQUEST['elapsed'])>3){
		die("suspected cheating. server time elapsed does not match client time");
	}*/
	$info=$_SESSION['_basic_lti_context'];
	//print_r($info);
	$grade = $_REQUEST['score']*1/$_REQUEST['outof']*1;
	echo $grade;
    $message = 'basic-lis-updateresult';
	$url = $info['ext_ims_lis_basic_outcome_url'];
	
}else{
    exit();
}

//if ( ! isset($_REQUEST['grade']) ) exit;

// Hack to detect the old form of outcomes 
if ( strpos($url,"imsblis/outcomes/") > 0 ) {
    $message = str_replace('basic', 'simple', $message);
}


$data = array(
  'lti_message_type' => $message,
  'sourcedid' => $info['lis_result_sourcedid'],
  'result_statusofresult' => 'final',
  'result_resultvaluesourcedid' => 'decimal',
  'result_resultscore_textstring' => $grade);

$oauth_consumer_key = $info['oauth_consumer_key'];

$newdata = signParameters($data, $url, 'POST', $oauth_consumer_key, $oauth_consumer_secret);

$retval = do_post_request($url, http_build_query($newdata));

//echo " \n";
$retval = str_replace("<","&lt;",$retval);
$retval = str_replace(">","&gt;",$retval);
//echo "<pre>\n";
//echo "Response from server\n";
//echo $retval;

?>
