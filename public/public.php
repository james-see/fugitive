<?php
	include('/usr/share/getit.php');
	#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);
// redis connection
//require 'Predis/Autoloader.php';
//Predis\Autoloader::register();
//$r = new Predis\Client();
// example to interact with redis
#$ismember = $r->sismember("current_public_users",$username);
#if ($ismember == 0) {
    #$r->sadd("current_public_users",$username);
    // new users as keys only
    $usernameformat = "user_%s";
    $r->set(sprintf($usernameformat,$username),date('Ymdhis'));
    $r->expire(sprintf($usernameformat,$username),30);
    $formatted = "%s:%s:%s";
    $fulluserinfo = sprintf($formatted,$username,$ip,$curdate);
    $r->sadd("users_ip_info",$fulluserinfo);
#}
//$r->sadd("current_public_users","jamesc");
$currentusers_array = $r->keys("user_*");
$totalusers = sizeof($currentusers_array);
echo "<h2>Current Users: (total: ${totalusers})</h2>";
echo "<ul style='margin:0 auto;width:70%;'>";
foreach ($currentusers_array as $key => $value) {
	$username = explode("_",$value);
	$actually = $username[1];
    echo "<li style='background: #A53F2B;box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.16), 0 2px 5px 0 rgba(0, 0, 0, 0.26);'><span class='sender' style='color:#E0E2DB;font-weight:normal;'>${actually}</span></li>";
} 
echo "</ul>";
?>