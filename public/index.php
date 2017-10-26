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
		header("Location: /");
		die();
	}
}

// on change identity, clear old one and generate new one
if(isset($_GET['clear'])) { 
    header("Location: /");
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
    header("Location: /bye/");
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
    'shadow_offset_y' => 3, 'min_length' => 4,
    'max_length' => 6,
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
    'shadow_offset_y' => 3, 'min_length' => 4,
    'max_length' => 6,
    'max_font_size' => 22,'characters' => 'ABCDEFGHJKLMNPRSTUVWXYZabcdefghjkmnprstuvwxyz23456789','shadow' => true,'fonts' => array('fonts/times_new_yorker.ttf','fonts/InputMonoCompressed-Medium.ttf','fonts/AppleMyungjo.ttf')));?>

<?php
}
else
{

    $username = $_SESSION['user'];

};
    // debugging tools debug session variables this way
    //echo '<pre>';
    //var_dump($_SESSION);
    //echo '</pre>';
?>
<head>
	<meta name="viewport" content="initial-scale=1, maximum-scale=1">
	<meta http-equiv="refresh" content="30" >
	<link rel="stylesheet" type="text/css" href="fugitive.css">
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
	color: #322E18;
    font-family: "Input Mono","PT Sans", "Consolas", monospace;
    font-size: 16px;
    background: #847979;
    letter-spacing: 0.08em;
    margin: 0 auto;
}
ul {
    list-style: none;
}

span.bar {
	margin: 2px 10px;

}

li.publik {
	display:block;
	padding: 5px;
	background: #ABA8B2;
	margin-right:5%;
	color: #A53F2B;
	margin-bottom: 5px;
	box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.16), 0 2px 5px 0 rgba(0, 0, 0, 0.26);
}

li {
    display: inline-block;
    padding:5px;
    margin:2px 4px;
}
span {
    color: white;
}
li span.sender {
	display:inline-block;
	margin: 2px 10px;
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
	color: #322E18;
	
}
a {
    color: #FFF;
    font-weight: bold;
}

span.sender {
    color: #A53F2B;
    font-weight: bold;
    margin: 2px 5px;
}

a.clearer {
    color: #E0E2DB;
    display: inline-block;
    top:10px;
    padding: 4px 10px;
    margin:10px;
    /*border: 2px solid #E0E2DB;*/
    background: #4C4B63;
    text-decoration: none;
    box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.16), 0 2px 5px 0 rgba(0, 0, 0, 0.26);
}
a.clearer-float {
    color: #E0E2DB;
    display: inline-block;
    top:10px;
    right: 300px;
    padding: 4px 10px;
    margin:10px;
    /*border: 2px solid #E0E2DB;*/
    background: #4C4B63;
    text-decoration: none;
    box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.16), 0 2px 5px 0 rgba(0, 0, 0, 0.26);
}
a:hover {
    color: #FFF;
}

h1 {
    overflow-x: hidden;
    white-space:nowrap;
    text-align: center;
}
a.clearer:hover, a.clearer-float:hover {
	color: #E0E2DB;
	text-decoration: underline;
	box-shadow: 0 17px 50px 0 rgba(0, 0, 0, 0.19), 0 12px 15px 0 rgba(0, 0, 0, 0.24);
	/*border: 2px solid #4C4B63;*/
}
input:invalid {
  outline: 2px solid #E0E2DB;
}
input:focus:invalid {
  color: red;
}

h2 {
	vertical-align: center;
	text-align: center;
	font-size: 1.2em;
}

