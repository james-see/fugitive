<!DOCTYPE html>
<style type="text/css"> 
body {
    font-family: "Input Mono","PT Sans", "Consolas", monospace;
    font-size: 16px;
    background: #343434;
    letter-spacing: 0.08em;
    color: white;
}
a,span.sender {
    color: #58C999;
    font-weight: bold;
}
a:hover {
    color: #9DC8C8;
}
</style>
<?php
// troubleshooting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// functions

// redis connection
require 'Predis/Autoloader.php';
Predis\Autoloader::register();
$r = new Predis\Client();
?>
<head>
</head>
<body>
    <?php if(isset($_GET['user'])) {$username = $_GET['user'];}
    echo "profile page for $username<br />";
    echo "<a href='index.php'>back to main page</a>";
    ?>
</body>
