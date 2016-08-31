<?php
if(!$folder){
	$folder="exercises";
	$passthru=0;
}else{
	$folder = "/home/bkinney/writable/" . $folder;
	$passthru=1;
}
$lessons = scandir($folder);
for($i=0;$i<count($lessons);$i++){
	if(is_dir($folder ."/" . $lessons[$i])) continue;
	if(strpos($lessons[$i],".html")===false) continue;
	$href = $context->info['launch_presentation_return_url'];
	$href .= '?&embed_type=basic_lti&url=' . urlencode("https://apps.ats.udel.edu/canvas/mashup/mashup.php?custom_redirect=".$folder ."/" . $lessons[$i]);
	echo '<p><a href="' . $href . '">' . str_replace(".html","",$lessons[$i]) . '</a></p>';
}
if(is_dir($folder . "/insecure")){
	$insecure = scandir($folder . "/insecure");
	echo '<h3>Your insecure assignments</h3>';
	for($i=0;$i<count($insecure);$i++){
		if(is_dir($folder ."/insecure/" . $insecure[$i])) continue;
		if(strpos($insecure[$i],".html")===false) continue;
		$href = $context->info['launch_presentation_return_url'];
		$href .= '?&embed_type=basic_lti&url=' . urlencode("https://apps.ats.udel.edu/canvas/mashup/mashup.php?custom_redirect=".$folder ."/insecure/" . $insecure[$i]);
		echo '<p><a href="' . $href . '">' . str_replace(".html","",$insecure[$i]) . '</a></p>';
	}
}



