<!DOCTYPE html>
<?php 
	
	function decodeHex($hex)
{
        $hex=strtoupper($hex);
        $chars="0123456789ABCDEF";
        $return="0";
        for($i=0;$i<strlen($hex);$i++)
        {
                $current=(string)strpos($chars,$hex[$i]);
                $return=(string)bcmul($return,"16",0);
                $return=(string)bcadd($return,$current,0);
        }
        return $return;
}
 
function encodeHex($dec)
{
        $chars="0123456789ABCDEF";
        $return="";
        while (bccomp($dec,0)==1)
        {
                $dv=(string)bcdiv($dec,"16",0);
                $rem=(integer)bcmod($dec,"16");
                $dec=$dv;
                $return=$return.$chars[$rem];
        }
        return strrev($return);
}
	
	function encodeBase58($hex)
{
        if(strlen($hex)%2!=0)
        {
                die("encodeBase58: uneven number of hex characters");
        }
        $orighex=$hex;
       
        $chars="123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
        $hex=decodeHex($hex);
        $return="";
        while (bccomp($hex,0)==1)
        {
                $dv=(string)bcdiv($hex,"58",0);
                $rem=(integer)bcmod($hex,"58");
                $hex=$dv;
                $return=$return.$chars[$rem];
        }
        $return=strrev($return);
       
        //leading zeros
        for($i=0;$i<strlen($orighex)&&substr($orighex,$i,2)=="00";$i+=2)
        {
                $return="1".$return;
        }
       
        return $return;
}

function base64_url_encode($input) {
 return strtr(base64_encode($input), '+/=', '._-');
}

$privchat = encodeBase58(bin2hex(random_bytes(16)));
//$encoded = base64_encode(sprintf('chat=%s',$privchat));
$notencoded = sprintf('chat=%s',$privchat);
	header('location:/privv/'.$privchat);
	?>
<head>
</head>
<body>
Coming soon.
</body>
</html>
