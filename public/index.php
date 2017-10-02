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

if(isset($_POST["public_chat"]) && isset($_POST["username_hidden"])) {
	$postdata = strip_tags($_POST["public_chat"]);
	if (strlen($postdata) > 500) {
		$postdata = truncateString($postdata, 450, true, '...');
	}
	Predis\Autoloader::register();
$r = new Predis\Client();
$chatkeyfix = "pc_%s";
$formattingfix = "%s_%s_%s";
$public_chat_key = sprintf($chatkeyfix,guidv4(random_bytes(16)));
$public_chat_data = sprintf($formattingfix,date('Ymdhis'),$_SESSION['user'],$postdata);
$r->set($public_chat_key,$public_chat_data);
$r->expire($public_chat_key,30);
unset($_POST["public_chat"]);
header('location:'.$_SERVER['PHP_SELF']);
die();
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
    echo "<span>Username: <b style='color: #58C999;'><a href='profile.php?user=$username'>".$username."</a></b></span>";
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
<style type="text/css"> 
input, select, textarea, button {
    font-family:inherit;
    font-size: 14px;
    }

body {
    font-family: "Input Mono","PT Sans", "Consolas", monospace;
    font-size: 16px;
    background: #343434;
    letter-spacing: 0.08em;
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
</style>
</head>
<body>
    <h1><?php echo str_repeat("FUGITIVE.CHAT ", 7);?></h1>
    <div>
        <form action='index.php' id='EXCOM' method='POST' autocomplete="off" maxlength="500">
            <ul>
                <li style='background: #4d4d4d;'><span class='sender'><?php echo $username;?></span></li>
                <li><input name='public_chat' style="min-width:300px;" placeholder="write PUBLIC message here" type="text"/></li><input type='hidden' name='username_hidden' value="<?php echo $_SESSION['user'];?>"/>
                <li><button id='submitted' value='send' type='submit'>send</button></li>
            </ul>
        </form>
    </div>
    <div>
        <h1>FUGITIVE PUBLIC CHAT</h1>
        <!-- echo out current users -->
        <?php include('public.php');?>
        
    </div>
    <div>
	    <h2>Latest messages (public messages expire every 30 seconds)</h2>
	    <?php include('get_pub.php');?>
    <footer>
	    <div class'cover'>

	    </div>
    </footer>
</body>