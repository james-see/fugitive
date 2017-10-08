<!DOCTYPE html>
<?php
	session_start();

	require 'Predis/Autoloader.php';
	#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);
	#echo date('Y-m-d');
	$curdate = date('Y-m-d');

function truncateString($str, $chars, $to_space, $replacement="...") {
   if($chars > strlen($str)) return $str;

   $str = substr($str, 0, $chars);
   $space_pos = strrpos($str, " ");
   if($to_space && $space_pos >= 0) 
       $str = substr($str, 0, strrpos($str, " "));

   return($str . $replacement);
}

function guidv4($data)
{
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

if(isset($_POST["public_chat"]) && isset($_POST["username_hidden"]) && isset($_POST['capt'])) {
	//session_start();
	if($_POST['capt'] == $_SESSION['captcha']['code']) {
		$postdata = strip_tags($_POST["public_chat"]);
		if (strlen($postdata) > 500) {
			$postdata = truncateString($postdata, 450, true, '...');
		}
		# redis log public chat
		Predis\Autoloader::register();
		$r = new Predis\Client();
		$chatkeyfix = "pc_%s";
		$formattingfix = "%s_%s_%s";
		$public_chat_key = sprintf($chatkeyfix,guidv4(random_bytes(16)));
		$public_chat_data = sprintf($formattingfix,date('Ymdhis'),$_SESSION['user'],$postdata);
		$r->set($public_chat_key,$public_chat_data);
		$r->expire($public_chat_key,300);
		unset($_POST["public_chat"]);
		unset($_POST["capt"]);
		unset($_POST["username_hidden"]);
		header('location:'.$_SERVER['PHP_SELF']);
		die();
	}
}

// on change identity, clear old one and generate new one
if(isset($_GET['clear'])) { 
    header("Location: index.php");
    session_start();
    $oldusername = $_SESSION['user'];
    Predis\Autoloader::register();
$r = new Predis\Client();
$fixed = "user_%s";
$r->del(sprintf($fixed,$oldusername));
    unset($_SESSION);
    unset($_SESSION['user']);
    session_unset();
    session_destroy();
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache"); 
    header("Expires: 0"); 
}

// on logout destroy user and session
if(isset($_GET['logout'])) { 
    header("Location: bye.php");
    session_start();
    $oldusername = $_SESSION['user'];
    unset($_SESSION);
    unset($_SESSION['user']);
    session_unset();
    session_destroy();
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache"); 
    header("Expires: 0"); 
Predis\Autoloader::register();
$r = new Predis\Client();
$fixed = "user_%s";
$r->del(sprintf($fixed,$oldusername));
}

// functions
function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

// redis connection
	include("assets/simple-php-captcha/simple-php-captcha.php");
	$_SESSION['captcha'] = simple_php_captcha(array('angle_min' => 20,
    'angle_max' => 40, 'min_font_size' => 14, 'color' => '#a3a3a3',  'shadow_color' => '#f2f2f2',
    'shadow_offset_x' => 3,
    'shadow_offset_y' => 3, 'min_length' => 5,
    'max_length' => 8,
    'max_font_size' => 22,'characters' => 'ABCDEFGHJKLMNPRSTUVWXYZabcdefghjkmnprstuvwxyz23456789','shadow' => true,'fonts' => array('fonts/times_new_yorker.ttf','fonts/InputMonoCompressed-Medium.ttf','fonts/AppleMyungjo.ttf')));
Predis\Autoloader::register();
$r = new Predis\Client();
//$r->srem("current_public_users","jamesc");
// example to interact with redis
//$r->set("set","new");
//echo $r->get("set");
//$coolio = $r->get("set");

// check for session id
//session_start();
if(!isset($_SESSION['user']))
{   
    $array = explode("\n", file_get_contents('./assets/username_seeds.txt'));
    //echo $array[array_rand($array)]; get single item from array
    $username = $array[array_rand($array)];
    $username = clean($username);
    $_SESSION['user'] = $username;
    $username = $_SESSION['user'];
	$_SESSION['captcha'] = simple_php_captcha(array('angle_min' => 20,
    'angle_max' => 40, 'min_font_size' => 14, 'color' => '#a3a3a3',  'shadow_color' => '#f2f2f2',
    'shadow_offset_x' => 3,
    'shadow_offset_y' => 3, 'min_length' => 5,
    'max_length' => 8,
    'max_font_size' => 22,'characters' => 'ABCDEFGHJKLMNPRSTUVWXYZabcdefghjkmnprstuvwxyz23456789','shadow' => true,'fonts' => array('fonts/times_new_yorker.ttf','fonts/InputMonoCompressed-Medium.ttf','fonts/AppleMyungjo.ttf')));

    echo "<span>Username: <b style='color: #58C999;'><a href='profile.php?user=$username'>".$username."</a></b></span>";
    	echo "<span><a class='clearer' href='private.php'>Start Private Chat</a></span>";
        echo "<span><a class='clearer' href='index.php?clear=true'>Generate new user</a></span>";
        echo "<span><a class='clearer-float' href='index.php?logout=true'>End Session</a></span>";
}
else
{

    $username = $_SESSION['user'];
    echo "<span>Username: <b style='color: #58C999;'><a href='profile.php?user=$username'>".$username."</a></b></span>";
       echo "<span><a class='clearer' href='private.php'>Start Private Chat</a></span>";
       echo "<span><a class='clearer' href='index.php?clear=true'>Generate new user</a></span>";
        echo "<span><a class='clearer-float' href='index.php?logout=true'>End Session</a></span>";
};
    // debugging tools debug session variables this way
    //echo '<pre>';
    //var_dump($_SESSION);
    //echo '</pre>';
?>
<head>
	<meta name="viewport" content="initial-scale=1, maximum-scale=1">
	<meta http-equiv="refresh" content="30" >
<style type="text/css"> 
	
/* remove right click ability */
html {
	-webkit-touch-callout: none;
-webkit-user-select: none;
-khtml-user-select: none;
-moz-user-select: none;
-ms-user-select: none;
user-select: none;
}

input, select, textarea, button {
    font-family:inherit;
    font-size: 14px;
    }

body {
    font-family: "Input Mono","PT Sans", "Consolas", monospace;
    font-size: 16px;
    background: #343434;
    letter-spacing: 0.08em;
    margin: 0 auto;
}
ul {
    list-style: none;
}

li.publik {
	display:block;
	padding: 5px;
	background: #4d4d4d;
	margin-right:5%;
}

li {
    display: inline-block;
    padding:5px;
    margin:2px 4px;
}
span {
    color: white;
}
li span {
	display:inline-block;
	margin: 2px 4px;
}
li span.titler {
	margin:2px 4px;
	text-transform: uppercase;
}
li span.dater {
	font-weight: 400;
	color: #E71D36;
}
li span.messager {
	float:right;
	
}
a,span.sender {
    color: #58C999;
    font-weight: bold;
}
a.clearer {
    color: #E71D36;
    display: inline-block;
    top:10px;
    padding: 4px 10px;
    margin:10px;
    border: 2px solid #E71D36;
    background: #f2f2f2;
}
a.clearer-float {
    color: #E71D36;
    display: inline-block;
    top:10px;
    right: 300px;
    padding: 4px 10px;
    margin:10px;
    border: 2px solid #E71D36;
    background:#f2f2f2;
}
a:hover {
    color: #FFF;
}

h1 {
    overflow-x: hidden;
    white-space:nowrap;
}
a.clearer:hover, a.clearer-float:hover {
	color: #121212;
}
input:invalid {
  outline: 2px solid red;
}
input:focus:invalid {
  color: red;
}

/* for countdown */
@-webkit-keyframes change-color {
  0%   { background-color: #333333; }
  50% { background-color: red; }
  100% {color: #333333; background-color:#333333;}
}
@-moz-keyframes change-color {
  0%   { background-color: #333333; }
  50% { background-color: red; }
  100% {color: #333333; background-color:#333333;}
}
@-o-keyframes change-color {
  0%   { background-color: #333333; }
  50% { background-color: red; }
  100% {color: #333333; background-color:#333333;}
}
@keyframes change-color {
  0%   { background-color: #333333; }
  50% { background-color: red; }
  100% {color: #333333; background-color:#333333;}
}

@-webkit-keyframes wipeme {
  0%   { visibility:visible; }
  100% { visibility: hidden; }
}
@-moz-keyframes wipeme {
  0%   { visibility:visible; }
  100% { visibility: hidden; }
  }
@-o-keyframes wipeme {
  0%   { visibility:visible; }
  100% { visibility: hidden; }
}
@keyframes wipeme {
  0%   { visibility:visible; }
  100% { visibility: hidden; }
}

table,td,tr {
	font-family: "Lucida Console", Monaco, monospace;
	margin: 0 auto;
	font-size: 16px;
}
.fader2 {
	color: red;
width:70%; 
    margin-left:5%; 
    margin-right:5%;
}
#td1 {
	animation:         change-color 30.0s ease-in-out 0s forwards;
}
#td2 {
	animation:         change-color 29.0s ease-in-out 0s forwards;
}
#td3 {
	animation:         change-color 28.0s ease-in-out 0s forwards;
}
#td4 {
	animation:         change-color 27.0s ease-in-out 0s forwards;
}
#td5 {
	animation:         change-color 26.0s ease-in-out 0s forwards;
}
#td6 {
	animation:         change-color 25.0s ease-in-out 0s forwards;
}
#td7 {
	animation:         change-color 24.0s ease-in-out 0s forwards;
}
#td8 {
	animation:         change-color 23.0s ease-in-out 0s forwards;
}
#td9 {
	animation:         change-color 22.0s ease-in-out 0s forwards;
}
#td10 {
	animation:         change-color 21.0s ease-in-out 0s forwards;
}
#td11 {
	animation:         change-color 20.0s ease-in-out 0s forwards;
}
#td12 {
	animation:         change-color 19.0s ease-in-out 0s forwards;
}
#td13 {
	animation:         change-color 18.0s ease-in-out 0s forwards;
}
#td14 {
	animation:         change-color 17.0s ease-in-out 0s forwards;
}
#td15 {
	animation:         change-color 16.0s ease-in-out 0s forwards;
}
#td16 {
	animation:         change-color 15.0s ease-in-out 0s forwards;
}
#td17 {
	animation:         change-color 14.0s ease-in-out 0s forwards;
}
#td18 {
	animation:         change-color 13.0s ease-in-out 0s forwards;
}
#td19 {
	animation:         change-color 12.0s ease-in-out 0s forwards;
}
#td20 {
	animation:         change-color 11.0s ease-in-out 0s forwards;
}
#td21 {
	animation:         change-color 10.0s ease-in-out 0s forwards;
}
#td22 {
	animation:         change-color 9.0s ease-in-out 0s forwards;
}
#td23 {
	animation:         change-color 8.0s ease-in-out 0s forwards;
}
#td24 {
	animation:         change-color 7.0s ease-in-out 0s forwards;
}
#td25 {
	animation:         change-color 6.0s ease-in-out 0s forwards;
}
#td26 {
	animation:         change-color 5.0s ease-in-out 0s forwards;
}
#td27 {
	animation:         change-color 4.0s ease-in-out 0s forwards;
}
#td28 {
	animation:         change-color 3.0s ease-in-out 0s forwards;
}
#td29 {
	animation:         change-color 2.0s ease-in-out 0s forwards;
}
#td30 {
	animation:         change-color 1.0s ease-in-out 0s forwards;
}

