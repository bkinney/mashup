<?php
//because the redirect may go above the site root
function fixname($str){
	//filenames have been urlencoded, but the get string was automatically unencoded. Need to re-encode just the filename!
$arr = explode("/",$str);
$f=array_pop($arr);
return implode("/",$arr) . "/" . urlencode($f);
}
include fixname($_REQUEST['custom_redirect']);



?>
