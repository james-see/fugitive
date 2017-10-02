<?php
	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// redis connection
//require 'Predis/Autoloader.php';
//Predis\Autoloader::register();
//$r = new Predis\Client();
// example to interact with redis
$ismember = $r->sismember("current_public_users",$username);
if ($ismember == 0) {
    $r->sadd("current_public_users",$username);
    $formatted = "%s:%s:%s";
    $fulluserinfo = sprintf($formatted,$username,$ip,$curdate);
    $r->sadd("user_ips",$fulluserinfo);
}
//$r->sadd("current_public_users","jamesc");
$currentusers_array = $r->smembers("current_public_users");
echo "<ul>";
foreach ($currentusers_array as $key => $value) {
    echo "<li><span class='sender'>$value</span></li>";
} 
echo "</ul>";
?>