.fader2 {
	animation: wipeme 30.0s ease-in-out 0s forwards;
}
</style>
</head>
<body oncontextmenu="return false">
    <h1><?php echo str_repeat("FUGITIVE.CHAT ", 7);?></h1>
    <div>
        <form action='index.php' id='EXCOM' method='POST' autocomplete="off" maxlength="500">
            <ul>
                <li style='background: #4d4d4d;'><span class='sender'><?php echo $username;?></span></li>
                <li><input name='public_chat' style="min-width:300px;" placeholder="write PUBLIC message here" type="text" required /></li><input type='hidden' name='username_hidden' value="<?php echo $_SESSION['user'];?>" required />
                <li><img src='<?php echo $_SESSION['captcha']['image_src'];?>'</li>
                <li><input name='capt' placeholder="put in captcha here" type="text" required /></li>
                <li><button id='submitted' value='send' type='submit'>send</button></li>
            </ul>
        </form>
        <table class="fader2" cellpadding="10" cellspacing="2">
<tr>
<td id='td30'>30</td>
<td id='td29'>29</td>
<td id='td28'>28</td>
<td id='td27'>27</td>
<td id='td26'>26</td>
<td id='td25'>25</td>
<td id='td24'>24</td>
<td id='td23'>23</td>
<td id='td22'>22</td>
<td id='td21'>21</td>
<td id='td20'>20</td>
<td id='td19'>19</td>
<td id='td18'>18</td>
<td id='td17'>17</td>
<td id='td16'>16</td>
<td id='td15'>15</td>
<td id='td14'>14</td>
<td id='td13'>13</td>
<td id='td12'>12</td>
<td id='td11'>11</td>
<td id='td10'>10</td>
<td id='td9'>9</td>
<td id='td8'>8</td>
<td id='td7'>7</td>
<td id='td6'>6</td>
<td id='td5'>5</td>
<td id='td4'>4</td>
<td id='td3'>3</td>
<td id='td2'>2</td>
<td id='td1'>1</td>
</tr>
</table>
    </div>
    <div>
        <h1>FUGITIVE PUBLIC CHAT</h1>
        <h2> (PAGE REFRESHES EVERY 30 SECONDS) </h2>
        <!-- echo out current users -->
        <?php include('public.php');?>
        
    </div>
    <div>
	    <h2>Latest messages (Each message expires after 5 minutes)</h2>
	    <?php include('get_pub.php');?>
    <footer>
	    <div class'cover'>

	    </div>
    </footer>
</body>