<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<head>
<title>Assignment Mashup Editor</title>

<?php 
session_start();
//if($_SESSION['owner'] != $_REQUEST['folder'])die("unauthorized");
error_reporting(E_ALL);
ini_set('display_errors', 1);
function fixname($str){
	//filenames have been urlencoded, but the get string was automatically unencoded. Need to re-encode just the filename!
$arr = explode("/",$str);
$f=array_pop($arr);
if($arr[0]=="http:"){
	$action = "http://apps.ats.udel.edu/canvas/mashup/show.php";
	$subfolder="/insecure";
}else{
	$action = "show.php";
	$subfolder="";
}
return implode("/",$arr) . "/" . urlencode($f);
}
if(in_array('folder',$_REQUEST)){
	echo "I have a folder named " . $_REQUEST['folder'];
	$folder = $_REQUEST['folder'];
}else{
	$folder="bkinney";
	
}
if(in_array( 'custom_redirect',$_REQUEST)){
	$p = fixname($_REQUEST['custom_redirect']);
$handle = fopen($p,'r');
	$xml = fread($handle,999999);
	$a = explode('<div id="quiz">',$xml);
	$aa = explode('<div id="success">',$a[1]);
	//preg_match('/<div id=\"quiz\">(.+)<div/', $xml,$aa);
	$quiz = $aa[0];
	preg_match('/<title>(.+)<\/title>/',$xml,$t);
	//print_r($t);
	$title=$t[1];
	preg_match('/<iframe src=\"(.+)\"/',$xml,$ffc);
	$fc = $ffc[1];

}else{
	$fc=$quiz=$title="";
	$action="show.php";
	$subfolder="";
}
?>

<!-- OF COURSE YOU NEED TO ADAPT NEXT LINE TO YOUR tiny_mce.js PATH -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script src="tinymce/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea",
	
	plugins :"lists,template,code,save,paste",
toolbar1: "save | newdocument | bold italic | numlist | subscript superscript | code | undo redo",
       /* toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote| inserttime preview | forecolor backcolor",
        toolbar3: "table | hr removeformat || charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",*/
	paste_auto_cleanup_on_paste : true,
	
	menubar: false,
	fix_list_elements : true,
	      paste_preprocess : function(pl, o) {
			 alert("Sorry, pasting lists from MS Word does not work well. Your questions will probably not display properly when you use Word to compose your questions."); 
            // Content string containing the HTML from the clipboard
/*			o.content = o.content.replace("</li></ol></li></ol><p>","<ol><li>");
			o.content = o.content.replace("</p>","</li></ol></li>");
			o.content = o.content.replace("i.","");*/
            
           // o.content = "-: CLEANED :-\n" + o.content;
        },
/*        paste_postprocess : function(pl, o) {
            // Content DOM node containing the DOM structure of the clipboard
            //alert(o.node.innerHTML);
           // o.node.innerHTML = o.node.innerHTML + "\n-: CLEANED :-";
        },*/
	
	templates: [
                {title: 'Self-Test Mashup', url: 'quiz-compact.html'}
                
        ],
		save_enablewhendirty:false,
		save_onsavecallback: function() { 
			if($("#content_url").val().indexOf("http://"==0) && $("#content_url").val().indexOf("/insecure"==-1)){
				var iu = $("#folder").val() + "/insecure";
				$("#folder").val(iu);
				$("#mce").attr("action","http://apps.ats.udel.edu/canvas/mashup/show.php");
			}
			if($("#title").val()==""){
			   alert("please enter a title");
			  // e.preventDefault();
		   }else{
			  $("#mce").submit();
		   }
		   }
	/*	  setup : function(ed) {
      ed.onSubmit.add(function(ed, e) {
           console.debug('Form submit:' + e.target);
      });
   }
	setup : function(ed) {
      ed.onSubmit.add(function(ed, e) {
           if($("#title").val()==""){
			   alert("please enter a title");
			   e.preventDefault();
		   }
      });
   })
		init_instance_callback : function() {                                                   
     tinyMCE.activeEditor.setContent('<?php echo $quiz ?>');
}*/

 });
   


</script>


</head>
<body >
<!-- OF COURSE YOU NEED TO ADAPT ACTION TO WHAT PAGE YOU WANT TO LOAD WHEN HITTING "SAVE" -->
<form method="post" action="<?php echo $action ?>" id="mce">
<p>To create your own assignment mashup, enter the url of the content page you want in the main panel. Then create your quiz questions as an ordered list. Questions should go in the first level, the second level is for answer options, the third for feedback. Always put the correct answer as the first answer option. Answer sequences will be randomized each time the assignment is loaded.</p>
content page: <input id="content_url" name="content_url" type="text" size="50" value="<?php echo $fc ?>"/>
assignment title*: <input id="title" name="title"  type="text" size="50" value="<?php echo urldecode($title) ?>"/>
<input id="folder" name="folder"  type="hidden"  value="<?php echo $folder . $subfolder ?>" size="20"/>
<p align="center">
<textarea id="tiny" name="content" cols="150" rows="30"> <?php echo $quiz ?> 
       </textarea>
</p>
</form>

</body>
</html>