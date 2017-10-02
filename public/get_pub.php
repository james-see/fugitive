<?php
	include('/usr/share/getit.php');
	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$public_chat_array = $r->keys("public_chat*");
//echo "<ul>";
$fullmessages = array();
foreach ($public_chat_array as $key => $value) {
	$content_get = $r->get($value);
	$fullmessages[] = $content_get;
	//$realvalues = explode('_',$content_get);
    //echo "<li><span class='sender'>DateTime: $realvalues[0] User: $realvalues[1] Message: $realvalues[2]</span></li>";
} 
//echo "</ul>";
echo "<ul>";
arsort( $fullmessages, SORT_STRING);
#var_dump($fullmessages);
foreach ($fullmessages as $keyy => $valuee) {
	$realvalues = explode('_',$valuee);
	echo "<li class='publik'><span>DateTime: $realvalues[0] User: $realvalues[1] Message: $realvalues[2]</span></li>";
}
echo "</ul>";
?>