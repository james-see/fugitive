<!DOCTYPE html>
<?php 
	
function base64_url_decode($input) {
 return base64_decode(strtr($input, '._-', '+/='));
}

$cooler = array_keys($_GET);
echo $cooler[0];
$decoded = base64_decode($cooler[0]);

$mucher = explode('=',$decoded);
?>

<head>
</head>
<body>
	<?php echo $mucher[1];?>
</body>
</html>