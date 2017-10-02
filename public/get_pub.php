<?php
//	ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
// we don't need to instantiate redis, already connected
$public_chat_array = $r->keys("pc_*");

// loop through public_chat keys to get values into array
$fullmessages = array();
foreach ($public_chat_array as $key => $value) {
	$content_get = $r->get($value);
	$fullmessages[] = $content_get; // add content to array
	} 

// sort array by date time descending using arsort
arsort( $fullmessages, SORT_STRING);

// for each message string parse out the values and print them in the public chat list in order of date desc
echo "<ul id='public-messages'>";
foreach ($fullmessages as $keyy => $valuee) {
	$realvalues = explode('_',$valuee);
	$newDate = date("m d Y h:i:s", strtotime($realvalues[0]));
	echo "<li class='publik'><span class='sender'> $realvalues[1]</span><span class='messager' title='$newDate'>$realvalues[2]</span></li>";
}
echo "</ul>";
?>