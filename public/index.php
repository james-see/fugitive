<!DOCTYPE html>
<?php
// troubleshooting
if(isset($_GET['clear'])) { 
    header("Location: index.php");
    session_start();
    $oldusername = $_SESSION['user'];
    unset($_SESSION);
    unset($_SESSION['user']);
    session_unset();
    session_destroy();
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache"); 
    header("Expires: 0"); 
    require 'Predis/Autoloader.php';
Predis\Autoloader::register();
$r = new Predis\Client();
$r->srem("current_public_users",$oldusername);
}

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
    require 'Predis/Autoloader.php';
Predis\Autoloader::register();
$r = new Predis\Client();
$r->srem("current_public_users",$oldusername);
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// functions
function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

// redis connection
require 'Predis/Autoloader.php';
Predis\Autoloader::register();
$r = new Predis\Client();
//$r->srem("current_public_users","jamesc");
// example to interact with redis
//$r->set("set","new");
//echo $r->get("set");
//$coolio = $r->get("set");

// check for session id
session_start();
if(!isset($_SESSION['user']))
{   
    $array = explode("\n", file_get_contents('./assets/username_seeds.txt'));
    //echo $array[array_rand($array)]; get single item from array
    $username = $array[array_rand($array)];
    $username = clean($username);
    $_SESSION['user'] = $username;
    $username = $_SESSION['user'];
    echo "<span>Your current session is active and your username is: <b style='color: #58C999;'><a href='profile.php?user=$username'>".$username."</a></b></span><br />";
}
else
{

    $username = $_SESSION['user'];
    echo "<span>Your current session is active and your username is: <b style='color: #58C999;'><a href='profile.php?user=$username'>".$username."</a></b></span><br />";
}
    // debugging tools debug session variables this way
    //echo '<pre>';
    //var_dump($_SESSION);
    //echo '</pre>';
?>
<head>
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
li {
    display: inline-block;
    padding:5px;
    margin:2px 4px;
}
span {
    color: white;
}
a,span.sender {
    color: #58C999;
    font-weight: bold;
}
a.clearer {
    color: #E71D36;
    position:fixed;
    bottom:20px;
    padding: 4px 10px;
    margin:10px;
    border: 2px solid #E71D36;
}
a.clearer-float {
    color: #E71D36;
    position: fixed;
    bottom:20px;
    left: 300px;
    padding: 4px 10px;
    margin:10px;
    border: 2px solid #E71D36;
    float:right;
}
a:hover {
    color: #FFF;
}

h1 {
    overflow-x: hidden;
    white-space:nowrap;
}
</style>
</head>
<body>
    <h1><?php echo str_repeat("FUGITIVE.CHAT ", 7);?></h1>
    <div>
        <form action='add.php' id='EXCOM' method='POST'>
            <ul>
                <li><span class='sender'><?php echo $username;?></span></li>
                <li><input style="min-width:300px;" placeholder="write PUBLIC message here" type="text"/></li>
                <li><button id='submitted' value='send' type='submit'>send</button></li>
            </ul>
        </form>
    </div>
    <div>
        <h1>FUGITIVE PUBLIC CHAT</h1>
        <h2>Current Users:</h2>
        <?php include('public.php');?>
    </div>
    <footer>
        <?php echo "<span><a class='clearer' href='index.php?clear=true'>Clear current session</a></span>";?>
        <?php echo "<span><a class='clearer-float' href='index.php?logout=true'>Logout</a></span>";?>
    </footer>
</body>