/* for countdown */
@-webkit-keyframes change-color {
  0%   { background-color: #847979; }
  50% { background-color: #ABA8B2; }
  100% {color: #847979; background-color:#847979;}
}
@-moz-keyframes change-color {
  0%   { background-color: #847979; }
  50% { background-color: #ABA8B2; }
  100% {color: #847979; background-color:#847979;}
}
@-o-keyframes change-color {
  0%   { background-color: #847979; }
  50% { background-color: #ABA8B2; }
  100% {color: #847979; background-color:#847979;}
}
@keyframes change-color {
  0%   { background-color: #847979; }
  50% { background-color: #ABA8B2; }
  100% {color: #847979; background-color:#847979;}
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

ul.fader2 {
	/*text-align:center;*/
	  -webkit-margin-before: 0px;
-webkit-margin-after: 0px;
-webkit-margin-start: 0px;
-webkit-margin-end: 0px;
-webkit-padding-start: 0px;
	font-family: "Lucida Console", Monaco, monospace;
	margin: 0 auto;
	font-size: 16px;
  list-style:none;
  padding:0;

}
ul.fader2 li {
	  -webkit-margin-before: 0px;
-webkit-margin-after: 0px;
-webkit-margin-start: 0px;
-webkit-margin-end: 0px;
-webkit-padding-start: 0px;
  display: inline-block;
  padding:5px;
  /*margin:2px 5px;*/

}
.fader2 {
	color: #A53F2B;
width:70%; 

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
	<div class='wrapped'>
	        <input class="burger-check" id="burger-check" type="checkbox"><label for="burger-check" class="burger"></label>
			<nav id="navigation1" class="navigation">
			  <ul>
			    <li><a href='/profile/?user=<?php echo $username;?>'><?php echo $username;?></a></li>
			    <li><a class='clearer' target='_blank' href='/private/'>Start Private Chat</a></li>
			    <li><a class='clearer' href='/?clear=true'>Generate new user</a></li>
			    <li><a class='clearer-float' href='/?logout=true'>End Session</a></li>
			  </ul>
			</nav>
	</div>
        <h2> PAGE REFRESHES EVERY 30 SECONDS </h2>
    <div>
        <form action='/' id='EXCOM' method='POST' autocomplete="off" maxlength="500">
            <ul>
                <li style='background: #4d4d4d;box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.16), 0 2px 5px 0 rgba(0, 0, 0, 0.26);'><span class='sender' style='color:#E0E2DB;'><?php echo $username;?></span></li>
                <li><input name='public_chat' style="min-width:300px;" placeholder="write PUBLIC message here" type="text" required /></li><input type='hidden' name='username_hidden' value="<?php echo $_SESSION['user'];?>" required />
                <li><img src='<?php echo $_SESSION['captcha']['image_src'];?>'</li>
                <li><input name='capt' placeholder="put in captcha here" type="text" required /></li>
                <li><button id='submitted' value='send' type='submit'>send</button></li>
            </ul>
        </form>
<div>
<ul class="fader2">
<li id='td30'>30</li>
<li id='td29'>29</li>
<li id='td28'>28</li>
<li id='td27'>27</li>
<li id='td26'>26</li>
<li id='td25'>25</li>
<li id='td24'>24</li>
<li id='td23'>23</li>
<li id='td22'>22</li>
<li id='td21'>21</li>
<li id='td20'>20</li>
<li id='td19'>19</li>
<li id='td18'>18</li>
<li id='td17'>17</li>
<li id='td16'>16</li>
<li id='td15'>15</li>
<li id='td14'>14</li>
<li id='td13'>13</li>
<li id='td12'>12</li>
<li id='td11'>11</li>
<li id='td10'>10</li>
<li id='td9'>9</li>
<li id='td8'>8</li>
<li id='td7'>7</li>
<li id='td6'>6</li>
<li id='td5'>5</li>
<li id='td4'>4</li>
<li id='td3'>3</li>
<li id='td2'>2</li>
<li id='td1'>1</li>
</ul>
</div>
    </div>
    <div>

        <!-- echo out current users -->
        <?php include('public.php');?>
        
    </div>
    <div>
	    <h2>Latest messages <br />(expires after 5 minutes)</h2>
	    <?php include('get_pub.php');?>
    <footer>
	    <div class'cover'>

	    </div>
    </footer>
</body>