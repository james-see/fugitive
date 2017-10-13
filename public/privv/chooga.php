<!DOCTYPE html>
<style type="text/css"> 
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
</style>
<?php
	$randnum = rand(15,42);
	session_start();
	require 'Predis/Autoloader.php';
	if(!isset($_SESSION['user']))
		{   
			header('Location:/');
		}
	else 
		{
			$username = $_SESSION['user'];
			$chatid = $_GET['chat'];
		}
?>
<head>
	<meta name="viewport" content="initial-scale=1, maximum-scale=1">
	<meta http-equiv="refresh" content="<?php echo $randnum ?>" >
<title>PRIVATE CHAT NUMBER <?php echo $chatid;?> BY <?php echo $username;?></title>
</head>
<body>
	<?php echo "<span>Welcome {$username} to chat session {$chatid}, page refreshing in {$randnum} seconds...</span>";?>
	<footer>
		<a href='/'>Back to home</a>
	</footer>
</body>

</html>